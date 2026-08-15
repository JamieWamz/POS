<?php

require_once __DIR__ . '/../core/bootstrap.php';

class ControllerSales
{
    private const PAYMENT_METHODS = ['cash', 'CC', 'DC'];

    public static function ctrShowSales($item, $value)
    {
        return ModelSales::mdlShowSales('sales', $item, $value);
    }

    private static function postedProducts(?string $fallbackJson = null): array
    {
        $json = trim((string) ($_POST['productsList'] ?? ''));
        if ($json === '' && $fallbackJson !== null) {
            $json = $fallbackJson;
        }
        $decoded = json_decode($json, true, 64, JSON_THROW_ON_ERROR);
        if (!is_array($decoded) || $decoded === [] || count($decoded) > 200) {
            throw new InvalidArgumentException('Add at least one product to the sale.');
        }

        $quantities = [];
        foreach ($decoded as $line) {
            $id = filter_var($line['id'] ?? null, FILTER_VALIDATE_INT);
            $quantity = filter_var($line['quantity'] ?? null, FILTER_VALIDATE_INT);
            if (!$id || !$quantity || $quantity < 1 || $quantity > 100000) {
                throw new InvalidArgumentException('A sale contains an invalid product quantity.');
            }
            $quantities[$id] = ($quantities[$id] ?? 0) + $quantity;
        }
        ksort($quantities, SORT_NUMERIC);
        return $quantities;
    }

    private static function paymentMethod(?string $fallback = null): string
    {
        $method = (string) ($_POST['newPaymentMethod'] ?? '');
        if (!in_array($method, self::PAYMENT_METHODS, true)) {
            throw new InvalidArgumentException('Select a valid payment method.');
        }
        if ($method !== 'cash') {
            $reference = trim((string) ($_POST['newTransactionCode'] ?? ''));
            if ($reference === '' && $fallback !== null && str_starts_with($fallback, $method . '-')) {
                return $fallback;
            }
            if (!preg_match('/^[A-Za-z0-9-]{3,60}$/', $reference)) {
                throw new InvalidArgumentException('Enter a valid transaction reference.');
            }
            return $method . '-' . $reference;
        }
        return 'cash';
    }

    private static function taxRate(): float
    {
        $rate = filter_var($_POST['newTaxSale'] ?? 0, FILTER_VALIDATE_FLOAT);
        if ($rate === false || $rate < 0 || $rate > 100) {
            throw new InvalidArgumentException('Tax must be between 0 and 100 percent.');
        }
        return (float) $rate;
    }

    private static function customerId(): int
    {
        $id = filter_var($_POST['selectCustomer'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            throw new InvalidArgumentException('Select a valid customer.');
        }
        $stmt = Connection::connect()->prepare('SELECT id FROM customers WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $id]);
        if (!$stmt->fetchColumn()) {
            throw new InvalidArgumentException('The selected customer no longer exists.');
        }
        return (int) $id;
    }

    private static function lockProducts(array $newQuantities, array $oldQuantities = []): array
    {
        $ids = array_unique(array_merge(array_keys($newQuantities), array_keys($oldQuantities)));
        sort($ids, SORT_NUMERIC);
        $products = [];
        foreach ($ids as $id) {
            $product = ProductsModel::mdlLockProduct((int) $id);
            if (!$product) {
                throw new RuntimeException('A product in this sale no longer exists.');
            }
            $products[(int) $id] = $product;
        }
        return $products;
    }

    private static function applyInventory(array $products, array $newQuantities, array $oldQuantities = []): array
    {
        $lines = [];
        $net = 0.0;
        $quantityTotal = 0;
        foreach ($products as $id => $product) {
            $restoredStock = (int) $product['stock'] + (int) ($oldQuantities[$id] ?? 0);
            $restoredSales = max(0, (int) $product['sales'] - (int) ($oldQuantities[$id] ?? 0));
            $quantity = (int) ($newQuantities[$id] ?? 0);
            if ($quantity > $restoredStock) {
                throw new RuntimeException('Not enough stock for ' . $product['description'] . '.');
            }

            ProductsModel::mdlSetInventory($id, $restoredStock - $quantity, $restoredSales + $quantity);
            if ($quantity > 0) {
                $unitPrice = round((float) $product['sellingPrice'], 2);
                $lineTotal = round($unitPrice * $quantity, 2);
                $net += $lineTotal;
                $quantityTotal += $quantity;
                $lines[] = [
                    'id' => $id,
                    'description' => (string) $product['description'],
                    'quantity' => $quantity,
                    'stock' => $restoredStock - $quantity,
                    'price' => number_format($unitPrice, 2, '.', ''),
                    'totalPrice' => number_format($lineTotal, 2, '.', ''),
                ];
            }
        }
        return ['lines' => $lines, 'net' => round($net, 2), 'quantity' => $quantityTotal];
    }

    private static function quantitiesFromSnapshot(string $json): array
    {
        $items = json_decode($json, true);
        $quantities = [];
        foreach (is_array($items) ? $items : [] as $item) {
            $id = (int) ($item['id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if ($id > 0 && $quantity > 0) {
                $quantities[$id] = ($quantities[$id] ?? 0) + $quantity;
            }
        }
        ksort($quantities, SORT_NUMERIC);
        return $quantities;
    }

    private static function adjustCustomerPurchases(int $customerId, int $delta): void
    {
        $stmt = Connection::connect()->prepare(
            'UPDATE customers SET purchases = GREATEST(0, purchases + :delta), lastPurchase = CURRENT_TIMESTAMP WHERE id = :id'
        );
        $stmt->execute(['delta' => $delta, 'id' => $customerId]);
    }

    private static function refreshCustomerLastPurchase(int $customerId): void
    {
        $stmt = Connection::connect()->prepare('SELECT MAX(saledate) FROM sales WHERE idCustomer = :id');
        $stmt->execute(['id' => $customerId]);
        $last = $stmt->fetchColumn() ?: null;
        $update = Connection::connect()->prepare('UPDATE customers SET lastPurchase = :lastPurchase WHERE id = :id');
        $update->execute(['lastPurchase' => $last, 'id' => $customerId]);
    }

    private static function saleData(int $code, int $customerId, array $calculated, float $taxRate, ?array $previousSale = null): array
    {
        $tax = round($calculated['net'] * $taxRate / 100, 2);
        $total = round($calculated['net'] + $tax, 2);
        $paymentMethod = self::paymentMethod(isset($previousSale['paymentMethod']) ? (string) $previousSale['paymentMethod'] : null);
        $amountTendered = null;
        $changeDue = 0.0;
        if ($paymentMethod === 'cash') {
            $rawTendered = str_replace(',', '', trim((string) ($_POST['newCashValue'] ?? '')));
            if ($rawTendered === '' && $previousSale !== null) {
                $rawTendered = (string) ($previousSale['amountTendered'] ?? $previousSale['totalPrice'] ?? '');
            }
            $validatedTendered = filter_var($rawTendered, FILTER_VALIDATE_FLOAT);
            if ($validatedTendered === false || $validatedTendered < $total || $validatedTendered > 999999999.99) {
                throw new InvalidArgumentException('Cash received must cover the sale total.');
            }
            $amountTendered = round((float) $validatedTendered, 2);
            $changeDue = round($amountTendered - $total, 2);
        }
        return [
            'code' => $code,
            'idCustomer' => $customerId,
            'idSeller' => (int) $_SESSION['id'],
            'products' => json_encode($calculated['lines'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
            'taxRate' => number_format($taxRate, 2, '.', ''),
            'tax' => number_format($tax, 2, '.', ''),
            'netPrice' => number_format($calculated['net'], 2, '.', ''),
            'totalPrice' => number_format($total, 2, '.', ''),
            'paymentMethod' => $paymentMethod,
            'amountTendered' => $amountTendered === null ? null : number_format($amountTendered, 2, '.', ''),
            'changeDue' => number_format($changeDue, 2, '.', ''),
        ];
    }

    public static function ctrCreateSale(): void
    {
        if (!isset($_POST['newSale'])) {
            return;
        }
        require_auth(['Administrator', 'Seller']);
        $db = Connection::connect();
        try {
            $db->beginTransaction();
            $quantities = self::postedProducts();
            $customerId = self::customerId();
            $products = self::lockProducts($quantities);
            $calculated = self::applyInventory($products, $quantities);
            $data = self::saleData(ModelSales::mdlNextCode(), $customerId, $calculated, self::taxRate());
            ModelSales::mdlAddSale('sales', $data);
            self::adjustCustomerPurchases($customerId, $calculated['quantity']);
            $db->commit();
            audit_event('sale.created', 'sale', $data['code'], ['total' => $data['totalPrice'], 'items' => $calculated['quantity']]);
            ui_alert('success', 'Sale completed. Your receipt is ready to print.', 'views/modules/print-receipt.php?code=' . $data['code'] . '&autoprint=1');
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            ui_alert('error', app_config('debug') ? $error->getMessage() : 'The sale could not be completed. Check stock and payment details.', 'create-sale');
        }
    }

    public static function ctrEditSale(): void
    {
        if (!isset($_POST['editSale'])) {
            return;
        }
        require_auth(['Administrator']);
        $code = filter_var($_POST['editSale'], FILTER_VALIDATE_INT);
        $db = Connection::connect();
        try {
            if (!$code) {
                throw new InvalidArgumentException('Invalid sale.');
            }
            $db->beginTransaction();
            $sale = ModelSales::mdlLockSaleByCode((int) $code);
            if (!$sale) {
                throw new RuntimeException('Sale not found.');
            }
            $oldQuantities = self::quantitiesFromSnapshot($sale['products']);
            $newQuantities = self::postedProducts($sale['products']);
            $customerId = self::customerId();
            $products = self::lockProducts($newQuantities, $oldQuantities);
            $calculated = self::applyInventory($products, $newQuantities, $oldQuantities);
            $data = self::saleData((int) $code, $customerId, $calculated, self::taxRate(), $sale);
            ModelSales::mdlEditSale('sales', $data);

            $oldQuantityTotal = array_sum($oldQuantities);
            if ((int) $sale['idCustomer'] === $customerId) {
                self::adjustCustomerPurchases($customerId, $calculated['quantity'] - $oldQuantityTotal);
            } else {
                self::adjustCustomerPurchases((int) $sale['idCustomer'], -$oldQuantityTotal);
                self::adjustCustomerPurchases($customerId, $calculated['quantity']);
                self::refreshCustomerLastPurchase((int) $sale['idCustomer']);
            }
            $db->commit();
            audit_event('sale.updated', 'sale', $code, ['total' => $data['totalPrice']]);
            ui_alert('success', 'Sale updated successfully.', 'sales');
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            ui_alert('error', app_config('debug') ? $error->getMessage() : 'The sale could not be updated.', 'sales');
        }
    }

    public static function ctrDeleteSale(): void
    {
        if (!isset($_POST['deleteSaleId'])) {
            return;
        }
        require_auth(['Administrator']);
        $id = filter_var($_POST['deleteSaleId'], FILTER_VALIDATE_INT);
        $db = Connection::connect();
        try {
            if (!$id) {
                throw new InvalidArgumentException('Invalid sale.');
            }
            $db->beginTransaction();
            $sale = ModelSales::mdlLockSaleById((int) $id);
            if (!$sale) {
                throw new RuntimeException('Sale not found.');
            }
            $oldQuantities = self::quantitiesFromSnapshot($sale['products']);
            $products = self::lockProducts([], $oldQuantities);
            self::applyInventory($products, [], $oldQuantities);
            ModelSales::mdlDeleteSale('sales', $id);
            self::adjustCustomerPurchases((int) $sale['idCustomer'], -array_sum($oldQuantities));
            self::refreshCustomerLastPurchase((int) $sale['idCustomer']);
            $db->commit();
            audit_event('sale.deleted', 'sale', $id);
            ui_alert('success', 'Sale deleted and inventory restored.', 'sales');
        } catch (Throwable $error) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            ui_alert('error', app_config('debug') ? $error->getMessage() : 'The sale could not be deleted.', 'sales');
        }
    }

    public static function ctrSalesDatesRange($initialDate, $finalDate)
    {
        $start = valid_date($initialDate);
        $end = valid_date($finalDate);
        if (($initialDate !== null || $finalDate !== null) && (!$start || !$end || $start > $end)) {
            throw new InvalidArgumentException('Invalid sales report date range.');
        }
        return ModelSales::mdlSalesDatesRange('sales', $start, $end);
    }

    private static function safeSpreadsheetCell(mixed $value): string
    {
        $value = (string) $value;
        return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
    }

    public function ctrDownloadReport($initialDate = null, $finalDate = null): void
    {
        require_auth(['Administrator', 'Seller']);
        $sales = self::ctrSalesDatesRange($initialDate, $finalDate);
        audit_event('report.exported', 'sales_report', null, ['from' => $initialDate, 'to' => $finalDate]);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="golden-tap-sales.csv"');
        $stream = fopen('php://output', 'wb');
        fwrite($stream, "\xEF\xBB\xBF");
        fputcsv($stream, ['Sale', 'Customer', 'Seller', 'Quantity', 'Products', 'VAT rate', 'VAT amount', 'Net price', 'Total', 'Payment', 'Date']);
        foreach ($sales as $sale) {
            $customer = ControllerCustomers::ctrShowCustomers('id', $sale['idCustomer']);
            $seller = ControllerUsers::ctrShowUsers('id', $sale['idSeller']);
            $products = json_decode($sale['products'], true) ?: [];
            fputcsv($stream, array_map([self::class, 'safeSpreadsheetCell'], [
                $sale['code'],
                $customer['name'] ?? 'Walk-in Customer',
                $seller['name'] ?? 'System',
                array_sum(array_column($products, 'quantity')),
                implode(', ', array_column($products, 'description')),
                ($sale['taxRate'] ?? 0) . '%',
                $sale['tax'],
                $sale['netPrice'],
                $sale['totalPrice'],
                $sale['paymentMethod'],
                $sale['saledate'],
            ]));
        }
        fclose($stream);
        exit;
    }

    public static function ctrAddingTotalSales()
    {
        return ModelSales::mdlAddingTotalSales('sales');
    }

    public static function ctrDownloadXML(?int $requestedCode = null): string|false
    {
        require_auth(['Administrator', 'Seller']);
        $code = $requestedCode ?? filter_input(INPUT_GET, 'xml', FILTER_VALIDATE_INT);
        if (!$code) {
            return false;
        }
        $sale = ModelSales::mdlShowSales('sales', 'code', $code);
        if (!$sale) {
            return false;
        }
        $products = json_decode($sale['products'], true) ?: [];
        $customer = ModelCustomers::mdlShowCustomers('customers', 'id', $sale['idCustomer']);
        $seller = UsersModel::MdlShowUsers('users', 'id', $sale['idSeller']);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><invoice/>');
        $xml->addChild('number', (string) $sale['code']);
        $xml->addChild('date', substr((string) $sale['saledate'], 0, 10));
        $xml->addChild('seller', (string) ($seller['name'] ?? 'System'));
        $xml->addChild('customer', (string) ($customer['name'] ?? 'Walk-in Customer'));
        $lines = $xml->addChild('items');
        foreach ($products as $product) {
            $line = $lines->addChild('item');
            $line->addChild('description', (string) ($product['description'] ?? ''));
            $line->addChild('quantity', (string) (int) ($product['quantity'] ?? 0));
            $line->addChild('unitPrice', (string) ($product['price'] ?? '0.00'));
            $line->addChild('total', (string) ($product['totalPrice'] ?? '0.00'));
        }
        $xml->addChild('taxRate', (string) ($sale['taxRate'] ?? '0.00'));
        $xml->addChild('tax', (string) $sale['tax']);
        $xml->addChild('net', (string) $sale['netPrice']);
        $xml->addChild('total', (string) $sale['totalPrice']);
        audit_event('invoice.exported', 'sale', $code, ['format' => 'xml']);
        return $xml->asXML();
    }
}

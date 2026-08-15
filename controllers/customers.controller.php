<?php

require_once __DIR__ . '/../core/bootstrap.php';

class ControllerCustomers
{
    private static function customerData(string $prefix): ?array
    {
        $name = trim((string) ($_POST[$prefix . 'Customer'] ?? ''));
        $email = trim((string) ($_POST[$prefix . 'Email'] ?? ''));
        $phone = trim((string) ($_POST[$prefix . 'Phone'] ?? ''));
        $address = trim((string) ($_POST[$prefix . 'Address'] ?? ''));
        $birthdate = valid_date((string) ($_POST[$prefix . 'Birthdate'] ?? ''));

        if (!preg_match('/^[\p{L}\p{N} .\'-]{2,120}$/u', $name)
            || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
            || !preg_match('/^[0-9+() -]{7,30}$/', $phone)
            || strlen($address) > 255
            || !$birthdate) {
            return null;
        }
        return compact('name', 'email', 'phone', 'address', 'birthdate');
    }

    public static function ctrCreateCustomer(): void
    {
        if (!isset($_POST['newCustomer'])) {
            return;
        }
        require_auth(['Administrator', 'Seller']);
        $data = self::customerData('new');
        $redirect = ($_POST['returnTo'] ?? '') === 'create-sale' ? 'create-sale' : 'customers';
        if (!$data) {
            ui_alert('error', 'Check the customer details and try again.', $redirect);
            return;
        }
        $result = ModelCustomers::mdlAddCustomer('customers', $data);
        if ($result === 'ok') { audit_event('customer.created', 'customer', null, ['name' => $data['name']]); }
        ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Customer saved.' : 'Customer could not be saved.', $redirect);
    }

    public static function ctrEditCustomer(): void
    {
        if (!isset($_POST['editCustomer'])) {
            return;
        }
        require_auth(['Administrator', 'Seller']);
        $data = self::customerData('edit');
        $id = filter_var($_POST['idCustomer'] ?? null, FILTER_VALIDATE_INT);
        if (!$data || !$id) {
            ui_alert('error', 'Check the customer details and try again.', 'customers');
            return;
        }
        $data['id'] = $id;
        $result = ModelCustomers::mdlEditCustomer('customers', $data);
        if ($result === 'ok') { audit_event('customer.updated', 'customer', $id); }
        ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Customer updated.' : 'Customer could not be updated.', 'customers');
    }

    public static function ctrShowCustomers($item, $value)
    {
        return ModelCustomers::mdlShowCustomers('customers', $item, $value);
    }

    public static function ctrDeleteCustomer(): void
    {
        if (!isset($_POST['deleteCustomerId'])) {
            return;
        }
        require_auth(['Administrator']);
        $id = filter_var($_POST['deleteCustomerId'], FILTER_VALIDATE_INT);
        $result = $id ? ModelCustomers::mdlDeleteCustomer('customers', $id) : 'error';
        if ($result === 'ok') { audit_event('customer.deleted', 'customer', $id); }
        $message = $result === 'ok' ? 'Customer deleted.' : ($result === 'in_use' ? 'Customers with sales history cannot be deleted.' : 'Customer could not be deleted.');
        ui_alert($result === 'ok' ? 'success' : 'error', $message, 'customers');
    }
}

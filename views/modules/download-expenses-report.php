<?php

require_once __DIR__ . '/../../core/bootstrap.php';
require_auth(['Administrator', 'Seller']);

require_once __DIR__ . '/../../controllers/expenses.controller.php';
require_once __DIR__ . '/../../models/expenses.model.php';
require_once __DIR__ . '/../../controllers/users.controller.php';
require_once __DIR__ . '/../../models/users.model.php';

$initialDate = valid_date(isset($_GET['initialDate']) ? (string) $_GET['initialDate'] : null);
$finalDate = valid_date(isset($_GET['finalDate']) ? (string) $_GET['finalDate'] : null);
if ((isset($_GET['initialDate']) || isset($_GET['finalDate'])) && (!$initialDate || !$finalDate || $initialDate > $finalDate)) {
    abort_request(422, 'Invalid report date range.');
}
$expenses = ControllerExpenses::ctrExpensesDatesRange($initialDate, $finalDate);
audit_event('report.exported', 'expense_report', null, ['from' => $initialDate, 'to' => $finalDate]);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="golden-tap-expenses.csv"');
$stream = fopen('php://output', 'wb');
fwrite($stream, "\xEF\xBB\xBF");
fputcsv($stream, ['#', 'Description', 'Category', 'Amount', 'Issued by', 'Date']);
foreach ($expenses as $index => $expense) {
    $user = ControllerUsers::ctrShowUsers('id', $expense['id_user']);
    $cells = [$index + 1, $expense['description'], $expense['category'], $expense['amount'], $user['name'] ?? 'N/A', $expense['date']];
    foreach ($cells as &$cell) {
        $cell = preg_match('/^[=+\-@]/', (string) $cell) ? "'" . $cell : $cell;
    }
    unset($cell);
    fputcsv($stream, $cells);
}
fclose($stream);
exit;

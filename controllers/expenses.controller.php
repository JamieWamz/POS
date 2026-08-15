<?php

require_once __DIR__ . '/../core/bootstrap.php';

class ControllerExpenses
{
    private static function expenseData(string $prefix): ?array
    {
        $description = trim((string) ($_POST[$prefix . 'Description'] ?? ''));
        $category = trim((string) ($_POST[$prefix . 'Category'] ?? ''));
        $amount = filter_var($_POST[$prefix . 'Amount'] ?? null, FILTER_VALIDATE_FLOAT);
        if (!preg_match('/^[\p{L}\p{N} &,.\'-]{2,180}$/u', $description)
            || !preg_match('/^[\p{L}\p{N} &.\'-]{2,80}$/u', $category)
            || $amount === false || $amount <= 0 || $amount > 999999999.99) {
            return null;
        }
        return [
            'description' => $description,
            'category' => $category,
            'amount' => number_format((float) $amount, 2, '.', ''),
        ];
    }

    public static function ctrCreateExpense(): void
    {
        if (!isset($_POST['newDescription'])) {
            return;
        }
        require_auth(['Administrator', 'Seller']);
        $data = self::expenseData('new');
        if (!$data) {
            ui_alert('error', 'Check the expense details and amount.', 'expenses');
            return;
        }
        $data['id_user'] = (int) $_SESSION['id'];
        $result = ModelExpenses::mdlAddExpense('expenses', $data);
        if ($result === 'ok') { audit_event('expense.created', 'expense', null, ['amount' => $data['amount'], 'category' => $data['category']]); }
        ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Expense saved.' : 'Expense could not be saved.', 'expenses');
    }

    public static function ctrShowExpenses($item, $value)
    {
        return ModelExpenses::mdlShowExpenses('expenses', $item, $value);
    }

    public static function ctrExpensesDatesRange($initialDate, $finalDate)
    {
        $start = valid_date($initialDate);
        $end = valid_date($finalDate);
        if (($initialDate !== null || $finalDate !== null) && (!$start || !$end || $start > $end)) {
            throw new InvalidArgumentException('Invalid expense report date range.');
        }
        return ModelExpenses::mdlExpensesDatesRange('expenses', $start, $end);
    }

    public static function ctrEditExpense(): void
    {
        if (!isset($_POST['editDescription'])) {
            return;
        }
        require_auth(['Administrator', 'Seller']);
        $data = self::expenseData('edit');
        $id = filter_var($_POST['idExpense'] ?? null, FILTER_VALIDATE_INT);
        $existing = $id ? ModelExpenses::mdlShowExpenses('expenses', 'id', $id) : false;
        $canEdit = $existing && (current_user_role() === 'Administrator' || (int) $existing['id_user'] === (int) $_SESSION['id']);
        if (!$data || !$id || !$canEdit) {
            ui_alert('error', 'Check the expense details and amount.', 'expenses');
            return;
        }
        $data['id'] = $id;
        $result = ModelExpenses::mdlEditExpense('expenses', $data);
        if ($result === 'ok') { audit_event('expense.updated', 'expense', $id, ['amount' => $data['amount']]); }
        ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Expense updated.' : 'Expense could not be updated.', 'expenses');
    }

    public static function ctrDeleteExpense(): void
    {
        if (!isset($_POST['deleteExpenseId'])) {
            return;
        }
        require_auth(['Administrator']);
        $id = filter_var($_POST['deleteExpenseId'], FILTER_VALIDATE_INT);
        $result = $id ? ModelExpenses::mdlDeleteExpense('expenses', $id) : 'error';
        if ($result === 'ok') { audit_event('expense.deleted', 'expense', $id); }
        ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Expense deleted.' : 'Expense could not be deleted.', 'expenses');
    }
}

<?php

require_once __DIR__ . '/../core/bootstrap.php';

class ControllerCategories
{
    private static function validName(string $name): bool
    {
        return (bool) preg_match('/^[\p{L}\p{N} &.\'-]{2,80}$/u', $name);
    }

    public static function ctrCreateCategory(): void
    {
        if (!isset($_POST['newCategory'])) {
            return;
        }
        require_auth(['Administrator', 'Special']);
        $name = trim((string) $_POST['newCategory']);
        if (!self::validName($name)) {
            ui_alert('error', 'Enter a valid category name.', 'categories');
            return;
        }
        $result = CategoriesModel::mdlAddCategory('categories', $name);
        if ($result === 'ok') { audit_event('category.created', 'category', null, ['name' => $name]); }
        ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Category saved.' : 'Category could not be saved.', 'categories');
    }

    public static function ctrShowCategories($item, $value)
    {
        return CategoriesModel::mdlShowCategories('categories', $item, $value);
    }

    public static function ctrEditCategory(): void
    {
        if (!isset($_POST['editCategory'])) {
            return;
        }
        require_auth(['Administrator', 'Special']);
        $name = trim((string) $_POST['editCategory']);
        $id = filter_var($_POST['idCategory'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || !self::validName($name)) {
            ui_alert('error', 'Enter a valid category name.', 'categories');
            return;
        }
        $result = CategoriesModel::mdlEditCategory('categories', ['Category' => $name, 'id' => $id]);
        if ($result === 'ok') { audit_event('category.updated', 'category', $id, ['name' => $name]); }
        ui_alert($result === 'ok' ? 'success' : 'error', $result === 'ok' ? 'Category updated.' : 'Category could not be updated.', 'categories');
    }

    public static function ctrDeleteCategory(): void
    {
        if (!isset($_POST['deleteCategoryId'])) {
            return;
        }
        require_auth(['Administrator']);
        $id = filter_var($_POST['deleteCategoryId'], FILTER_VALIDATE_INT);
        $result = $id ? CategoriesModel::mdlDeleteCategory('categories', $id) : 'error';
        if ($result === 'ok') { audit_event('category.deleted', 'category', $id); }
        $message = $result === 'ok' ? 'Category deleted.' : ($result === 'in_use' ? 'Move or delete products in this category first.' : 'Category could not be deleted.');
        ui_alert($result === 'ok' ? 'success' : 'error', $message, 'categories');
    }
}

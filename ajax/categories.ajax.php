<?php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../controllers/categories.controller.php';
require_once __DIR__ . '/../models/categories.model.php';

require_method('POST');
require_auth(['Administrator', 'Special']);
require_csrf();
header('Content-Type: application/json; charset=utf-8');

$id = filter_input(INPUT_POST, 'idCategory', FILTER_VALIDATE_INT);
if (!$id) {
    abort_request(422, 'A valid category is required.');
}
echo json_encode(ControllerCategories::ctrShowCategories('id', $id), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

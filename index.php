<?php declare(strict_types=1);

spl_autoload_register(function ($class) {
    include "./src/$class.php";
});
set_error_handler([ErrorHandler::class, 'handleError']);
set_exception_handler([ErrorHandler::class, 'handleException']);
header('Content-Type: application/json; charset=UTF-8');
$method = $_SERVER['REQUEST_METHOD'];
$parts = explode('/', $_SERVER['REQUEST_URI']);
if ($parts[2] != 'products') {
    http_response_code(404);
    exit;
}
$id = $parts[3] ?? '';
$database = new Database('localhost', 'product_db', 'root', 'admin123');
$gateway = new ProductGateway($database);
$controller = new ProductController($gateway);
$controller->processRequest($method, $id);

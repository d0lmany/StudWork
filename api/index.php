<?php
#conf
require_once "./configuration.php";
require_once "./views/baseView.php";
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    baseView::render(['error' => $e->getMessage()], 503, true);
    exit;
}
#settings
$method = $_SERVER['REQUEST_METHOD'];
$uriParts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$entity = $uriParts[1] ?? null;
$id = $uriParts[2] ?? null;
#routers
if (empty($entity)) {
    if (DEV_MODE) {
        require_once "./wire/page.php";
    } else {
        baseView::render(['message' => 'wait for requests'], 200);
    }
} else {
    require_once "./controllers/baseController.php";
    $controller = new baseController($pdo, $entity, $validations);
    switch ($method) {
        case 'GET':
            if ($id) {
                $data = $controller->getOne($id);
                baseView::render($data, 200);
            } else {
                $data = $controller->getAll();
                baseView::render($data, 200);
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            switch ($id) {
                case 'filtered':
                    $result = $controller->getFiltered($data);
                    baseView::render($result, 200);
                    break;
                case 'search':
                    $result = $controller->search($data);
                    baseView::render($result, 200);
                    break;
                case 'token':
                    $result = $controller->generateToken($data);
                    baseView::render($result, 201);
                    break;
                case 'auth':
                    $result = $controller->auth($data);
                    baseView::render($result, 200);
                    break;
                default:
                    $result = $controller->add($data);
                    baseView::render($result, 201);
                    break;
            }
            break;
        case 'DELETE':
            if ($id) {
                $result = $controller->delete($id);
                baseView::render($result, 202);
            } else {
                baseView::render(['error' => 'object is not selected'], 400);
            }
            break;
        case 'PUT':
            if ($id) {
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $controller->put($id, $data);
                baseView::render($result, 202);
            } else {
                baseView::render(['error' => 'object is not selected'], 400);
            }
            break;
        case 'PATCH':
            if ($id) {
                $data = json_decode(file_get_contents('php://input'), true);
                $result = $controller->patch($id, $data);
                baseView::render($result, 202);
            } else {
                baseView::render(['error' => 'object is not selected'], 400);
            }
            break;
        default:
            baseView::render(['error' => 'method is not implement'], 405);
    }
}
#logging
if (LOGGING) {
    try {
        if (file_exists("logging.txt") || fopen("logging.txt", 'a')) {
            $row = date('Y-m-d H:i:s') . " | $method | " . $_SERVER['REQUEST_URI'] . PHP_EOL;
            file_put_contents("logging.txt", $row, FILE_APPEND);
        }
    } catch (Exception $e) {echo $e;}
}
#the end
$pdo = null;
$controller = null;
$data = null;
$result = null;
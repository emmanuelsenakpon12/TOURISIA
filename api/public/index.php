<?php 
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../core/ErrorHandler.php';

try {
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = $scriptName . '/index.php';
    if (strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
        if ($requestUri === '') $requestUri = '/';
    }

    $routes = require __DIR__ . '/../config/routes.php';

    if (isset($routes[$requestMethod][$requestUri])) {
        $handler = $routes[$requestMethod][$requestUri];
        list($controllerName, $methodName) = explode('@', $handler);
        require_once __DIR__ . "/../controllers/$controllerName.php";
        $controller = new $controllerName();
        $data = json_decode(file_get_contents('php://input'), true) ?? $_REQUEST;
        $controller->$methodName($data);
    } else {
        throw new Exception("Route non trouvée", 404);
    }
} catch (Throwable $e) {
    ErrorHandler::handle($e);
}

<?php
// Router - Simple MVC Router with Array Map
require_once __DIR__ . '/config/config.php';

// Route mapping
$routes = [
    'landing' => ['controller' => 'LandingController', 'method' => 'index', 'needs_id' => false],
    'dashboard' => ['controller' => 'DashboardController', 'method' => 'index', 'needs_id' => false],
    'index' => ['controller' => 'HomeController', 'method' => 'index', 'needs_id' => false],
    'create' => ['controller' => 'HomeController', 'method' => 'create', 'needs_id' => false],
    'edit' => ['controller' => 'HomeController', 'method' => 'edit', 'needs_id' => true],
    'view' => ['controller' => 'ResultController', 'method' => 'view', 'needs_id' => true],
    'delete' => ['controller' => 'HomeController', 'method' => 'delete', 'needs_id' => true],
    'simulasi' => ['controller' => 'SimulasiController', 'method' => 'index', 'needs_id' => false]
];

$action = $_GET['action'] ?? 'landing';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (isset($routes[$action])) {
    $route = $routes[$action];
    $controllerFile = __DIR__ . '/controllers/' . $route['controller'] . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controllerClass = $route['controller'];
        $controller = new $controllerClass();
        
        if ($route['needs_id']) {
            if ($id <= 0) {
                http_response_code(400);
                die('Invalid ID parameter');
            }
            $controller->{$route['method']}($id);
        } else {
            $controller->{$route['method']}();
        }
    } else {
        http_response_code(404);
        die('Controller not found');
    }
} else {
    http_response_code(404);
    die('Page not found');
}
?>

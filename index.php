<?php
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

// Get action and id from URL
$action = $_GET['action'] ?? 'landing';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if route exists
if (isset($routes[$action])) {
    $route = $routes[$action];
    $controllerFile = __DIR__ . '/controllers/' . $route['controller'] . '.php';
    
    // Check if controller file exists
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controllerClass = $route['controller'];
        
        // Validate ID if needed
        if ($route['needs_id']) {
            if ($id <= 0) {
                http_response_code(400);
                die('Invalid ID parameter');
            }
        }
        
        // Instantiate controller and call method
        try {
            $controller = new $controllerClass();
            
            if ($route['needs_id']) {
                $controller->{$route['method']}($id);
            } else {
                $controller->{$route['method']}();
            }
        } catch (Exception $e) {
            http_response_code(500);
            die('Error: ' . $e->getMessage());
        }
    } else {
        http_response_code(404);
        die('Controller not found: ' . $route['controller']);
    }
} else {
    // Default to landing page if action not found
    header('Location: ' . BASE_URL . '?action=landing');
    exit;
}
?>

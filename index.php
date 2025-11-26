<?php
// Router - Simple MVC Router
require_once __DIR__ . '/config/config.php';

// Get action and id from URL
$action = $_GET['action'] ?? 'landing';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Route to appropriate controller
switch ($action) {
    case 'landing':
        require_once __DIR__ . '/controllers/LandingController.php';
        $controller = new LandingController();
        $controller->index();
        break;
        
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case 'index':
        require_once __DIR__ . '/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
        
    case 'create':
        require_once __DIR__ . '/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->create();
        break;
        
    case 'edit':
        require_once __DIR__ . '/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->edit($id);
        break;
        
    case 'view':
        require_once __DIR__ . '/controllers/ResultController.php';
        $controller = new ResultController();
        $controller->view($id);
        break;
        
    case 'delete':
        require_once __DIR__ . '/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->delete($id);
        break;
        
    default:
        require_once __DIR__ . '/controllers/LandingController.php';
        $controller = new LandingController();
        $controller->index();
        break;
}
?>

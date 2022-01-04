<?php
define('PROJECT_ROOT', dirname(__FILE__));
define('PR', PROJECT_ROOT);

/**
 * Routing
 */
// Fetch the route values
$requestUri = $_SERVER['REQUEST_URI'];
if (count(explode('/', $requestUri)) > 2) {
    list($zero, $requestPath['controller'], $requestPath['action']) = explode('/', $requestUri);
}

// Fall back to default values if none where provided
if (empty($requestPath['controller'])) {
    $requestPath['controller'] = 'home';
}
if (empty($requestPath['action'])) {
    $requestPath['action'] = 'index';
}

/**
 * Controller
 */
// Decide which controller to use
$controllerName = $requestPath['controller'];
$ucfirstControllerName = ucfirst($controllerName);
$methodName = $requestPath['action'];

include_once("controllers/$controllerName.class.php");
$controller = new $ucfirstControllerName();
$controller->$methodName();

// Merge the provided viewVars
require_once(PR . '/handlers/responseHandler.class.php');
$responseHandler = ResponseHandler::init();
$responseHandler->setVars(
    array(
        'pageTitle' => 'Braille converter',
        'pageSubject' => $ucfirstControllerName,
    )
);

// Make the viewVars available for the upcoming view(s)
extract($responseHandler->getVars());

/**
 * View
 */
define('RESOURCES_BASE', '/resources/');
$base = array(
    'css' => RESOURCES_BASE . 'css/',
    'javascript' => RESOURCES_BASE . 'js/'
);

// Fetch messages if provided
$messagesArray = $responseHandler->messages;
// Convert the message to HTML
include_once(PR."/views/elements/messages.php");

// Load the view
include_once(PR."/views/$controllerName/$methodName.php");

echo $output;

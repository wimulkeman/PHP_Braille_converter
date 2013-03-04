<?php
/**
 * Default waarden
 */
define('PROJECT_ROOT', dirname(__FILE__));
define('PR', PROJECT_ROOT);

/**
 * Routing
 */
// Vang af waar naar verwezen wordt
$requestUri = $_SERVER['REQUEST_URI'];
list($zero, $requestPath['controller'], $requestPath['action']) = explode('/', $requestUri);
// Controleer op de standaard waarden
if (empty($requestPath['controller'])) {
    $requestPath['controller'] = 'home';
}
if (empty($requestPath['action'])) {
    $requestPath['action'] = 'index';
}

/**
 * Controller
 */
// Laad de juiste controller in
$controllerName = $requestPath['controller'];
$ucfirstControllerName = ucfirst($controllerName);
$methodName = $requestPath['action'];

include_once("controllers/$controllerName.class.php");
$controller = new $ucfirstControllerName();
$controller->$methodName();

// Voeg de opgegeven viewVars samen
require_once(PR . '/handlers/responseHandler.class.php');
$responseHandler = ResponseHandler::init();
$responseHandler->setVars(
    array(
        'pageTitle' => 'Braille converter',
        'pageSubject' => $ucfirstControllerName,
    )    
);

// Maak de view variabelen beschikbaar
extract($responseHandler->getVars());

/**
 * View
 */
// De basis variabelen
define('RESOURCES_BASE', '/resources/');
$base = array(
    'css' => RESOURCES_BASE . 'css/',
    'javascript' => RESOURCES_BASE . 'js/'
);

// Haal eventuele berichten op
$messagesArray = $responseHandler->messages;
// Zet de berichten om naar html
include_once(PR."/views/elements/messages.php");

// Laad de view in
include_once(PR."/views/$controllerName/$methodName.php");

echo $output;

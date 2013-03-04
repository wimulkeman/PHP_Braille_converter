<?php
/**
 * De response handler zorgt voor de uiteindelijke communicatie naar de gebruiker toe
 *
 * @author WIM
 */
class ResponseHandler
{
    /**
     * De berichten die getoond moeten worden aan de gebruiker
     *
     * @var array
     */
    public $messages = array();

    /**
     * De te gebruiken variabelen in de view
     *
     * @var array
     */
    public $viewVars = array();

    /**
     * In deze variabele zal de instantie van deze class worden opgeslagen
     *
     * @var object
     */
    private static $_instanceOfMe;

    /**
     * De construct van deze class is private om te voorkomen dat hij van buitenaf
     * aangeroepen kan worden
     *
     * @return void
     * @access private
     * @author WIM
     */
    private function __construct()
    {
    }

    /**
     * Deze functie moet aangeroepen worden om een instantie van deze class te kunnen
     * verkrijgen
     *
     * @return object  Een instantie van deze class
     * @access public
     * @author WIM
     */
    public static function init()
    {
        // Controleer of de class al geiniteerd is door een andere aanroep
        if (empty(self::$_instanceOfMe)) {
            self::$_instanceOfMe = new self;
        }

        // Geef een initiatie van de class terug
        return self::$_instanceOfMe;
    }

    /**
     * Stel een bericht in die aan de gebruiker getoond moet worden
     *
     * @param string $message Het te tonen bericht
     * @param string $class   Het soort bericht dat getoond moet worden
     *
     * @return void
     * @access public
     * @author WIM
     */
    public function setMessage($message, $class = '')
    {
        $this->messages[] = array(
            'message' => $message,
            'class' => $class
        );
    }

    /**
     * Gebruik deze functie als een andere view moet worden getoond dan waar de gebruiker
     * op uit zou komen
     *
     * @param options Het gedeelte waarnaar de gebruiker doorverwezen moet worden
     *
     * @return void
     * @access public
     * @author WIM
     */
    public function setView($options)
    {
        global $ucfirstControllerName;
        global $controllerName;
        global $methodName;

        $_options = array(
            'controller' => 'home',
            'action' => 'index'
        );

        $options = array_merge($_options, $options);

        $controllerName = $options['controller'];
        $methodName = $options['action'];
        $ucfirstControllerName = ucfirst($options['controller']);
    }

    /**
     * Gebruik deze functie om variabelen beschikbaar te maken voor de view
     *
     * @param array $vars De weer te geven vars
     *
     * @return void
     * @access public
     * @author WIM
     */
    public function setVars($vars = array())
    {
        $this->viewVars = array_merge($this->viewVars, $vars);
    }

    /**
     * Haal de viewVars op
     *
     * @return array
     * @access public
     * @author WIM
     */
    public function getVars()
    {
        return $this->viewVars;
    }
}

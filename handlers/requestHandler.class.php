<?php
/**
 * De request handler maakt de binnenkomende data makkelijk benaderbaar
 *
 * @author WIM
 */
class RequestHandler
{
    /**
     * De opgegveven parameters bij de aanvraag
     *
     * @var array
     */
    public $requestParam = array();

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
     * Haal de aangevraagde parameters op
     *
     * @return array  De opgegeven request parameters
     * @access public
     * @author WIM
     */
    public function getRequestParam()
    {
        return $this->requestParam = array_merge($this->requestParam, $_GET, $_POST);
    }
}

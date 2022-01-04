<?php
/**
 * The ResponseHandler is used for the communication back to the user
 *
 * @author WIM
 */
class ResponseHandler
{
    /**
     * Messages that needs to be shown to the user
     *
     * @var array
     */
    public $messages = array();

    /**
     * Variables required in the views
     *
     * @var array
     */
    public $viewVars = array();

    /**
     * Keeps singleton of the class
     *
     * @var object
     */
    private static $_instanceOfMe;

    /**
     * The construct is made private to force users in using the class as a
     * singleton
     *
     * @author WIM
     */
    private function __construct()
    {
    }

    /**
     * Initialize the class for the first time, or get the singleton instance
     *
     * @return self An instance of the class
     * @author WIM
     */
    public static function init()
    {
        // Initialize the class if not yet available
        if (empty(self::$_instanceOfMe)) {
            self::$_instanceOfMe = new self();
        }

        // Return the class instance
        return self::$_instanceOfMe;
    }

    /**
     * Set the message for the user
     *
     * @param string $message
     * @param string $class   The class which should be used for displaying the message
     *
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
     * Use this method to use another view then the one expected based on the used controller + action
     *
     * @param options Define the view required to load
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
     * Provide this variables within the view
     *
     * @param array $vars
     *
     * @author WIM
     */
    public function setVars($vars = array())
    {
        $this->viewVars = array_merge($this->viewVars, $vars);
    }

    /**
     * Retrieve the variables for the view
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

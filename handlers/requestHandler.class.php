<?php
/**
 * @author WIM
 */
class RequestHandler
{
    /**
     * Provided request params
     *
     * @var array
     */
    public $requestParam = array();

    /**
     * Singleton instance
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
            self::$_instanceOfMe = new self;
        }

        // Return the class instance
        return self::$_instanceOfMe;
    }

    /**
     * Retrieve the request params
     *
     * @return array  De provided request params
     * @access public
     * @author WIM
     */
    public function getRequestParam()
    {
        return $this->requestParam = array_merge($this->requestParam, $_GET, $_POST);
    }
}

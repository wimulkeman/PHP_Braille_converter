<?php
/**
 * This class is used to fetch provided text and show it as Braille
 *
 * @author WIM
 */
class Braille
{
    public function __construct()
    {
        // This files are required for the convertion process
        require_once(PR . '/handlers/requestHandler.class.php');
        require_once(PR . '/handlers/responseHandler.class.php');
        require_once(PR . '/handlers/brailleHandler.class.php');

        // Initiate the handlers and make them available within the class
        $this->requestHandler = RequestHandler::init();
        $this->responseHandler = ResponseHandler::init();
        $this->brailleHandler = BrailleHandler::init();
    }

    /**
     * The convert method is used to convert the provided text to a Braille
     * representation.
     *
     * @return array  The view variables
     * @access public
     * @author WIM
     */
    public function convert()
    {
        $requestParam = $this->requestHandler->getRequestParam();

        $response = array(
            'text' => '',
        );

        // Return a message if no text has been provided by the user
        if (empty($requestParam['text'])) {
            $this->responseHandler->setMessage('You need to provide a text.', 'error');
            $this->responseHandler->setView(array('controller' => 'home', 'action' => 'index'));
            $this->responseHandler->setVars($response);
            return;
        }

        // Store the initial text for optional display in the response
        $response['text'] = $requestParam['text'];

        // Convert the provided text to a Braille notation
        $response['braille'] = $this->brailleHandler->convertText($requestParam['text']);

        // Direct the user to the finish step with the final results
        $this->responseHandler->setMessage('The text has been converted succesfully.');
        $this->responseHandler->setView(array('controller' => 'braille', 'action' => 'finish'));
        $this->responseHandler->setVars($response);
    }

    /**
     * This step is the final step for the user. In this step the user will
     * see the end result of the convertion
     *
     * @author WIM
     */
    public function finish()
    {
    }
}

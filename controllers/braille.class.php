<?php
/**
 * Onderstaande class wordt gebruikt voor het opvangen en in gang zetten van
 * het omzettingsproces voor tekst naar braille
 *
 * @author WIM
 */
class Braille
{
    /**
     * De construct functie wordt als eerste aangeroepen
     *
     * @return void
     * @access public
     * @author WIM
     */
    public function __construct()
    {
        // Laad de benodigde bestanden in
        require_once(PR . '/handlers/requestHandler.class.php');
        require_once(PR . '/handlers/responseHandler.class.php');
        require_once(PR . '/handlers/brailleHandler.class.php');

        // Initieer de request en de braille components
        $this->requestHandler = RequestHandler::init();
        $this->responseHandler = ResponseHandler::init();
        $this->brailleHandler = BrailleHandler::init();
    }

    /**
     * De convert functie wordt gebruikt om een opgegeven tekst om te zetten naar
     * het braille schrift
     *
     * @return array  De view variabelen
     * @access public
     * @author WIM
     */
    public function convert()
    {
        // Haal de parameters op
        $requestParam = $this->requestHandler->getRequestParam();

        $response = array(
            'text' => '',
        );

        // Controleer of er een tekst aanwezig is, als dat niet het geval is, laad dan
        // een foutmelding zien
        if (empty($requestParam['text'])) {
            $this->responseHandler->setMessage('U moet een tekst ingeven.', 'error');
            $this->responseHandler->setView(array('controller' => 'home', 'action' => 'index'));
            $this->responseHandler->setVars($response);
            return;
        }

        // Maak de tekst beschikbaar voor de view
        $response['text'] = $requestParam['text'];

        // Zet de tekst om naar braille
        $response['braille'] = $this->brailleHandler->convertText($requestParam['text']);

        // Toon de finish stap
        $this->responseHandler->setMessage('Uw tekst is succesvol omgezet.');
        $this->responseHandler->setView(array('controller' => 'braille', 'action' => 'finish'));
        $this->responseHandler->setVars($response);
    }

    /**
     * De finish functie is de laatste stap waarin het eindresultaat van de convertatie
     * wordt getoond aan de gebruiker.
     *
     * @return array  De view variabelen
     * @access public
     * @author WIM
     */
    public function finish()
    {
    }
}

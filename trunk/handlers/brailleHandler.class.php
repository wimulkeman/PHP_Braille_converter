<?php
/**
 * De braille handler regelt alle converteringen voor het omzetten van tekst naar braille
 *
 * De reactie van de converter acties in deze class bestaat uit de volgende indeling
 *
 * array(
 *      0 => array(
 *          'text' => [tekst],
 *          'braille' => [braille]
 *      ),
 *      1 => array(
 *          'special' => [break | space]
 *      )
 * )
 *
 * @author WIM
 */
class BrailleHandler
{
    /**
     * Het braille alfabet
     * De verdeling van de punten ten opzichte van de 0/1 notatie is bij bijvoorbeeld
     * de n is:
     * Braille: Nummering:      Bitnotatie:
     * o o      32 16    = 56   110110
     * - o      8  4
     * o -      2  1
     *
     * @var array
     * @access public
     */
    public $brailleAlphabet = array(
        'a' => '100000', 'b' => '101000', 'c' => '110000', 'd' => '110100', 'e' => '100100',
        'f' => '111000', 'g' => '111100', 'h' => '011100', 'i' => '011000', 'j' => '011100',
        'k' => '100010', 'l' => '101010', 'm' => '110010', 'n' => '110110', 'o' => '100110',
        'p' => '111010', 'q' => '111110', 'r' => '101110', 's' => '011010', 't' => '011110',
        'u' => '100011', 'v' => '101011', 'w' => '011101', 'x' => '110011', 'y' => '110111',
        'z' => '100111'
    );

    /**
     * De braille nummering
     * Voor de verdeling, zie het braille alfabet
     *
     * @var array
     * @access public
     */
    public $brailleIntegers = array(
        1 => '100000', 2 => '101000', 3 => '110000', 4 => '110100', 5 => '100100',
        6 => '111000', 7 => '111100', 8 => '011100', 9 => '011000', 0 => '011100',
    );

    /**
     * Speciale tekens in braille
     * Voor de verdeling, zie het braille alfabet
     *
     * @var array
     * @access public
     */
    public $brailleSpecialCharacters = array(
        'capital' =>    '010001',
        'integer' =>    '010111',
        ',' =>          '001000',
        ';' =>          '001010',
        ':' =>          '001100',
        '.' =>          '001101',
        '?' =>          '001001',
        '!' =>          '001110',
        '"' =>          '001111', // Double quotes
        '(' =>          '001011',
        '*' =>          '000110',
        ')' =>          '000111',
        '\'' =>         '000010', // Single quote
        '-' =>          '000011',
        '/' =>          '010010',
    );

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
        // Laad de benodigde bestanden in
        require_once(PR . '/handlers/responseHandler.class.php');

        // Initieer de request en de braille components
        $this->responseHandler = ResponseHandler::init();
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
            self::$_instanceOfMe = new self();
        }

        // Geef een initiatie van de class terug
        return self::$_instanceOfMe;
    }

    /**
     * Zet een stuk tekst om naar braille
     *
     * @param string $text Het stuk tekst dat omgezet moet worden
     *
     * @return string De braille weergave voor de tekst
     * @access array
     * @author WIM
     */
    public function convertText($text)
    {
        // Het eindresultaat
        $output = array();

        // Haal eerst alle paragrafen uit de tekst
        $paragraphs = explode("\n", $text);

        // Loop door de paragrafen heen en zet deze om
        foreach ($paragraphs as $paragraph) {
            // Kijk of er sprake is van een break punt
            if (empty($paragraph)) {
                $output[] = array(
                    'special' => 'break'
                );
                continue;
            }

            $braille = $this->convertParagraph($paragraph);
            // Plaats na elk stuk weer een enter om de explode werking op te heffen
            $braille[] = array(
                'special' => 'break'
            );
            $output = array_merge($output, $braille);
        }

        return $output;
    }

    /**
     * Zet een paragraaf om naar braille
     *
     * @param string $paragraph De paragraaf die omgezet moet worden
     *
     * @return string De braille weergave voor de paragraaf
     * @access array
     * @author WIM
     */
    public function convertParagraph($paragraph)
    {
        return $this->convertSentence($paragraph);
    }

    /**
     * Zet een zin om naar braille
     *
     * @param string $sentence De zin die omgezet moet worden
     *
     * @return array De braille weergave voor de zin
     * @access array
     * @author WIM
     */
    public function convertSentence($sentence)
    {
        // Het eindresultaat
        $output = array();

        // Haal eerst alle woorden uit de tekst
        $words = explode(" ", $sentence);

        // Loop door de woorden heen en zet deze om
        foreach ($words as $word) {
            // Kijk of er sprake is van een break punt
            if (empty($word)) {
                $output[] = array(
                    'special' => 'space'
                );
                continue;
            }

            $output[] = $this->convertWord($word);
            // Plaats na elk stuk weer een spatie om de explode werking op te heffen
            $output[] = array(
                'special' => 'space'
            );
        }

        return $output;
    }

    /**
     * Zet een woord om naar braille
     *
     * @param string $word Het woord dat omgezet moet worden
     *
     * @return array De braille weergave voor het woord
     * @access array
     * @author WIM
     */
    public function convertWord($word)
    {
        // De geformateerde braille string
        $braille = '';

        // Geeft aan of een bepaalde indicator wordt toegevoegd
        $indicatorAdded = false;
        $indicator = '';

        // Kijk of het woord alleen cijfers is
        if (preg_match('/^[0-9]+$/', $word)) {
            $indicatorAdded = true;
            $indicator = $this->brailleSpecialCharacters['integer'];
        }

        // Zet het woord per karakter om
        for ($i = 0; $i < strlen($word); ++ $i) {
            $braille .= $this->convertCharacter($word[$i], $indicatorAdded);
        }

        // Geef de uitkomst terug
        return array(
            'text' => $word,
            'braille' => $indicator . $braille
        );
    }

    /**
     * Zet het karakter om naar een braille teken
     *
     * @param string  $letter         De letter die omgezet moet worden
     * @param boolean $indicatorAdded Geeft aan of al een indicatie teken is toegevoegd
     *                                (nummer | hoofdletter)
     *
     * @return array De braille weergave voor de letter
     * @access array
     * @author WIM
     */
    public function convertCharacter($character, $indicatorAdded = false)
    {
        // De geformateerde braille string
        $braille = '';

        if (empty($character)) {
            return $braille;
        }

        // Kijk wat van soort karakter het is
        // Voor letters
        if (preg_match('/[a-z]/i', $character)) {
            // Controleer of het om een hoofdletter gaat en of dit aangegeven moet worden
            if ($indicatorAdded == false && preg_match('/[A-Z]/', $character)) {
                $braille .= $this->brailleSpecialCharacters['capital'];
            }

            $character = strtolower($character);

            // Zet het letter om naar het alfabet
            return $braille . $this->brailleAlphabet[$character];
        }

        // Voor getallen
        if (preg_match('/[0-9]/', $character)) {
            // Controleer of het om een hoofdletter gaat en of dit aangegeven moet worden
            if ($indicatorAdded == false) {
                $braille .= $this->brailleSpecialCharacters['integer'];
            }

            return $braille . $this->brailleIntegers[$character];
        }

        // Kijk of het speciale teken omgezet kan worden
        if (!empty($this->brailleSpecialCharacters[$character])) {
            return $braille . $this->brailleSpecialCharacters[$character];

        }

        // Als niks kan worden gevonden, Geef dan een foutmelding en retourneer een lege string
        $this->responseHandler->setMessage("Het volgende karakter werd niet herkent: $character", 'error');

        return $braille;
    }
}


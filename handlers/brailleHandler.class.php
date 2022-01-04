<?php
/**
 * The BrailleHandler is used for converting text to a Braille notation.
 *
 * The convertion methods of this class will use the following response:
 *
 * array(
 *      0 => array(
 *          'text' => [text],
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
     * The Braille alphabet.
     *
     * Characters are written like a bitwise notation substituting the dots in the Braille
     * notation.
     *
     * This corresponds as following, in this example for the letter n:
     * Braille: Numbering:          Bitwise notation:
     * o o      32 16       = 56    110110
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
     * Braille numbers
     * For explanation about how its written, see the alphabet property
     *
     * @var array
     */
    public $brailleIntegers = array(
        1 => '100000', 2 => '101000', 3 => '110000', 4 => '110100', 5 => '100100',
        6 => '111000', 7 => '111100', 8 => '011100', 9 => '011000', 0 => '011100',
    );

    /**
     * Braille special characters
     * For explanation about how its written, see the alphabet property
     *
     * @var array
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
        require_once(PR . '/handlers/responseHandler.class.php');

        $this->responseHandler = ResponseHandler::init();
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
     * Convert the text to the Braille bit notation
     *
     * @param string $text The text that needs to be converted
     *
     * @return string The bit notation for displaying the text as Braille
     * @access array
     * @author WIM
     */
    public function convertText($text)
    {
        $output = array();

        // Split the text on paragraphs
        $paragraphs = explode("\n", $text);

        foreach ($paragraphs as $paragraph) {
            // Check if text is present in the paragraph
            if (empty($paragraph)) {
                $output[] = array(
                    'special' => 'break'
                );
                continue;
            }

            $braille = $this->convertParagraph($paragraph);
            // Always add an enter at the end to signal the end of the paragraph
            $braille[] = array(
                'special' => 'break'
            );
            $output = array_merge($output, $braille);
        }

        return $output;
    }

    /**
     * Convert the paragraph text to Braille
     *
     * @param string $paragraph The text within the paragraph
     *
     * @return string The Braille (bitwise) representation of the text
     * @access array
     * @author WIM
     */
    public function convertParagraph($paragraph)
    {
        return $this->convertSentence($paragraph);
    }

    /**
     * Convert the sentence to Braille
     *
     * @param string $sentence Content of the sentence
     *
     * @return array The Braille (bitwise) representation of the sentence
     * @access array
     * @author WIM
     */
    public function convertSentence($sentence)
    {
        $output = array();

        // Split the words withing the sentence
        $words = explode(" ", $sentence);

        foreach ($words as $word) {
            // Add a space if the user has add double spaces
            if (empty($word)) {
                $output[] = array(
                    'special' => 'space'
                );
                continue;
            }

            $output[] = $this->convertWord($word);
            // Restore the space removed during the explode
            $output[] = array(
                'special' => 'space'
            );
        }

        return $output;
    }

    /**
     * Convert a word to the Braille notation
     *
     * @param string $word
     *
     * @return array The Braille representation for the word
     * @author WIM
     */
    public function convertWord($word)
    {
        $braille = '';

        // Keep track if a special indicator is required in the notation
        $indicatorAdded = false;
        $indicator = '';

        // Check if the word only contains numbers
        if (preg_match('/^[0-9]+$/', $word)) {
            $indicatorAdded = true;
            $indicator = $this->brailleSpecialCharacters['integer'];
        }

        // Convert the word
        for ($i = 0; $i < strlen($word); ++ $i) {
            $braille .= $this->convertCharacter($word[$i], $indicatorAdded);
        }

        return array(
            'text' => $word,
            'braille' => $indicator . $braille
        );
    }

    /**
     * Convert the character to the Braille notation
     *
     * @param string  $letter
     * @param boolean $indicatorAdded Use a indicator in case of a numbers or capital letter
     *
     * @return array The Braille representation for the letter
     * @author WIM
     */
    public function convertCharacter($character, $indicatorAdded = false)
    {
        $braille = '';

        if (empty($character)) {
            return $braille;
        }

        // Check the type of character
        // Alphabetic
        if (preg_match('/[a-z]/i', $character)) {
            // Is it a capital letter and no indicator added yet?
            if ($indicatorAdded == false && preg_match('/[A-Z]/', $character)) {
                $braille .= $this->brailleSpecialCharacters['capital'];
            }

            $character = strtolower($character);

            return $braille . $this->brailleAlphabet[$character];
        }

        // Numeric
        if (preg_match('/[0-9]/', $character)) {
            // Is a indicator required?
            if ($indicatorAdded == false) {
                $braille .= $this->brailleSpecialCharacters['integer'];
            }

            return $braille . $this->brailleIntegers[$character];
        }

        // Special character
        if (!empty($this->brailleSpecialCharacters[$character])) {
            return $braille . $this->brailleSpecialCharacters[$character];

        }

        // Fallback: if the character is unrecognized show the character to the user
        $this->responseHandler->setMessage("The following character could not be converted: $character", 'error');

        return $braille;
    }
}


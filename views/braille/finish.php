<?php
// Maak van elk stukje een eigen braille blok
$braillePiecesWrapper = <<<HTML
<table>
    <tr>
        %s
    </tr>
</table>
HTML;

$braillePiece = <<<HTML
<td>
    <table>
        <tr>
            <td class="%s">&nbsp;</td><td class="%s">&nbsp;</td>
        </tr>
        <tr>
            <td class="%s">&nbsp;</td><td class="%s">&nbsp;</td>
        </tr>
        <tr>
            <td class="%s">&nbsp;</td><td class="%s">&nbsp;</td>
        </tr>
    </table>
</td>
HTML;

// Zet de braille om in html
$brailleHtml = '';
if (!empty($braille)) {
    $brailleHtml .= '<div>';
    foreach ($braille as $piece) {
        $braillePieces = array();

        if (!empty($piece['special']) && $piece['special'] == 'space') {
            $braillePieces[] = sprintf($braillePiece, '', '', '', '', '', '');
            $brailleHtml .= sprintf($braillePiecesWrapper, implode('', $braillePieces));
            continue;
        } elseif (!empty($piece['special']) && $piece['special'] == 'break') {
            $brailleHtml .= '</div><div>';
            continue;
        }

       // Hak de string in blokjes braille
        $strPieces = str_split($piece['braille'], 6);
        foreach ($strPieces as $strPiece) {
            // Zet de binaire waarden om naar een stip
            $dot1 = ($strPiece[0] == '1') ? 'dot' : '' ;
            $dot2 = ($strPiece[1] == '1') ? 'dot' : '' ;
            $dot3 = ($strPiece[2] == '1') ? 'dot' : '' ;
            $dot4 = ($strPiece[3] == '1') ? 'dot' : '' ;
            $dot5 = ($strPiece[4] == '1') ? 'dot' : '' ;
            $dot6 = ($strPiece[5] == '1') ? 'dot' : '' ;

            // Voeg het HTML gedeelte toe
            $braillePieces[] = sprintf($braillePiece, $dot1, $dot2, $dot3, $dot4, $dot5, $dot6);
        }

        $brailleHtml .= sprintf($braillePiecesWrapper, implode('', $braillePieces));
    }
    $brailleHtml .= '</div>';
}

// Bouw de html pagina op
$output = <<<HTML
<html>
    <head>
        <title>$pageTitle | $pageSubject</title>
        <link rel="stylesheet" type="text/css" href="{$base['css']}layout.css">
    </head>
    <body>
        <h1>Braille converter</h1>
        $messages
        <p>Terug naar <a href="/" title="Terug naar de homepagina">home</a></p>
        <div class="text">
            <h2>Uw tekst:</h2>
            $text
        </div>
        <div class="braille">
            <h2>De braille vertaling:</h2>
            $brailleHtml
        </div>
    </body>
</html>
HTML;

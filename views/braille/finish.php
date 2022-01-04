<?php
// Give every character its own block
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

// Convert the Braille bit notation to a HTML display
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

        $strPieces = str_split($piece['braille'], 6);
        foreach ($strPieces as $strPiece) {
            $dot1 = ($strPiece[0] == '1') ? 'dot' : '' ;
            $dot2 = ($strPiece[1] == '1') ? 'dot' : '' ;
            $dot3 = ($strPiece[2] == '1') ? 'dot' : '' ;
            $dot4 = ($strPiece[3] == '1') ? 'dot' : '' ;
            $dot5 = ($strPiece[4] == '1') ? 'dot' : '' ;
            $dot6 = ($strPiece[5] == '1') ? 'dot' : '' ;

            $braillePieces[] = sprintf($braillePiece, $dot1, $dot2, $dot3, $dot4, $dot5, $dot6);
        }

        $brailleHtml .= sprintf($braillePiecesWrapper, implode('', $braillePieces));
    }
    $brailleHtml .= '</div>';
}

$output = <<<HTML
<html>
    <head>
        <title>$pageTitle | $pageSubject</title>
        <link rel="stylesheet" type="text/css" href="{$base['css']}layout.css">
    </head>
    <body>
        <h1>Braille converter</h1>
        $messages
        <p>Back to <a href="/" title="Back to the homepage">home</a></p>
        <div class="text">
            <h2>Your text:</h2>
            $text
        </div>
        <div class="braille">
            <h2>The Braille notation:</h2>
            $brailleHtml
        </div>
    </body>
</html>
HTML;

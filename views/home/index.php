<?php
$text = (empty($text)) ? '' : $text ;
$output = <<<HTML
<html>
    <head>
        <title>$pageTitle | $pageSubject</title>
        <link rel="stylesheet" type="text/css" href="{$base['css']}layout.css">
    </head>
    <body>
        <h1>Braille converter</h1>
        <p>Op deze pagina kunt u tekst om laten zetten naar braille.</p>
        $messages
        <form action="/braille/convert" method="post">
            <h2>Uw tekst:</h2>
            <textarea name="text">$text</textarea><br/>
            <input type="submit" value="Omzetten">
        </form>
    </body>
</html>
HTML;

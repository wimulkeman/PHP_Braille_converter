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
        <p>On this page you can view how your text would be converted to Braille notation.</p>
        $messages
        <form action="/braille/convert" method="post">
            <h2>Your text:</h2>
            <textarea name="text">$text</textarea><br/>
            <input type="submit" value="Convert">
        </form>
    </body>
</html>
HTML;

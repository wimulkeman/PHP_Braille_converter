<?php
$messages = '';

if (!empty($messagesArray)) {
    foreach ($messagesArray as $message) {
        $messages .= <<<HTML
<div class="message {$message['class']}">
    {$message['message']}
</div>
HTML;
    }
}

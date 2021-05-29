<?php

include('MessageBroker.php');

$messageBroker = new MessageBroker();
$request = ['user_id' => 1, 'image_base64' => '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'];
$message = json_encode($request);
$messageBroker->sendMessage($message);
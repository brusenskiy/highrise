<?php

require_once 'Ext/String.php';
require_once 'Ext/Date.php';
require_once 'Highrise/Api.php';
require_once 'Highrise/Person.php';
require_once 'Highrise/Note.php';
require_once 'Highrise/Task.php';
require_once 'Highrise/Tag.php';
require_once 'YouCompanyHighrise.php';

$api = new YouCompanyHighrise('you-company-login', 'user-token');

$result = $api->registerFormRequest(
    $api->getPerson('Jonh Smith', 'jsmith@gmail.com', '929 9799810', 'mail'),
    'Call',
    'China Wants Its Movies to Be Big in the U.S., Too',
    array('Logo')
);

print_r($result);

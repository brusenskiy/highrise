<?php

require_once 'Ext/String.php';
require_once 'Ext/Date.php';
require_once 'Highrise/Api.php';
require_once 'Highrise/Person.php';
require_once 'Highrise/Note.php';
require_once 'Highrise/Task.php';
require_once 'Highrise/Tag.php';

class YouCompanyHighriseService extends Highrise\Service {}

$serv = new YouCompanyHighriseService('you-company-login', 'user-token');

$result = $serv->registerFormRequest(
    $serv->getPerson('Jonh Smith', 'jsmith@gmail.com', '929 9799810', 'mail'),
    'Call',
    'China Wants Its Movies to Be Big in the U.S., Too',
    array('Logo')
);

var_dump($result);

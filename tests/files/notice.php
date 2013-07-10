<?php

require_once dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$handler = new \Webforge\FatalErrorHandler('null@ps-webforge.com');
$handler->register();

print @$_SERVER['QUERY_STRING'];
?>
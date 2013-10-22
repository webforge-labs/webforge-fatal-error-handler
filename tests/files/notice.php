<?php

require_once dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$handler = new \Webforge\FatalErrorHandler('null@ps-webforge.com', function($recipient, $subject, $text, $headersString) {
  file_put_contents(__DIR__.DIRECTORY_SEPARATOR.'output.txt', json_encode((object) compact('recipient', 'subject', 'text', 'headersString')));
  return TRUE;
});
$handler->register();

print @$_SERVER['QUERY_STRING'];
?>
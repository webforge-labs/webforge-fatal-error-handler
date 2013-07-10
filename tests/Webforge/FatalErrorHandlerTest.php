<?php

namespace Webforge;

use PHPUnit_Framework_TestCase;
use PHP_Invoker;

class FatalErrorHandlerTest extends PHPUnit_Framework_TestCase {

  public function testAcceptanceToMail() {
    $phpFile = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'acceptance.php';

    exec('php -f '.$phpFile, $out);

    $out = implode("\n", $out);

    $this->assertContains("Class 'banane' not found", $out);
  }
}
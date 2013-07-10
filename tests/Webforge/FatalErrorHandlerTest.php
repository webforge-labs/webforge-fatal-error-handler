<?php

namespace Webforge;

use PHPUnit_Framework_TestCase;
use PHP_Invoker;

class FatalErrorHandlerTest extends PHPUnit_Framework_TestCase {

  public function testAcceptanceToMail() {
    $out = $this->execFile('acceptance.php');
    $this->assertContains("Class 'banane' not found", $out);
  }

  public function testNoticeToMail() {
    $out = $this->execFile('notice.php');
    $this->assertContains("Undefined index: QUERY_STRING", $out);
  }

  protected function execFile($name) {
    $phpFile = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$name;

    exec('php -f '.$phpFile, $out);

    return implode("\n", $out);
  }
}
<?php

namespace Webforge;

use PHPUnit_Framework_TestCase;
use PHP_Invoker;

class FatalErrorHandlerTest extends PHPUnit_Framework_TestCase {

  public function testAcceptanceToMail() {
    $out = $this->execFile('acceptance.php');
    $this->assertContains("Class 'banane' not found", $out);
  }

  public function testNoticeIsNotMailed() {
    $out = $this->execFile('notice.php');
    $this->assertNotContains("Undefined index: QUERY_STRING", $out);
  }

  public function testMailContents() {
    $output = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.'output.txt';

    if (file_exists($output)) {
      unlink($output);
    }
    
    $out = $this->execFile('mailing.php');
    $this->assertContains("Class 'banane' not found", $out);

    $this->assertFileExists($output, 'test error: output should be written from mailing.php script output: '.$out);

    $mail = json_decode(file_get_contents($output));
    $this->assertNotEmpty($mail, 'mail contents should be decoded as json');

    $this->assertEquals('null@ps-webforge.com', $mail->recipient);
    $this->assertEquals('[FatalErrorHandler] Class \'banane\' not found', $mail->subject);
  }

  protected function execFile($name) {
    $phpFile = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'files'.DIRECTORY_SEPARATOR.$name;

    exec('php -f '.$phpFile, $out);

    return implode("\n", $out);
  }
}
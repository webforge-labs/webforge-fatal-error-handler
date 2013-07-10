<?php

namespace Webforge;

class FatalErrorHandler {

  private static $errors =  Array(
    E_ERROR              => 'Fatal Error',
    E_WARNING            => 'Warning',
    E_PARSE              => 'E_PARSE',
    E_NOTICE             => 'Notice',
    E_CORE_ERROR         => 'E_CORE_ERROR',
    E_CORE_WARNING       => 'E_CORE_WARNING',
    E_COMPILE_ERROR      => 'E_COMPILE_ERROR',
    E_COMPILE_WARNING    => 'E_COMPILE_WARNING',
    E_USER_ERROR         => 'Fatal Error',
    E_USER_WARNING       => 'Warning',
    E_USER_NOTICE        => 'Notice',
    E_STRICT             => 'Strict Standards',
    E_RECOVERABLE_ERROR  => 'Catchable Fatal Error',
    E_DEPRECATED         => 'E_DEPRECATED'
  );

  /**
   * @var string email
   */
  protected $recipient;

  public function __construct($recipient) {
    $this->recipient = $recipient;
  }

  public function handle() {
    $type = E_CORE_ERROR;
    $file = "unknown file";
    $line  = "shutdown";
    $message = "unkown message";

    $error = error_get_last();

    if (is_array($error)) {
      extract($error);
    }

    $this->sendDebugEmail($type, $message, $file, $line);

  }

  public function sendDebugEmail($type, $message, $file, $line) {
    $typeString = self::$errors[$type];

    /* Debug-Mail */
    $text = NULL;
    $text .= '['.date('d.M.Y H:i:s')."]\n";
    $text .= sprintf("%s in %s:%d with message:\n", $typeString, $file, $line);
    $text .= "\n";
    $text .= $message."\n";
    $text .= "\n";
    $text .= "--\n";
    $text .= sprintf("sent from %s\n%s:%d\n", __CLASS__, __FILE__, __LINE__);

    $subject = '[FatalErrorHandler] '.substr($message, 0, 120);
    
    $ret = @mail(
      $this->recipient,
      $subject,
      $text,
      'From: www@'.$this->getHostName()."\r\n".
      'Content-Type: text/plain; charset=UTF-8'."\r\n"
    );

    if ($ret === FALSE) {
      error_log('[FatalErrorHandler.php:'.__LINE__.'] Die Fehlerinformationen konnten nicht an den lokalen Mailer übergeben werden.', 0);
    }
  }


  protected function getHostName() {
    if (isset($_SERVER['HTTP_HOST'])) {
      return $_SERVER['HTTP_HOST'];
    } elseif (PHP_SAPI === 'cli') {
      return php_uname('n');
    } else {
      return 'unkown-host';
    }
  }

  public function register() {
    register_shutdown_function(array($this, "handle"));
  }
}

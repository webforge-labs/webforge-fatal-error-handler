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

  /**
   * @var Closure function($recipient, $subject, $text, $headersString) {
   */
  protected $mailCallback;

  public function __construct($recipient, \Closure $mailCallback = NULL) {
    $this->recipient = $recipient;
    $this->mailCallback = $mailCallback ?: function($recipient, $subject, $text, $headersString) {
      return @mail($recipient, $subject, $text, $headersString);
    };
  }

  public function handle() {
    $type = E_CORE_ERROR;
    $file = "unknown file";
    $line  = "shutdown";
    $message = "unkown message";
    $trace = debug_backtrace();

    $error = error_get_last();

    if (is_array($error)) {
      extract($error);

      $this->sendDebugEmail($type, $message, $file, $line, $trace);
    }
  }

  public function sendDebugEmail($type, $message, $file, $line, $trace) {
    $typeString = self::$errors[$type];

    /* Debug-Mail */
    $text = NULL;
    $text .= '['.date('d.M.Y H:i:s')."]\n";
    $text .= sprintf("%s in %s:%d with message:\n", $typeString, $file, $line);
    $text .= "\n";
    $text .= $message."\n";
    $text .= "\n";
    $text .= $this->formatBacktrace($trace);
    $text .= "\n";
    $text .= "--\n";
    $text .= sprintf("sent from %s\n%s:%d\n", __CLASS__, __FILE__, __LINE__);


    $subject = '[FatalErrorHandler] '.substr($message, 0, 120);
    
    $cb = $this->mailCallback;
    $ret = $cb(
      $this->recipient,
      $subject,
      $text,
      'From: www@'.$this->getHostName()."\r\n".
      'Content-Type: text/plain; charset=UTF-8'."\r\n"
    );

    if ($ret === FALSE) {
      error_log('[FatalErrorHandler.php:'.__LINE__.'] Cannot mail fatal-error-details with a local mailer.', 0);
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

  protected function formatBacktrace($trace) {
    //http://stackoverflow.com/questions/1423157/print-php-call-stack

    $stack = '';
    $i = 1;
    foreach($trace as $key=>$node) {
      if ($key === 0) {
        $node['file'] = __FILE__;
        $node['line'] = __LINE__;
      }
      $stack .= "#$i ".$node['file'] ."(" .$node['line']."): "; 
      if(isset($node['class'])) {
        $stack .= $node['class'] . "->"; 
      }
      $stack .= $node['function'] . "()" . "\n";
      $i++;
    }

    return $stack;
  }

  public function register() {
    register_shutdown_function(array($this, "handle"));
  }
}

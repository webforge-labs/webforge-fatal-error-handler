webforge-fatal-error-handler
============================

A really simple class that can report fata errors by mail

## installation

Use [Composer](http://getcomposer.org) to install.
```
composer require -v --prefer-source webforge/fatal-error-handler @stable
```

## Usage

```php
// do vendor autoload

$handler = new \Webforge\FatalErrorHandler('yourmail@your-domain.com');
$handler->register();
```

Fatal errors coming after these lines will be tried to be mailed with mail(). If mail() fails a notice is written into the php error log

Its now possible to add a custom callback to replace the php mail() call with your own logic to send the mail. Test your callback really really well!

```php
// do vendor autoload

$handler = new \Webforge\FatalErrorHandler('yourmail@your-domain.com', function($recipient, $subject, $text, $headersString) {
  // send your own mail here, or log, etc
  return $successOfMail;
});
$handler->register();
```

the header string is already encoded for the php mail() function. (full string with \r\n seperated headers);

## Roadmap

  - add some simple rules to filter some errors (e.g. wanted errors from tests)

## Known issues

  - sometimes dom library triggers the fatal error handler for entities that cannot be parsed

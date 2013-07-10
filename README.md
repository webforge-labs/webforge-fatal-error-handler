webforge-fatal-error-handler
============================

A really simple class that can report fata errors by mail

## installation

Use [Composer](http://getcomposer.org) to install.
```
composer require -v --prefer-source webforge/fatal-error-handler
```

## Usage

```php
// do vendor autoload

$handler = new \Webforge\FatalErrorHandler('yourmail@your-domain.com');
$handler->register();
```

Fatal errors coming after these lines will be tried to be mailed with mail(). If mail() fails a notice is written into the php error log
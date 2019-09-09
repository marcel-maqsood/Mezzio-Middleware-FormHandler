# Middleware-FormHandler

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
## Installation

Run the following to install this library:

```bash
$ composer require depa/middleware-formularhandler
```

## Documentation
In order to use the FormHandler you have to make sure that zend-problem-details:
```php
$ comsrcposer require zendframework/zend-problem-details
```
is installed.

The E-Mail Adapter's can handle with the "reply-to" email-header
you can define it inside the Adapter config like this:
```php
'reply-to' => [
    'status' => true, // true/false
    'field' => 'mail' // Das Feld im post, welches die Email Adresse der Person benhaltet
],
```
if the field attribute is not defined but the status is true,
the adapter will check if there is any 'type' => 'email' defined inside the 'fields' definition.
if not, the reply-to-header is not getting set.

## Credits

This bundle has been developed by [designpark](https://www.designpark.de).


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


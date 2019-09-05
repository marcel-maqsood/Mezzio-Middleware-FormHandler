# Middleware-FormHandler

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
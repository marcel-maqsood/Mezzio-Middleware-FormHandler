# Middleware-FormHandler

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![StyleCI](https://styleci.io/repos/201788719/shield?branch=master)](https://github.styleci.io/repos/201788719)

This library allows you to handle your forms, 
check for missing fields and only uses fields that you realy expect to be submitted.

## Installation

Run the following to install this library:

```bash
$ composer require depa/middleware-formularhandler
```

## Info ##
If your form has fields that are not defined inside the config, the handler will not use them due to security reasons.

## Documentation

At the bottom of the Doc, i'll show you a quick Example on how the config is build like.

### The Implementation ###
To Implement the Middleware, just add a Route to your routes files that passes it's request into the middleware:
   ```php
    $app->route(
        '/formhandler[/]',
        [
            depa\FormularHandlerMiddleware\FormularHandlerMiddleware::class,
        ],
        ['POST'],
        'formHandler'
    );
   ```
after that, be sure to provide a config-file inside your config/autoload folder, that contains anything the Middleware needs to check your forms.
(we recommend you to use our config-file (which is in the config- folder) and just adjust it to fit your needs)

### The Adapters ###
Currently there are 2 working Adapters:
* phpmail
    <br/>
    definition looks like this:
    ```php
  'adapter' => [
      'phpmail' => [
          'reply-to' => [
              'status' => true,
              'field'  => 'mail',
          ],
              'recipients' => ['example@example.com'],
              'subject'    => 'subject',
              'sender'     => 'sender@example.com',
              'senderName' => 'Form', 
              'template'   => 'Test {{message}} {{mail}}',
      ],
  ],
    ```
    phpmail sends the mail (as you may expect) via the php method: mail().
* smtpmail
    <br/>
    definition looks like this:
    ```php
  'adapter' => [
      'smtpmail' => [
          //same as on phpmail but includes:
          'email_transfer' => [
              'method' => 'smtp',
              'config' => [
                  'service'    => 'smtp.googlemail.com',
                  'port'       => '465',
                  'encryption' => 'ssl',
                  'email'      => 'example@gmail.com',
                  'password'   => 'examplepw',
              ],
          ],
      ],
  ],
    ```
    smtpmail sends the mail via Swift_SmtpTransport.
    
    you can implement them (as later described) as global or as local ones, global adapters do overwrite the local ones.

### The Local-Adapters ###
    
The Adapter field must be directly inside the form-definition:
```php
  'forms' => [
      'contact' => [
          'fields' => [
              ...
          ],
          'adapter' => [
              ...
          ],
      ],
  ],
```
    
### The Global-Adapters ###
A Global Adapter is defined in the very top of the config: 
```php
'depaForm' => [
    'adapter' => [
        'globalTestAdapter-1' => [
            'smtpmail' => [
                'recipients'     => ['example@example.com'],
                'subject'        => 'base subject all forms that uses this specific adaper has',
                'sender'         => 'example@example.com',
                'senderName'     => 'form',
                'template'       => 'nothing',
                'email_transfer' => [
                    'config' => [
                         'service'    => 'smtp.googlemail.com',
                         'port'       => '465',
                         'encryption' => 'ssl',
                         'email'      => 'example@gmail.com',
                         'password'   => 'examplepw',
                    ],
                ],
            ],
        ],
    ],
],
```

if you defined a global adapter and want to use it, 
go ahead and put the name of it (in this case: globalTestAdapter-1)
inside of the adapter-field of your form-config:
```php
'forms' => [
    'contact' => [
        'fields' => [
            ...
        ],
        'adapter' => [
            'globalTestAdapter-1' //or any other name you have used for an adapter.
        ],
    ],
],
```


### The EMail-Template ###
The Template, you specified in the Config can be dynamic through twig:
```php
'template' => 'my Name is {{name}}'
```
the variables you use must be valid fields of your form and also defined in your config.

### The EMail-Subject ###
Like the EMail-Template, also the EMail-Subject do support the twig-renderer.

### The Reply-To Header ###
The E-Mail Adapter's can handle with the "Reply-To" email-header
you can define it inside the Adapter config like this:
```php
'reply-to' => [
    'status' => true,
    'field' => 'mail'
],
```
reply-to only works if:
* reply-to is defined and the following is correct;
* Status is defined and true;
* field is defined (and in config) or not defined (but then some field of your config must be type of email):
```php
'forms' => [
    'contact' => [
        'fields' => [
            'someFieldName' => [
                'type' => 'email',
            ],
        ],
        'adapter' => [
            ...
        ],
    ],
],
```

### The Required-Attribute ###
The Formhandler can check if a field is required in your form and dont accept the request if it is missing.
if you don't define required or required as false, the handler might not get data of the field because it was missing in the request.
If you want to set a field as required add this into the config of the field:
```php
'required' => true
```

### The Example ###

```php
'depaForm' => [
    'adapter' => [
        'globalTestAdapter-1' => [
            'smtpmail' => [
                'recipients'     => ['marcel.dp.designpark@gmail.com'],
                'subject'        => 'base subject all forms that uses this specific adaper has',
                'sender'         => 'example@example.com',
                'senderName'     => 'Kontaktformular',
                'template'       => 'nothing',
                'email_transfer' => [
                    'config' => [
                        'service'    => 'smtp.googlemail.com',
                        'port'       => '465',
                        'encryption' => 'ssl',
                        'email'      => 'marcel.dp.designpark@gmail.com',
                        'password'   => 'marceldesignpark',
                    ],
                ],
            ],
        ],
    ],
    'forms' => [
        'contact' => [
            'fields' => [
                'name' => [
                    'required' => true,
                ],
                'company' => [
                ],
                'street' => [
                ],
                'city' => [
                ],
                'country' => [
                    'required' => true,
                ],
                'phone' => [
                    'required' => true,
                ],
                'mail' => [
                    'required' => true,
                    'type'     => 'email',
                ],
                'message' => [
                    'required' => true,
                ],
            ],
            'adapter' => [
                'globalTestAdapter-1'
            ],
        ],
        'otherForm' => [
            'fields' => [
                'name' => [
                    'required' => true,
                ],
                'company' => [
                ],
                'street' => [
                ],
                'city' => [
                ],
                'country' => [
                    'required' => false,
                ],
                'phone' => [
                    'required' => true,
                ],
                'mail' => [
                    'required' => true,
                    'type'     => 'email',
                ],
                'message' => [
                    'required' => true,
                ],
            ],
            'adapter' => [
                'phpmail' => [
                    'reply-to' => [
                        'status' => true,
                        'field'  => 'mail'
                    ],
                    'recipients' => ['example@example.com'],
                    'subject'    => 'subject',
                    'sender'     => 'sender@example.com',
                    'senderName' => 'Form',
                    'template'       => 'Test {{message}} {{mail}}',
                ],
            ],
        ],
    ],
],
```

## Credits

This bundle has been developed by [designpark](https://www.designpark.de).


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


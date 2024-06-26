# Middleware-FormHandler

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

This library allows you to handle your forms,
check for missing fields and only uses fields that you really expect to be submitted.

## Installation

Run the following to install this library:

```bash
$ composer require marcel-maqsood/mezzio-middleware-formhandler:dev-master
```

## Info ##
If your form has fields that are not defined inside the config, the handler will not use them due to security reasons.
```adapter``` is no longer the correct keyword for DataAdapters, the key is now ```adapters``` as the Formhandler is now able to use multiple adapters in one go.

### BE AWARE: if ```adapters``` contain an entry 'null', that no other adapter after that entry will be used as null passes the data down the pipe. ###

## Documentation

At the bottom of the Doc, i'll show you a quick example on how the config is build like.


### The Implementation ###
To implement the middleware, add a route to your routes file that passes its request into the middleware:
   ```
    $app->route(
        '/formhandler[/]',
        [
            MazeDEV\FormularHandlerMiddleware\FormularHandlerMiddleware::class,
        ],
        ['POST'],
        'formHandler'
    );
   ```

Since our FormHandler is now a real middleware, you can even implement it like this:
   ```php
    $app->route(
        '/formhandler[/]',
        [
            MazeDEV\FormularHandlerMiddleware\FormularHandlerMiddleware::class,
            App\Handler\YourHandler::class
        ],
        ['POST'],
        'formHandler'
    );
   ```

after that, be sure to provide a config-file inside your config/autoload folder, that contains anything the Middleware needs to check your forms.
We recommend you to use our config-file ```/config/form-config.local.php``` paste it into your ```/config/autoload/``` folder and adjust it to fit your needs.

### Needed Data ###
The Formhandler needs either JSON-, Multidata- or plain POST Requests to run properly and it responds with JSON, describing whats going on.
- You can either adress the FormHandler by submitting your form the regualr way (button type="submit" form method="post") or via AJAX. if you go through AJAX, you can use our base JavaScript to prepare the form in accordance to the Handlers needs.

## Important Notes: ##
- Included in this Project, there is a basic JavaScript ```/js/FormToJSON.js``` that is important to send data to the FormHandler when using AJAX Requests.
You can implement your own logic but you may need it to begin with.

- It is essential to define an ```<input type="hidden" name="data[config]" value="YourFormName">``` field inside your form. This field provides our FormHandler with the necessary information about the form it must validate against.

- Also, each Input must begin with "data", like this: ```<input type="hidden" name="data[config]" value="aValue"/>```. Failure to follow this format will result in the input not being recognized by our FormHandler.

### HTML Example ###
    ```html
    <form id="aId" method="post">
        <input type="hidden" name="data[config]" value="aValue"/>
        <div class="row mb-1">
            <div class="col-6">
                <input id="surname" name="data[nachname]" type="text" class="form-control bg-dark"
                placeholder="Surname" required/>
            </div>
            <div class="col-6">
                <input id="Name" name="data[name]" type="text" class="form-control bg-dark"
                placeholder="Name" required/>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="form-group">
                    <label class="text-muted label-center">Empty Fields will be ignored.</label>
                    <button id="submit" type="submit" class="btn btn-success w-100">Submit</button>
                </div>
            </div>
        </div>
    </form>
    ```


### The Adapters ###
Currently there are 3 working Adapters:
* **phpmail**
    <br/>
    definition looks like this:
    ```php
  'adapters' => [
      'phpmail' => [
          'reply-to' => [
              'status' => true,
              'field'  => 'mail',
          ],
              'recipients' => ['example@example.com'],
              'subject'    => 'subject',
              'sender'     => 'sender@example.com',
              'senderName' => 'Form', 
              'template'   => 'app::test',
      ],
  ],
    ```
    phpmail sends the mail (as you may expect) via the php method: mail().
* **smtpmail**
    <br/>
    definition looks like this:
    ```php
  'adapters' => [
      'smtpmail' => [
          //same as on phpmail but includes:
          'email_transfer' => [
              'method' => 'smtpmail',
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
* **null**
    <br/>
    definition looks like this:
    ```php
    'adapters' => null,
    ```
    If you define ```'adapters' => null```, our FormHandler will pass the validated form onto the next Handler in your route, which can then perform its own magic on it.

### The Local-Adapters ###
    
The Adapter field must be directly inside the form-definition:
```php
    'forms' => [
      'contact' => [
            'fields' => [
                ...
            ],
            'adapters' => [
                null
            ],
        ],
    ],
```
    
### The Global-Adapters ###
A Global Adapter is defined in the very top of the config: 
```php
'mazeform' => [
    'adapters' => [
        'globalTestAdapter-1' => [
            [   
                'method'         => 'smtpmail',
                'recipients'     => ['example@example.com'],
                'subject'        => 'base subject all forms that uses this specific adaper has',
                'sender'         => 'example@example.com',
                'senderName'     => 'form',
                'template'       => 'app::test',
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
        'adapters' => [
            'globalTestAdapter-1',
            'secondAdapter',
            null
        ],
    ],
],
```

## Recipients ##
As your forms should be able to send automated responses, you can define any number of recipients within ```php 'recipients'``` and also use '%submit%' so that the handler will map this variable to the first "email" field submitted within your form, eg: max.mustermann@mustermail.de so that max knows that your system took notice.

### The EMail-Template ###
The Template, you specified in the Config can be dynamic through twig, however since v1.0.23, the template field must contain a valid template's name:
```
'template' => 'app::test'
```
the variables you use must be valid fields of your form and also defined in your config.

### The EMail-Subject ###
Like the EMail-Template, also the EMail-Subject do support the twig-renderer, however the Subject does not support template-names, its a plain string:
``` 'subject'        => 'base subject all forms that uses this specific adaper has {{ some_twig_variable }}'```

### The Reply-To Header ###
The E-Mail Adapters can handle with the "Reply-To" email-header
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
* field is defined (and exists in config) or not defined (but then one field of your config must be type of email):
```php
'forms' => [
    'contact' => [
        'fields' => [
            'someFieldName' => [
                'type' => 'email',
            ],
        ],
        'adapters' => [
            ...
        ],
    ],
],
```

### CSRF Protection ###
As your Forms will at some point either be stored within a database or send via email, our FormHandler can check for CSRF-Tokens to protect your application from getting bloated.

To use CSRF Protection, you have to install [Mezzio-CSRF](https://github.com/mezzio/mezzio-csrf) and configure it correctly.
You dont have to define a field as required, but we suggest to use ``` 'required' => 'true' ``` as only then your request are guranteed to be CSRF protected.

After that, be sure to define one of your fields as ``` 'type' => 'csrf' ```:


```php
'contactForm' => [
    'fields' => [
        'somename' => [
            'required' => true
            'type' => 'csrf',
        ],
    ]
]
```

If the request was submitted with an invalid CSRF-Token, our FormHandler won't process the request further and instead passes the request down the pipe with the additional attribute ``` 'csrfError' ``` which you can use within your code:

```php
if($request->getAttribute('csrfError') != null)
{
    //your error handling goes here...
}
```            

### Multi-Layer Submit Arrays ###
As your application might send and receive nested arrays, eg. on contact-forms, our handler is able to retrieve an email field from within a nested arrays:
You only need to define a field as ``` 'type' => 'array'``` if you want to use this behaviour.

```php
'contactForm' => [
    'fields' => [
        'contact' => [
            'type' => 'array',
            'childs' => [
                'salutation' => [
                    'required' => true,
                    'type' => 'text'
                ],
                'name' => [
                    'required' => true,
                    'type' => 'text'
                ],
                'surname' => [
                    'required' => true,
                    'type' => 'text'
                ],
                'email' => [
                    'required' => true,
                    'type' => 'email'
                ],
                'tel' => [
                    'required' => true,
                    'type' => 'int'
                ],
            ]
            //in this case, there is no
        ],
    ],
    'adapters' => [
            ...
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
'mazeform' => [
    'adapters' => [
        'globalExampleAdapter-1' => [
            [
                'method'         => 'smtpmail',
                'recipients'     => ['recipient@example.com'],
                'subject'        => 'base subject all forms that uses this specific adaper has',
                'sender'         => 'example@example.com',
                'senderName'     => 'Kontaktformular',
                'template'       => 'app::test',
                'email_transfer' => [
                    'config' => [
                        'service'    => 'smtp.example.com',
                        'port'       => '465',
                        'encryption' => 'ssl',
                        'email'      => 'example@example.com',
                        'password'   => 'yourPassword',
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
                'somefield' => [
                    'required' => true,
                    'type' => 'csrf'
                ]
            ],
            'adapters' => [
                'globalExampleAdapter-1',
                null
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
                'somefield' => [
                    'required' => true,
                    'type' => 'csrf'
                ]
            ],
            'adapters' => [
                [
                    'method'         => 'phpmail',
                    'reply-to' => [
                        'status' => true,
                        'field'  => 'mail'
                    ],
                    'recipients' => ['example@example.com'],
                    'subject'    => 'example subject',
                    'sender'     => 'sender@example.com',
                    'senderName' => 'Form',
                    'template'       => 'app::test',
                ],
                [
                    'method'         => 'smtpmail',
                    'recipients'     => ['recipient@example.com'],
                    'subject'        => 'base subject all forms that uses this specific adaper has',
                    'sender'         => 'example@example.com',
                    'senderName'     => 'Kontaktformular',
                    'template'       => 'app::test',
                    'email_transfer' => [
                        'config' => [
                            'service'    => 'smtp.example.com',
                            'port'       => '465',
                            'encryption' => 'ssl',
                            'email'      => 'example@example.com',
                            'password'   => 'yourPassword',
                        ],
                    ],
                ],
            ],
        ],
    ],
],
```

## Credits

This bundle has been developed by [designpark](https://www.designpark.de) and was forked by [ElectricBrands](https://www.electricbrands.de).
To maintain this project without further mess, it is now forked onto my own github. (It was mainly me who developed it anyways).


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.


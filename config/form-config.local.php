<?php

return [

    'depaForm' => [
        'adapter' => [
            'testAdapter-1' => [
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
                        //macht mehr sinn, name hier drin zu definieren?
                        'required' => true,
                        //type is not used yet since it gets implemented when it comes to form-generation
                        'type' => 'text',
                    ],
                    'company' => [
                        'type' => 'text',
                    ],
                    'street' => [
                        'type' => 'text',
                    ],
                    'city' => [
                        'type' => 'text',
                    ],
                    'country' => [
                        'type' => 'text',
                    ],
                    'phone' => [
                        'required' => true,
                        'type'     => 'tel',
                    ],
                    'mail' => [
                        'required' => true,
                        'type'     => 'email',
                    ],
                    'message' => [
                        'required' => true,
                        /*Wie stellt man sowas dar? type war eigentlich nur amf HTML-Attribute bezogen.*/
                        'type' => 'textarea',
                    ],
                ],
                'adapter' => [
                    'testAdapter-1'
                ],
            ],
            'otherForm' => [
                'fields' => [
                    'name' => [
                        //macht mehr sinn, name hier drin zu definieren?
                        'required' => true,
                        //type is not used yet since it gets implemented when it comes to form-generation
                        'type' => 'text',
                    ],
                    'company' => [
                        'type' => 'text',
                    ],
                    'street' => [
                        'type' => 'text',
                    ],
                    'city' => [
                        'type' => 'text',
                    ],
                    'country' => [
                        'type' => 'text',
                    ],
                    'phone' => [
                        'required' => true,
                        'type'     => 'tel',
                    ],
                    'mail' => [
                        'required' => true,
                        'type'     => 'email',
                    ],
                    'message' => [
                        'required' => true,
                        /*Wie stellt man sowas dar? type war eigentlich nur amf HTML-Attribute bezogen.*/
                        'type' => 'textarea',
                    ],
                ],
                'adapter' => [
                    'phpmail' => [
                        'reply-to' => [
                            'status' => true, // true/false
                            'field'  => 'mail', // Das Feld im post, welches die Email Adresse der Person benhaltet
                        ],
                        'recipients' => ['example@example.com'],
                        'subject'    => 'subject',
                        'sender'     => 'sender@example.com',
                        'senderName' => 'Form',
                        //Template bleibt in dieser Form nicht bestehen.
                        'template'       => 'Test {{message}} {{mail}}',
                    ],
                ],
            ],
        ],
    ],
];

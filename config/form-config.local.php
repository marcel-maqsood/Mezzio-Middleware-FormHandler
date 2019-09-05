<?php

return [

    'depaForm' => [
        'adapter' => [
            'testAdapter-1' => [
                'smtpmail' => [
                    'recipients' => ['maqsood@designpark.de'],
                    'subject' => 'Anmeldung als Arbeitnehmer über paulihealthpeople.de',
                    'sender' => 'formular@paulihealthpeople.de',
                    'senderName' => 'Kontaktformular',
                    'template' => 'formular::bewerber',
                    'email_transfer' => [
                        'method' => "smtp",
                        'config' => [
                            'service' => "smtp.googlemail.com",
                            'port' => "465",
                            'encryption' => "ssl",
                            'email' => "marcel.dp.designpark@gmail.com",
                            'password' => "marceldesignpark",
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
                        'type' => 'text'
                    ],
                    'country' => [
                        'type' => 'text',
                    ],
                    'phone' => [
                        'required' => true,
                        'type' => 'tel',
                    ],
                    'mail' => [
                        'required' => true,
                        'type' => 'email',
                    ],
                    'message' => [
                        'required' => true,
                        /*Wie stellt man sowas dar? type war eigentlich nur amf HTML-Attribute bezogen.*/
                        'type' => 'textarea',
                    ],
                ],
                'adapter' => [
                    'smtpmail' => [
                        'reply-to' => [
                            'status' => true, // true/false
                            'field' => 'mail' // Das Feld im post, welches die Email Adresse der Person benhaltet
                        ],
                        'recipients' => ['maqsood@designpark.de'],
                        'subject' => 'Anfrage über paulihealthpeople.de',
                        'sender' => 'formular@paulihealthpeople.de',
                        'senderName' => 'Kontaktformular',
                        //Template bleibt in dieser Form nicht bestehen.
                        'template' => 'formular::contact',
                        'email_transfer' => [
                            'method' => "smtp",
                            'config' => [
                                'service' => "smtp.googlemail.com",
                                'port' => "465",
                                'encryption' => "ssl",
                                'email' => "marcel.dp.designpark@gmail.com",
                                'password' => "marceldesignpark",
                            ],
                        ],
                    ],
                ],
            ],
            'arbeitnehmer' => [
                'fields' => [
                    'name' => [
                        'required' => true,
                        'type' => 'text',
                    ],
                    'forename' => [
                        'required' => true,
                        'type' => 'text',
                    ],
                    'qualification' => [
                        'required' => true,
                        'type' => "text",
                    ],
                    'street' => [
                        'required' => true,
                        'type' => 'text',
                    ],
                    'city' => [
                        'required' => true,
                        'type' => 'text'
                    ],
                    'email' => [
                        'required' => true,
                        'type' => 'email',
                    ],
                    'phone' => [
                        'required' => true,
                        'type' => 'tel',
                    ],
                    'message' => [
                        'required' => true,
                    ],
                    'interest' => [
                        'required' => true,
                    ],
                ],
                'adapter' => [
                    'smtpmail' => [
                        'recipients' => ['maqsood@designpark.de'],
                        'subject' => 'Anmeldung als Arbeitnehmer über paulihealthpeople.de',
                        'sender' => 'formular@paulihealthpeople.de',
                        'senderName' => 'Kontaktformular',
                        'template' => 'formular::bewerber',
                        'email_transfer' => [
                            'method' => "smtp",
                            'config' => [
                                'service' => "smtp.googlemail.com",
                                'port' => "465",
                                'encryption' => "ssl",
                                'email' => "marcel.dp.designpark@gmail.com",
                                'password' => "marceldesignpark",
                            ],
                        ],
                    ],
                ],
            ],
            'arbeitgeber' => [
                'fields' => [
                    'einrichtung' => [
                        //macht mehr sinn, name hier drin zu definieren?
                        'required' => true,
                        //type is not used yet since it gets implemented when it comes to form-generation
                        'type' => 'text',
                    ],
                    'ansprechpartner_name' => [
                        'required' => true,
                        'type' => 'text',
                    ],
                    'ansprechpartner_forename' => [
                        'required' => true,
                        'type' => 'text',
                    ],
                    'funktion' => [
                        'required' => true,
                        'type' => 'text'
                    ],
                    'street_arbeitgeber' => [
                        'required' => true,
                        'type' => 'text',
                    ],
                    'zipcode_arbeitgeber' => [
                        'required' => true,
                        'type' => 'text',
                    ],
                    'city_arbeitgeber' => [
                        'required' => true,
                        'type' => 'email',
                    ],
                    'phone_arbeitgeber' => [
                        'required' => true,
                        /*Wie stellt man sowas dar? type war eigentlich nur amf HTML-Attribute bezogen.*/
                        'type' => 'textarea',
                    ],
                    'email_arbeitgeber' => [
                        'required' => true,
                        /*Wie stellt man sowas dar? type war eigentlich nur amf HTML-Attribute bezogen.*/
                        'type' => 'textarea',
                    ],
                    'message' => [
                        'required' => true,
                        /*Wie stellt man sowas dar? type war eigentlich nur amf HTML-Attribute bezogen.*/
                        'type' => 'textarea',
                    ],
                ],
                'adapter' => [
                    'testAdapter-1',
                    'smtpmail' => [
                        'recipients' => ['maqsood@designpark.de'],
                        'subject' => 'Anmeldung als Arbeitnehmer über paulihealthpeople.de',
                        'sender' => 'formular@paulihealthpeople.de',
                        'senderName' => 'Kontaktformular',
                        'template' => 'formular::bewerber',
                        'email_transfer' => [
                            'method' => "smtp",
                            'config' => [
                                'service' => "smtp.googlemail.com",
                                'port' => "465",
                                'encryption' => "ssl",
                                'email' => "marcel.dp.designpark@gmail.com",
                                'password' => "marceldesignpark",
                            ],
                        ],
                    ],

                ],
            ],
            // und so weiter...
        ],
    ],
];
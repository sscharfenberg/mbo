<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'name' => [
            'required' => 'Bitte geben Sie den Benutzernamen an.',
            'unique' => 'Dieser Benutzername ist bereits vergeben.',
            'max' => 'Der Benutzername darf nicht mehr als :max Zeichen enthalten.',
            'min' => 'Der Benutzername muss mindestens :min Zeichen enthalten.'
        ],
        'email' => [
            'required' => 'Bitte geben Sie die E-Mail-Adresse an.',
            'email' => 'Dies scheint keine gültige E-Mail-Adresse zu sein.',
            'max' => 'Die E-Mail-Adresse darf nicht mehr als :max Zeichen enthalten.'
        ],
        'password' => [
            'required' => 'Bitte geben Sie das Passwort an.',
            'min' => 'Das Passwort muss mindestens :min Zeichen enthalten.',
            'entropy' => 'Das Passwort ist nicht sicher genug.'
        ],
        'password_confirmation' => [
            'same' => 'Die Passwort Bestätigung entspricht nicht dem eigentlichen Passwort.',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];

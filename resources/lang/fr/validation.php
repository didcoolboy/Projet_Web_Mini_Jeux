<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lignes de langue pour la validation
    |--------------------------------------------------------------------------
    */

    'accepted' => 'Le champ :attribute doit etre accepte.',
    'alpha' => 'Le champ :attribute ne doit contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne doit contenir que des lettres, chiffres, tirets et underscores.',
    'alpha_num' => 'Le champ :attribute ne doit contenir que des lettres et des chiffres.',
    'array' => 'Le champ :attribute doit etre un tableau.',
    'boolean' => 'Le champ :attribute doit etre vrai ou faux.',
    'confirmed' => 'La confirmation de :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'email' => 'Le champ :attribute doit etre une adresse e-mail valide.',
    'file' => 'Le champ :attribute doit etre un fichier.',
    'image' => 'Le champ :attribute doit etre une image.',
    'integer' => 'Le champ :attribute doit etre un entier.',
    'max' => [
        'numeric' => 'Le champ :attribute ne doit pas etre superieur a :max.',
        'file' => 'Le champ :attribute ne doit pas depasser :max kilo-octets.',
        'string' => 'Le champ :attribute ne doit pas depasser :max caracteres.',
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max elements.',
    ],
    'min' => [
        'numeric' => 'Le champ :attribute doit etre au moins de :min.',
        'file' => 'Le champ :attribute doit faire au moins :min kilo-octets.',
        'string' => 'Le champ :attribute doit contenir au moins :min caracteres.',
        'array' => 'Le champ :attribute doit contenir au moins :min elements.',
    ],
    'numeric' => 'Le champ :attribute doit etre un nombre.',
    'password' => 'Le mot de passe est incorrect.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est obligatoire.',
    'same' => 'Les champs :attribute et :other doivent etre identiques.',
    'size' => [
        'numeric' => 'Le champ :attribute doit etre de :size.',
        'file' => 'Le fichier :attribute doit faire :size kilo-octets.',
        'string' => 'Le champ :attribute doit contenir :size caracteres.',
        'array' => 'Le champ :attribute doit contenir :size elements.',
    ],
    'string' => 'Le champ :attribute doit etre une chaine de caracteres.',
    'unique' => 'Le champ :attribute est deja utilise.',
    'url' => 'Le champ :attribute doit etre une URL valide.',

    'custom' => [
        'pseudo' => [
            'unique' => 'Ce pseudo est deja utilise.',
        ],
        'email' => [
            'unique' => 'Cette adresse mail est deja active.',
        ],
        'username' => [
            'unique' => 'Ce pseudo est deja utilise.',
        ],
        'name' => [
            'unique' => 'Ce pseudo est deja utilise.',
        ],
        'password' => [
            'min' => 'Le mot de passe doit contenir au moins :min caracteres.',
        ],
    ],

    'attributes' => [
        'name' => 'pseudo',
        'pseudo' => 'pseudo',
        'username' => 'pseudo',
        'password' => 'mot de passe',
        'email' => 'adresse e-mail',
    ],

];

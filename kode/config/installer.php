<?php

use App\Enums\StatusEnum;

return [

    'app_name'    => "Beepost",
    'software_id' => "O6QIE523BF3NBBO6",
    'version'     => 2.2,

    'cacheFile'   => 'YmVlcG9zdA==',

    'core' => [
        'appVersion' => '2.2',
        'minPhpVersion' => '8.2'
    ],

    'requirements' => [

        'php' => [
            'Core',
            'bcmath',
            'openssl',
            'pdo_mysql',
            'mbstring',
            'tokenizer',
            'json',
            'curl',
            'gd',
            'zip',
            'mbstring',


        ],
        'apache' => [
            'mod_rewrite',
        ],

    ],
    'permissions' => [
        '.env'     => '666',
        'storage'     => '775',
        'bootstrap/cache/'       => '775',
    ],

];

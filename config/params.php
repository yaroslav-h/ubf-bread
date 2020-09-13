<?php

const LOCALE_UK_UA = 1;
const LOCALE_EN_US = 2;
const LOCALE_RU_RU = 3;

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',

    'locales' => [
        LOCALE_UK_UA => 'uk-UA',
        LOCALE_EN_US => 'en-US',
        LOCALE_RU_RU => 'ru-RU',
    ],
    'availableLocales' => [
        ['code' => 'uk', 'locale' => 'uk-UA', 'name' => 'Ukrainian', 'lang' => 1],
        ['code' => 'ru', 'locale' => 'ru-RU', 'name' => 'Russian', 'lang' => 3],
        ['code' => 'en', 'locale' => 'en-US', 'name' => 'English', 'lang' => 2],
    ]
];

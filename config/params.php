<?php

const LOCALE_UK_UA = 1;
const LOCALE_EN_US = 2;
const LOCALE_RU_RU = 3;

const LANG_UK_UA = 1;
const LANG_EN_US = 2;
const LANG_RU_RU = 3;

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
        ['code' => 'uk', 'locale' => 'uk-UA', 'name' => 'Ukrainian', 'lang' => LANG_UK_UA],
        ['code' => 'ru', 'locale' => 'ru-RU', 'name' => 'Russian', 'lang' => LANG_RU_RU],
        ['code' => 'en', 'locale' => 'en-US', 'name' => 'English', 'lang' => LANG_EN_US],
    ]
];

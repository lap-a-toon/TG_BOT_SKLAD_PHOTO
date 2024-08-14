<?php
// Указываем токен телеграм-бота
define('BOT_API_TOKEN','YOUR_BOT_API_TOKEN');
// Указываем имя телеграм-бота
define('BOT_USERNAME','YOUR_BOT_USERNAME_bot');

// Указываем свой часовой пояс (т.к. дата-время используется при именовании файлов и по-умолчанию подхватывается время UTC+0)
define('TIME_ZONE',new DateTimeZone('Europe/Moscow'));
// Указываем каталог, в который будут сохраняться фотографии
define('MAIN_PHOTO_FOLDER',__DIR__.'/YOUR_FINALLY_COLLECTING_FOLDER'); // Если необходим абсолютный путь - убираем __DIR__.

define('EXTENSIONS_ACCEPTED',['jpg','jpeg','png','bmp','heic','avi','mp4']);

/**
 * Массив пользователей, заполнять вручную по шаблону
 * [
 *      'TG_USER_ID' => [
 *           'fio'=>'Фамилия_сотрудника',
 *           'sklad'=> 'База/Субсклад',
 *      ]
 * ]
 * где:
 *   TG_USER_ID - Телеграм ID, который пользователь получит в ответ на старт бота
 *   fio и sklad используются для именования каталогов при сохранении, соответственно, если использовать / в имени, то создастся подкаталог
 */
$usersAccepted = [
    'TG_USER_ID'=>[
        'fio'=>'USER_LAST_NAME',
        'sklad'=> 'ADDRESS_NAME/SUB_SKLAD',
    ],
];
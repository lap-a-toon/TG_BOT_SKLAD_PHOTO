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

function getBotUsers(){
    $json = file_get_contents(__DIR__.'/users.json'); 

    // Check if the file was read successfully
    if ($json === false) {
        die('Error reading the JSON file');
    }

    // Decode the JSON file
    $json_data = json_decode($json, true); 

    // Check if the JSON was decoded successfully
    if ($json_data === null) {
        die('Error decoding the JSON file');
    }
    return $json_data;
}
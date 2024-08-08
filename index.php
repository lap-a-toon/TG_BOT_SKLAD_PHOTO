<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Entities\Message;
use Longman\TelegramBot\Entities\User;

require __DIR__.'/config.php';

/* предварительно подгрузили корневой сертификат для проверки, чтобы не сыпало ошибками https://curl.se/ca/cacert.pem
 и указали его в настройках php:
[curl]
; A default value for the CURLOPT_CAINFO option. This is required to be an
; absolute path.
;curl.cainfo =
curl.cainfo = "C:/php/cacert.pem"
*/

$telegram = new Telegram(BOT_API_TOKEN, BOT_USERNAME);
$telegram->useGetUpdatesWithoutDatabase(); // turn off MySQL connection
$telegram->setDownloadPath(__DIR__.'/Download');

// Получение обновлений
$lastUpdateId = 0;
$updates = $telegram->handleGetUpdates(['offset' => $lastUpdateId])->getResult();
$usersAlerted = [];
/** @var Update $update */
foreach ($updates as $update) {
    $isEditedMsg = false;
    $lastUpdateId = $update->getUpdateId() + 1;
    $message = $update->getMessage();
    if($message === null){
        $message = $update->getEditedMessage();
        $isEditedMsg = true;
    }
    $chatId = $message->getChat()->getId();
    $from = $message->getFrom();
    $fromId = $from->getId(); // Получаем ID пользователя
    $fromName = $from->getFirstName();
    $messageType = $message->getType();
    echo "Message from: {$fromName}, Message type: {$messageType}" . PHP_EOL;

    switch ($messageType){
        case 'command':
            // reaction on command
            // Отправляем ответ пользователю
            if( in_array(mb_strtolower($message->text),['/start']) ){
                $response = Request::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Ваш ID: `{$fromId}`\r\nСообщите его администрации",
                    'parse_mode'=>'Markdown'
                ]);
            }
            break;
        case 'text':
            // reaction on text msg
            // Отправляем ответ пользователю
            if( in_array(mb_strtolower($message->text),['/start','привет','я']) ){
                $response = Request::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Ваш ID: `{$fromId}`\r\nСообщите его администрации",
                    'parse_mode'=>'Markdown'
                ]);
            }
            break;
        case 'photo':
            if(!isset($usersAccepted[$chatId])) break; // Если пользователя нет в списке разрешенных - пропускаем
            // поругаться, что в сжатом виде
            if(!in_array($chatId, $usersAlerted)){
                $usersAlerted[]=$chatId;
                $response = Request::sendMessage([
                    'chat_id' => $chatId,
                    'text' => "Фотографии надо присылать в несжатом виде! Это важно!\r\nНо эти, так уж и быть примем.",
                ]);
            }
            // break; // шагаем дальше
        case 'video':
            if(!isset($usersAccepted[$chatId])) break; // Если пользователя нет в списке разрешенных - пропускаем
        case 'document':
            if(!isset($usersAccepted[$chatId])) break; // Если пользователя нет в списке разрешенных - пропускаем
            // обработка файла
            if(get_files($telegram, $message)){
                if(!in_array($chatId, $usersAlerted)){
                    $usersAlerted[]=$chatId;
                    $response = Request::sendMessage([
                        'chat_id' => $chatId,
                        'text' => "Принимаем файлы",
                    ]);
                }
            }
            break;
        default:
            break;
    }
}
    
/**
 * get_files
 *
 * @param  Telegram $telegram
 * @param  Message $message
 * @return bool
 */
function get_files($telegram, $message){
    $download_path = $telegram->getDownloadPath();
    $message_type = $message->getType();
    $doc = $message->{'get' . ucfirst($message_type)}();
    
    // For photos, get the best quality!
    ($message_type === 'photo') && $doc = end($doc);

    $file_id = $doc->getFileId();
    $file    = Request::getFile(['file_id' => $file_id]);
    if ($file->isOk() && Request::downloadFile($file->getResult())) {
        $pathToSave = getPathToSave($message->getFrom(), $message);
        if (!@mkdir(MAIN_PHOTO_FOLDER . '/' . $pathToSave, 0755, true) && !is_dir(MAIN_PHOTO_FOLDER . '/' . $pathToSave)) { // создаём папку, если ее нет
            echo "trouble creating dir MAIN_PHOTO_FOLDER/$pathToSave";
        }
        $date = new DateTime('now',TIME_ZONE);
        $ext = mb_strtolower(pathinfo($download_path . '/' . $file->getResult()->getFilePath(), PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png','bmp'])){
            rename($download_path . '/' . $file->getResult()->getFilePath(), MAIN_PHOTO_FOLDER . '/'. $pathToSave . '/' . $message->getMessageId() . '-' . $date->format('H-i-s') . '.' . $ext);
            return true;
        }else{
            echo "Wrong extention".PHP_EOL;
            echo $message->getMessageId();
            Request::sendMessage([
                'chat_id' => $message->getFrom()->getId(),
                'text' => "Мы не можем принять такой файл",
                'reply_to_message_id' => $message->getMessageId(),
            ]);
            unlink($download_path . '/' . $file->getResult()->getFilePath());
            return false;
        }
    } else {
        $text = 'ОШИБКА!\r\nПри получении ваших фото что-то пошло не так\r\nПопробуйте еще раз, если снова получите ошибку - сообщите администрации.';
        Request::sendMessage([
            'chat_id' => $message->getChat()->getId(),
            'text' => $text,
            'reply_to_message_id' => $message->getMessageId(),
        ]);
        return false;
    }
}
/**
 * Summary of getPathToSave
 * @param User $user
 * @param Message $message
 * @return string|bool
 */
function getPathToSave($user,$message){
    global $usersAccepted;
    if (!isset($usersAccepted[$user->getId()])){
        return false;
    }
    $date = new DateTime('now',TIME_ZONE);
    /**
     * указываем путь по которому будет помещен файл в формате на примере:
     *   [База]Склад /  2024 / 02 / 28 / Иванов 
     */ 
    $answer = $usersAccepted[$user->getId()]['sklad'] . '/' . $date->format('Y/m/d') . '/' . $usersAccepted[$user->getId()]['fio'];
    return $answer;
}
#!/bin/bash
# запускается через cron каждую минуту
# для сработки каждые 5 секунд - запускаем с интервалом в 5 секунд 12 раз

php PATH_TO_YOUR_BOT_FOLDER/index.php >> PATH_TO_YOUR_BOT_FOLDER/log_sklad_photo.log
sleep 10
php PATH_TO_YOUR_BOT_FOLDER/index.php >> PATH_TO_YOUR_BOT_FOLDER/log_sklad_photo.log
sleep 10
php PATH_TO_YOUR_BOT_FOLDER/index.php >> PATH_TO_YOUR_BOT_FOLDER/log_sklad_photo.log
sleep 10
php PATH_TO_YOUR_BOT_FOLDER/index.php >> PATH_TO_YOUR_BOT_FOLDER/log_sklad_photo.log
sleep 10
php PATH_TO_YOUR_BOT_FOLDER/index.php >> PATH_TO_YOUR_BOT_FOLDER/log_sklad_photo.log
sleep 10
php PATH_TO_YOUR_BOT_FOLDER/index.php >> PATH_TO_YOUR_BOT_FOLDER/log_sklad_photo.log
sleep 10

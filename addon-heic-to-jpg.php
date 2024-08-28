<?php

if(PHP_OS === "Linux"){ // Проверяем, что запущено на Linux
    // Проверяем установлен ли пакет libheif-examples, без него конвертация невозможна
    $output = null;
    $retval = null;
    $try = exec("dpkg -s libheif-examples 2>/dev/null",$output,$retval);
    if(!$retval){
        /**
         * heic_to_php
         *
         * @param  string $inputFile Путь к файлу, который надо сконвертировать
         * @return string Путь к результирующему файлу
         */
        function heic_to_jpg(string $inputFile):string{
            $result = $inputFile;
            $output=null;
            $retval=null;
            $try = exec("heif-convert $inputFile $inputFile.jpg 2>/dev/null",$output,$retval);
            if($retval){
                echo "Error converting file: $inputFile" . PHP_EOL;
            }else{
                shell_exec("rm $inputFile >/dev/null");
                $result .= ".jpg";
            }
            return $result;
        }
    }
}
<?php
    namespace Utilities;

    class JSONUtils {
        public static function readFileToObj($file) {
            $JSON = file_get_contents($file) ? file_get_contents($file) : '';
            return unserialize($JSON);
        }
        public static function writeObjToFile($file, $obj) {
            $wFile = fopen($file, 'w');
            fwrite($wFile, serialize($obj));
            fclose($wFile);
        }
    }
<?php
    namespace Utilities;

    class Validator {
        //Regex
        public static function validateRegex($in, $regex, $properFormat, $isRequired=false) {
            if (strlen($in) == 0 && $isRequired) {
                return 'Required.';
            }
            else if (strlen($in) > 0) {
                if (!preg_match($regex, $in)) {
                    return $properFormat;
                }
                else return '';
            }
            else return '';
        }
        //Filter
        public static function validateFilter($in, $filter, $properFormat, $isRequired=false) {
            if (strlen($in) == 0 && $isRequired) {
                return 'Required.';
            }
            else if (strlen($in) > 0) {
                if (!filter_var($in, $filter)) {
                    return $properFormat;
                }
                else return '';
            }
            else return '';
        }
        //Length
        public static function validateLength($in, $min, $max, $isRequired=false) {
            if (strlen($in) == 0 && $isRequired) {
                return 'Required.';
            }
            if ($min != 0) {
                if (strlen($in) < $min || strlen($in) > $max) {
                    return "Must be between $min and $max characters.";
                }
            }
            else if (strlen($in) > $max) {
                return "Must be less than $max characters.";
            }
            else return '';
        }
        //Error Checking
        public static function checkErrors($err) {
            foreach ($err as $e) {
                if (strlen($e) != 0) {
                    return false;
                }
            }
            return true;
        }
    }
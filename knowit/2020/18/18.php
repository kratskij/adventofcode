<?php

$words = file(__DIR__ . DIRECTORY_SEPARATOR . "wordlist", FILE_IGNORE_NEW_LINES);

echo count(array_filter($words, "isPaliNestenDrome")) . "\n";
#Correct: 252

function isPaliNestenDrome($word) {
    if (mb_strlen($word) == 2 || mb_strrev($word) == $word) {
        return false;
    }
    while ($word) {
        if (mb_substr($word, -1) == mb_substr($word, 0, 1)) {
            $word = mb_substr($word, 1, -1);
        } else if (mb_substr($word, -2) == mb_substr($word, 0, 2)) {
            $word = mb_substr($word, 2, -2);
        } else {
            return false;
        }
    }
    return true;
}

function mb_strrev(string $string, string $encoding = null): string
{
    $chars = mb_str_split($string, 1, $encoding ?: mb_internal_encoding());
    return implode('', array_reverse($chars));
}

function mb_str_split($string, $split_length = 1, $encoding = null)
{
    if (null !== $string && !\is_scalar($string) && !(\is_object($string) && \method_exists($string, '__toString'))) {
        trigger_error('mb_str_split(): expects parameter 1 to be string, '.\gettype($string).' given', E_USER_WARNING);
        return null;
    }
    if (null !== $split_length && !\is_bool($split_length) && !\is_numeric($split_length)) {
        trigger_error('mb_str_split(): expects parameter 2 to be int, '.\gettype($split_length).' given', E_USER_WARNING);
        return null;
    }
    $split_length = (int) $split_length;
    if (1 > $split_length) {
        trigger_error('mb_str_split(): The length of each segment must be greater than zero', E_USER_WARNING);
        return false;
    }
    if (null === $encoding) {
        $encoding = mb_internal_encoding();
    } else {
        $encoding = (string) $encoding;
    }

    if (! in_array($encoding, mb_list_encodings(), true)) {
        static $aliases;
        if ($aliases === null) {
            $aliases = [];
            foreach (mb_list_encodings() as $encoding) {
                $encoding_aliases = mb_encoding_aliases($encoding);
                if ($encoding_aliases) {
                    foreach ($encoding_aliases as $alias) {
                        $aliases[] = $alias;
                    }
                }
            }
        }
        if (! in_array($encoding, $aliases, true)) {
            trigger_error('mb_str_split(): Unknown encoding "'.$encoding.'"', E_USER_WARNING);
            return null;
        }
    }

    $result = [];
    $length = mb_strlen($string, $encoding);
    for ($i = 0; $i < $length; $i += $split_length) {
        $result[] = mb_substr($string, $i, $split_length, $encoding);
    }
    return $result;
}

<?php
namespace helpers;

class StringGenerator
{
    /** @var string Набор символов для генерации строки */
    public static string $pattern = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';

    /**
     * Метод для генерации строки указанной длины
     * @param int $length
     * @return string
     */
    public static function generate(int $length = 10): string
    {
        $patternLength = strlen(self::$pattern) - 1;
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= self::$pattern[rand(0, $patternLength)];
        }
        return $string;
    }

    /**
     * Генерируем уникальную строку для различения элементов
     * @return string
     */
    public static function unique(): string
    {
        $string = self::generate(30);
        return md5($string . date('YmdHis'));
    }
}
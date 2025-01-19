<?php

namespace helpers;

class PrepareData
{
    /**
     * Подготовка значения переменной
     * @param string|float|bool|int|null $value Значение в виде строки
     * @param string $filter
     * @return bool|float|int|string|null
     */
    public static function filter(string|float|bool|int|null $value, string $filter): float|bool|int|string|null
    {
        if ($value === '0') {
            return 0;
        }

        if (empty($value)) {
            return null;
        }

        return match ($filter) {
            'number' => (int)trim(filter_var($value, FILTER_SANITIZE_NUMBER_INT)),
            'string' => trim(filter_var($value, FILTER_UNSAFE_RAW)),
            'date' => trim(filter_var(trim($value), FILTER_SANITIZE_NUMBER_INT)),
            'quoted_string' => trim(filter_var($value, FILTER_UNSAFE_RAW, ['flags' => FILTER_FLAG_NO_ENCODE_QUOTES])),
            'float' => (float)self::removeSpaces($value),
            'checkbox' => in_array(filter_var($value, FILTER_UNSAFE_RAW), ['true', 'on']),
            'email' => trim(filter_var($value, FILTER_SANITIZE_EMAIL)),
            default => null
        };
    }

    /**
     * Фильтрация из входного массива
     * @param array $data Входной массив
     * @param string|int $field Наименование поля массива
     * @param string $filter Вид фильтра
     * @return float|bool|int|string|null
     */
    public static function dataFilter(array $data, string|int $field, string $filter): float|bool|int|string|null
    {
        if (isset($data[$field])) {
            return self::filter($data[$field], $filter);
        }
        return null;
    }

    /**
     * Преобразуем массив по уникальному ключу
     * @param array $array Входной массив
     * @return array
     */
    public static function prepareByUniqueFromArray(array $array): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $lst = explode('_', $key);
            if (count($lst) < 2) {
                continue;
            }
            $newArray[$lst[1]][$lst[0]] = $value;
            unset($array[$key]);
        }
        return [
            'unique' => $newArray,
            'remainder' => $array
        ];
    }

    /**
     * Получаем корректное 3-х или 4-х буквенное расширение
     * @param string $filename Имя файла с расширением
     * @return array|string|string[]
     */
    public static function getCorrectExtension(string $filename): array|string
    {
        $array = explode('.', $filename);
        return strtolower(array_pop($array));
    }

    /**
     * Нормализация количества пробелов в строке
     * @param string $string Входящая строка с лишними пробелами
     * @return string
     */
    public static function removeIncorrectSpaces(string $string = ''): string
    {
        $string = trim($string);
        return preg_replace('/\s+/', ' ', $string);
    }

    /**
     * Удаление пробелов из строки
     * @param string $string Входная строка
     * @return string
     */
    public static function removeSpaces(string $string = ''): string
    {
        $string = trim($string);
        return preg_replace('/\s+?/', '', $string);
    }

    /**
     * Удаляем указанные символы из строки
     * @param string $string Входная строка
     * @param string|array $needle Символы к удалению. Один или массив символов
     * @return string
     */
    public static function removeSymbols(string $string, string|array $needle): string
    {
        if (!is_array($needle)) {
            $needle = [$needle];
        }
        foreach ($needle as $pin) {
            $pin = (string)$pin;
            $string = str_ireplace($pin, '', $string);
        }
        return $string;
    }

    /**
     * Очищаем массив от элементов с пустым значением
     * @param array $array Входной массив
     * @return array
     */
    public static function clearArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if (empty($value)) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
     * Видоизменяем имя файла для корректного
     * @param string $name Имя ключа массива в методе
     * @return string
     */
    public static function prepareFileName(string $name): string
    {
        $value = trim(filter_var($name, FILTER_UNSAFE_RAW));
        return str_ireplace(' ', '_', $value);
    }

    /**
     * Сериализация данных с заменой \r\n на плейсхолдер
     * @param mixed $input Входные данные (обычно массив)
     * @return string
     */
    public static function serialize(mixed $input): string
    {
        return serialize(self::prepareSerializableString($input));
    }

    /**
     * Десериализация данных с заменой плейсхолдера на \r\n
     * @param string $input Входные данные - сериализованная строка
     * @return mixed
     */
    public static function unSerialize(string $input): mixed
    {
        $data = unserialize($input);
        return self::prepareUnSerializedArray($data);
    }

    /**
     * Рекурсивный обход массива для замены \r\n на плейсхолдер
     * @param array|int|string|null $data входные данные (обычно массив)
     * @return string|array|int|null
     */
    public static function prepareSerializableString(array|int|string|null $data): string|array|int|null
    {
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                $data[$key] = self::prepareSerializableString($item);
            }
        } else {
            $data = str_ireplace(["\r\n", "\n"], '{n}',$data);
            $data = self::removeIncorrectSpaces($data);
        }
        return $data;
    }

    /**
     * Рекурсивный обход десериализованного массива для замены плейсхолдера на \r\n
     * @param mixed $data Входные данные (обычно массив)
     * @return mixed
     */
    public static function prepareUnSerializedArray(mixed $data): mixed
    {
        if (is_array($data)) {
            foreach ($data as $key => $item) {
                $data[$key] = self::prepareUnserializedArray($item);
            }
        } else {
            if (!empty($data)) {
                $data = str_ireplace('{n}', "\r\n", $data);
            }
        }
        return $data;
    }

}
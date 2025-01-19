<?php

namespace helpers;

/**
 * Класс для описания кодов ответов
 */
class AnswerCode
{
    /** @var int Все хорошо */
    const OK = 1;
    /** @var int Все плохо, но непонятно, что */
    const BAD = 2;
    /** @var int Неизвестно, что произошло */
    const UNKNOWN = 3;
    /** @var int Время сессии истекло */
    const SESSION_EXPIRED = 4;
    /** @var int Не аутентифицировано */
    const NOT_AUTHENTICATED = 5;
    /** @var int Вид ответа - заголовок */
    const ANSWER_HEADER = 6;
    /** @var int Вид ответа - страница */
    const ANSWER_PAGE = 7;
    /** @var int Вид ответа - ответ на асинхронный запрос */
    const ANSWER_AJAX = 8;

    /** @var int Наличие кириллицы в строке */
    const STRING_CYRILLIC = 9;
    /** @var int Наличие символов, запрещенных для паролей, имен файлов */
    const STRING_DEPRECATED_SYMBOLS = 10;
    /** @var int Уэе существует */
    const IS_PRESENT = 11;
    /** @var int Популярный пароль */
    const POPULAR_PASSWORD = 12;

    const NOT_FOUND = 13;

}
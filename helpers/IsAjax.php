<?php
namespace helpers;

class IsAjax
{
    /**
     * Проверка на Ajax
     */
    public static function isAjax(): bool
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
            return true;
        }
        return false;
    }
}
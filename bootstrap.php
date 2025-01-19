<?php
/**
 * Функция возврата значения по AJAX, когда все хорошо
 * @param string|array $content
 * @return bool|string
 */
function ajaxTrue(string|array $content = ''): bool|string
{
    return json_encode(['result' => true, 'content' => $content], JSON_UNESCAPED_UNICODE);
}

/**
 * Функция возврата значения по AJAX, при возникновении ошибки
 * @param string|array|null $content
 * @return bool|string
 */
function ajaxError(null|string|array $content = 'Неизвестная ошибка'): bool|string
{
    return json_encode(['result' => false, 'content' => $content], JSON_UNESCAPED_UNICODE);
}

/**
 * Функция возврата значения по AJAX, для перезагрузки страницы
 * @param string|null $content
 * @return bool|string
 */
function ajaxReload(null|string $content = ''): bool|string
{
    return json_encode(['result' => 'reload', 'content' => $content]);
}
<?php

namespace project\Models\Dto;

use helpers\AnswerCode;

/**
 * Класс для передачи ответов
 */
class ResponseDto
{
    public int $code = AnswerCode::OK;

    public mixed $content = '';
}
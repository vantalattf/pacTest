<?php
namespace db;

/** Класс для получения конфигурации для настроек БД */
class GetPDOConfig
{
    public static array $pac = [
        'userName' => DB_USERNAME,
        'password' => DB_PASSWD,
        'database' => DB_NAME,
    ];
}

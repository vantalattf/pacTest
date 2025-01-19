<?php
/** Разделитель директорий */

use db\DBPDO;

const DS = '\\';
/** Путь к корню */
const ROOT_PATH = 'C:\\OSPanel\\domains\\pac.test\\';

/** Путь к шаблонам TWIG */
const TEMPLATES_PATH = ROOT_PATH . 'project' . DS . 'Templates';

// Путь для сохранения файлов
const BASE_FILE_PATH = ROOT_PATH . 'web' . DS . 'files' . DS;

/** Наименование основной БД проекта */
const PROJECT_DB = 'pac';

/** Режим разработки. Чтобы в сети не отображались ошибки */
const DEVELOPER_MODE = true;

/** Наименование БД */
const DB_NAME = 'pac';
/** Имя пользователя БД */
const DB_USERNAME = 'pac';
/** Пароль пользователя БД */
const DB_PASSWD = '123';

date_default_timezone_set('Europe/Moscow');
error_reporting(E_ALL);
$db = DBPDO::getDb();
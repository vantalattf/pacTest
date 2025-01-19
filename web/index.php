<?php
global $db;
require_once '../vendor/autoload.php';  // Подключаем автозагрузчик классов
require_once '../config.php';           // Подключаем конфиги
require_once '../bootstrap.php';        // Подключаем функции, необходимые всему проекту

use helpers\AnswerCode;
use helpers\IsAjax;
use project\Controller;
use project\Models\Dto\ResponseDto;

/**
 * Роутер для маршрутизации запросов
 * 1 элемент - контроллер
 * 2 элемент - действие. При создании Действия обязательно указывать массив как входной аргумент
 * начиная с 3 элемента - передаваемые данные, которые могут использоваться для обработки
 * как GET параметры в сыром виде
 */

// Разбираем по "?" и "/" чтобы отделить пути и GET
$queryData = explode('/', explode('?', $_SERVER['REQUEST_URI'])[0]);

// Чистка массива от пустых данных
foreach ($queryData as $key => $value) {
    if (empty($value)) {
        unset($queryData[$key]);
    }
}
// Указываем именно так, потому что в разных системах разные DIRECTORY_SEPARATOR
$controllersPath = 'project\\Controllers\\';
$cntr = ''; // Дефолтное значение, для проверки авторизации

// Обработка массива с адресом
if (empty($queryData)) { // Если не указан контроллер
    $controller = $controllersPath . 'DefaultController';
    $action = 'actionDefault';
} else { // Если контроллер указан
    $cntr = filter_var(ucfirst(array_shift($queryData)), FILTER_UNSAFE_RAW);

    if (class_exists($controllersPath . $cntr . 'Controller')) { // Если существует класс контроллера
        $controller = $controllersPath . $cntr . 'Controller';

        if (empty($queryData)) { // Если метод не указан
            $action = 'actionDefault';
        } else { // Если метод указан

            $act = filter_var(ucfirst(array_shift($queryData)), FILTER_UNSAFE_RAW);

            if (method_exists($controller, 'action' . $act)) {
                $action = 'action' . $act;
            } else { // Если метода в контроллере не существует
                $action = 'actionDefault';
            }
        }
    } else { // Если класса контроллера не существует, то и метода тоже не существует
        $controller = $controllersPath . 'DefaultController';
        $action = 'actionDefault';
    }
}

// Собираем общий массив из входных массивов
$dataArray = [];
if (!empty($queryData)) {
    foreach ($queryData as $item) {
        $dataArray[] = $item;
    }
}

if (!empty($_GET)) {
    foreach ($_GET as $key => $value) {
        $dataArray[$key] = $value;
    }
}

if (!empty($_POST)) {
    foreach ($_POST as $key => $value) {
        $dataArray[$key] = $value;
    }
}

// Обращаемся к классам и методам и выводим готовую страницу
try {
    /** @var Controller $class Экземпляр необходимого контроллера */
    $class = new $controller($db); // Создаём экземпляр контроллера
    $answer = $class->$action($dataArray); // Обращаемся к методу выбранного контроллера и выводим результат
    if ($answer instanceof ResponseDto) {
        if ($answer->code == AnswerCode::ANSWER_HEADER) {
            header($answer->content);
            exit;
        } else {
            echo $answer->content;
        }
    } else {
        echo $answer;
    }
} catch (Throwable $e) {
    if (IsAjax::isAjax()) {
        echo ajaxError($e->getMessage());
    } else {
        echo $e->getMessage();
    }
}

<?php

namespace project\Models\Services;

use helpers\AnswerCode;
use PDO;
use project\Models\Dto\ResponseDto;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Абстрактный клас-родитель для служб контроллеров
 */
abstract class ControllerService
{
    /** @var PDO Подключение к БД */
    protected PDO $db;
    /** @var Environment Объект шаблонизатора */
    protected Environment $twig;

    /**
     * Конструктор объекта
     * @param PDO $db Подключение к текущей БД
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $loader = new FilesystemLoader(TEMPLATES_PATH);
        if (DEVELOPER_MODE) {
            $this->twig = new Environment($loader, [
                'debug' => true
            ]);
            $this->twig->addExtension(new DebugExtension());
        } else {
            $this->twig = new Environment($loader);
        }
    }

    /**
     * Получаем значение из массива
     * @param array $array
     * @param string $key
     * @return mixed|null
     */
    public static function getArrayValue(array $array, string $key): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        return null;
    }

    /**
     * Функция для добавления файла в файловую систему
     * @param array $fileData Относительный путь, от корня веб-приложения
     * @param string $storageName Имя в $_FILES
     * @return ResponseDto
     */
    protected function addFile(array $fileData, string $storageName): ResponseDto
    {
        $response = new ResponseDto();
        if (!file_exists(BASE_FILE_PATH)) {
            mkdir(BASE_FILE_PATH, 0777, true);
        }
        // Тут сохраняем файл
        if (!move_uploaded_file($fileData['tmp_name'], BASE_FILE_PATH . $storageName)) {
            $response->code = AnswerCode::BAD;
        }
        return $response;
    }
}
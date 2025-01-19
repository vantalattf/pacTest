<?php
namespace db;
use PDO;

/**
 * Класс для подключения к БД
 */
class DBPDO {
    /** Имя пользователя БД */
    private string $userName = '';
    /** Пароль пользователя БД */
    private string $password = '';
    /** Хост БД (по умолчанию - локальный) */ 
    private string $host = 'localhost';
    /** База данных по умолчанию */
    private mixed $database = '';
    /** Статическое свойство для синглтона */ 
    public static array $db = [];

    /**
     * @var array $config Конфигурация подключения к БД
     *                  ['userName'] Имя пользователя
     *                  ['password'] Пароль
     *                  ['host'] Хост
     *                  ['database'] База данных по дефолту
     */
    public function __construct(array $config = []) {
        if (empty($config)) {
            return null;
        }
        $this->userName = $config['userName'];
        $this->password = $config['password'];
        if (isset($config['database'])) {
            $this->database = $config['database'];
        }
        if (isset($config['host'])) {
            $this->host = $config['host'];
        }
        if (isset($config['port'])) {
            $this->host = $this->host . ':' . $config['port'];
        }
    }

    /** Получаем подключение к БД
     * @return PDO
     */
    private function getConnection(): PDO
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=UTF8';

        return new PDO($dsn, $this->userName, $this->password,
                            array(PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                                  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
    }

    /** Получаем подключение к БД
     * @param string $project
     * @return PDO|null
     */
    public static function getDb(string $project = PROJECT_DB): ?PDO
    {
        if (empty(self::$db[$project])) {
            self::$db[$project] = (new self(GetPDOConfig::$$project))->getConnection();
            self::$db[$project]->query('SET SESSION wait_timeout = 30000');
        }
        return self::$db[$project];
    }

    /**
     * Получаем отдельное подключение к БД для создания записей
     * Это важно, потому что получение идентификаторов работает относительно подключения к БД
     * @param string $project Идентификатор проекта
     * @return PDO
     */
    public static function getNewConnection(string $project = PROJECT_DB): PDO
    {
        return (new self(GetPDOConfig::$$project))->getConnection();
    }

    /**
     * Преобразование массива, где в результате ключ - обязательно идентификатор записи, значение - значение следующего поля
     * @param array $data Массив с данными, где первое значение - идентификатор записи, второе - значение, присваиваемое ключу
     * @return array
     */
    public static function parseIdKeys(array $data): array
    {
        $array = [];
        foreach ($data as $item) {
            $key = array_shift($item);
            $value = array_shift($item);
            $array[$key] = $value;
        }
        return $array;
    }

}

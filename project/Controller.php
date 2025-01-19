<?php

namespace project;

use Exception;
use helpers\AnswerCode;
use helpers\IsAjax;
use PDO;
use project\Models\Auth;
use project\Models\Dto\ResponseDto;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * Основной контроллер, от которого наследовать все контроллеры системы
 */
abstract class Controller
{
    /** @var Environment|null Объект шаблонизатор */
    protected ?Environment $twig = null;
    /** @var PDO Подключение БД окружения */
    protected PDO $db;

    /**
     * Main constructor.
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
     * Дефолтное действие
     * @param array $data
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionDefault(array $data): ResponseDto
    {
        $response = new ResponseDto();
        if (IsAjax::isAjax()) {
            $response->code = AnswerCode::ANSWER_AJAX;
            $response->content = ajaxError('Не найден корректный метод');
            return $response;
        }
        $response->code = AnswerCode::ANSWER_PAGE;
        $page = $this->twig->render('pages/ship.list.twig');
        $response->content = $this->mainWrap($page);
        return $response;
    }

    /**
     * Оборачивалка в основной шаблон с общим функционалом
     * @param string $string Строка для оборачивания
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Exception
     */
    protected function mainWrap(string $string = ''): string
    {
        return $this->twig->render('main.twig', [
            'content' => $string,
        ]);
    }
}
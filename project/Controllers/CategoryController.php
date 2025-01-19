<?php

namespace project\Controllers;

use helpers\AnswerCode;
use helpers\PrepareData;
use project\Controller;
use project\Models\Dto\ResponseDto;
use project\Models\Services\CategoryControllerService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Контроллер для работы с категориями кают
 */
class CategoryController extends Controller
{
    /**
     * По дефолту получаем список категорий кают
     * @param array $data
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionDefault(array $data): ResponseDto
    {
        $response = new ResponseDto();
        $response->code = AnswerCode::ANSWER_PAGE;
        $service = new CategoryControllerService($this->db);
        $response->content = $this->mainWrap($service->renderCategories());
        return $response;
    }

    /**
     * Рендерим отдельную категорию кают
     * @param array $data
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionDetail(array $data): ResponseDto
    {
        $response = new ResponseDto();
        $response->code = AnswerCode::ANSWER_PAGE;
        $id = PrepareData::dataFilter($data, 0,'number');

        $service = new CategoryControllerService($this->db);
        $response->content = $this->mainWrap($service->renderCabin($id));

        return $response;
    }


}
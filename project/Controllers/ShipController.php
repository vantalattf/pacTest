<?php

namespace project\Controllers;

use helpers\AnswerCode;
use helpers\PrepareData;
use project\Controller;
use project\Models\Dto\ResponseDto;
use project\Models\Services\ControllerService;
use project\Models\Services\ShipControllerService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Класс для работы с лайнерами
 */
class ShipController extends Controller
{

    /**
     * Рендерим страницу с данными Лайнеров
     * @param array $data
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionDetail(array $data): ResponseDto
    {
        $response  = new ResponseDto();
        $response->code = AnswerCode::ANSWER_PAGE;
        $id = PrepareData::dataFilter($data, 0,'number');
        $service = new ShipControllerService($this->db);
        $page = $service->renderShip($id);
        $response->content = $this->mainWrap($page);
        return $response;
    }


}
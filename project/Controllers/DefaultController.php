<?php

namespace project\Controllers;

use helpers\AnswerCode;
use helpers\PrepareData;
use project\Controller;
use project\Models\Dto\ResponseDto;
use project\Models\Services\DefaultControllerService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Дефолтный контроллер
 */
class DefaultController extends Controller
{
    /**
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

        $service = new DefaultControllerService($this->db);
        $page = $service->renderShipList();
        $response->content = $this->mainWrap($page);
        return $response;
    }

    /**
     * Получаем форму для редактирования описания лайнера
     * @param array $data
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionEditDescriptionForm(array $data): ResponseDto
    {
        $response = new ResponseDto();
        $response->code = AnswerCode::ANSWER_AJAX;
        $id = PrepareData::dataFilter($data, 'id', 'number');
        $type = PrepareData::dataFilter($data, 'type', 'string');
        $place = PrepareData::dataFilter($data, 'place', 'string');
        if (empty($id)) {
            $response->content = ajaxError('Не указан идентификатор редактируемого описания');
            return $response;
        }

        $service = new DefaultControllerService($this->db);
        $resp = $service->getEditDescriptionForm($id, $type, $place);
        if ($resp->code != AnswerCode::OK) {
            $response->content = ajaxError($resp->content);
            return $response;
        }

        $response->content = ajaxTrue($resp->content);
        return $response;
    }

    /**
     * Сохраняем описание лайнера
     * @param array $data Массив входных данных
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionSaveDescription(array $data): ResponseDto
    {
        $response = new ResponseDto();
        $response->code = AnswerCode::ANSWER_AJAX;
        $id = PrepareData::dataFilter($data, 'id', 'number');
        $type = PrepareData::dataFilter($data, 'type', 'string');
        $description = PrepareData::dataFilter($data, 'description', 'string');
        if (empty($id)) {
            $response->content = ajaxError('Идентификатор редактируемого описания');
            return $response;
        }

        $service = new DefaultControllerService($this->db);
        $result = $service->saveDescription($id, $type, $description);
        if ($result->code != AnswerCode::OK) {
            $response->content = ajaxError($result->content);
            return $response;
        }

        $response->content = ajaxTrue($result->content);
        return $response;
    }

}
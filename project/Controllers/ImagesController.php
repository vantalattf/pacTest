<?php

namespace project\Controllers;

use helpers\AnswerCode;
use helpers\PrepareData;
use project\Controller;
use project\Models\Dto\ResponseDto;
use project\Models\Services\ImagesControllerService;
use project\Models\Services\ShipControllerService;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Клас для работы с иллюстрациями
 */
class ImagesController extends Controller
{
    /**
     * По дефолту получаем список имеющихся изображений
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
        $service = new ImagesControllerService($this->db);
        $response->content = $this->mainWrap($service->renderImagesList());
        return $response;
    }

    /**
     * Получаем форму для добавления
     * @param array $data
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function actionAddImageForm(array $data): ResponseDto
    {
        $response = new ResponseDto();
        $response->code = AnswerCode::ANSWER_AJAX;
        $service = new ShipControllerService($this->db);
        $reference = $service->getShipReference();
        $form = $this->twig->render('forms/add.image.form.twig', [
            'shipReference' => $reference,
        ]);
        $response->content = ajaxTrue($form);
        return $response;
    }

    /**
     * Добавляем картинку
     * @param array $data
     * @return ResponseDto
     */
    public function actionAddImage(array $data): ResponseDto
    {
        $response = new ResponseDto();
        $response->code = AnswerCode::ANSWER_AJAX;
        $shipId = PrepareData::dataFilter($data, 'shipId', 'number');
        $title = PrepareData::dataFilter($data, 'title', 'string');
        $url = (string)PrepareData::dataFilter($data, 'url', 'string');
        if (empty($shipId)) {
            $response->content = ajaxError('Не указан лайнер');
            return $response;
        }
        if (empty($title)) {
            $response->content = ajaxError('Не указана подпись к картинке');
            return $response;
        }
        if (empty($url) && empty($_FILES['file'])) {
            $response->content = ajaxError('Не указан URL или не прикреплен файл.');
            return $response;
        }
        $service = new ImagesControllerService($this->db);
        $result = $service->addImage($shipId, $title, $url, 'file');
        if ($result->code != AnswerCode::OK) {
            $response->content = ajaxError('Не удалось добавить файл');
            return $response;
        }
        $response->content = ajaxTrue();
        return $response;
    }

    /**
     * Удаляем запись о картинке
     * @param array $data
     * @return ResponseDto
     */
    public function actionDeleteImage(array $data): ResponseDto
    {
        $response = new ResponseDto();
        $response->code = AnswerCode::ANSWER_AJAX;
        $id = PrepareData::dataFilter($data, 'id', 'number');
        $service = new ImagesControllerService($this->db);
        $result = $service->deleteImage($id);
        if ($result->code != AnswerCode::OK) {
            $response->content = ajaxError($result->content);
            return $response;
        }
        $response->content = ajaxTrue();
        return $response;
    }
}
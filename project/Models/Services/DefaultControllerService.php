<?php

namespace project\Models\Services;

use helpers\AnswerCode;
use helpers\POD;
use project\Models\Dto\ResponseDto;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Класс для обслуживания нужд дефолтного контроллера
 */
class DefaultControllerService extends ControllerService
{

    /**
     * Получаем отрендеренный список лайнеров
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderShipList(): string
    {
        $data = $this->getShipList();
        return $this->twig->render('pages/ship.list.twig', [
            'data' => $data
        ]);
    }

    /**
     * Получаем список лайнеров
     * @return array
     */
    private function getShipList(): array
    {

        $q = $this->db->query('SELECT * FROM `ships` ORDER BY `ordering`');
        $res = $q->fetchAll();
        if (empty($res)) {
            return [];
        }

        foreach ($res as $key => $ship) {
            $res[$key]['specify'] = json_decode($ship['spec']);
        }
        return $res;
    }

    /**
     * Получаем отрендеренную форму для редактирования
     * @param int $id Идентификатор записи
     * @param string $type Вид описания
     * @param string $place
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getEditDescriptionForm(int $id, string $type, string $place): ResponseDto
    {
        $response = new ResponseDto();

        switch ($type) {
            case 'ship-description':
                $service = new ShipControllerService($this->db);
                $ship = $service->getShip($id);
                if (empty($ship)) {
                    $response->code = AnswerCode::BAD;
                    $response->content = 'По указанному идентификатору не найдено описания';
                    return $response;
                }
                $content = ControllerService::getArrayValue($ship, 'description');
                $title = 'описание лайнера';
                break;
            case 'category-description':
                $service = new CategoryControllerService($this->db);
                $category = $service->getCategory($id);
                if (empty($category)) {
                    $response->code = AnswerCode::BAD;
                    $response->content = 'По указанному идентификатору не найдено описания';
                    return $response;
                }
                $content = ControllerService::getArrayValue($category, 'description');
                $title = 'описание категории каюты';
                break;
            case 'category-images':
                $service = new CategoryControllerService($this->db);
                $images = $service->getCategory($id);
                if (empty($images)) {
                    $response->code = AnswerCode::BAD;
                    $response->content = 'По указанному идентификатору не найдено изображений';
                }
                $raw = ControllerService::getArrayValue($images, 'photos');
                $content = implode("\n", $raw);
                $title = 'состав изображений для категории каюты';
                break;
            default:
                $response->code = AnswerCode::BAD;
                $response->content = 'Не указан тип редактируемого описания';
                return $response;
        }
        $response->content = $this->twig->render('forms/edit.description.form.twig', [
            'description' => $content,
            'id' => $id,
            'title' => $title,
            'type' => $type,
            'place' => $place
        ]);

        return $response;
    }

    /**
     * Сохраняем описание
     * @param int $id Идентификатор сущности
     * @param string $type Вид сущности
     * @param string $description Текст описания
     * @return ResponseDto
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function saveDescription(int $id, string $type, string $description): ResponseDto
    {
        $response = new ResponseDto();
        switch ($type) {
            case 'ship-description':
                $q = $this->db->prepare('UPDATE `ships` SET `description` = :description WHERE `id` = :id');
                $q->execute([
                    'description' => $description,
                    'id' => $id
                ]);
                $response->content = $description;
                break;
            case 'category-description':
                $q = $this->db->prepare('UPDATE `cabin_categories` SET `description` = :description WHERE `id` = :id');
                $q->execute([
                    'description' => $description,
                    'id' => $id
                ]);
                $response->content = $description;
                break;
            case 'category-images':
                $array = explode("\n", $description);
                $string = json_encode($array);
                $q = $this->db->prepare('UPDATE `cabin_categories` SET `photos` = :photos WHERE `id` = :id');
                $q->execute([
                    'photos' => $string,
                    'id' => $id
                ]);
                $description = '';
                foreach ($array as $item) {
                    $description .= $this->twig->render('elements/image.template.twig', [
                        'photo' => $item,
                    ]);
                }
                $response->content = $description;
                break;
            default:
                $response->code = AnswerCode::BAD;
                $response->content = 'Не корректно указан тип описания';
                break;
        }
        return $response;
    }



}
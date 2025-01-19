<?php

namespace project\Models\Services;

use helpers\AnswerCode;
use helpers\PrepareData;
use project\Models\Dto\ResponseDto;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Класс для обслуживания нужд контроллера изображений
 */
class ImagesControllerService extends ControllerService
{

    /**
     * По дефолту получаем список имеющихся в системе картинок
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderImagesList(): string
    {
        $data = $this->getImagesList();
        $imageList = '';
        foreach ($data as $image) {
            $imageList .= $this->twig->render("elements/image.list.row.twig", [
                "image" => $image
            ]);
        }
        return $this->twig->render('pages/image.list.twig', [
            'imageList' => $imageList
        ]);
    }

    /**
     * Получаем список изображений
     * @return array
     */
    public function getImagesList(): array
    {
        $q = $this->db->query('SELECT `g`.*, `s`.`title` AS `ship_title` 
                                      FROM `ships_gallery` AS `g` 
                                      LEFT JOIN `ships` AS `s` ON `g`.`ship_id` = `s`.`id`
                                      ORDER BY `g`.`ship_id`,`g`.`ordering`');
        $res = $q->fetchAll();
        if (empty($res)) {
            return [];
        }
        return $res;
    }

    /**
     * Добавляем изображение
     * @param int $shipId Идентификатор лайнера
     * @param null|string $title Подпись к изображению
     * @param string $url Веб-адрес картинки
     * @param string $name Имя поля в $_FILES
     * @return ResponseDto
     */
    public function addImage(int $shipId, ?string $title, string $url, string $name): ResponseDto
    {
        $response = new ResponseDto();

        if (!empty($_FILES[$name])) {
            $fileData = $_FILES[$name];
            $ext = PrepareData::getCorrectExtension($fileData['name']);
            $storageName = md5($fileData['name'] . date('YmdHis')) . '.' . $ext;
            $save = $this->addFile($fileData, $storageName);
            if ($save->code != AnswerCode::OK) {
                return $save;
            }
            $url = '/files/' . $storageName;
        }
        $q = $this->db->prepare('SELECT MAX(`ordering`) AS `ordering` FROM ships_gallery WHERE `ship_id` = ?');
        $q->execute([$shipId]);
        $ordering = $q->fetchColumn();
        if (empty($ordering)) {
            $ordering = 0;
        }
        $ordering += 1;
        $q = $this->db->prepare('INSERT INTO ships_gallery (ship_id, title, url, ordering) VALUES (?, ?, ?, ?)');
        $q->execute([$shipId, $title, $url, $ordering]);
        return $response;
    }

    /**
     * Удаляем запись о картинке
     * @param int $id Идентификатор записи
     * @return ResponseDto
     */
    public function deleteImage(int $id): ResponseDto
    {
        $response = new ResponseDto();
        $q = $this->db->prepare('SELECT `url` FROM `ships_gallery` WHERE `id` = ?');
        $q->execute([$id]);
        $url = $q->fetchColumn();
        if (empty($url)) {
            $response->code = AnswerCode::NOT_FOUND;
            $response->content = 'Запись по данному идентификатору не найдена';
        }

        if (str_starts_with($url, "/files/")) {
            if (file_exists(ROOT_PATH . 'web' . $url)) {
                unlink(ROOT_PATH . 'web' . $url);
            }
        }

        $q = $this->db->prepare('DELETE FROM `ships_gallery` WHERE `id` = ?');
        $q->execute([$id]);

        return $response;
    }


}
<?php

namespace project\Models\Services;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Класс для обслуживания нужд контроллера Категорий кают
 */
class CategoryControllerService extends ControllerService
{
    /**
     * Получаем отрендеренный список категорий
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderCategories(): string
    {
        $data = $this->getCategoryList();
        return $this->twig->render('pages/category.list.twig', [
            'data' => $data
        ]);
    }

    /**
     * Получаем список категорий
     * @return array
     */
    private function getCategoryList(): array
    {
        $q = $this->db->query("SELECT `c`.*, `s`.`title` AS `ship_title` 
                                      FROM `cabin_categories` AS `c` 
                                      LEFT JOIN `ships` AS `s` ON `c`.`ship_id` = `s`.`id`
                                      ORDER BY `c`.`ship_id`,`c`.`ordering`");
        $res = $q->fetchAll();
        if (empty($res)) {
            return [];
        }
        foreach ($res as $key => $category) {
            $res[$key]['photos'] = json_decode($category['photos']);
        }
        return $res;
    }

    /**
     * Рендерим одну категорию кают
     * @param int $id Идентификатор категории
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderCabin(int $id): string
    {
        $cabin = $this->getCategory($id);
        if (empty($cabin)) {
            return '';
        }
        return $this->twig->render('pages/category.twig', [
            'data' => $cabin
        ]);
    }

    /**
     * Получаем категорию каюты по идентификатору
     * @param int $id
     * @return null|array
     */
    public function getCategory(int $id): ?array
    {
        $q = $this->db->prepare('SELECT `c`.*,`s`.`title` AS `ship_title` 
                                        FROM `cabin_categories` AS `c` 
                                        LEFT JOIN `ships` AS `s` ON `c`.`ship_id` = `s`.`id`
                                        WHERE c.id = :id');
        $q->execute([
            'id' => $id
        ]);
        $res = $q->fetch();
        if (empty($res)) {
            return null;
        }
        $res['photos'] = json_decode($res['photos']);
        $images = '';
        foreach ($res['photos'] as $photo) {
            $images .= $this->twig->render('elements/image.template.twig', [
                'photo' => $photo
            ]);
        }
        $res['images'] = $images;
        return $res;
    }
}
<?php

namespace project\Models\Services;

use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Класс для обслуживания контроллера лайнеров
 */
class ShipControllerService extends ControllerService
{

    /**
     * Рендерим шаблон для отображения одной единицы лайнера
     * @param int $shipId
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderShip(int $shipId): string
    {
        $data = $this->getShipData($shipId);
        return $this->twig->render('pages/ship.twig', [
            'data' => $data
        ]);
    }

    /**
     * Получаем данные по Лайнеру
     * @param int $shipId Идентификатор лайнера
     * @return array
     */
    private function getShipData(int $shipId): array
    {
        $data = [];
        $q = $this->db->prepare("SELECT * FROM `ships` WHERE `id` = :id");
        $q->execute(['id' => $shipId]);
        $ship = $q->fetch();
        if (!empty($ship)) {
            $ship['specify'] = json_decode($ship['spec']);
            $data['ship'] = $ship;
        }
        $q = $this->db->prepare('SELECT * FROM `cabin_categories` WHERE `ship_id` = :id ORDER BY `ordering`');
        $q->execute(['id' => $shipId]);
        $cabins = $q->fetchAll();
        if (!empty($cabins)) {
            foreach ($cabins as $key => $cabin) {
                $cabins[$key]['photos'] = json_decode($cabin['photos']);
            }
        }
        $data['cabins'] = $cabins;
        return $data;
    }

    /**
     * Получение данных по идентификатору лайнера
     * @param int $shipId Идентификатор лайнера
     * @return null|array
     */
    public function getShip(int $shipId): ?array
    {
        $q = $this->db->prepare("SELECT * FROM `ships` WHERE `id` = :id");
        $q->execute(['id' => $shipId]);
        $ship = $q->fetch();
        if (empty($ship)) {
            return null;
        }
        return $ship;
    }

    /**
     * Получаем референс лайнеров
     * @return array
     */
    public function getShipReference(): array
    {
        $q = $this->db->query("SELECT `id`,`title` FROM `ships`");
        $res = $q->fetchAll();
        if (!empty($res)) {
            return $res;
        }
        return [];
    }

}
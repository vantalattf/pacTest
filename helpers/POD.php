<?php

namespace helpers;

/**
 * Класс для работы с page-order-direction
 */
class POD
{
    /** @var int Номер страницы */
    private int $page;
    /** @var int Сдвиг для пагинации */
    private int $offset;
    /** @var string Полк для сортировки */
    private string $order;
    /** @var int Направление сортировки в адресной строке */
    private int $direction;
    /** @var string Направление сортировки в таблице */
    private string $tableDirection;

    /**
     * Конструктор класса
     * @param array $data Массив с входными данными в контроллер
     * @param array $array Массив с возможными полями сортировки
     * @param string $defaultOrder Дефолтная сортировка
     */
    public function __construct(array $data, array $array = ['id'], string $defaultOrder = 'id')
    {
        if (isset($data['page']) && (int)$data['page'] > 0) {
            $this->page = (int)$data['page'];
            $this->offset = (int)$data['page'] - 1;
        } else {
            $this->page = 1;
            $this->offset = 0;
        }

        if (isset($data['order']) && in_array($data['order'], $array)) {
            $this->order = $data['order'];
        } else {
            $this->order = $defaultOrder;
        }

        if (isset($data['direction']) && $data['direction'] == '-1') {
            $this->direction = -1;
            $this->tableDirection = ' DESC ';
        } else {
            $this->direction = 1;
            $this->tableDirection = '';
        }
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getTableDirection(): string
    {
        return $this->tableDirection;
    }

    public function setTableDirection(string $tableDirection): void
    {
        $this->tableDirection = $tableDirection;
    }

}
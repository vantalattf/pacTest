<?php

namespace helpers;
/**
 * Class Paginator
 * Класс для формирования пагинации
 * @package helpers
 */
class Pagination
{
    /** @var int Квант для пагинации */
    public static int $quant = 30;

    /**
     * Получение блока с готовой формой
     * @param int $currentPage Текущая страница
     * @param int $maxPage Максимальная страница
     * @param string $link Ссылка для попадания на другую страницу, "/.../...?id="
     * @param string $order Сортировка
     * @param string $direction Направление сортировки
     * @return string
     */
    public static function pagination(int $currentPage = 0, int $maxPage = 0, string $link = '', string $order = '', string $direction = ''): string
    {
        $pagination = '';
        // Если страница только одна, то не выводим ничего
        if ($maxPage <= 1) {
            return $pagination;
        }

        if (!empty($order)) {
            $qOrder = '&order=' . $order;
        } else {
            $qOrder = '';
        }

        if (!empty($direction)) {
            $qDir = '&direction=' . $direction;
        } else {
            $qDir = '';
        }

        // Если страниц немного, то можно вывести их все
        if ($maxPage < 6) {
            for ($i = 1; $i <= $maxPage; $i++) {
                $pagination .= '<a href="' . $link . $i . $qOrder . $qDir . '"><div class="node';
                if ($currentPage == $i) {
                    $pagination .= ' current-page ';
                }
                $pagination .= '">' . $i . '</div></a>';
            }
            return $pagination;
        }

        // Теперь обрабатываем, если страниц много

        // Страница первая или если не указана
        if ($currentPage <= 1) {
            $pagination .= '<a><div class="node current-page">1</div></a>';
            $pagination .= '<a href="' . $link . '2' . $qOrder . $qDir . '"><div class="node">2</div></a>';
            $pagination .= '<a><div class="node">...</div></a>';
            $pagination .= '<a href="' . $link . $maxPage - 1 . $qOrder . $qDir . '"><div class="node">' . $maxPage - 1 . '</div></a>';
            $pagination .= '<a href="' . $link . $maxPage . $qOrder . $qDir . '"><div class="node">' . $maxPage . '</div></a>';
            $pagination .= '<a href="' . $link . $currentPage + 1 . $qOrder . $qDir . '"><div class="node"><i class="icon-fi-rr-arrow-right"></i></div></a>';
            return $pagination;
        }

        // Страница вторая
        $pagination .= '<a href="' . $link . $currentPage - 1 . $qOrder . $qDir . '"><div class="node"><i class="icon-fi-rr-arrow-left"></i></div></a>';
        $pagination .= '<a href="' . $link . '1' . $qOrder . $qDir . '"><div class="node">1</div></a>';

        if ($currentPage == 2) {
            $pagination .= '<a><div class="node current-page">2</div></a>';
            $pagination .= '<a><div class="node">...</div></a>';
            $pagination .= '<a href="' . $link . $maxPage - 1 . $qOrder . $qDir . '"><div class="node">' . $maxPage - 1 . '</div></a>';
            $pagination .= '<a href="' . $link . $maxPage . $qOrder . $qDir . '"><div class="node">' . $maxPage . '</div></a>';
            $pagination .= '<a href="' . $link . $currentPage + 1 . $qOrder . $qDir . '"><div class="node"><i class="icon-fi-rr-arrow-right"></i></div></a>';
            return $pagination;
        }

        // Страница последняя
        if ($currentPage == $maxPage) {
            $pagination .= '<a><div class="node">...</div></a>';
            $pagination .= '<a href="' . $link . $maxPage - 1 . $qOrder . $qDir . '"><div class="node">' . $maxPage - 1 . '</div></a>';
            $pagination .= '<a><div class="node current-page">' . $maxPage . '</div></a>';
            return $pagination;
        }

        // Страница предпоследняя
        if ($currentPage == $maxPage - 1) {
            $pagination .= '<a><div class="node">...</div></a>';
            $pagination .= '<a><div class="node current-page">' . $currentPage . '</div></a>';
            $pagination .= '<a href="' . $link . $maxPage . $qOrder . $qDir . '"><div class="node">' . $maxPage . '</div></a>';
            $pagination .= '<a href="' . $link . $currentPage + 1 . $qOrder . $qDir . '"><div class="node"><i class="icon-fi-rr-arrow-right"></i></div></a>';
            return $pagination;
        }

        // Страница любая другая
        $pagination .= '<a href="' . $link . '2"><div class="node">2</div></a>';
        if (($currentPage - 2) > 1) {
            $pagination .= '<a><div class="node">...</div></a>';
        }
        $pagination .= '<a><div class="node current-page">' . $currentPage . '</div></a>';
        if (($maxPage - $currentPage) > 1) {
            $pagination .= '<a><div class="node">...</div></a>';
        }
        $pagination .= '<a href="' . $link . $maxPage - 1 . $qOrder . $qDir . '"><div class="node">' . $maxPage - 1 . '</div></a>';
        $pagination .= '<a href="' . $link . $maxPage . $qOrder . $qDir . '"><div class="node">' . $maxPage . '</div></a>';
        $pagination .= '<a href="' . $link . $currentPage + 1 . $qOrder . $qDir . '"><div class="node"><i class="icon-fi-rr-arrow-right"></i></div></a>';
        return $pagination;
    }

    /**
     * Получаем пагинацию для списка в виджете
     * @param int $currentPage Текущая страница, на которой находимся
     * @param int $maxPage Максимальное количество страниц
     * @return string
     */
    public static function widgetPagination(int $currentPage = 0, int $maxPage = 0): string
    {
        $pagination = '';
        if ($maxPage <= 1) {
            return $pagination;
        }
        // Если страниц немного, то можно вывести их все
        if ($maxPage < 6) {
            for ($i = 1; $i <= $maxPage; $i++) {
                $pagination .= '<div class="node';
                if ($currentPage == $i) {
                    $pagination .= ' current-page ';
                }
                $pagination .= '" data-page="' . $i . '" onclick="paginateList(this)">' . $i . '</div>';
            }
            return $pagination;
        }

        // Страница первая или если не указана
        if ($currentPage <= 1) {
            $pagination .= '<div class="node current-page">1</div>';
            $pagination .= '<div class="node" data-page="2" onclick="paginateList(this)">2</div>';
            $pagination .= '<div class="node">...</div>';
            $pagination .= '<div class="node" data-page="' . $maxPage - 1 . '" onclick="paginateList(this);">' . $maxPage - 1 . '</div>';
            $pagination .= '<div class="node" data-page="' . $maxPage . '" onclick="paginateList(this);">' . $maxPage . '</div>';
            $pagination .= '<div class="node" data-page="2" onclick="paginateList(this)"><i class="icon-fi-rr-arrow-right"></i></div>';
            return $pagination;
        }

        // Страница вторая
        $pagination .= '<div class="node" data-page="' . $currentPage - 1 . '" onclick="paginateList(this)"><i class="icon-fi-rr-arrow-left"></i></div>';
        $pagination .= '<div class="node" data-page="1" onclick="paginateList(this)">1</div></a>';

        if ($currentPage == 2) {
            $pagination .= '<div class="node current-page" data-page="2" onclick="paginateList(this)">2</div>';
            $pagination .= '<div class="node">...</div>';
            $pagination .= '<div class="node" data-page="' . $maxPage - 1 . '" onclick="paginateList(this);">' . $maxPage - 1 . '</div>';
            $pagination .= '<div class="node" data-page="' . $maxPage . '" onclick="paginateList(this);">' . $maxPage . '</div>';
            $pagination .= '<div class="node" data-page="' . $currentPage + 1 . '" onclick="paginateList(this);"><i class="icon-fi-rr-arrow-right"></i></div>';
            return $pagination;
        }

        // Страница последняя
        if ($currentPage == $maxPage) {
            $pagination .= '<div class="node">...</div>';
            $pagination .= '<div class="node" data-page="' . $maxPage - 1 . '" onclick="paginateList(this);">' . $maxPage - 1 . '</div>';
            $pagination .= '<div class="node current-page">' . $maxPage . '</div>';
            return $pagination;
        }

        // Страница предпоследняя
        if ($currentPage == $maxPage - 1) {
            $pagination .= '<div class="node">...</div>';
            $pagination .= '<div class="node current-page">' . $currentPage . '</div>';
            $pagination .= '<div class="node" data-page="' . $maxPage . '" onclick="paginateList(this);">' . $maxPage . '</div>';
            $pagination .= '<div class="node" data-page="' . $maxPage . '" onclick="paginateList(this);"><i class="icon-fi-rr-arrow-right"></i></div>';
            return $pagination;
        }
        // Страница любая другая
        $pagination .= '<div class="node" data-page="2" onclick="paginateList(this);">2</div>';
        if (($currentPage - 2) > 1) {
            $pagination .= '<div class="node">...</div>';
        }
        $pagination .= '<div class="node current-page">' . $currentPage . '</div>';
        if (($maxPage - $currentPage) > 1) {
            $pagination .= '<div class="node">...</div>';
        }
        $pagination .= '<div class="node" data-page="' . $maxPage - 1 . '" onclick="paginateList(this);">' . $maxPage - 1 . '</div>';
        $pagination .= '<div class="node" data-page="' . $maxPage . '" onclick="paginateList(this);">' . $maxPage . '</div>';
        $pagination .= '<div class="node" data-page="' . $currentPage + 1 . '" onclick="paginateList(this);"><i class="icon-fi-rr-arrow-right"></i></div>';
        return $pagination;
    }

}
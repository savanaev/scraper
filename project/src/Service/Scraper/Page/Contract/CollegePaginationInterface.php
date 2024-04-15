<?php

namespace App\Service\Scraper\Page\Contract;

interface CollegePaginationInterface
{
    /**
     * Общее количество страниц.
     */
    public function getTotalPages(): int;

    /**
     * Общее количество колледжей.
     */
    public function getTotalColleges(): int;

    /**
     * Количество колледжей на последней странице.
     */
    public function getTotalCollegesOnLastPage(): int;

    /**
     * Список ссылок на страницы.
     */
    public function getUrlList(): array;

    /**
     * Получение страницы по урлу.
     */
    public function scrape(string $url): void;

    /**
     * Инициализация пагинации.
     *
     * @param string $baseUrl Урл основной страницы
     */
    public function initiatePagination(string $baseUrl): self;
}

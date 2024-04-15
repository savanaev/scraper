<?php

namespace App\Service\Scraper\Common;

/**
 * Интерфейс для работы со скраперами.
 */
interface CommonScraperInterface
{
    /**
     * Запуск скрапера.
     *
     * @param array $collegeUrls список страниц для получения контента
     */
    public function run(array $collegeUrls): \Generator;
}

<?php

namespace App\Service\Scraper\Page;

use App\Service\Scraper\Page\Contract\CollegePaginationInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Класс для пагинации по колледжам.
 */
class CollegePagination implements CollegePaginationInterface
{
    /**
     * @var int количество колледжей на странице
     */
    private const COLLEGES_PER_PAGE = 25;

    /**
     * @var int общее количество страниц
     */
    private int $totalPages = 0;

    /**
     * @var int общее количество колледжей
     */
    private int $totalColleges = 0;

    /**
     * @var int количество колледжей на последней странице
     */
    private int $totalCollegesOnLastPage = 0;

    /**
     * @var Crawler объект для работы с контентом последней страницы
     */
    private Crawler $lastPage;

    /**
     * @var array массив ссылок на страницы со списками колледжей
     */
    private array $urlList = [];

    /**
     * @param HttpClientInterface $httpClient объект для работы с HTTP клиентом
     * @param Crawler             $crawler    объект для работы с контентом страницы
     */
    public function __construct(
        private HttpClientInterface $httpClient,
        private Crawler $crawler
    ) {
    }

    /**
     * Иниуиализация.
     *
     * @param string $baseUrl базовый урл страницы
     *
     * @return $this
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function initiatePagination(string $baseUrl): self
    {
        if (empty($baseUrl)) {
            throw new \InvalidArgumentException('Не указан базовый урл страницы');
        }

        $this->scrape($baseUrl);
        $this->parseTotalPages();
        $this->setLastPage($baseUrl);
        $this->setTotalCollegesOnLastPage();
        $this->setTotalColleges();
        $this->setUrlList($baseUrl);

        return $this;
    }

    /**
     * Скрапинг.
     *
     * @param string $url урл страницы
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function scrape(string $url): void
    {
        $htmlContent = $this->httpClient->request('GET', $url)->getContent();
        $this->addContent($htmlContent);
    }

    /**
     * Добавление контента страницы для последующе обработки.
     *
     * @param string $htmlContent HTML контент
     */
    private function addContent(string $htmlContent): void
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($htmlContent);
    }

    /**
     * Общее количество страниц.
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * Расчет общего количества страниц.
     */
    private function parseTotalPages(): void
    {
        $pageInput = $this->crawler->filter('input#Page');
        $nextDiv = $pageInput->nextAll()->filter('div')->first();
        preg_match('/Page \d+ of (\d+)/', $nextDiv->text(), $matches);

        $this->totalPages = (int) $matches[1];
    }

    /**
     * Количество колледжей на последней странице.
     */
    public function getTotalCollegesOnLastPage(): int
    {
        return $this->totalCollegesOnLastPage;
    }

    /**
     * Расчет количества колледжей на последней странице.
     */
    private function setTotalCollegesOnLastPage(): void
    {
        $lastPage = $this->getLastPage();
        $this->totalCollegesOnLastPage = $lastPage->filter('.row.vertical-padding')->count();
    }

    /**
     * Общее количество колледжей.
     */
    public function getTotalColleges(): int
    {
        return $this->totalColleges;
    }

    /**
     * Расчет общего количества колледжей.
     */
    private function setTotalColleges(): void
    {
        $this->totalColleges = (self::COLLEGES_PER_PAGE * ($this->totalPages - 1)) + $this->totalCollegesOnLastPage;
    }

    /**
     * Формирование списка ссылок на страницы с колледжами.
     */
    public function getUrlList(): array
    {
        return $this->urlList;
    }

    /**
     * Формирование списка ссылок на страницы с колледжами.
     *
     * @param string $baseUrl Ссылка на страницу с колледжами
     */
    private function setUrlList(string $baseUrl): void
    {
        for ($page = 1; $page <= $this->totalPages; ++$page) {
            $this->urlList[] = $this->createUrl($baseUrl, $page);
        }
    }

    /**
     * Формирование последней страницы с колледжами.
     *
     * @param string $baseUrl Ссылка на страницу с колледжами
     */
    private function urlOnLastPage(string $baseUrl): string
    {
        return $this->createUrl($baseUrl, $this->totalPages);
    }

    /**
     * Создание ссылки на страницу с колледжами.
     *
     * @param string $baseUrl Ссылка на страницу с колледжами
     * @param int    $page    Номер страницы
     */
    private function createUrl(string $baseUrl, int $page): string
    {
        return "{$baseUrl}&page={$page}";
    }

    /**
     * Установка объекта для работы с контентом последней страницы.
     */
    private function getLastPage(): Crawler
    {
        return $this->lastPage;
    }

    /**
     * Установка объекта для работы с контентом последней страницы.
     *
     * @param string $url базовый урл страницы
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function setLastPage(string $url): void
    {
        $urlOnLastPage = $this->urlOnLastPage($url);
        $this->scrape($urlOnLastPage);
        $this->lastPage = $this->crawler->last();
    }
}

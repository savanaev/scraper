<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Persistence\Service;

use App\Colleges\Domain\Entity\CollegeList;
use App\Colleges\Domain\Service\CollegeListScraperServiceInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CollegeListScraperService implements CollegeListScraperServiceInterface
{
    /**
     * @var string Домен сайта.
     */
    private const DOMAIN = 'https://www.princetonreview.com';

    /**
     * @var string Страница со списком колледжей.
     */
    private const SEARCH_PAGE = self::DOMAIN . '/college-search?ceid=cp-1022984';

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(protected HttpClientInterface $httpClient)
    {}

    /**
     * Получение информации о колледжах.
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function scrapeCollegesList(): array
    {
        $response = $this->httpClient->request('GET', self::SEARCH_PAGE);
        $content = $response->getContent();
        $colleges = $this->parseColleges($content);

        return $colleges;
    }

    /**
     * Парсинг информации о колледжах.
     *
     * @param string $content HTML код страницы.
     * @return array
     */
    private function parseColleges(string $content): array
    {
        $colleges = [];

        $crawler = new Crawler($content);

        $crawler->filter('.row.vertical-padding')->each(function (Crawler $node) use (&$colleges) {
            $imageUrl = $this->getCollegeImage($node);
            $name = $this->getCollegeName($node);
            [$city, $state] = $this->getCollegeLocation($node);
            $url = $this->getCollegePageUrl($node);

            $college = new CollegeList();
            $college->setImageUrl($imageUrl);
            $college->setName($name);
            $college->setCity($city);
            $college->setState($state);
            $college->setUrl($url);

            $colleges[] = $college;
        });

        return $colleges;
    }

    /**
     * Получение изображения колледжа.
     *
     * @param Crawler $node Элемент дерева DOM для обработки.
     * @return string|null
     */
    private function getCollegeImage(Crawler $node): ?string
    {
        $imageUrl = null;
        if ($node->filter('.school-image')->count() > 0) {
            $imageUrl = $node->filter('.school-image')->attr('src');
        } elseif ($node->filter('.school-image-large')->count() > 0) {
            $imageUrl = $node->filter('.school-image-large')->attr('src');
        }

        return $imageUrl;
    }

    /**
     * Получение названия колледжа.
     *
     * @param Crawler $node Элемент дерева DOM для обработки.
     * @return string|null
     */
    private function getCollegeName(Crawler $node): ?string
    {
        $name = null;
        $node = $node->filter('.margin-top-none a');
        if ($node->count() > 0) {
            $name = $node->text();
        }

        return $name;
    }

    /**
     * Получение локации колледжа.
     *
     * @param Crawler $node Элемент дерева DOM для обработки.
     * @return array
     */
    private function getCollegeLocation(Crawler $node): array
    {
        $location = [];
        $node = $node->filter('.location');
        if ($node->count() > 0) {
            $location = explode(', ', $node->text());
        }
        $city = $location[0] ?? null;
        $state = $location[1] ?? null;

        return [$city, $state];
    }

    /**
     * Получение ссылки на страницу колледжа.
     *
     * @param Crawler $node Элемент дерева DOM для обработки.
     * @return string
     */
    private function getCollegePageUrl(Crawler $node): string
    {
        $pageUrl = self::DOMAIN;
        $node = $node->filter('h2 a');
        if ($node->count() > 0) {
            $pageUrl .= $node->attr('href');
        }

        return $pageUrl;
    }
}

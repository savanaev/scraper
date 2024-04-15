<?php

namespace App\Service\Scraper\Page;

use App\DTO\CollegeListDTO;
use App\Service\Scraper\Page\Contract\CollegeListInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Скрапинг данных со страницы списка колледжей.
 */
class Colleges implements CollegeListInterface
{
    /**
     * @var string домен сайта
     */
    private const DOMAIN = 'https://www.princetonreview.com';

    /**
     * @var array массив с информацией о колледжах
     */
    private array $colleges = [];

    public function __construct(private Crawler $crawler)
    {
    }

    /**
     * Скрапинг колледжей со страницы.
     *
     * @param string $content HTML код страницы
     *
     * @return $this
     */
    public function scrape(string $content): self
    {
        $this->addContent($content);
        $this->crawler->filter('.row.vertical-padding')->each(function (Crawler $node) {
            $imageUrl = $this->parseImage($node);
            $name = $this->parseName($node);
            [$city, $state] = $this->parseLocation($node);
            $url = $this->parsePageUrl($node);

            $this->colleges[] = new CollegeListDTO(
                $imageUrl,
                $name,
                $city,
                $state,
                $url
            );
        });

        return $this;
    }

    /**
     * Добавление страницы для последующего ее разбора.
     *
     * @param string $content HTML код страницы
     */
    private function addContent(string $content): void
    {
        $this->clear();
        $this->crawler->addHtmlContent($content);
    }

    /**
     * Очистка данных о колледжах.
     */
    private function clear(): void
    {
        $this->colleges = [];
        $this->crawler->clear();
    }

    /**
     * Массив с информацией о колледжах.
     */
    public function getDetails(): array
    {
        return $this->colleges;
    }

    /**
     * Получение изображения колледжа.
     *
     * @param Crawler $node элемент дерева DOM для обработки
     */
    private function parseImage(Crawler $node): ?string
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
     * @param Crawler $node элемент дерева DOM для обработки
     */
    private function parseName(Crawler $node): ?string
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
     * @param Crawler $node элемент дерева DOM для обработки
     */
    private function parseLocation(Crawler $node): array
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
     * @param Crawler $node элемент дерева DOM для обработки
     */
    private function parsePageUrl(Crawler $node): string
    {
        $pageUrl = self::DOMAIN;
        $node = $node->filter('h2 a');
        if ($node->count() > 0) {
            $pageUrl .= $node->attr('href');
        }

        return $pageUrl;
    }
}

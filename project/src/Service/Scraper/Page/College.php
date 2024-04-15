<?php

namespace App\Service\Scraper\Page;

use App\DTO\CollegeDetailsDTO;
use App\Service\Scraper\Page\Contract\CollegeDetailsInterface;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Скрапинг деталей колледжа.
 */
class College implements CollegeDetailsInterface
{
    /**
     * @var string|null Название колледжа
     */
    private ?string $name;

    /**
     * @var string|null Адрес колледжа
     */
    private ?string $address;

    /**
     * @var string|null Телефон колледжа
     */
    private ?string $phone;

    /**
     * @var string|null Сайт колледжа
     */
    private ?string $website;

    /**
     * @param Crawler $crawler Скрапер
     */
    public function __construct(private readonly Crawler $crawler)
    {
    }

    /**
     * Скрапинг.
     *
     * @param string $content HTML страницы колледжа
     */
    public function scrape(string $content): self
    {
        $this->addContent($content);

        $this->name = $this->parseName();
        $this->address = $this->parseAddress();
        $this->phone = $this->parsePhone();
        $this->website = $this->parseWebsite();

        return $this;
    }

    /**
     * Добавление контента.
     *
     * @param string $content HTML страницы колледжа
     */
    private function addContent(string $content): void
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($content);
    }

    /**
     * Информация о колледже.
     */
    public function getDetails(): CollegeDetailsDTO
    {
        return new CollegeDetailsDTO(
            $this->name,
            $this->address,
            $this->phone,
            $this->website
        );
    }

    /**
     * Получение названия колледжа.
     */
    private function parseName(): ?string
    {
        $name = null;
        $node = $this->crawler->filter('h1.school-headline [itemprop="name"]');
        if ($node->count() > 0) {
            $name = $node->text();
        }

        return $name;
    }

    /**
     * Получение адреса колледжа.
     */
    private function parseAddress(): ?string
    {
        $address = null;
        $node = $this->crawler->filter('.school-contacts .col-xs-6:contains("Address")');
        if ($node->count() > 0) {
            $address = $node->nextAll()->text();
        } elseif ($this->crawler->filterXPath('//div[@itemprop="address"]')->count() > 0) {
            $address = $this->crawler->filterXPath('//div[@itemprop="address"]/span[@itemprop="streetAddress"]')->text();
            $address .= ', '.$this->crawler->filterXPath('//div[@itemprop="address"]/span[@itemprop="addressLocality"]')->text();
            $address .= ', '.$this->crawler->filterXPath('//div[@itemprop="address"]/span[@itemprop="addressRegion"]')->text();
            $postalCode = $this->crawler->filterXPath('//div[@itemprop="address"]/span[@itemprop="postalCode"]')->text();
            $address .= ' '.$postalCode;
        }

        return $address;
    }

    /**
     * Получение телефона колледжа.
     */
    private function parsePhone(): ?string
    {
        $phone = null;
        $node = $this->crawler->filter('.school-contacts .col-xs-6:contains("Phone")');
        if ($node->count() > 0) {
            $phone = $node->nextAll()->text();
        }

        return $phone;
    }

    /**
     * Получение сайта колледжа.
     */
    private function parseWebsite(): ?string
    {
        $website = null;
        $node = $this->crawler->filter('.school-headline-address [itemprop="url"]');
        if ($node->count() > 0) {
            $website = $node->attr('href');
        }

        return $website;
    }
}

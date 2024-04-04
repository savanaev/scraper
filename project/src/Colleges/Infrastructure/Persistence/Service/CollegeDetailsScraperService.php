<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Persistence\Service;

use App\Colleges\Domain\Entity\CollegeDetails;
use App\Colleges\Domain\Entity\CollegeList;
use App\Colleges\Domain\Service\CollegeDetailsScraperServiceInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CollegeDetailsScraperService implements CollegeDetailsScraperServiceInterface
{
    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(protected HttpClientInterface $httpClient)
    {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function scrapeCollegeDetails(CollegeList $college): ?CollegeDetails
    {
        $url = $college->getUrl();
        $response = $this->httpClient->request('GET', $url);
        $content = $response->getContent();
        $collegeDetails = $this->parseCollegeDetails($content);

        return $collegeDetails;
    }

    /**
     * Парсинг информации о колледже.
     *
     * @param string $content HTML код страницы.
     * @return CollegeDetails
     */
    private function parseCollegeDetails(string $content): CollegeDetails
    {
        $crawler = new Crawler($content);

        $name = $this->getCollegeDetailsName($crawler);
        $address = $this->getCollegeDetailsAddress($crawler);
        $phone = $this->getCollegeDetailsPhone($crawler);
        $website = $this->getCollegeDetailsWebsite($crawler);

        $collegeDetails = new CollegeDetails();
        $collegeDetails->setName($name);
        $collegeDetails->setAddress($address);
        $collegeDetails->setPhone($phone);
        $collegeDetails->setWebsite($website);

        return $collegeDetails;
    }

    /**
     * Получение названия колледжа.
     *
     * @param Crawler $crawler Дерево DOM для обработки.
     * @return string|null
     */
    private function getCollegeDetailsName(Crawler $crawler): ?string
    {
        $name = null;
        $node = $crawler->filter('h1.school-headline [itemprop="name"]');
        if ($node->count() > 0) {
            $name = $node->text();
        }

        return $name;
    }

    /**
     * Получение адреса колледжа.
     *
     * @param Crawler $crawler Дерево DOM для обработки.
     * @return string|null
     */
    private function getCollegeDetailsAddress(Crawler $crawler): ?string
    {
        $address = null;
        $node = $crawler->filter('.school-contacts .col-xs-6:contains("Address")');
        if ($node->count() > 0) {
            $address = $node->nextAll()->text();
        } elseif ($crawler->filterXPath('//div[@itemprop="address"]')->count() > 0) {
            $address = $crawler->filterXPath('//div[@itemprop="address"]/span[@itemprop="streetAddress"]')->text();
            $address .= ', ' . $crawler->filterXPath('//div[@itemprop="address"]/span[@itemprop="addressLocality"]')->text();
            $address .= ', ' . $crawler->filterXPath('//div[@itemprop="address"]/span[@itemprop="addressRegion"]')->text();
            $postalCode = $crawler->filterXPath('//div[@itemprop="address"]/span[@itemprop="postalCode"]')->text();
            $address .= ' ' . $postalCode;
        }

        return $address;
    }

    /**
     * Получение телефона колледжа.
     *
     * @param Crawler $crawler Дерево DOM для обработки.
     * @return string|null
     */
    private function getCollegeDetailsPhone(Crawler $crawler): ?string
    {
        $phone = null;
        $node = $crawler->filter('.school-contacts .col-xs-6:contains("Phone")');
        if ($node->count() > 0) {
            $phone = $node->nextAll()->text();
        }

        return $phone;
    }

    /**
     * Получение сайта колледжа.
     *
     * @param Crawler $crawler Дерево DOM для обработки.
     * @return string|null
     */
    private function getCollegeDetailsWebsite(Crawler $crawler): ?string
    {
        $website = null;
        $node = $crawler->filter('.school-headline-address [itemprop="url"]');
        if ($node->count() > 0) {
            $website = $node->attr('href');
        }

        return $website;
    }
}

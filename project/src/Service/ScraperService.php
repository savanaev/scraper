<?php

namespace App\Service;

use App\Entity\CollegeList;
use App\Event\CollegeListScrapedEvent;
use App\Service\Scraper\Page\Contract\CollegeDetailsInterface;
use App\Service\Scraper\Page\Contract\CollegePaginationInterface;
use App\Service\Scraper\Scraper;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Сервис для сбора данных о колледжах.
 */
class ScraperService
{
    public function __construct(
        private readonly Scraper $scraper,
        private readonly CollegePaginationInterface $pagination,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CollegeDetailsInterface $collegeDetails,
        private readonly CollegeDetailsService $collegeDetailsService,
        private readonly CollegesStateService $collegesStateService
    ) {
    }

    /**
     * Сбор данных о колледжах.
     *
     * @throws InvalidArgumentException
     */
    public function collegeList(): bool
    {
        $collegeUrls = $this->getPageUrls();
        if (!empty($collegeUrls)) {
            $generator = $this->scraper->scrape($collegeUrls);

            foreach ($generator as $collegeUrl => $contentList) {
                $collegePageScrapedEvent = new CollegeListScrapedEvent([$collegeUrl => $contentList]);
                $this->eventDispatcher->dispatch($collegePageScrapedEvent, 'app.college_list_scraped');
            }

            $this->collegesStateService->removeDeletedColleges();
            $this->collegesStateService->updateCollegesState();

            return true;
        }

        return false;
    }

    /**
     * Сбор данных о колледжах в деталях.
     *
     * @param array $collegeList массив с колледжами
     */
    public function collegeDetails(array $collegeList): void
    {
        $collectCollegesDetails = $this->loadDetails($collegeList);
        $this->collegeDetailsService->add($collectCollegesDetails, $collegeList);
    }

    /**
     * Инициализация пагинатора.
     */
    private function paginator(): CollegePaginationInterface
    {
        return $this->pagination->initiatePagination($_ENV['COLLEGE_LIST_URL']);
    }

    /**
     * Массив ссылок на страницы с колледжами.
     */
    private function getPageUrls(): array
    {
        return $this->paginator()->getUrlList();
    }

    /**
     * Получение массива ссылок на колледжи из массива колледжей.
     *
     * @param array $collegeList массив с колледжами
     */
    private function getUrlsFromCollegeList(array $collegeList): array
    {
        $urls = [];
        /** @var CollegeList $college */
        foreach ($collegeList as $college) {
            if (empty($college->getUrl())) {
                continue;
            }

            $urls[] = $college->getUrl();
        }

        return $urls;
    }

    /**
     * Загрузка деталей колледжей.
     *
     * @param array $collegeList массив с колледжами
     */
    private function loadDetails(array $collegeList): array
    {
        $urls = $this->getUrlsFromCollegeList($collegeList);
        $collegesDetailsContentIterator = $this->scraper->scrape($urls);

        return $this->getCollectCollegesDetails($collegesDetailsContentIterator);
    }

    /**
     * Сбор данных о колледжах в деталях.
     *
     * @param iterable $collegesDetailsContentIterator итератор с контентом деталей колледжей
     */
    private function getCollectCollegesDetails(iterable $collegesDetailsContentIterator): array
    {
        $collectCollegesDetails = [];

        foreach ($collegesDetailsContentIterator as $collegeUrl => $content) {
            if (is_string($content)) {
                $collegeDetailsDTO = $this->collegeDetails->scrape($content)->getDetails();
                $collectCollegesDetails[$collegeUrl] = $collegeDetailsDTO;
            }
        }

        return $collectCollegesDetails;
    }
}

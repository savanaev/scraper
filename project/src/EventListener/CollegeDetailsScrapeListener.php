<?php

namespace App\EventListener;

use App\Event\CollegeDetailsScrapedEvent;
use App\Service\ScraperService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CollegeDetailsScrapeListener
{
    public function __construct(private ScraperService $scraperService)
    {
    }

    /**
     * Слушатель события о том что часть колледжей из списка обработана, и теперь
     * можно получить детали об этих колледжах
     *
     * @param CollegeDetailsScrapedEvent $event
     * @return void
     */
    #[AsEventListener(event: 'app.college_list_added')]
    public function onAppCollegeListAdded(CollegeDetailsScrapedEvent $event): void
    {
        $collegeList = $event->getContentList();
        $this->scraperService->collegeDetails($collegeList);
    }
}

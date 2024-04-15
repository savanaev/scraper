<?php

namespace App\EventListener;

use App\Event\CollegeListScrapedEvent;
use App\Service\CollegeListService;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CollegePageScrapedListener
{
    public function __construct(private CollegeListService $collegeListService)
    {
    }

    /**
     * Слушатель события отработки списка колледжей
     *
     * @param CollegeListScrapedEvent $event
     * @return void
     */
    #[AsEventListener(event: 'app.college_list_scraped')]
    public function onAppCollegeListScraped(CollegeListScrapedEvent $event): void
    {
        $contentList = $event->getContentList();
        $this->collegeListService->add($contentList);
    }
}

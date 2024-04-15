<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CollegeDetailsScrapedEvent extends Event
{
    public function __construct(private array $contentList)
    {
    }

    public function getContentList(): array
    {
        return $this->contentList;
    }
}

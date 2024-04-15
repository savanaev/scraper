<?php

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class CollegeListScrapedEvent extends Event
{
    public function __construct(private readonly array $contentList)
    {
    }

    public function getContentList(): array
    {
        return $this->contentList;
    }
}

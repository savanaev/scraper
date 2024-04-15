<?php

namespace App\Service;

use App\DTO\CollegeListDTO;
use App\Entity\CollegeList;
use App\Event\CollegeDetailsScrapedEvent;
use App\Repository\CollegeListRepository;
use App\Service\Scraper\Page\Contract\CollegeListInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Заполнение информации из списка колледжей.
 */
class CollegeListService
{
    public function __construct(
        private CollegeListRepository $collegeListRepository,
        private CollegeListInterface $colleges,
        private EventDispatcherInterface $eventDispatcher,
        private CollegesStateService $collegesStateService
    ) {
    }

    /**
     * Заполняет данные колледжей.
     *
     * @param array $contentList Список контента
     */
    public function add(array $contentList): void
    {
        $details = $this->loadDetails($contentList);
        $details = $this->removeDuplicates($details);

        if (!empty($details)) {
            $colleges = $this->fillEntity($details);
            $this->collegeListRepository->batchAdd($colleges);

            $this->sendEvent($colleges);
        }
    }

    /**
     * Загружает детали колледжей.
     *
     * @param array $contentList Список контента
     */
    private function loadDetails(array $contentList): array
    {
        foreach ($contentList as $content) {
            $this->colleges->scrape($content);
        }

        return $this->colleges->getDetails();
    }

    /**
     * Удаляет дубликаты из списка колледжей.
     *
     * @param CollegeListDTO[] $colleges Список колледжей
     */
    private function removeDuplicates(array $colleges): array
    {
        $uniqueColleges = [];

        if (!empty($colleges)) {
            foreach ($colleges as $college) {
                $hasCollege = $this->collegesStateService->hasCollegeByUrl($college->getUrl());
                if (false === $hasCollege) {
                    $this->collegesStateService->addCollege($college->getUrl());
                    $uniqueColleges[] = $college;
                } else {
                    $this->collegesStateService->removeCollegeByUrl($college->getUrl());
                }
            }
        }

        return $uniqueColleges;
    }

    /**
     * Отправляет событие об обновлении колледжей.
     *
     * @param array $colleges Список колледжей
     */
    private function sendEvent(array $colleges): void
    {
        $collegePageScrapedEvent = new CollegeDetailsScrapedEvent($colleges);
        $this->eventDispatcher->dispatch($collegePageScrapedEvent, 'app.college_list_added');
    }

    /**
     * Заполняет колледжи данными из DTO.
     *
     * @param CollegeListDTO[] $details Список колледжей
     */
    private function fillEntity(array $details): array
    {
        $colleges = [];
        foreach ($details as $collegeListDTO) {
            $college = new CollegeList();
            $college->setImageUrl($collegeListDTO->getImageUrl());
            $college->setName($collegeListDTO->getName());
            $college->setCity($collegeListDTO->getCity());
            $college->setState($collegeListDTO->getState());
            $college->setUrl($collegeListDTO->getUrl());
            $college->setCreatedAt();
            $colleges[$collegeListDTO->getUrl()] = $college;
        }

        return $colleges;
    }

    /**
     * Получает список колледжей с учетом пагинации.
     *
     * @param int $page     Номер страницы
     * @param int $pageSize Колличество колледжей на странице
     *
     * @return array Список колледжей на текущей странице
     */
    public function getColleges(int $page, int $pageSize): array
    {
        $offset = ($page - 1) * $pageSize;

        return $this->collegeListRepository->findBy([], null, $pageSize, $offset);
    }

    /**
     * Выполняется полное очищение таблицы.
     *
     * @throws InvalidArgumentException
     */
    public function clear(): bool
    {
        $this->collegeListRepository->clearTable();
        $this->collegesStateService->clear();

        return true;
    }
}

<?php

namespace App\Service;

use App\Repository\CollegeListRepository;
use App\Service\Cache\CacheService;
use App\Service\Hash\HashFactoryInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Сервис для работы с состоянием колледжей.
 */
class CollegesStateService
{
    /**
     * @var string ключ кэша для хранения текущих данных о списке колледжей
     */
    private const CACHE_STATE_KEY = 'state_colleges_list';

    /**
     * @var array текущие данные о списке колледжей
     */
    private array $currentState;

    /**
     * @var array предыдущие данные о списке колледжей
     */
    private array $previousState;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        private CollegeListRepository $collegeListRepository,
        private CacheService $cacheService,
        private HashFactoryInterface $hashFactory
    ) {
        $this->currentState = [];
        $this->setPreviousState();
    }

    /**
     * Добавление колледжа в список, если его еще нет в списке.
     *
     * @param string $collegeUrl url колледжа
     */
    public function addCollege(string $collegeUrl): bool
    {
        $isAdded = false;
        if (!in_array($collegeUrl, $this->currentState)) {
            $hash = $this->hashFactory->create($collegeUrl);
            $this->currentState[$hash] = $collegeUrl;

            $isAdded = true;
        }

        return $isAdded;
    }

    /**
     * Проверка наличия колледжа в списке.
     *
     * @param string $collegeUrl url колледжа
     */
    public function hasCollegeByUrl(string $collegeUrl): bool
    {
        if (empty($this->previousState)) {
            return null !== $this->collegeListRepository->findByUrl($collegeUrl);
        }

        return in_array($collegeUrl, $this->previousState);
    }

    /**
     * Удаление колледжа из списка.
     *
     * @param string $collegeUrl url колледжа
     */
    public function removeCollegeByUrl(string $collegeUrl): void
    {
        $hash = $this->hashFactory->create($collegeUrl);
        unset($this->previousState[$hash]);
    }

    /**
     * Устанавливает предыдущие данные о списке колледжей.
     *
     * @throws InvalidArgumentException
     */
    private function setPreviousState(): void
    {
        $this->previousState = $this->cacheService->get(self::CACHE_STATE_KEY) ?: [];
    }

    /**
     * Удаление колледжей оставшихся в данном списке, так как в каталоге они пропали.
     */
    public function removeDeletedColleges(): void
    {
        if (!empty($this->previousState)) {
            $this->collegeListRepository->deleteByUrls($this->previousState);
        }
    }

    /**
     * Обновление данных о списке колледжей в кэше.
     *
     * @throws InvalidArgumentException
     */
    public function updateCollegesState(): void
    {
        $this->cacheService->delete(self::CACHE_STATE_KEY);
        $this->cacheService->set(self::CACHE_STATE_KEY, $this->currentState);
    }

    /**
     * Очистка кэша данных о списке колледжей.
     *
     * @throws InvalidArgumentException
     */
    public function clear(): void
    {
        $this->cacheService->delete(self::CACHE_STATE_KEY);
    }
}

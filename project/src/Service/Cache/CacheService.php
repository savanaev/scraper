<?php

namespace App\Service\Cache;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CacheService implements CacheServiceInterface
{
    public function __construct(private AbstractAdapter $cache)
    {
    }

    /**
     * Получение данных из кэша по ключу.
     *
     * @throws InvalidArgumentException
     */
    public function get(string $key): mixed
    {
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        return null;
    }

    /**
     * Запись данных в кэш.
     *
     * @param string   $key   ключ
     * @param mixed    $value данные для записи в кэш
     * @param int|null $ttl   время жизни кэша в секундах
     *
     * @throws InvalidArgumentException
     */
    public function set(string $key, mixed $value, ?int $ttl = null): void
    {
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($value);
        if (null !== $ttl) {
            $cacheItem->expiresAfter($ttl);
        }
        $this->cache->save($cacheItem);
    }

    /**
     * Удаление данных из кэша по ключу.
     *
     * @param string $key ключ
     *
     * @throws InvalidArgumentException
     */
    public function delete(string $key): void
    {
        $this->cache->deleteItem($key);
    }

    /**
     * Очистка кэша.
     */
    public function clear(): void
    {
        $this->cache->clear();
    }

    /**
     * Сохранение данных в кэш.
     *
     * @param ItemInterface $item Элемент кэша
     */
    public function save(ItemInterface $item): void
    {
        $this->cache->save($item);
    }
}

<?php

namespace App\Service\Cache;

use Symfony\Contracts\Cache\ItemInterface;

interface CacheServiceInterface
{
    /**
     * Получение данных из кэша.
     *
     * @param string $key Ключ кэша
     */
    public function get(string $key): mixed;

    /**
     * Добавление данных в кеш.
     *
     * @param string   $key   Ключ кеша
     * @param mixed    $value Данные кеша
     * @param int|null $ttl   Время жизни кеша (в секундах)
     */
    public function set(string $key, mixed $value, ?int $ttl = null): void;

    /**
     * Сохранение элемента кеша.
     *
     * @param ItemInterface $item Элемент кеша
     */
    public function save(ItemInterface $item): void;

    /**
     * Удаление кеша по ключу.
     *
     * @param string $key Ключ кеша
     */
    public function delete(string $key): void;

    /**
     * Очистка кеша.
     */
    public function clear(): void;
}

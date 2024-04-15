<?php

namespace App\Service\Hash;

abstract class AbstractHashFactory implements HashFactoryInterface
{
    /**
     * @var array Хранилище хешей
     */
    protected array $pool = [];

    /**
     * Создает хешированный ключ для заданного ключа.
     *
     * @param string $key Ключ для хеширования
     */
    public function create(string $key): string
    {
        if (!isset($this->pool[$key])) {
            $this->pool[$key] = $this->hash($key);
        }

        return $this->pool[$key];
    }

    /**
     * Хеширует ключ.
     */
    abstract protected function hash(string $key): string;
}

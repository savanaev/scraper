<?php

namespace App\Service\Hash;

/**
 * Интерфейс фабрики хеширования ключей.
 */
interface HashFactoryInterface
{
    /**
     * Создает хешированный ключ для заданного ключа.
     *
     * @param string $key Ключ для хеширования
     */
    public function create(string $key): string;
}

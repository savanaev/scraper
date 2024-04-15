<?php

namespace App\Service\Hash;

/**
 * Фабрика хеширования ключейпо алгоритму sha256.
 */
class Sha256HashFactory extends AbstractHashFactory
{
    /**
     * Хеширует ключ.
     */
    protected function hash(string $key): string
    {
        return hash('sha256', $key);
    }
}

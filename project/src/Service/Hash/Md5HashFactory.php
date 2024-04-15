<?php

namespace App\Service\Hash;

/**
 * Фабрика хеширования ключей по алгоритму md5.
 */
class Md5HashFactory extends AbstractHashFactory
{
    /**
     * Хеширует ключ.
     */
    protected function hash(string $key): string
    {
        return md5($key);
    }
}

<?php

namespace App\DTO;

/**
 * Информации о деталях колледже.
 */
readonly class CollegeDetailsDTO
{
    public function __construct(
        private ?string $name = null,
        private ?string $address = null,
        private ?string $phone = null,
        private ?string $website = null
    ) {
    }

    /**
     * Название колледжа.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Адрес колледжа.
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * Телефон колледжа.
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Сайт колледжа.
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }
}

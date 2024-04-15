<?php

namespace App\DTO;

/**
 * Данные о колледже в каталоге.
 */
readonly class CollegeListDTO
{
    public function __construct(
        private ?string $imageUrl,
        private ?string $name,
        private ?string $city,
        private ?string $state,
        private ?string $url
    ) {
    }

    /**
     * Url на изображение колледжа.
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * Название колледжа.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Город колледжа.
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * Штат колледжа.
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * Url на страницу колледжа в каталоге.
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}

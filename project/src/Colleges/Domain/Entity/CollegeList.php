<?php

declare(strict_types=1);

namespace App\Colleges\Domain\Entity;

use App\Colleges\Infrastructure\Persistence\Repository\CollegeListRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CollegeListRepository::class)]
class CollegeList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 100)]
    private ?string $city = null;

    #[ORM\Column(type: "string", length: 100)]
    private ?string $state = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $url = null;

    #[ORM\Column(type: "string", length: 40, unique: true)]
    private ?string $hash = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToOne(mappedBy: 'collegeList', cascade: ['persist', 'remove'])]
     private ?CollegeDetails $collegeDetails = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): static
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): void
    {
        $this->hash = $hash;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCollegeDetails(): ?CollegeDetails
    {
        return $this->collegeDetails;
    }

    public function setCollegeDetails(CollegeDetails $collegeDetails): static
    {

        // set the owning side of the relation if necessary
        if ($collegeDetails->getCollegeList() !== $this) {
            $collegeDetails->setCollegeList($this);
        }

        $this->collegeDetails = $collegeDetails;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Colleges\Domain\Entity;

use App\Colleges\Infrastructure\Persistence\Repository\CollegeDetailsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollegeDetailsRepository::class)]
class CollegeDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $address = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\OneToOne(inversedBy: 'collegeDetails', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'college_list_id', referencedColumnName: 'id', unique: true, nullable: true,  onDelete: 'CASCADE')]
    private ?CollegeList $collegeList = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getCollegeList(): ?CollegeList
    {
        return $this->collegeList;
    }

    public function setCollegeList(CollegeList $collegeList): static
    {
        $this->collegeList = $collegeList;

        return $this;
    }
}

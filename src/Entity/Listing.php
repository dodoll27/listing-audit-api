<?php

namespace App\Entity;

use App\Enum\PropertyType;
use App\Repository\ListingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks] 
#[ORM\Entity(repositoryClass: ListingRepository::class)]
class Listing
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[Assert\NotBlank]
    #[ORM\Column]
    #[Assert\Positive]
    private ?float $surface = null;

    #[ORM\Column(enumType: PropertyType::class)]
    private ?PropertyType $type = null;

    #[Assert\NotBlank]
    #[ORM\Column]
    #[Assert\Positive]
    private ?float $price = null;

    #[Assert\NotBlank]
    #[ORM\Column]
    #[Assert\PositiveOrZero] 
    private ?int $photosCount = null;

    #[ORM\Column(length: 5000, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, AuditReport>
     */
    #[ORM\OneToMany(targetEntity: AuditReport::class, mappedBy: 'listing', orphanRemoval: true)]
    private Collection $auditReports;

    public function __construct()
    {
        $this->auditReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(float $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function getType(): ?PropertyType
    {
        return $this->type;
    }

    public function setType(PropertyType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPhotosCount(): ?int
    {
        return $this->photosCount;
    }

    public function setPhotosCount(int $photosCount): static
    {
        $this->photosCount = $photosCount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    #[ORM\PrePersist] // 👈 add this method
    public function setCreatedAtOnPersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, AuditReport>
     */
    public function getAuditReports(): Collection
    {
        return $this->auditReports;
    }

    public function addAuditReport(AuditReport $auditReport): static
    {
        if (!$this->auditReports->contains($auditReport)) {
            $this->auditReports->add($auditReport);
            $auditReport->setListing($this);
        }

        return $this;
    }

    public function removeAuditReport(AuditReport $auditReport): static
    {
        if ($this->auditReports->removeElement($auditReport)) {
            // set the owning side to null (unless already changed)
            if ($auditReport->getListing() === $this) {
                $auditReport->setListing(null);
            }
        }

        return $this;
    }
}

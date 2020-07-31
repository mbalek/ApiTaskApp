<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $estimatedRevenue;

    /**
     * @ORM\Column(type="integer")
     */
    private $adImpressions;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=2)
     */
    private $adEcpm;

    /**
     * @ORM\Column(type="integer")
     */
    private $clicks;

    /**
     * @ORM\Column(type="decimal", precision=8, scale=1)
     */
    private $adCtr;

    /**
     * @ORM\ManyToOne(targetEntity=Setting::class, inversedBy="clients")
     * @ORM\JoinColumn(nullable=false)
     */
    private $setting;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="clients")
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getEstimatedRevenue(): ?float
    {
        return $this->estimatedRevenue;
    }

    public function setEstimatedRevenue(float $estimatedRevenue): self
    {
        $this->estimatedRevenue = $estimatedRevenue;

        return $this;
    }

    public function getAdImpressions(): ?int
    {
        return $this->adImpressions;
    }

    public function setAdImpressions(int $adImpressions): self
    {
        $this->adImpressions = $adImpressions;

        return $this;
    }

    public function getAdEcpm(): ?string
    {
        return $this->adEcpm;
    }

    public function setAdEcpm(string $adEcpm): self
    {
        $this->adEcpm = $adEcpm;

        return $this;
    }

    public function getClicks(): ?int
    {
        return $this->clicks;
    }

    public function setClicks(int $clicks): self
    {
        $this->clicks = $clicks;

        return $this;
    }

    public function getAdCtr(): ?string
    {
        return $this->adCtr;
    }

    public function setAdCtr(string $adCtr): self
    {
        $this->adCtr = $adCtr;

        return $this;
    }

    public function getSetting(): ?Setting
    {
        return $this->setting;
    }

    public function setSetting(?Setting $setting): self
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }
}

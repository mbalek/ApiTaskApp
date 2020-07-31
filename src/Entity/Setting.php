<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SettingRepository::class)
 */
class Setting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=3)
     * @Assert\NotBlank
     */
    private $currency;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     */
    private $periodLength;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $groupBy;

    /**
     * @ORM\OneToMany(targetEntity=Client::class, mappedBy="setting")
     */
    private $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPeriodLength(): ?int
    {
        return $this->periodLength;
    }

    public function setPeriodLength(int $periodLength): self
    {
        $this->periodLength = $periodLength;

        return $this;
    }

    public function getGroupBy(): ?string
    {
        return $this->groupBy;
    }

    public function setGroupBy(?string $groupBy): self
    {
        $this->groupBy = $groupBy;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
            $client->setSetting($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->contains($client)) {
            $this->clients->removeElement($client);
            // set the owning side to null (unless already changed)
            if ($client->getSetting() === $this) {
                $client->setSetting(null);
            }
        }

        return $this;
    }
}

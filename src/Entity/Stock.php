<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    public ?int $STOCK_SKU = null;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $STOCK_BRANCH = null;

    #[ORM\Column(nullable: true)]
    public ?int $STOCK_STOCK = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSTOCKSKU(): ?int
    {
        return $this->STOCK_SKU;
    }

    public function setSTOCKSKU(?int $STOCK_SKU): self
    {
        $this->STOCK_SKU = $STOCK_SKU;

        return $this;
    }

    public function getSTOCKBRANCH(): ?string
    {
        return $this->STOCK_BRANCH;
    }

    public function setSTOCKBRANCH(?string $STOCK_BRANCH): self
    {
        $this->STOCK_BRANCH = $STOCK_BRANCH;

        return $this;
    }

    public function getSTOCKSTOCK(): ?float
    {
        return $this->STOCK_STOCK;
    }

    public function setSTOCKSTOCK(?float $STOCK_STOCK): self
    {
        $this->STOCK_STOCK = $STOCK_STOCK;

        return $this;
    }
}

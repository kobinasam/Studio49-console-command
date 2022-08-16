<?php

namespace App\Message;

class SkuOutOfStockEmail {

    private $sku;
    public function __construct(int $sku)
    {
        $this->sku = $sku;
    }

    public function getSku(): int {
        return $this->sku;
    }
}
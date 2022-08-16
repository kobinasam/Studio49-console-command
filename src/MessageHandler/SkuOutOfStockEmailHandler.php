<?php 

namespace App\MessageHandler;

use App\Message\SkuOutOfStockEmail;
use Symfony\Component\Messager\Handler\MessageHandlerInterface;

class SkuOutOfStockEmailHandler implements MessageHandlerInterface {
    public function __invoke(SkuOutOfStockEmail $skuOutOfStockEmail)
    {
        echo "Sending an email now...";
    }
}



<?php 

namespace App\Command;

use App\Entity\Stock;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\VarDumper\Cloner\Stub;

class updatestockcommand extends Command {
    
    protected static $defaultName = 'app:update-stock';
    
    public function __construct($projectDir, EntityManagerInterface $entityManager )
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;

        parent::__construct();
    }
    

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stocks = $this->getCsvdata();
        $stockRepo = $this->entityManager->getRepository(Stock::class);

        $existingCount = 0;
        $newCount = 0;
            
            foreach($stocks as $stock){
                //when the location and the stock already exist update the stock 
                if($existingItem = $stockRepo->findOneBy(['STOCK_BRANCH' => $stock['BRANCH'], 'STOCK_STOCK' => $stock['STOCK']])){ 
                    $this->updateStock($existingItem, $stock);
                    $existingCount++;
                    continue;
                }
                
                $this->createStock($stock);
                $newCount++;
            }
            $this->entityManager->flush();
            
        $io = new SymfonyStyle($input, $output);
        $io->success($existingCount ." existing items have been updated ". $newCount ." items have beed added");
        return Command::SUCCESS;
    }
    
    public function getCsvdata(){
        ini_set('memory_limit', '8192M');
        $inputFile = $this->projectDir.'/public/stock-inventory/test-stock-file.csv'; 
        $decoder = new Serializer([new ObjectNormalizer()], [ new CsvEncoder()]);
        return $decoder->decode(file_get_contents($inputFile), 'csv');
            
    }

    public function updateStock($existingItem, $stock){
        $existingItem->setSTOCKSTOCK($stock['STOCK']); 
        $this->entityManager->persist($existingItem);
        
    }

    public function createStock($stock){
        $newStock = new Stock();
        $newStock->setSTOCKSKU($stock['SKU']);
        $newStock->setSTOCKBRANCH($stock['BRANCH']);
        $newStock->setSTOCKSTOCK($stock['STOCK']);
        $this->entityManager->persist($newStock);
    }


}
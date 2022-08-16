<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Message\SkuOutOfStockEmail;
use App\Repository\StockRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class StockController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(StockRepository $stockRepository, Request $request, PaginatorInterface $paginator):Response
    {
       
        $em = $this->getDoctrine()->getManager();

        $stockRepo = $em->getRepository(Stock::class);
        $stockQuery = $stockRepo->createQueryBuilder('s')
        ->where("s.id is not null")
        ->getQuery();

        $myStocks = $paginator->paginate(
            $stockQuery,
            $request->query->getInt('page', 1),
            10
        );
        
            
        return $this->render('home/index.html.twig', [
            'myStocks' => $myStocks,
        ]);
    }

    #[Route('/stock', name: 'stock-items')]
    public function stock(){

       return $this->render('/base.html.twig', []);
    }

    #[Route('/add', name: 'add-stock')]
    public function addStock(Request $request){
        $newStock = new Stock();
        $newStock->setSTOCKSKU($request->get('sku'));
        $newStock->setSTOCKBRANCH($request->get('branch'));
        $newStock->setSTOCKSTOCK($request->get('stock'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($newStock);
        $em->flush($newStock);
        return $this->render('home/add.html.twig', []);
       
    }

    #[Route('/notify', name: 'notify-out_of_stock')]
    public function monitorStock(MessageBusInterface $bus) {
        $bus->dispatch(new SkuOutOfStockEmail(1));
        return new Response("Sku at location is out of stock");
    }

    #[Route('/email', name: 'email-out-of-stock')]
    public function SendEmail(MailerInterface $mailer, $appEmail, StockRepository $stockRepository, EntityManagerInterface $entityManager) {

        $em = $this->getDoctrine()->getManager();

        $stockRepo = $em->getRepository(Stock::class);
        $stockQuery = $stockRepo->createQueryBuilder('s')
        ->where("s.STOCK_STOCK = 0")
        ->getQuery();
        $data = $stockQuery;
         $myResults =  $data->getResult();

         foreach($myResults as $val){
            $email = (new TemplatedEmail())
                ->from($appEmail)
                ->to('sammaxkobby@gmail.com')
                ->subject( $val->STOCK_SKU. ' at '. $val->STOCK_BRANCH .' is out of stock')
                ->htmlTemplate('emails/order-out-of-stock.html.twig')
                ->context([
                    'delivery_date'=>date_create('+3 days'),
                    'SKU'=> ''
                ]);
        $mailer->send($email);
        return new Response('Email Sent');
         }
         
        // $stock = $entityManager->getRepository(Stock::class);
        // $query = $stock->createQueryBuilder(
        //     'SELECT p
        //     FROM App\Entity\Stock s
        //     WHERE s.STOCK = :stock'
        // )->setParameter('stock', 0);
        // $data = $query->getQuery();
        // $myResults =  $data->getResult();

         
         
       
    }
}
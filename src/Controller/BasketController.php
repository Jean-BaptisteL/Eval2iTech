<?php

namespace App\Controller;

use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/basket', name: 'app_basket')]
    public function index(BasketRepository $basketRepository, ProductRepository $productRepository): Response
    {

        $session = $this->requestStack->getSession();

        $user = $this->getUser();
        $baskets = $basketRepository->findBy(['user' => $user]);
        $basketList = [];
        $totalPrice = 0;
        
        if (!$session->get('basket') && !$this->getUser()){
            foreach ($baskets as $basket) {
                $product = $productRepository->find($basket->getProduct()->getId());
                array_push($basketList, ['product' => $product->getName(), 'quantity' => $basket->getQuantity(), 'price' => 'Prix unitaire = ' . $product->getPrice() . '€ Prix total = ' . $product->getPrice()*$basket->getQuantity() . '€']);
                $totalPrice += intval($product->getPrice()*$basket->getQuantity());
            }
        } else {
            $baskets = $session->get('basket');
            foreach ($baskets as $basket) {
                $product = $productRepository->find($basket['product']->getId());
                array_push($basketList, ['product' => $product->getName(), 'quantity' => $basket['quantity'], 'price' => 'Prix unitaire = ' . $product->getPrice() . '€ Prix total = ' . $product->getPrice()*$basket['quantity'] . '€']);
                $totalPrice += intval($product->getPrice()*$basket['quantity']);
            }
        }

        return $this->render('basket/index.html.twig', [
            'basketList' => $basketList,
            'totalPrice' => $totalPrice
        ]);
    }
}

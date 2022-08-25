<?php

namespace App\Controller;

use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    #[Route('/basket', name: 'app_basket')]
    public function index(BasketRepository $basketRepository, ProductRepository $productRepository): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        $baskets = $basketRepository->findBy(['user' => $user]);
        $basketList = [];
        $totalPrice = 0;

        foreach ($baskets as $basket) {
            $product = $productRepository->find($basket->getProduct()->getId());
            array_push($basketList, ['product' => $product->getName(), 'quantity' => $basket->getQuantity(), 'price' => 'Prix unitaire = ' . $product->getPrice() . '€ Prix total = ' . $product->getPrice()*$basket->getQuantity() . '€']);
            $totalPrice += intval($product->getPrice()*$basket->getQuantity());
        }

        return $this->render('basket/index.html.twig', [
            'basketList' => $basketList,
            'totalPrice' => $totalPrice
        ]);
    }
}

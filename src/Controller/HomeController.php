<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $product, BasketRepository $basketRepository): Response
    {

        $session = $this->requestStack->getSession();
        if ($session->get('basket') && $this->getUser()) {
            $baskets = $session->get('basket');
            $basket = new Basket;
            foreach ($baskets as $sessionBasket) {
                $basket->setUser($this->getUser());
                $basket->setProduct($sessionBasket['product']);
                $basket->setQuantity($sessionBasket['quantity']);
                $basketRepository->add($basket, true);
            }
        }

        $homeProducts = $product->getProductsForHomePage();

        return $this->render('home/index.html.twig', [
            'products' => $homeProducts
        ]);
    }
}

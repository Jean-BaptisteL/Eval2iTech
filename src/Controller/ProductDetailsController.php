<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Form\BasketType;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductDetailsController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/product/{id}', name: 'app_product_details')]
    public function index(Request $request, ProductRepository $productRepository, BasketRepository $basketRepository, int $id): Response
    {

        $session = $this->requestStack->getSession();

        $basket = new Basket;
        $form = $this->createForm(BasketType::class ,$basket);
        
        $product = $productRepository->find($id);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $basket = $form->getData();
            $basket->setProduct($product);
            $basket->setUser($this->getUser());
            if ($this->getUser()) {
                $basketRepository->add($basket, true);
            } else {
                if (!$session->get('basket')) {
                    $session->set('basket', [['product' => $basket->getProduct(), 'quantity' => $basket->getQuantity]]);
                } else {
                    $oldBasket = $session->get('basket');
                    array_push($oldBasket, ['product' => $basket->getProduct(), 'quantity' => $basket->getQuantity]);
                    $session->set('basket', $oldBasket);
                }
            }
        }

        return $this->renderForm('product_details/index.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }
}

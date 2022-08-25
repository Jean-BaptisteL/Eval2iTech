<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Form\BasketType;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductDetailsController extends AbstractController
{
    #[Route('/product/{id}', name: 'app_product_details')]
    public function index(Request $request, ProductRepository $productRepository, BasketRepository $basketRepository, int $id): Response
    {
        $basket = new Basket;
        $form = $this->createForm(BasketType::class ,$basket);
        
        $product = $productRepository->find($id);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $basket = $form->getData();
            $basket->setProduct($product);
            $basket->setUser($this->getUser());
            $basketRepository->add($basket, true);
        }

        return $this->renderForm('product_details/index.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }
}

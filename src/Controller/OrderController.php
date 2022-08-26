<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\BasketRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, ManagerRegistry $managerRegistry, BasketRepository $basketRepository, ProductRepository $productRepository, OrderRepository $orderRepository): Response
    {
        $entityManager = $managerRegistry->getManager();
        $order = new Order;
        $form = $this->createFormBuilder($order)
            ->add('saveOrder', SubmitType::class, ['label' => 'Payer'])
            ->getForm();
        
        if(!$this->getUser()){
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        $baskets = $basketRepository->findBy(['user' => $user]);
        $basketList = [];
        $orderProducts = [];
        $totalPrice = 0;
        $finishedOrder = '';

        foreach ($baskets as $basket) {
            $product = $productRepository->find($basket->getProduct()->getId());
            array_push($basketList, ['product' => $product->getName(), 'quantity' => $basket->getQuantity(), 'price' => 'Prix unitaire = ' . $product->getPrice() . '€ Prix total = ' . $product->getPrice()*$basket->getQuantity() . '€']);
            array_push($orderProducts, ['productId' => $product->getId(), 'productName' => $product->getName(), 'quantity' => $basket->getQuantity(), 'price' => $product->getPrice()*$basket->getQuantity()]);
            $totalPrice += intval($product->getPrice()*$basket->getQuantity());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $order->setProducts($orderProducts);
            $order->setTotalPrice($totalPrice);
            $order->setUser($user);
            if (random_int(1, 2) === 1) {
                $finishedOrder = 'OK';
                $orderRepository->add($order, true);
                foreach ($baskets as $basket) {
                    $product = $productRepository->find($basket->getProduct()->getId());
                    $oldQuantity = $product->getQuantity();
                    $newQuantity = $oldQuantity - $basket->getQuantity();
                    $product->setQuantity($newQuantity);
                    $entityManager->flush();
                    $basketRepository->remove($basket, true);
                }

            } else {
                $finishedOrder = 'NO';
            }
        }
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'basketList' => $basketList,
            'totalPrice' => $totalPrice,
            'finishedOrder' => $finishedOrder
        ]);
    }
}

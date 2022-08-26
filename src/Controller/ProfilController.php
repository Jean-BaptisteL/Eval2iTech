<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        if(!$this->getUser()){
            return $this->redirectToRoute('app_home');
        }

        $user = $this->getUser();
        $orders = $user->getOrders();

        return $this->render('profil/index.html.twig', [
            'user' => $user,
            'orders' => $orders
        ]);
    }
}

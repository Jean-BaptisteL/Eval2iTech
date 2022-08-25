<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{slug}', name: 'app_category_details')]
    public function productsByCategory(CategoryRepository $categoryRepository, string $slug): Response
    {
        $category = $categoryRepository->findOneBy(array('slug' => $slug));
        return $this->render('categories/category.html.twig', [
            'category' => $category,
        ]);
    }
}

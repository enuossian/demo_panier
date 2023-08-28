<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(ProductRepository $repo): Response
    {
        $produits = $repo->findAll();
        return $this->render('main/index.html.twig', [
            'produits' => $produits,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{   
    #[Route('/cart', name: 'cart')]
    public function index(RequestStack $rs, ProductRepository $repo)
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);

        //Je vais créer un nouveau tableau qui contiendra des objets Product et les quantité de chaque objet
        $cartWithData = [];

        //Pour chaque id qui se trouve dans le tableau $cart, on ajoute une case(tableau) dans cartWithData, qui est un tableau multidimensionnel
        foreach($cart as $id => $quantity)
        {
            $cartWithData[] = [
                'product' => $repo->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0; // j'initialise mon Total
        foreach ($cartWithData as $item)
        {
            $sousTotal = $item['product']->getPrice() * $item['quantity'];
            $total = $total + $sousTotal;
        }

        return $this->render('panier/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);

    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, RequestStack $rs)
    {
        // Nous allons récupérer une session grace à la classe RequestStack
        $session = $rs->getSession();

        // Je récupère l'attribut de session 'cart', s'il n'existe pas, un tableau vide
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        // indice => valeur // idProduit => QuantitéduProduitDansLePanier
        // dans mon tableau $cart, à la case $id je donne la valeur 1
        
        // Si le produit existe déjà, j'incrémente sa quantité sinon j'initialise à 1
        
        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt++;
        } else 
        {
            $qt++;
            $cart[$id] = 1;
        }

        // Je sauvegarde l'état de mon panier en session à l'attribut de session 'cart'
        $session->set('cart', $cart);
        $session->set('qt', $qt);
        return $this->redirectToRoute('accueil');

    }

    #[Route('/cart/remove/{id}', name:'cart_remove')]
    public function remove($id, RequestStack $rs)
    {
        $session = $rs->getSession();
        $cart = $session->get('cart', []);

        //quantité du panier
        $qt = $session->get('qt', 0);

        //! si l'id existe dans mon panier, je le supprime du tableau grace a unset()
        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }

        // gérer une potentielle erreur négative
        if($qt < 0)
        {
            $qt = 0;
        }

        $session->set('qt', $qt);
        $session->set('cart', $cart);


        return $this->redirectToRoute('cart');
    }
}



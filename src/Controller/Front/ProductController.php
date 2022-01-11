<?php

namespace App\Controller\Front;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * @Route("products", name="product_list")
     */
    public function productList(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll(); // findAll() récupère tous les products de la bdd

        return $this->render('front/products.html.twig', ['products' => $products]);
    }

    /**
     * @Route("product/{id}", name="product_show")
     */
    public function productShow($id, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id); // find() permet de récupérer un producte grâce à son id.

        return $this->render('front/product.html.twig', ['product' => $product]);
    }
}
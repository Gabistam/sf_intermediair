<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    /**
     * @Route("/admin/products", name="admin_product_list")
     */
    public function productList(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render("admin/adminProducts.html.twig", ['products' => $products]);
    }

    /**
     * @Route("/admin/product/{id}", name="admin_product_show")
     */
    public function productShow($id, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);

        return $this->render("admin/adminProduct.html.twig", ['product' => $product]);
    }

    /**
     * @Route("/admin/update/product/{id}", name="admin_update_product")
     */
    public function adminUpdateProduct(
        $id,
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {

        $product = $productRepository->find($id);

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_product_list");
        }


        return $this->render("admin/adminProductform.html.twig", ['productForm' => $productForm->createView()]);
    }

    /**
     * @Route("admin/create/product/", name="admin_product_create")
     */
    public function adminProductCreate(Request $request, EntityManagerInterface $entityManagerInterface)
    {
        $product = new Product();

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_product_list");
        }


        return $this->render("admin/adminProductform.html.twig", ['productForm' => $productForm->createView()]);
    }

    /**
     * @Route("admin/delete/product/{id}", name="admin_delete_product")
     */
    public function adminDeleteProduct(
        $id,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManagerInterface
    ) {

        $product = $productRepository->find($id);

        $entityManagerInterface->remove($product);

        $entityManagerInterface->flush();

        return $this->redirectToRoute("admin_product_list");
    }
}

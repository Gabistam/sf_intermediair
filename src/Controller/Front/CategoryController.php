<?php

namespace App\Controller\Front;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("categories", name="category_list")
     */
    public function categoryList(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render("front/categories.html.twig", ['categories' => $categories]);
    }

    /**
     * @Route("category/{id}", name="category_show")
     */
    public function categoryShow($id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        return $this->render("front/category.html.twig", ['category' => $category]);
    }

    /**
     * @Route("update/category/{id}", name="category_update")
     */
    public function categoryUpdate(
        $id,
        CategoryRepository $categoryRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {
        $category = $categoryRepository->find($id);

        // Création du formulaire
        $categoryForm = $this->createForm(CategoryType::class, $category);

        // Utilisation de handleRequest pour demander au formulaire de traiter les informations
        // rentrées dans le formulaire
        // Utilisation de request pour récupérer les informations rentrées dans le formualire
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            // persist prépare l'enregistrement dans la bdd
            // analyse le changement à faire 
            $entityManagerInterface->persist($category);
            // flush enregistre dans la bdd
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'La categorie a été modifiée'
            );

            return $this->redirectToRoute('category_list');
        }

        return $this->render('front/categoryform.html.twig', ['categoryForm' => $categoryForm->createView()]);
    }
}
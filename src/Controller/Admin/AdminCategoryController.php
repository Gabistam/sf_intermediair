<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCategoryController extends AbstractController
{
    /**
     * @Route("/admin/categories", name="admin_category_list")
     */
    public function categoryList(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render("admin/adminCategories.html.twig", ['categories' => $categories]);
    }

    /**
     * @Route("/admin/adminCategory/{id}", name="admin_category_show")
     */
    public function categoryShow($id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        return $this->render("admin/adminCategory.html.twig", ['category' => $category]);
    }

    /**
     * @Route("/admin/update/category/{id}", name="admin_category_update")
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

            return $this->redirectToRoute('admin_category_list');
        }

        return $this->render('admin/adminCategoryform.html.twig', ['categoryForm' => $categoryForm->createView()]);
    }
    /**
     * @Route("admin/create/category/", name="admin_category_create")
     */
    public function adminCategoryCreate(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            // On récupère le fichier que l'on rentre dans le champs du formulaire
            $mediaFile = $categoryForm->get('media')->getData();

            if ($mediaFile) {

                // On crée un nom unique avec le nom original de l'image pour éviter 
                // tout problème lors de l'enregistrement dans le dossier public

                // on récupère le nom original du fichier
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                // On utilise slug sur le nom original pouur avoir un nom valide
                $safeFilename = $sluggerInterface->slug($originalFilename);

                // On ajoute un id unique au nom du fichier
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                // On déplace le fichier dans le dossier public/media
                // la destination est définie dans 'images_directory'
                // du fichier config/services.yaml

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $category->setMedia($newFilename);
            }


            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_category_list");
        }


        return $this->render("admin/categoryform.html.twig", ['categoryForm' => $categoryForm->createView()]);
    }

    /**
     * @Route("/admin/delete/category/{id}", name="admin_delete_category")
     */
    public function deleteCategory(
        $id,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManagerInterface
    ) {
        $category = $categoryRepository->find($id);

        $entityManagerInterface->remove($category);
        $entityManagerInterface->flush();

        $this->addFlash(
            'notice',
            'La catégorie est supprimée'
        );

        return $this->redirectToRoute("category_list");
    }
}

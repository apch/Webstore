<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * @Route("/categories", name="all_categories")
     *
     * @return Response
     */
    public function viewAll()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('categories/view_all.html.twig',
            [
                'categories' => $categories
            ]
        );
    }

    /**
     * @Route("/categories/add", name="add_category_form")
     * @Method("GET")
     *
     * @return Response
     */
    public function add()
    {
        $form = $this->createForm(CategoryType::class);
        return $this->render("categories/add.html.twig",
            [
                'categoryForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("categories/add", name="add_category_process")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function addProcess(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash("success", "Category with name ". $category->getName() . " was added successfully");

            return $this->redirectToRoute("all_categories");
        }

        return $this->render("categories/add.html.twig",
            [
                'categoryForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/categories/edit/{id}", name="edit_category_form")
     * @Method("GET")
     *
     * @param Category $category
     * @return Response
     */
    public function edit(Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);

        return $this->render("categories/edit.html.twig",
            ['categoryForm' => $form->createView()]);
    }

    /**
     * @Route("/categories/edit/{id}", name="edit_category_process")
     * @Method("POST")
     *
     * @param Category $category
     * @param Request $request
     *
     * @return Response
     */
    public function editProcess(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash("success", "Category with name ". $category->getName() . " was edited successfully");

            return $this->redirectToRoute("all_categories");
        }

        return $this->render("categories/add.html.twig",
            [
                'categoryForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/categories/delete/{id}", name="delete_category_process")
     * @Method("POST")
     *
     * @param Category $category
     * @return Response
     */
    public function delete(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash("delete", "Category deleted!!!");
        return $this->redirectToRoute("all_categories");
    }
}

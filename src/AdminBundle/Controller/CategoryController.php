<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Category;
use AdminBundle\Entity\Promotion;
use AdminBundle\Form\CategoryType;
use AdminBundle\Form\PromoCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * @Route("/admin/categories", name="all_categories")
     *
     * @return array
     * @Template()
     */
    public function viewAllAction()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return ['categories' => $categories];
    }

    /**
     * @Route("/admin/categories/add", name="add_category_form")
     * @Method("GET")
     *
     * @return array
     * @Template()
     */
    public function addAction()
    {
        $form = $this->createForm(CategoryType::class);
        return ['categoryForm' => $form->createView()];
    }

    /**
     * @Route("/admin/categories/add", name="add_category_process")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function addProcessAction(Request $request)
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

        return $this->render("@Admin/Category/add.html.twig",
            [
                'categoryForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/admin/categories/edit/{id}", name="edit_category_form")
     * @Method("GET")
     *
     * @param Category $category
     * @return array
     * @Template()
     */
    public function editAction(Category $category)
    {
        $form = $this->createForm(CategoryType::class, $category);

        return ['categoryForm' => $form->createView()];
    }

    /**
     * @Route("/admin/categories/edit/{id}", name="edit_category_process")
     * @Method("POST")
     *
     * @param Category $category
     * @param Request $request
     *
     * @return Response
     */
    public function editProcessAction(Category $category, Request $request)
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

        return $this->render("@Admin/Category/edit.html.twig",
            [
                'categoryForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/admin/categories/delete/{id}", name="delete_category_process")
     * @Method("POST")
     *
     * @param Category $category
     * @return Response
     */
    public function deleteAction(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        $this->addFlash("delete", "Category deleted!!!");
        return $this->redirectToRoute("all_categories");
    }

    /**
     * @Route("/admin/categories/promo/add", name="add_category_promo_form")
     * @Method("GET")
     *
     * @return array
     * @Template()
     */
    public function addPromoAction()
    {
        $form = $this->createForm(PromoCategoryType::class);
        return ['categoryPromoForm' => $form->createView()];
    }

    /**
     * @Route("/admin/categories/promo/add", name="add_category_promo_process")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function addPromoProcessAction(Request $request)
    {
        $promotion = new Promotion();

        $form = $this->createForm(PromoCategoryType::class, $promotion);
        $form->handleRequest($request);

        if($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();

            $this->addFlash("success", "Promotion was added successfully");

            return $this->redirectToRoute("all_categories");
        }

        return $this->render("@Admin/Category/addPromo.html.twig",
            [
                'categoryForm' => $form->createView()
            ]
        );
    }
}

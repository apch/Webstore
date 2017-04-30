<?php

namespace StoreBundle\Controller;

use AdminBundle\Entity\Category;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StoreController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');

        $allProducts = $this->getDoctrine()
            ->getRepository('AdminBundle:Product')
            ->findBy([], ['price' => 'asc', 'name' => 'asc']);
        $productRepository = $this->getDoctrine()->getRepository('AdminBundle:Product');
        $products = $paginator->paginate(
            $allProducts,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 4)
        );

        $calc = $this->get('app.price_calculator');

        $max_promotion = $this->get('promotion_manager')->getGeneralPromotion();

        $featuredProducts = $productRepository->getFeatured(12, $this->getUser());

        return $this->render('home/index.html.twig',
            [
                'products' => $products,
                'max_promotion' => $max_promotion,
                'calc' => $calc,
                'featured_products' => $featuredProducts
            ]
        );
    }


    /**
     * render categories menu
     */
    public function categoriesMenuAction()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('/includes/categoriesMenu.html.twig',
            ['categories' => $categories]);
    }

    /**
     * @Route("/category/{id}", name="category")
     * @Method("GET")
     * @Template("categories/showProducts.html.twig")
     */
    public function categoryAction(Request $request, Category $category, $id)
    {
        /**
         * @var $em EntityManager
         */
        $em = $this->getDoctrine()->getManager();
        $paginator = $this->get('knp_paginator');
        $productRepository = $em->getRepository('AdminBundle:Product');

        $productsQuery = $productRepository->findByCategoryQB($id);
        $limit = $this->getParameter('category_products_pagination_count');
        $products = $paginator->paginate(
            $productsQuery,
            $request->query->getInt('page', 1),
            $limit
        );
        $calc = $this->get('app.price_calculator');
        return ['category' => $category,
            'products' => $products,
            'sortedby' => $this->get('app.utilities')->getSortingParamName($request),
            'calc' => $calc
        ];
    }
}

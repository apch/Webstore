<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Product;
use AdminBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @Route("/admin/products", name="admin_all_products")
     * @Method("GET")
     * @Template()
     */
    public function viewAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository('AdminBundle:Product');
        $paginator = $this->get('knp_paginator');

        //if search is required
        $searchWords = trim($request->get('search_words'));

        $qb = $productRepository->searchProductsAdmin($searchWords);
        $limit = $this->getParameter('admin_products_pagination_count');

        $products = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $limit
        );

        return ['products' => $products,
            'search_words' => $searchWords
        ];
    }

    /**
     * @Route("/admin/products/add", name="admin_add_product_form")
     * @Method("GET")
     *
     * @Template()
     */
    public function addAction()
    {
        $form = $this->createForm(ProductType::class);
        return ['productForm' => $form->createView()];
    }

    /**
     * @Route("/admin/products/add", name="admin_add_product_process")
     * @Method("POST")
     *
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Template("@Admin/Product/add.html.twig")
     */
    public function addProcessAction(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isValid()) {

            // $file stores the uploaded PDF file
            /**
             * @var UploadedFile $file
             */
            $file = $product->getImageUrl();
            $fileName = $this->get('app.product_image_uploader')->upload($file);

            $product->setImageUrl($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash("success", "Product with name ". $product->getName() . " was added successfully");

            return $this->redirectToRoute("admin_all_products");
        }

        return ['productForm' => $form->createView()];
    }

    /**
     * @Route("/admin/products/edit/{id}", name="admin_edit_product_form")
     * @Method("GET")
     *
     * @param Product $product
     * @return array
     * @Template("@Admin/Product/edit.html.twig")
     */
    public function editAction(Product $product)
    {
        $product->setImageUrl(
            new File($this->getParameter('products_images_directory').'/'.$product->getImageUrl())
        );
        $form = $this->createForm(ProductType::class, $product);

        return ['productForm' => $form->createView()];
    }

    /**
     * @Route("/admin/products/edit/{id}", name="admin_edit_product_process")
     * @Method("POST")
     *
     * @param Product $product
     * @param Request $request
     *
     * @return array|Response
     */
    public function editProcessAction(Product $product, Request $request)
    {
        $product->setImageUrl(
            new File($this->getParameter('products_images_directory').'/'.$product->getImageUrl())
        );
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isValid()) {

            // $file stores the uploaded PDF file
            /**
             * @var UploadedFile $file
             */
            $file = $product->getImageUrl();
            $fileName = $this->get('app.product_image_uploader')->upload($file);

            $product->setImageUrl($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash("success", "Product with name ". $product->getName() . " was edited successfully");

            return $this->redirectToRoute("admin_all_products");
        }

        return $this->render("@Admin/Product/edit.html.twig",
            [
                'productForm' => $form->createView()
            ]);
    }


    /**
     * @Route("/admin/products/delete/{id}", name="admin_delete_product_process")
     * @Method("POST")
     *
     * @param Product $product
     * @return Response
     */
    public function deleteAction(Product $product)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        $this->addFlash("delete", "Product deleted!!!");
        return $this->redirectToRoute("admin_all_products");
    }
}

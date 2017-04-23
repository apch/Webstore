<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Product;
use AdminBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @Route("/admin/products", name="all_products")
     *
     * @return Response
     */
    public function viewAllAction()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy([], ['price' => 'asc', 'name' => 'asc']);

        return $this->render('admin/products/view_all.html.twig',
            [
                'products' => $products
            ]
        );
    }

    /**
     * @Route("/admin/products/add", name="add_product_form")
     * @Method("GET")
     *
     * @return Response
     */
    public function addAction()
    {
        $form = $this->createForm(ProductType::class);
        return $this->render("admin/products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/admin/products/add", name="add_product_process")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
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

            return $this->redirectToRoute("all_products");
        }

        return $this->render("admin/products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/admin/products/edit/{id}", name="edit_product_form")
     * @Method("GET")
     *
     * @param Product $product
     * @return Response
     */
    public function editAction(Product $product)
    {
        $product->setImageUrl(
            new File($this->getParameter('products_images_directory').'/'.$product->getImageUrl())
        );
        echo $product->getImageUrl();
        $form = $this->createForm(ProductType::class, $product);

        return $this->render("admin/products/edit.html.twig",
            ['productForm' => $form->createView()]);
    }

    /**
     * @Route("/admin/products/edit/{id}", name="edit_product_process")
     * @Method("POST")
     *
     * @param Product $product
     * @param Request $request
     *
     * @return Response
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

            return $this->redirectToRoute("all_products");
        }

        return $this->render("admin/products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/admin/products/delete/{id}", name="delete_product_process")
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
        return $this->redirectToRoute("all_products");
    }
}

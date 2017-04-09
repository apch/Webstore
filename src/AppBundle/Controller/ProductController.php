<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
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
     * @Route("/products", name="all_products")
     *
     * @return Response
     */
    public function viewAll()
    {
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy([], ['price' => 'asc', 'name' => 'asc']);

        return $this->render('products/view_all.html.twig',
            [
                'products' => $products
            ]
        );
    }

    /**
     * @Route("/products/add", name="add_product_form")
     * @Method("GET")
     *
     * @return Response
     */
    public function add()
    {
        $form = $this->createForm(ProductType::class);
        return $this->render("products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("products/add", name="add_product_process")
     * @Method("POST")
     *
     * @param Request $request
     * @return Response
     */
    public function addProcess(Request $request)
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

        return $this->render("products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/products/edit/{id}", name="edit_product_form")
     * @Method("GET")
     *
     * @param Product $product
     * @return Response
     */
    public function edit(Product $product)
    {
        $product->setImageUrl(
            new File($this->getParameter('products_images_directory').'/'.$product->getImageUrl())
        );
        echo $product->getImageUrl();
        $form = $this->createForm(ProductType::class, $product);

        return $this->render("products/edit.html.twig",
            ['productForm' => $form->createView()]);
    }

    /**
     * @Route("/products/edit/{id}", name="edit_product_process")
     * @Method("POST")
     *
     * @param Product $product
     * @param Request $request
     *
     * @return Response
     */
    public function editProcess(Product $product, Request $request)
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

        return $this->render("products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/products/delete/{id}", name="delete_product_process")
     * @Method("POST")
     *
     * @param Product $product
     * @return Response
     */
    public function delete(Product $product)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        $this->addFlash("delete", "Product deleted!!!");
        return $this->redirectToRoute("all_products");
    }
}

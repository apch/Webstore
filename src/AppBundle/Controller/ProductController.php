<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * @Route("/products", name="all_products")
     */
    public function viewAll()
    {
        return new Response();
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

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('products_images_directory'),
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $product->setImageUrl($fileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash("success", "Product with name ". $product->getName() . " was added successfully");

            return $this->redirectToRoute("add_product_form");
        }

        return $this->render("products/add.html.twig",
            [
                'productForm' => $form->createView()
            ]
        );
    }
}

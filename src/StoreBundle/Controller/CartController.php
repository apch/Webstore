<?php

namespace StoreBundle\Controller;

use AdminBundle\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CartController extends Controller
{

    /**
     * Lists all Category entities.
     *
     * @Route("/showcart", name="showcart")
     * @Method("GET")
     * @Template("/cart/showCart.html.twig")
     */
    public function showCartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $productRepository = $em->getRepository('AdminBundle:Product');
        $productsArray = [];
        $cart = [];
        $totalSum = 0;

        $cookies = $request->cookies->all();

        if (isset($cookies['cart'])) {
            $cart = json_decode($cookies['cart']);
        }

        foreach ($cart as $productId => $productQuantity) {
            /**
             * @var Product $product
             */
            $product = $productRepository->find((int)$productId);
            if (is_object($product)) {
                $productPosition = [];

                $quantity = abs((int)$productQuantity);
                $price = $product->getPrice();
                $sum = $price * $quantity;

                $productPosition['product'] = $product;
                $productPosition['quantity'] = $quantity;
                $productPosition['price'] = $price;
                $productPosition['sum'] = $sum;
                $totalSum += $sum;

                $productsArray[] = $productPosition;
            }
        }

        return ['products' => $productsArray,
            'totalsum' => $totalSum
        ];
    }

    /**
     * Count cart from cookies
     *
     * @Method("GET")
     * @Template("/cart/navbarCart.html.twig")
     */
    public function navbarCartAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        //quantity -> sum array
        $cartArray = ['cart' => ['quantity' => 0, 'sum' => 0]];
        $cookies = $request->cookies->all();

        if (isset($cookies['cart'])) {
            $cart = json_decode($cookies['cart']);
            if ($cart == '') {
                return $cartArray;
            }
        } else {
            return $cartArray;
        }

        $productRepository = $em->getRepository('AdminBundle:Product');

        foreach ($cart as $productId => $productQuantity) {
            /**
             * @var Product $product
             */
            $product = $productRepository->find((int)$productId);
            if (is_object($product)) {
                $cartArray['cart']['sum'] += ($product->getPrice() * abs((int)$productQuantity));
                $cartArray['cart']['quantity'] += abs((int)$productQuantity);
            }
        }

        return $cartArray;
    }
}

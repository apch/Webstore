<?php

namespace StoreBundle\Controller;

use AdminBundle\Entity\Orders;
use AdminBundle\Entity\Product;
use AdminBundle\Entity\User;
use AdminBundle\Form\OrdersType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
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
                //$sum = $price * $quantity;
                $sum = number_format($this->get('app.price_calculator')->calculate($product) * $quantity, 2);

                $productPosition['product'] = $product;
                $productPosition['quantity'] = $quantity;
                $productPosition['price'] = number_format($this->get('app.price_calculator')->calculate($product), 2);;
                $productPosition['sum'] = $sum;
                $totalSum += $sum;

                $productsArray[] = $productPosition;
            }
        }

        $calc = $this->get('app.price_calculator');

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
                $cartArray['cart']['sum'] += (number_format($this->get('app.price_calculator')->calculate($product) * abs((int)$productQuantity), 2));
                $cartArray['cart']['quantity'] += abs((int)$productQuantity);
            }
        }

        return $cartArray;
    }

    /**
     * Shows order form.
     *
     * @Route("/orderform", name="orderform")
     * @Method({"GET", "POST"})
     * @Template("/cart/orderForm.html.twig")
     */
    public function orderFormAction(Request $request)
    {
        $order = new Orders();
        $form = $this->createForm(\StoreBundle\Form\OrdersType::class, $order);
        $calc = $this->get('app.price_calculator');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $orderSuccess = $this->get('app.utilities')->createOrderRecord($request, $order, $this->getUser());

            if (!$orderSuccess) {
                return $this->redirect($this->generateUrl('cartisempty')); //check valid cart
            }

            return $this->render('/cart/thankYou.html.twig'); //redirect to thankyou page
        }

        if (is_object($user = $this->getUser())) {
            $this->fillWithUserData($user, $form);
        }

        return [
            'order' => $order,
            'ordersForm' => $form->createView()
        ];
    }

    /**
     * @param User $user
     * @param Form $form
     * @return void
     */
    private function fillWithUserData($user, $form)
    {
        $form->get('name')->setData($user->getFullName());
        $form->get('email')->setData($user->getEmail());
        $form->get('phone')->setData($user->getPhone());
        $form->get('address')->setData($user->getAddress());
    }

    /**
     * If cart is empty.
     *
     * @Route("/cartisempty", name="cartisempty")
     * @Method("GET")
     * @Template("/cart/cartIsEmpty.html.twig")
     */
    public function cartIsEmptyAction()
    {
        return [];
    }
}

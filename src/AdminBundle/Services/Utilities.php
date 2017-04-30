<?php
/**
 * Created by PhpStorm.
 * User: apch
 * Date: 4/29/17
 * Time: 6:27 PM
 */

namespace AdminBundle\Services;


use AdminBundle\Entity\OrderProduct;
use AdminBundle\Entity\Orders;
use AdminBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Utilities
{
    /**
     * @var EntityManager $em
     */
    private $em;

    private $calc;

    public function __construct(EntityManager $entityManager, PriceCalculator $priceCalculator)
    {
        $this->em = $entityManager;
        $this->calc = $priceCalculator;
    }

    /**
     * Record cart to db order.
     *
     * @param Request $request
     * @param Orders $order
     * @param User $user
     * @return bool
     */
    public function createOrderRecord(Request $request, Orders $order, User $user = null)
    {
        $productRepository = $this->em->getRepository('AdminBundle:Product');

        $cart = $this->getCartFromCookies($request);
        if ((!$cart) || !(count($cart))) {
            return false;
        }

        //parse cart json form cookies
        $sum = 0; //total control sum of the order
        foreach ($cart as $productId => $productQuantity) {
            $product = $productRepository->find((int)$productId);
            if (is_object($product)) {
                $quantity = abs((int)$productQuantity);
                $sum += ($quantity * $this->calc->calculate($product));

                $orderProduct = new OrderProduct();
                $orderProduct->setOrder($order);
                $orderProduct->setProduct($product);
                $orderProduct->setPrice($this->calc->calculate($product));
                $orderProduct->setQuantity($quantity);
                $this->em->persist($orderProduct);

                $order->addOrderProduct($orderProduct);
            }
        }

        $order->setUser($user); //can be null if not registered
        $order->setSum($sum);
        $this->em->persist($order);
        $this->em->flush();

        $this->clearCart();
        return true;
    }

    /**
     * Get cart from cookies and return cart or false.
     *
     * @param Request $request
     * @return mixed
     */
    private function getCartFromCookies(Request $request)
    {
        $cookies = $request->cookies->all();

        if (isset($cookies['cart'])) {
            $cart = json_decode($cookies['cart']);

            $cartObj = $cart; //check if cart not empty
            if (!empty($cartObj) && count((array)$cartObj)) {
                return $cart;
            }
        }

        return false;
    }

    /**
     * clear cookies cart
     *
     * @return void
     */
    public function clearCart()
    {
        $response = new Response();
        $response->headers->clearCookie('cart');
        $response->sendHeaders();
    }

    /**
     * return sorting name param from request
     *
     * @param Request $request
     * @return string
     */
    public function getSortingParamName(Request $request)
    {
        $sortedBy = '';
        $sortParam = $request->get('sort');

        switch ($sortParam) {
            case 'p.name':
                $sortedBy = 'Name';
                break;
            case 'p.price':
                $sortedBy = 'Price';
                break;
            default:
                $sortedBy = 'Default';
                break;
        }
        return $sortedBy;
    }

}
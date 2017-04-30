<?php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AdminBundle\Entity\Role;
use AdminBundle\Entity\User;
use AdminBundle\Form\UserType;
use AdminBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $roleRepository = $this->getDoctrine()->getRepository(Role::class);
            $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);

            $user->addRole($userRole);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('security_login');
        }

        return $this->render(
            'user/register.html.twig',
            array('form' => $form->createView())
        );
    }



    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/admin/user/profile", name="user_profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();
        return $this->render("user/profile.html.twig", ['user'=>$user]);
    }

    /**
     * Lists all User entities.
     *
     * @Route("/admin/user/", name="admin_user_list")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $em->getRepository('AdminBundle:User');
        $paginator = $this->get('knp_paginator');

        $qb = $userRepository->getAllUsersAdminQB();
        $limit = $this->getParameter('admin_users_pagination_count');

        $entities = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $limit
        );

        return ['entities' => $entities];
    }

    /**
     * Shows user info
     *
     * @Route("/admin/user/{id}", name="admin_user_info")
     * @Method("GET")
     * @Template()
     */
    public function showUserInfoAction(User $user)
    {
        return ['user' => $user];
    }

    /**
     * Shows users orders
     *
     * @Route("/admin/user/{id}/orders", name="admin_user_orders")
     * @Method("GET")
     * @Template()
     */
    public function showUserOrdersAction(Request $request, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $ordersRepository = $em->getRepository('AdminBundle:Orders');
        $paginator = $this->get('knp_paginator');

        $qb = $ordersRepository->getUserOrdersAdminQB($user);
        $limit = $this->getParameter('admin_user_orders_pagination_count');

        $orders = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $limit
        );

        return ['user' => $user, 'orders' => $orders];
    }
}

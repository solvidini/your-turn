<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class UsersServiceController extends AbstractController
{
    /**
     * @Route("/users/list", name="users_service")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request, UserInterface $user)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);

        $parameters = $request->query->get('user');

        !empty($parameters) ?
        $parameters = array_filter($parameters, function($value) { return ($value !== ''); }) : $parameters = [];

        $users = $repository->findBy($parameters);

        return $this->render('users_service/list.html.twig', [
            'userz' => $users,
            'users' => $paginator->paginate(
                $users,
                $request->query->getInt('page', 1),
                10
            )
        ]);
    }
}

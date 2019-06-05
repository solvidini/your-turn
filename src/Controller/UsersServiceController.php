<?php

namespace App\Controller;

use App\Entity\Flat;
use App\Entity\Notification;
use App\Service\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class UsersServiceController extends AbstractController {
    /**
     * @Route("/users/list", name="users_service")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request, UserInterface $user) {
        $repository = $this->getDoctrine()->getRepository(User::class);

        $parameters = $request->query->get('user');

        !empty($parameters) ?
            $parameters = array_filter($parameters, function ($value) { return ($value !== ''); }) : $parameters = [];

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

    /**
     * @Route("/users/invite", name="invite_user")
     * @param Request $request
     * @param Validator $validator
     * @param UserInterface $user
     * @return RedirectResponse
     */
    public function invite(Request $request, Validator $validator, UserInterface $user) {
        $recipientId = $request->get(0);
        $flatId = $request->get('flat');

        $flat = $this->getDoctrine()->getRepository(Flat::class)->find($flatId);
        $recipient = $this->getDoctrine()->getRepository(User::class)->find($recipientId);

        if (!$validator->alreadyInvited($flat, $recipient)){

            $notification = new Notification();

            $notification->setFlat($flat);
            $notification->setRecipient($recipient);
            $notification->setSender($user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notification);
            $entityManager->flush();
        }

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

}

<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationsController extends AbstractController
{
    /**
     * @Route("/notifications", name="notifications")
     * @param Request $request
     * @return RedirectResponse
     */
    public function index(Request $request)
    {
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/notifications/accept", name="accept_notification")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function accept(Request $request, UserInterface $user)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $notification_id = $request->get('id');

        $notification = $this->getDoctrine()
            ->getRepository(Notification::class)
            ->find($notification_id);

        $user->addFlat($notification->getFlat());
        $entityManager->persist($user);
        $entityManager->remove($notification);
        $entityManager->flush();

        return new Response("", 200);
    }

    /**
     * @Route("/notifications/reject", name="reject_notification")
     * @param Request $request
     * @return Response
     */
    public function reject(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $notification_id = $request->get('id');

        $notification = $this->getDoctrine()
            ->getRepository(Notification::class)
            ->find($notification_id);

        $entityManager->remove($notification);
        $entityManager->flush();

        return new Response("", 200);
    }
}

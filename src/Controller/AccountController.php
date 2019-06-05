<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\UpdateProfileFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountController extends AbstractController {
    /**
     * @Route("/account", name="account")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        return $this->render('account/account.html.twig');
    }

    /**
     * @Route("/account/delete", name="delete_account")
     * @param Request $request
     * @param UserInterface $user
     * @param UserPasswordEncoderInterface $encoder
     * @param SessionInterface $session
     * @return RedirectResponse
     */
    public function delete(Request $request, UserInterface $user, UserPasswordEncoderInterface $encoder, SessionInterface $session) {
        $password = $request->get('password');
        if($encoder->isPasswordValid($user, $password)){
            $entityManager = $this->getDoctrine()->getManager();
            $notifications = $user->getNotifications();
            foreach ($notifications as $notification){
                $entityManager->remove($notification);
            }
            $flats = $user->getFlats();
            foreach ($flats as $flat){
                if (count($flat->getUsers()) < 2){
                    $notifications = $flat->getNotifications();
                    foreach($notifications as $notification){
                        $entityManager->remove($notification);
                    }
                    $entityManager->remove($flat);
                }
            }
            // force manual logout of logged in user
            $this->get('security.token_storage')->setToken(null);
            $entityManager->remove($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_logout');
        }
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/account/change/password", name="change_password")
     * @param Request $request
     * @param UserInterface $user
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse
     */
    public function change(Request $request, UserInterface $user, UserPasswordEncoderInterface $encoder) {
        $old_password = $request->get('old_password');
        $new_password = $request->get('new_password');
        if($encoder->isPasswordValid($user, $old_password) && $new_password != null){
            $new_password = $encoder->encodePassword(
                $user,
                $new_password
            );
            $user->setPassword($new_password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('profile');
        }
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}

<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\UpdateProfileFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function index(Request $request,UserInterface $user)
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        $tasks = $repository->findBy(
          ['nextUser' => $user]
        );
        $form = $this->createForm(UpdateProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('profile/list.html.twig', [
            'updateProfileForm' => $form->createView(),
            'tasks' => $tasks
        ]);
    }
}

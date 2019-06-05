<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Validator;
use App\Form\FlatFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Flat;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\RegistrationFormType;
use Symfony\Component\Validator\Constraints\Valid;

class FlatsServiceController extends AbstractController {
    /**
     * @Route("/flats/list", name="flats_service")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request, UserInterface $user) {
        $repository = $this->getDoctrine()->getRepository(Flat::class);
        $em = $this->getDoctrine()->getManager();

        $msg = null;
        if (!empty($request->get('msg')))
            $msg = $request->get('msg');

        if ($request->query->has('option') && !empty($request->query->get('value'))) {
            $value = $request->query->get('value');
            $option = $request->query->get('option');

            $flats = $repository->findBy([
                $option => $value
            ]);
        } else {
            $flats = $repository->findAll();
        }

        $numberOfUsers = [];
        $alreadyIn = [];
        $oneMember = [];

        foreach ($flats as $key => $flat) {
            $q = $em->createQuery("SELECT count(u.id) FROM App:User u WHERE ?1 MEMBER OF u.flats")->setParameter(1, $flats[$key]->getId());
            $numberOfUsers [$key] = $q->getSingleScalarResult();

            $alreadyIn [$key] = false;
            $oneMember [$key] = false;

            // check if the association exists
            foreach ($user->getFlats() as $each) {
                if ($each->getId() == $flat->getId()) {
                    $alreadyIn [$key] = true;
                }
                if (sizeof($flat->getUsers()) == 1) {
                    $oneMember [$key] = true;
                }
            }
        }

        return $this->render('flats_service/list.html.twig', [
            'flats' => $paginator->paginate(
                $flats,
                $request->query->getInt('page', 1),
                10
            ),
            'numberOfUsers' => $numberOfUsers,
            'alreadyIn' => $alreadyIn,
            'oneMember' => $oneMember,
            'msg' => $msg
        ]);
    }

    /**
     * @Route("/flats/create", name="create_flat")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function create(Request $request, UserInterface $user) {
        $flat = new Flat();
        $form = $this->createForm(FlatFormType::class, $flat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $flat->setName($data->getName());
            $flat->setPassword($data->getPassword());

            $user->addFlat($flat);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($flat);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('flats_service');
        }

        return $this->render('flats_service/create.html.twig', [
            'flatForm' => $form->createView()
        ]);
    } //INSERT INTO `users_flats` (`user_id`, `flat_id`) VALUES ('6', '1');

    /**
     * @Route("/flats/join", name="join_flat")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function join(Request $request, UserInterface $user) {
        $entityManager = $this->getDoctrine()->getManager();
        $flatId = $request->query->get('0');
        $pw = $request->get('password');

        $flat = $this->getDoctrine()
            ->getRepository(Flat::class)
            ->find($flatId);

        if ($pw != $flat->getPassword()) {
            return $this->redirectToRoute('flats_service', [
                'msg' => "wrong password"
            ]);
        }

        $user->addFlat($flat);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/flats/leave", name="leave_flat")
     * @param Request $request
     * @param UserInterface $user
     * @param Validator $validator
     * @return Response
     */
    public function leave(Request $request, UserInterface $user, Validator $validator) {
        $entityManager = $this->getDoctrine()->getManager();
        $flatId = $request->query->get('0');

        $flat = $this->getDoctrine()
            ->getRepository(Flat::class)
            ->find($flatId);

        if ($validator->memberOfFlat($flat, $user)) {

            $tasks = $flat->getTasks();
            foreach ($tasks as $task) {
                $currentSequence = json_decode($task->getSequence());
                if ($task->getNextUser() == $user) {
                    $entityManager->remove($task);
                } else if (in_array($user->getId(), $currentSequence)) {

                    foreach ($currentSequence as $key => $each) {
                        if ($currentSequence[$key] == null || $each == $user->getId())
                            unset($currentSequence[$key]);
                    }
                    $currentSequence = json_encode($currentSequence);
                    $task->setSequence($currentSequence);
                    $entityManager->persist($task);
                }
            }
            $entityManager->persist($flat);

            $user->removeFlat($flat);
            $entityManager->persist($user);

            $entityManager->flush();

        }
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/flats/yours", name="your_flats")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function yourFlats(PaginatorInterface $paginator, Request $request, UserInterface $user) {
        $em = $this->getDoctrine()->getManager();

        $flats = $user->getFlats();

        $msg = null;
        if (!empty($request->get('msg')))
            $msg = $request->get('msg');

        $numberOfUsers = [];

        foreach ($flats as $key => $flat) {
            $q = $em->createQuery("SELECT count(u.id) FROM App:User u WHERE ?1 MEMBER OF u.flats")->setParameter(1, $flats[$key]->getId());
            $numberOfUsers [$key] = $q->getSingleScalarResult();
        }

        return $this->render('flats_service/your_flats.html.twig', [
            'flats' => $paginator->paginate(
                $flats,
                $request->query->getInt('page', 1),
                10
            ),
            'numberOfUsers' => $numberOfUsers,
            'msg' => $msg
        ]);
    }

    /**
     * @Route("/flats/delete", name="delete_flat")
     * @param Request $request
     * @param UserInterface $user
     * @param Validator $validator
     * @return Response
     */
    public function delete(Request $request, UserInterface $user, Validator $validator) {
        $entityManager = $this->getDoctrine()->getManager();
        $flatId = $request->query->get('0');

        $pw = $request->get('password');
        $flat = $this->getDoctrine()
            ->getRepository(Flat::class)
            ->find($flatId);

        if ($validator->memberOfFlat($flat, $user)) {
            if ($pw != $flat->getPassword()) {
                return $this->redirectToRoute('your_flats', [
                    'msg' => "wrong password"
                ]);
            }
            $tasks = $flat->getTasks();
            $notifications = $flat->getNotifications();

            foreach ($notifications as $notification) {
                $flat->removeNotification($notification);
                $entityManager->remove($notification);
            }

            foreach ($tasks as $task) {
                $flat->removeTask($task);
                $entityManager->remove($task);
            }

            $entityManager->remove($flat);
            $entityManager->flush();
        }

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}


<?php

namespace App\Controller;

use App\Entity\Flat;
use App\Entity\Task;
use App\Entity\User;
use App\Form\FlatFormType;
use App\Form\TaskFormType;
use App\Service\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TasksServiceController extends AbstractController {
    /**
     * @Route("/tasks/list", name="tasks_service")
     * @param UserInterface $user
     * @return Response
     */
    public function index(UserInterface $user) {
        $flats = $user->getFlats();

        return $this->render('tasks_service/list.html.twig', [
            'flats' => $flats,
            'user' => $user
        ]);
    }

    /**
     * @Route("/tasks/create", name="create_task")
     * @param Request $request
     * @param UserInterface $user
     * @return RedirectResponse|Response
     */
    public function create(Request $request, UserInterface $user) {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $flatId = $request->query->get('0');

        $flat = $this->getDoctrine()->getRepository(Flat::class)->find($flatId);
        //EntityType
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $task->setName($data->getName());
            $task->setFlat($flat);
            $task->setType($data->getType());
            $task->setUser($user);
            $task->setNextUser($user);
            $sequence = [
                0 => $user->getId()
            ];
            $task->setNextKey(0);
            $sequence = json_encode($sequence);
            $task->setSequence($sequence);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('tasks_service');
        }

        return $this->render('tasks_service/create.html.twig', [
            'taskForm' => $form->createView(),
            'flat' => $flat
        ]);
    }

    /**
     * @Route("/tasks/yourTurn", name="your_turn")
     * @param Request $request
     * @param UserInterface $user
     * @param Validator $validator
     * @return RedirectResponse
     */
    public function yourTurn(Request $request, UserInterface $user, Validator $validator) {
        $entityManager = $this->getDoctrine()->getManager();
        $taskID = $request->query->get('0');
        $task = $this->getDoctrine()->getRepository(Task::class)->find($taskID);

        if ($validator->isYourTask($task, $user)) {
            $sequence = json_decode($task->getSequence());
            $key = $task->getNextKey();

            $key++;
            if (!array_key_exists($key, $sequence)) {
                $new_key = 0;
                foreach ($sequence as $key) {
                    $sequence[$new_key] = $key;
                    $new_key++;
                }
            }

            if ($key >= sizeof($sequence))
                $task->setNextKey(0);
            else $task->setNextKey($key);

            $nextUserID = (int)$sequence[$task->getNextKey()];
            $nextUser = $this->getDoctrine()->getRepository(User::class)->find($nextUserID);

            $task->setNextUser($nextUser);

            $entityManager->persist($task);

            $entityManager->flush();
        }

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/tasks/edit/sequence", name="edit_sequence")
     * @param Request $request
     * @param UserInterface $user
     * @param Validator $validator
     * @return Response
     */
    public function editSequence(Request $request, UserInterface $user, Validator $validator) {
        $entityManager = $this->getDoctrine()->getManager();
        $taskID = $request->query->get('0');
        $task = $this->getDoctrine()->getRepository(Task::class)->find($taskID);
        if ($validator->isYourTask($task, $user)) {
            $users = $task->getFlat()->getUsers();
            $whichUser = [];
            foreach ($users as $each) {
                $whichUser[$each->getId()] = $each->getFullName();
            }

            $currentSequence = json_decode($task->getSequence());
            $sequenceArray = [];
            foreach ($currentSequence as $key => $each) {
                $eachUser = $this->getDoctrine()->getRepository(User::class)->find($each);
                $sequenceArray[$key] = $eachUser->getFullName();
            }

            if ($request->request->has('who')) {
                $sequence = $request->get('who');
                $newSequence = [];
                foreach ($sequence as $key => $each) {
                    $newSequence[$key] = $each;
                }

                $nextUserID = (int)$sequence[0];
                $nextUser = $this->getDoctrine()->getRepository(User::class)->find($nextUserID);

                $newSequence = json_encode($newSequence);

                $task->setSequence($newSequence);
                $task->setNextKey(0);
                $task->setNextUser($nextUser);

                $entityManager->persist($task);

                $entityManager->flush();

                return $this->redirectToRoute('tasks_service');
            }
            return $this->render('tasks_service/edit_sequence.html.twig', [
                'task' => $task,
                'whichUser' => $whichUser,
                'currentSequence' => $sequenceArray
            ]);
        }
        return $this->redirectToRoute('tasks_service');
    }

    /**
     * @Route("/tasks/delete", name="delete_task")
     * @param Request $request
     * @param UserInterface $user
     * @param Validator $validator
     * @return Response
     */
    public function delete(Request $request, UserInterface $user, Validator $validator) {
        $entityManager = $this->getDoctrine()->getManager();
        $taskId = $request->query->get('0');
        $pw = $request->get('password');

        $task = $this->getDoctrine()
            ->getRepository(Task::class)
            ->find($taskId);
        if ($validator->isYourTask($task, $user)) {
            $flat = $task->getFlat();


            if ($pw != $flat->getPassword()) {
                return $this->redirectToRoute('tasks_service', [
                    'msg' => "wrong password"
                ]);
            }

            $entityManager->remove($task);
            $entityManager->flush();
        }
        return $this->redirectToRoute('tasks_service');
    }
}

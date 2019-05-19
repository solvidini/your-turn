<?php

namespace App\Controller;

use App\Entity\Flat;
use App\Entity\Task;
use App\Entity\User;
use App\Form\FlatFormType;
use App\Form\TaskFormType;
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
        $flats = [];
        if (sizeof($user->getFlats()) == 0)
            $flats['-'] = '-';
        foreach ($user->getFlats() as $flat) {
            $flats[(string)$flat->getName()] = $flat;
        }

        $form->add('flat', ChoiceType::Class, [
            'choices' => $flats
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $task->setName($data->getName());
            $task->setFlat($data->getFlat());
            $task->setType($data->getType());
            $task->setUser($user);
            $task->setNextUser($user);
            $sequence = [
                0 => $user->getId(),
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
            'taskForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/tasks/yourTurn", name="your_turn")
     * @param Request $request
     * @param UserInterface $user
     * @return RedirectResponse
     */
    public function yourTurn(Request $request, UserInterface $user) {
        $entityManager = $this->getDoctrine()->getManager();
        $taskID = $request->query->get('0');
        //tu trza naprawic, bo luka jak ch*& !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $task = $this->getDoctrine()->getRepository(Task::class)->find($taskID);
        $sequence = json_decode($task->getSequence());

        $key = $task->getNextKey();

        $key++;

        if ($key >= sizeof($sequence))
            $task->setNextKey(0);
        else $task->setNextKey($key);

        $nextUserID = (int)$sequence[$task->getNextKey()];
        $nextUser = $this->getDoctrine()->getRepository(User::class)->find($nextUserID);

        $task->setNextUser($nextUser);

        $entityManager->persist($task);

        $entityManager->flush();

        return $this->redirectToRoute('tasks_service');
    }

    /**
     * @Route("/tasks/edit/sequence", name="edit_sequence")
     * @param Request $request
     * @param UserInterface $user
     * @return Response
     */
    public function editSequence(Request $request, UserInterface $user) {
        $entityManager = $this->getDoctrine()->getManager();
        $taskID = $request->query->get('0');
        $task = $this->getDoctrine()->getRepository(Task::class)->find($taskID);
        $users = $task->getFlat()->getUsers();
        $whichUser = [];

        foreach ($users as $each) {
            $whichUser[$each->getId()] = $each->getFullName();
        }

        if ($request->request->has('sequence')){
            $sequence = $request->get('sequence');
            $newSequence = [];
            foreach($sequence as $key => $each){
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
        }

        return $this->render('tasks_service/edit_sequence.html.twig', [
            'task' => $task,
            'whichUser' => $whichUser
        ]);
    }
}

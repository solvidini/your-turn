<?php


namespace App\Service;


use App\Entity\Flat;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Validator {

    private $em;

    public function __construct(EntityManagerInterface $em){
        $this->em = $em;
    }

    public function memberOfFlat($flat, UserInterface $user){
        if ($flat == null) return false;
        return in_array($flat, $user->getFlats()->toArray());
    }

    public function isYourTask($task, UserInterface $user){
        if ($task == null) return false;
        return in_array($task->getFlat(), $user->getFlats()->toArray());
    }
}
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

    public function alreadyInvited($flat, $recipient){
        $query = $this->em->createQuery(
            'SELECT COUNT(n) 
            FROM App\Entity\Notification n
            WHERE n.flat = :flat
            AND n.recipient = :recipient'
        )->setParameters([
            'flat' => $flat,
                'recipient' => $recipient,
            ]
        );
        $result = $query->execute();
        return $result[0][1] > 0 ? true : false;
    }
}
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 * @ORM\Table(name="tasks")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $nextUser;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Flat", inversedBy="tasks")
     * @ORM\JoinColumn(name="flat_id", referencedColumnName="id")
     */
    private $flat;

    /**
     * @ORM\Column(type="json_array")
     */
    private $sequence;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Column(name="next_key")
     */
    private $nextKey;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNextUser(): ?User
    {
        return $this->nextUser;
    }

    public function setNextUser(?User $nextUser): self
    {
        $this->nextUser = $nextUser;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFlat(): ?Flat
    {
        return $this->flat;
    }

    public function setFlat(?Flat $flat): self
    {
        $this->flat = $flat;

        return $this;
    }

    public function getSequence()
    {
        return $this->sequence;
    }

    public function setSequence($sequence): self
    {
        $this->sequence = $sequence;

        return $this;
    }

    public function getNextKey(): ?int
    {
        return $this->nextKey;
    }

    public function setNextKey(int $nextKey): self
    {
        $this->nextKey = $nextKey;

        return $this;
    }
}

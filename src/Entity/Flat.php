<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;


/**
 * @ORM\Entity(repositoryClass="App\Repository\FlatRepository")
 * @ORM\Table(name="flats")
 */
class Flat
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
    private $password;

    /**
     * @var Collection|User[]
     * @ORM\ManyToMany(targetEntity="User", mappedBy="flats")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="flat")
     * @ORM\JoinColumn(name="task_id", referencedColumnName="id")
     */
    private $tasks;

    public function __construct() {
        $this->users = new ArrayCollection();
        $this->tasks = new ArrayCollection();
    }

    public function __toString() {
        return $this->name;
    }

    public function getUsers() {
        return $this->users;
    }

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addFlat($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeFlat($this);
        }

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setFlat($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getFlat() === $this) {
                $task->setFlat(null);
            }
        }

        return $this;
    }
}

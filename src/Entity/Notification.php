<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
{
    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_REQUEST = 2;
    const STATUS_ACCEPTED = 3;
    const STATUS_REJECTED = 4;
    const STATUS_UNFOLLOWED = 5;

    const TYPE_SYSTEM = 0;
    const TYPE_PRIVATE = 1;
    const TYPE_ACTION = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fromUser;

    /**
     * @ORM\Column(type="integer")
     */
    private $toUser;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $addedOn;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $readOn;

    /**
     * @ORM\Column(type="integer")
     */
    private $typeMessage;

    public function __construct(array $params)
    {
        $keys = array_keys($params);

        if(in_array('from_user', $keys)) {
            $this->setFromUser($params['from_user']);
        }

        if(in_array('to_user', $keys)) {
            $this->setToUser($params['to_user']);
        }

        if(in_array('message', $keys)) {
            $this->setMessage($params['message']);
        }

        if(in_array('status', $keys)) {
            $this->setStatus($params['status']);
        }

        if(in_array('read_on', $keys)) {
            $this->setReadOn($params['read_on']);
        }

        if(in_array('type_message', $keys)) {
            $this->setTypeMessage($params['type_message']);
        }

        $this->addedOn = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromUser(): ?int
    {
        return $this->fromUser;
    }

    public function setFromUser(?int $fromUser): self
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    public function getToUser(): ?int
    {
        return $this->toUser;
    }

    public function setToUser(int $toUser): self
    {
        $this->toUser = $toUser;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAddedOn(): ?\DateTimeInterface
    {
        return $this->addedOn;
    }

    public function setAddedOn(\DateTimeInterface $addedOn): self
    {
        $this->addedOn = $addedOn;

        return $this;
    }

    public function getReadOn(): ?\DateTimeInterface
    {
        return $this->readOn;
    }

    public function setReadOn(?\DateTimeInterface $readOn): self
    {
        $this->readOn = $readOn;

        return $this;
    }

    public function getTypeMessage(): ?int
    {
        return $this->typeMessage;
    }

    public function setTypeMessage(int $type_message): self
    {
        $this->typeMessage = $type_message;

        return $this;
    }
}

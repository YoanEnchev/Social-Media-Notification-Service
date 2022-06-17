<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification implements JsonSerializable
{
    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_REQUEST = 2;
    const STATUS_ACCEPTED = 3;
    const STATUS_REJECTED = 4;

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

    public function getStatusAsString(): string
    {
        switch($this->status) {

            case self::STATUS_UNREAD:
                return 'unread';
                
            case self::STATUS_READ:
                return 'read';

            case self::STATUS_REQUEST:
                return 'request';
            
            case self::STATUS_ACCEPTED:
                return 'accepted';
            
            default:
                return 'rejected';
        }
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

    public function markAsRead(): self
    {
        $this->readOn = new DateTime();
        $this->status = self::STATUS_READ;

        return $this;
    }

    public function getTypeMessage(): ?int
    {
        return $this->typeMessage;
    }

    public function getTypeAsString(): string
    {
        switch($this->typeMessage) {
            
            case self::TYPE_SYSTEM:
                return 'system';

            case self::TYPE_PRIVATE:
                return 'private';
            
            default:
                return 'action';
        }
    }

    public function setTypeMessage(int $type_message): self
    {
        $this->typeMessage = $type_message;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'from_user' => $this->getFromUser(),
            'to_user' => $this->getToUser(),
            'message'=> $this->getMessage(),
            'status' => $this->getStatusAsString(),
            'type' => $this->getTypeAsString(),
            'added_on' => $this->getAddedOn(),
            'read_on' => $this->getReadOn()
        ];
    }
}

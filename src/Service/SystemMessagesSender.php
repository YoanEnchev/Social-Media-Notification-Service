<?php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class SystemMessagesSender
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function send(string $messageText, array $userIds)
    {
        $batchSize = 200;
        $valuesArr = [];

        $status = Notification::STATUS_UNREAD;
        $messageType = Notification::TYPE_SYSTEM;

        $messageText = str_replace("'", "''", $messageText); // Handle quotes.

        foreach ($userIds as $i => $userId) {

             $valuesArr[] = " ($userId, '$messageText', $status, $messageType, NOW()) ";
             
             // Add restricted amount of records to the table by 1 SQL query.
             // Flushing all at once (for example 10000 inserts) with 1 SQL query could cause the server to run out of memory.
             if ($i > 0 && ($i % $batchSize) == 0) {
                
                $this->insertValues($valuesArr);
                $valuesArr = [];
             }
        }
        

        if (count($valuesArr) > 0) {
            // Flush the remaining objects if such exist.
            $this->insertValues($valuesArr);
        }
    }

    private function insertValues(array $valuesArr)
    {   // echo "INSERT INTO Notification(to_user, `message`, `status`, type_message, added_on) VALUES " . implode(', ', $valuesArr);
        // Perform multi insert with one SQL statement: insert into (...) values (...), (...)
        $this->entityManager->getConnection()->executeUpdate("INSERT INTO Notification(to_user, `message`, `status`, type_message, added_on) VALUES " . implode(', ', $valuesArr));
    }
}
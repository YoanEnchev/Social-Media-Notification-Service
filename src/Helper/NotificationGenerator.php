<?php

namespace App\Helper;

use App\Entity\Notification;

// Generates notifications based on request parameters
class NotificationGenerator
{
    public static function generateFollowRequest(array $params): Notification
    {
        $fromUser = $params['from_user'];

        $notification = new Notification([
            'from_user' => (int) $fromUser['id'],
            'to_user' => (int) $params['to_user']['id'],
            'message' => $fromUser['full_name'] . ' wants to follow you. Do you want to accept or reject?',
            'status' => Notification::STATUS_REQUEST,
            'type_message' => Notification::TYPE_SYSTEM
        ]);

        return $notification;
    }
} 
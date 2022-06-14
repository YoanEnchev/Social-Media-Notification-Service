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
            'type_message' => Notification::TYPE_ACTION
        ]);

        return $notification;
    }

    public static function generateFollowRequestAcceptance(array $params): Notification
    {
        $notification = new Notification([
            'from_user' => (int) $params['from_user']['id'],
            'to_user' => (int) $params['to_user']['id'],
            'message' => 'Congratulations, your follow request was accepted.',
            'status' => Notification::STATUS_ACCEPTED,
            'type_message' => Notification::TYPE_SYSTEM
        ]);

        return $notification;
    }

    public static function generateFollowRequestDeclining(array $params): Notification
    {
        $notification = new Notification([
            'from_user' => (int) $params['from_user']['id'],
            'to_user' => (int) $params['to_user']['id'],
            'message' => 'Your follow request has been declined.',
            'status' => Notification::STATUS_REJECTED,
            'type_message' => Notification::TYPE_SYSTEM
        ]);

        return $notification;
    }
} 
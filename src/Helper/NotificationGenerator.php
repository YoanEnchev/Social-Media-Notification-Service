<?php

namespace App\Helper;

use App\Entity\Notification;

// Generates notifications based on request parameters
class NotificationGenerator
{
    public static function generateFollowRequest(array $params): Notification
    {
        $follower = $params['follower'];

        $notification = new Notification([
            'from_user' => (int) $follower['id'],
            'to_user' => (int) $params['followed']['id'],
            'message' => $follower['full_name'] . ' wants to follow you. Do you want to accept or reject?',
            'status' => Notification::STATUS_REQUEST,
            'type_message' => Notification::TYPE_ACTION
        ]);

        return $notification;
    }

    public static function generateFollowRequestAcceptance(array $params): Notification
    {
        $notification = new Notification([
            'from_user' => (int) $params['followed']['id'],
            'to_user' => (int) $params['follower']['id'],
            'message' => 'Congratulations, your follow request was accepted.',
            'status' => Notification::STATUS_ACCEPTED,
            'type_message' => Notification::TYPE_SYSTEM
        ]);

        return $notification;
    }

    public static function generateFollowRequestDeclining(array $params): Notification
    {
        $notification = new Notification([
            'from_user' => (int) $params['followed']['id'],
            'to_user' => (int) $params['follower']['id'],
            'message' => 'Your follow request has been declined.',
            'status' => Notification::STATUS_REJECTED,
            'type_message' => Notification::TYPE_SYSTEM
        ]);

        return $notification;
    }

    public static function generateUnfollowAction(array $params): Notification
    {
        $follower = $params['follower'];

        $notification = new Notification([
            'from_user' => (int) $follower['id'],
            'to_user' => (int) $params['followed']['id'],
            'message' => 'User ' . $follower['full_name'] . ' no longer follows you.',
            'status' => Notification::STATUS_UNREAD,
            'type_message' => Notification::TYPE_SYSTEM
        ]);

        return $notification;
    }

    public static function generateWelcomeNotification(array $params): Notification
    {
        $notification = new Notification([
            'to_user' => $params['user_id'],
            'message' => 'Welcome to our platform.',
            'status' => Notification::STATUS_UNREAD,
            'type_message' => Notification::TYPE_SYSTEM
        ]);

        return $notification;
    }

    public static function generateChatMessage(array $params): Notification
    {
        $notification = new Notification([
            'from_user' => $params['sender_id'],
            'to_user' => $params['receiver_id'],
            'message' => $params['message_text'],
            'status' => Notification::STATUS_UNREAD,
            'type_message' => Notification::TYPE_PRIVATE
        ]);

        return $notification;
    }
} 
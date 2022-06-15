<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;
use App\Helper\NotificationGenerator;

class NotificationController extends AbstractController
{
    /**
     * @Route("/api/notification/follow", methods={"POST"})
     */
    public function handleFollowRequests(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $params = $request->request->all();
        $type = $params['action_type'];

        $notificationRepo = $entityManager->getRepository(Notification::class);
        
        if ($type === 'follow_request' || $type === 'accept_follow_request' || $type === 'decline_follow_request') {
        
            // Requests type which require to check if certain invitation exists
            $followInvitation = $notificationRepo->getFollowInvitation((int) $params['follower']['id'], (int) $params['followed']['id']);
            $followInvitationExists = $followInvitation !== null;
    
            if ($type === 'follow_request') {
            
                if($followInvitationExists) {
                    
                    return $this->json([
                        'message' => 'Follow invitation already exists.',
                    ], 400);
                }
    
                $notification = NotificationGenerator::generateFollowRequest($params);
                $notificationRepo->add($notification);
            }
            else if ($type === 'accept_follow_request') {
                
                if(!$followInvitationExists) {
                    
                    return $this->json([
                        'message' => 'Cannot accept invitation that does not exist.',
                    ], 400);
                }
    
                $notificationRepo->remove($followInvitation);
                $notificationRepo->add(NotificationGenerator::generateFollowRequestAcceptance($params));
            }
            else if ($type === 'decline_follow_request') {
                
                if(!$followInvitationExists) {
                    
                    return $this->json([
                        'message' => 'Cannot decline invitation that does not exist.',
                    ], 400);
                }
    
                $notificationRepo->remove($followInvitation);
                $notificationRepo->add(NotificationGenerator::generateFollowRequestDeclining($params));
            }
        }
        else {
            // Unfollow request.
            $notification = NotificationGenerator::generateUnfollowAction($params);
            $notificationRepo->add($notification);
        }

        $entityManager->flush();

        return $this->json([
            'message' => 'Successful.',
        ], 200);
    }

    /**
     * @Route("/api/notifications", methods={"GET"})
     */
    public function getUnreadNotifications(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $notificationRepo = $entityManager->getRepository(Notification::class);

        return $this->json(
            $notificationRepo->getUnseenNotificationsForUser((int) $request->query->get('user_id')), 200
        );
    }

    /**
     * @Route("/api/notifications", methods={"POST"})
     */
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $notificationRepo = $entityManager->getRepository(Notification::class);
        $params = $request->request->all();

        $notification = null; // For scope visibility.
        $type = $params['type'];

        if ($type === 'chat_message') {
            $notification = NotificationGenerator::generateChatMessage($params);
        }
        else if ($type === 'user_registration') {
            $notification = NotificationGenerator::generateWelcomeNotification($params);
        }

        $notificationRepo->add($notification, true);

        return $this->json([
            'message' => 'Success'
        ], 200 );
    }

    /**
     * @Route("/api/notifications/{id}", methods={"PATCH"})
     */
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $notificationRepo = $entityManager->getRepository(Notification::class);
        $params = $request->request->all();

        $notification = $notificationRepo->find($id);

        if ($params['type'] === 'mark_as_read') {
            
            if($params['user_id'] != $notification->getToUser()) {
                return $this->json([
                    'message' => 'Cannot access this notification.'
                ], 401);
            }

            $notification->markAsRead();
        }
        // Add more types in future if needed.
        
        $entityManager->persist($notification);
        $entityManager->flush();

        return $this->json([
            'message' => 'Success'
        ], 200 );
    }
}

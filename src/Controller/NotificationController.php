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
}

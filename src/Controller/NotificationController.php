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
        $followInvitation = $notificationRepo->getFollowInvitation((int) $params['from_user']['id'], (int) $params['to_user']['id']);
        
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

        $entityManager->flush();

        return $this->json([
            'message' => 'Successful.',
        ], 200);
    }
}

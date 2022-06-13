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
     * @Route("/api/notification", methods={"POST"})
     */
    public function store(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $params = $request->request->all();
        $type = $params['action_type'];

        $notificationRepo = $entityManager->getRepository(Notification::class);

        if ($type === 'follow_request') {
        
            if(!$notificationRepo->followInvitationExists($params['from_user']['id'], $params['to_user']['id'])) {

                $notification = NotificationGenerator::generateFollowRequest($params);
                $notificationRepo->add($notification, true);
            }
            
            // Skip if notification already exists.
        }

        return $this->json([
            'message' => 'Successful.',
        ], 200);
    }
}

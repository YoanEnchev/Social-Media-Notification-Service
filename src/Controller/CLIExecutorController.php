<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Notification;
use App\Service\SystemMessagesSender;

// Methods of this controller are called through the cli of the main app.
class CLIExecutorController extends AbstractController
{
    /**
     * @Route("/api/cli/send-system-message", methods={"POST"})
     */
    public function sendSystemMessage(Request $request, SystemMessagesSender $systemMessageSender): JsonResponse
    {
        $systemMessageSender->send($request->get('message_text'), $request->get('users_ids'));
    
        return $this->json([
            'message' => 'Successful.',
        ], 200);
    }

    /**
     * @Route("/api/cli/mark-as-read", methods={"POST"})
     */
    public function readAll(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Successful.',
        ], 200);
    }
}
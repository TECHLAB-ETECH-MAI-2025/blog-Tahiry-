<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/chat', name: 'api_chat_')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ChatController extends AbstractController
{
    #[Route('/send/{receiverId}', name: 'send', methods: ['POST'])]
    public function sendMessage(
        int $receiverId,
        Request $request,
        EntityManagerInterface $em,
        HubInterface $hub
    ): JsonResponse {
        $sender = $this->getUser();
        $receiver = $em->getRepository(User::class)->find($receiverId);
        if (!$receiver) {
            return $this->json(['error' => 'Receiver not found'], 404);
        }

        $content = $request->request->get('content');
        if (!$content) {
            return $this->json(['error' => 'Message vide'], 400);
        }

        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setContent($content);
        $message->setCreatedAt(new \DateTime());
        $message->setIsRead(false);
        $em->persist($message);
        $em->flush();

        $data = [
            'sender_id' => $sender->getId(),
            'receiver_id' => $receiver->getId(),
            'content' => $message->getContent(),
            'createdAt' => $message->getCreatedAt()->format('H:i')
        ];

       $update = new Update(
        sprintf('/chat/%d', $receiver->getId()),
         json_encode($data),
            true // <= rends ce topic public
            );

        $hub->publish($update);

        return $this->json(['status' => 'Message envoyé']);
    }
}

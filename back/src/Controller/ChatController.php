<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageTypeForm;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'chat_home')]
    public function chatHome(EntityManagerInterface $em, MessageRepository $messageRepo): Response
    {
        $currentUser = $this->getUser();
        $discussions = $messageRepo->findDiscussions($currentUser);
        $otherUsers = $em->getRepository(User::class)->createQueryBuilder('u')
            ->where('u != :me')
            ->setParameter('me', $currentUser)
            ->getQuery()
            ->getResult();

        return $this->render('chat/index.html.twig', [
            'discussions' => $discussions,
            'otherUsers' => $otherUsers
        ]);
    }

    #[Route('/chat/messages/{receiverId}', name: 'chat_messages')]
    public function chatMessages(int $receiverId, EntityManagerInterface $em, MessageRepository $messageRepo): Response
    {
        $sender = $this->getUser();
        $receiver = $em->getRepository(User::class)->find($receiverId);

        if (!$receiver) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $messages = $messageRepo->findConversation($sender->getId(), $receiver->getId());

        foreach ($messages as $msg) {
            if ($msg->getReceiver() === $sender && !$msg->isRead()) {
                $msg->setIsRead(true);
            }
        }
        $em->flush();

        return $this->render('chat/_messages.html.twig', [
            'messages' => $messages,
        ]);
    }
  

    #[Route('/chat/conversation/{receiverId}', name: 'chat_conversation')]
    public function conversation(int $receiverId, Request $request, EntityManagerInterface $em, MessageRepository $messageRepo): Response
    {
        $sender = $this->getUser();
        $receiver = $em->getRepository(User::class)->find($receiverId);

        if (!$receiver) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $messages = $messageRepo->findConversation($sender->getId(), $receiver->getId());

        foreach ($messages as $msg) {
            if ($msg->getReceiver() === $sender && !$msg->isRead()) {
                $msg->setIsRead(true);
            }
        }
        $em->flush();

        $message = new Message();
        $form = $this->createForm(MessageTypeForm::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($sender);
            $message->setReceiver($receiver);
            $message->setCreatedAt(new \DateTime());
            $em->persist($message);
            $em->flush();
        }

        $html = $this->renderView('chat/_conversation.html.twig', [
            'receiver' => $receiver,
            'messages' => $messageRepo->findConversation($sender->getId(), $receiverId),
            'form' => $form->createView(),
        ]);

        return new Response($html);
    }
}

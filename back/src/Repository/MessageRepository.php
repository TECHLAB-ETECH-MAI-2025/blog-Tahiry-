<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Message>
 */

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findConversation(int $userAId, int $userBId): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :a AND m.receiver = :b) OR (m.sender = :b AND m.receiver = :a)')
            ->setParameter('a', $userAId)
            ->setParameter('b', $userBId)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getConversation(User $user1, User $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :u1 AND m.receiver = :u2) OR (m.sender = :u2 AND m.receiver = :u1)')
            ->setParameter('u1', $user1)
            ->setParameter('u2', $user2)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }


   public function findDiscussions(User $user): array
    {
        $qb = $this->createQueryBuilder('m')
            ->select('IDENTITY(m.sender) AS senderId, IDENTITY(m.receiver) AS receiverId, MAX(m.createdAt) as lastMessage')
            ->where('m.sender = :user OR m.receiver = :user')
            ->groupBy('senderId, receiverId')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $seen = [];
        $discussions = [];

        foreach ($qb as $row) {
            $senderId = $row['senderId'];
            $receiverId = $row['receiverId'];

            $otherUserId = ($senderId == $user->getId()) ? $receiverId : $senderId;

            if (in_array($otherUserId, $seen)) {
                continue; 
            }
            $seen[] = $otherUserId;

            $lastMessage = $this->createQueryBuilder('m')
                ->where('(m.sender = :user AND m.receiver = :other) OR (m.sender = :other AND m.receiver = :user)')
                ->setParameter('user', $user->getId())
                ->setParameter('other', $otherUserId)
                ->orderBy('m.createdAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $unread = $this->createQueryBuilder('m')
                ->select('count(m.id)')
                ->where('m.receiver = :me AND m.sender = :other AND m.isRead = false')
                ->setParameter('me', $user)
                ->setParameter('other', $otherUserId)
                ->getQuery()
                ->getSingleScalarResult();

            $discussions[] = [
                'sender' => $lastMessage->getSender(),
                'receiver' => $lastMessage->getReceiver(),
                'lastMessage' => $lastMessage,
                'unread' => $unread
            ];
        }

        return $discussions;
    }

}

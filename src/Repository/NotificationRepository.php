<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function add(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFollowInvitation(int $fromUserId, int $toUserId): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->where("n.fromUser = :from_user")->setParameter('from_user', $fromUserId)
            ->andWhere("n.toUser = :to_user")->setParameter('to_user', $toUserId)
            ->andWhere("n.status = " . Notification::STATUS_REQUEST)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
    * @return Notification[] Returns an array of Notification objects
    */
    public function getUnseenNotificationsForUser(int $userId)
    {
        return $this->createQueryBuilder('n')
            ->where("n.toUser = :to_user")->setParameter('to_user', $userId)
            ->andWhere("n.readOn IS NULL")
            ->getQuery()
            ->getResult();
    }

    public function markAsReadMessagesForUser(int $userId)
    {
        $date = new DateTime();

        $this->createQueryBuilder('n')
        ->update()
        ->where('n.toUser = :userId')->setParameter('userId', $userId)
        ->andWhere('n.status = :whereStatus')->setParameter('whereStatus', Notification::STATUS_UNREAD)
        ->set('n.readOn', ':readOn')->setParameter('readOn', $date)
        ->set('n.status', ':status')->setParameter('status', Notification::STATUS_READ)
        ->getQuery()
        ->execute();
    }

    public function remove(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Notification[] Returns an array of Notification objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Notification
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

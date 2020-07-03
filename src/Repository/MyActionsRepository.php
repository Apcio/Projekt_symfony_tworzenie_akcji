<?php

namespace App\Repository;

use App\Entity\MyActions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MyActions|null find($id, $lockMode = null, $lockVersion = null)
 * @method MyActions|null findOneBy(array $criteria, array $orderBy = null)
 * @method MyActions[]    findAll()
 * @method MyActions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MyActionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MyActions::class);
    }

    public function getShortList() {
        $res = $this->createQueryBuilder('m')
                ->select('m.title, m.created_at, m.id')
                ->orderBy('m.title', 'ASC')
                ->getQuery();
        return $res->getArrayResult();
    }
}

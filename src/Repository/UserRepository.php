<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllUsers()
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->select('u');
        return $qb->getQuery()->getArrayResult();
    }

    public function findById($id)
    {
        $qb = $this
            ->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id);
        return $qb->getQuery();
    }
}
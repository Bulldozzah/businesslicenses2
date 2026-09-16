<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BusinessLocationRepository extends EntityRepository
{
    /**
     * Find all Business Location not deleted
     * 
     * @return void
     */
    public function findAllBusinessLocation()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->getQuery()
            ->execute();
    }
    /**
     * Find all Business Location not deleted
     * 
     * @return void
     */
    public function findAllBusinessLocationCount()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.deleted', 0),
            $expr->eq('li.status', 1)
        );

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.licenses', 'li')
            ->where($andX)
            ->getQuery()
            ->execute();
    }
}
<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BusinessTypeRepository extends EntityRepository
{
    /**
     * Find all Business Type not deleted
     *
     * @return void
     */
    public function findAllBusinessType()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bt.deleted', 0)
        );

        return $this->createQueryBuilder('bt')
            ->where($andX)
            ->getQuery()
            ->execute();
    }
    /**
     * Count all Business Type not deleted
     *
     * @return void
     */
    public function findAllBusinessTypeCount()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bt.deleted', 0),
            $expr->eq('l.status', 1),
            $expr->eq('l.deleted', 0)
        );

        return $this->createQueryBuilder('bt')
            ->leftJoin('bt.activities', 'u')
            ->leftJoin('u.licenses', 'l')
            ->where($andX)
            ->getQuery()
            ->execute();
    }
}

<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BusinessLocationCategoryRepository extends EntityRepository
{
    /**
     * Find all Business Location Categories with Data only
     *
     * @return void
     */
    public function findAllBusinessLocationCategory()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.deleted', 0),
            $expr->eq('l.deleted', 0),
            $expr->eq('l.status', 1)
        );

        return $this->createQueryBuilder('blc')
            ->leftJoin('blc.location', 'bl')
            ->leftJoin('bl.licenses', 'l')
            ->where($andX)
            ->getQuery()
            ->execute();
    }
}

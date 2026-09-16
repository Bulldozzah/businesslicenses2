<?php

namespace WebmastersAfrica\PressBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BannerRepository extends EntityRepository
{
    /**
     * Find all Banners not deleted
     * 
     * @return void
     */
    public function findAllBanners()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bn.deleted', 0)
        );

        return $this->createQueryBuilder('bn')
            ->where($andX)
            ->getQuery()
            ->execute();
    }
}
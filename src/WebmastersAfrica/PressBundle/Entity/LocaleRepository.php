<?php

namespace WebmastersAfrica\PressBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LocaleRepository extends EntityRepository
{
    /**
     * Find all Locale default is 1
     * 
     * @param int $default
     * 
     * @return void
     */
    public function findByLocale($default)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('l.is_default', ':default')
        );

        return $this->createQueryBuilder('l')
            ->where($andX)
            ->setParameter('default', $default)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
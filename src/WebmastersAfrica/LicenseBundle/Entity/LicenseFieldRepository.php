<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LicenseFieldRepository extends EntityRepository
{
    /**
     * Find all Business Location not deleted
     * 
     * @return void
     */
    public function findFieldData($showed = true)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.showed', ':showed'),
            $expr->isNotNull('fd.fielddata'),
            $expr->neq('fd.fielddata', ':empty')
        );

        return $this->createQueryBuilder('p')
            ->leftJoin('p.fielddatas', 'fd')
            ->where($andX)
            ->setParameters(['showed' => $showed, 'empty' => ''])
            ->orderBy('p.fieldlabel', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LicenseFieldDataRepository extends EntityRepository
{
    /**
     * Find all Business Location not deleted
     * 
     * @return void
     */
    public function findFieldData($id)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.license_id', ':license_id'),
            $expr->isNotNull('p.fielddata'),
            $expr->neq('p.fielddata', ':empty')
        );

        return $this->createQueryBuilder('p')
            ->where($andX)
            ->setParameters(['license_id' => $id, 'empty' => ''])
            ->getQuery()
            ->getResult();
    }
}

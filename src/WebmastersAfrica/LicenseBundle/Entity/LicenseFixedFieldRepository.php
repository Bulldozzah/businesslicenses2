<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LicenseFixedFieldRepository extends EntityRepository
{
    /**
     * Find all Business Location not deleted
     * 
     * @return void
     */
    public function findFixedFields($show = true)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.showed', ':showed')
        );

        return $this->createQueryBuilder('p')
            ->where($andX)
            ->setParameters(['showed' => $show])
            ->orderBy('p.fieldlabel', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RegulationFieldRepository extends EntityRepository
{
    /**
     * Find all Business Location not deleted
     *
     * @return void
     */
    public function findFieldData($showed, $regulation)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.showed', ':showed'),
            $expr->eq('fd.regulation', ':regulation'),
            $expr->isNotNull('fd.fielddata')
        );

        return $this->createQueryBuilder('p')
            ->leftJoin('p.fielddatas', 'fd')
            ->where($andX)
            ->setParameters(['showed' => (int)$showed, 'regulation' => $regulation])
            ->orderBy('p.fieldlabel', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RegulationFieldDataRepository extends EntityRepository
{
    /**
     * Find all Field Data with field data empty
     *
     * @return void
     */
    public function findFieldData($id)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.regulation_id', ':regulation_id'),
            $expr->isNotNull('p.fielddata')
        );

        return $this->createQueryBuilder('p')
            ->where($andX)
            ->setParameters(['regulation_id' => $id])
            ->getQuery()
            ->getResult();
    }
}

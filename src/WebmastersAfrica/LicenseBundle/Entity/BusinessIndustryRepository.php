<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BusinessIndustryRepository extends EntityRepository
{
    /**
     * Find all Business Industry not deleted
     *
     * @return void
     */
    public function findAllActiveBusinessIndustry()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bi.deleted', 0)
        );

        return $this->createQueryBuilder('bi')
            ->where($andX)
            ->orderBy('bi.name', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function getAllIndustriesWithLicenses()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bi.deleted', 0),
            $expr->eq('bl.deleted', 0),
            $expr->eq('bl.status', 1)
        );

        return $this->createQueryBuilder('bi')
            ->innerJoin('bi.businesstypes', 'b')
            ->innerJoin('b.activities', 'a')
            ->innerJoin('a.licenses', 'bl')
            ->where($andX)
            ->orderBy('bi.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getIndustriesWithMoreThan10Regulations()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bi.deleted', 0),
            $expr->eq('r.published', 1),
            $expr->eq('r.isPublic', 1),
            $expr->eq('r.deleted', 0)
        );

        return $this->createQueryBuilder('bi')
            ->leftJoin('bi.regulation', 'r')
            ->groupBy('r.id')
            ->having($expr->gte('COUNT(r.id)', '1'))
            ->where($andX)
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }
    
    public function getIndustriesWithRegulations()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bi.deleted', 0),
            $expr->eq('r.published', 1),
            $expr->eq('r.isPublic', 1),
            $expr->eq('r.deleted', 0)
        );

        return $this->createQueryBuilder('bi')
            ->leftJoin('bi.regulation', 'r')
            ->where($andX)
            ->orderBy('bi.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

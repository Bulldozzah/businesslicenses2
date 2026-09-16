<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BusinessAgencyRepository extends EntityRepository
{
    /**
     * Find all Business Industry not deleted
     *
     * @return void
     */
    public function getActiveAgencies()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bi.deleted', 0)
        );

        return $this->createQueryBuilder('bi')
            ->where($andX)
            ->orderBy('bi.title', 'ASC')
            ->getQuery()
            ->execute();
    }

    public function filterAgencyByNameSearch($agency)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('ba.deleted', 0)
        );
        $orX = $expr->orX(
            $expr->like("ba.title", ":title"),
            $expr->like("ba.name", ":name"),
            $expr->like("ba.acronym", ":acronym")
        );
        $andX->add($orX);
        $params['title'] = "%" . $agency . "%";
        $params['name'] = "%" . $agency . "%";
        $params['acronym'] = "%" . $agency . "%";
        return $this->createQueryBuilder('ba')
                ->where($andX)
                ->orderBy('ba.title', 'ASC')
                ->setParameters($params)
                ->getQuery()
                ->getResult();
    }

    public function getAgenciesWithForwardPlansOnly()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('ba.deleted', 0),
            $expr->eq('fp.deleted', 0),
            $expr->eq('fp.published', true),
            $expr->eq('fpr.published', true)
        );
        return $this->createQueryBuilder('ba')
                ->leftJoin('ba.forwardPlans', 'fp')
                ->leftJoin('fp.forwardPlans', 'fpr')
                ->where($andX)
                ->orderBy('ba.title', 'ASC')
                ->getQuery()
                ->getResult();
    }

    public function getAgenciesWithLicensesOnly()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('ba.deleted', 0),
            $expr->eq('bl.deleted', 0),
            $expr->eq('bl.status', 1)
        );
        return $this->createQueryBuilder('ba')
                ->leftJoin('ba.licenses', 'bl')
                ->where($andX)
                ->orderBy('ba.title', 'ASC')
                ->getQuery()
                ->getResult();
    }
    public function getAgencyWithPublishedForwardPlanRegulations($agency)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('ba.deleted', 0),
            $expr->eq('fp.deleted', 0),
            $expr->eq('fp.published', true),
            $expr->eq('fpr.published', true),
            $expr->like('ba.slug', ":agency")
        );
        return $this->createQueryBuilder('ba')
                ->leftJoin('ba.forwardPlans', 'fp')
                ->leftJoin('fp.forwardPlans', 'fpr')
                ->where($andX)
                ->setParameter('agency', $agency)
                ->orderBy('ba.title', 'ASC')
                ->getQuery()
                ->getResult();
    }

    public function getAgenciesWithRegulations()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('ba.deleted', 0),
            $expr->eq('r.deleted', 0),
            $expr->eq('r.isPublic', 1),
            $expr->eq('r.published', 1)
        );
        return $this->createQueryBuilder('ba')
        		->leftJoin('ba.regulations', 'r')
            ->where($andX)
            ->orderBy('ba.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
    /**
     * Find all Business Industry not deleted
     *
     * @return void
     */
    public function getActiveAgenciesWithId0()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bi.deleted', 0),
            $expr->eq('bi.id', 0)
        );

        return $this->createQueryBuilder('bi')
            ->where($andX)
            ->orderBy('bi.name', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function filterAgencyByTitle($character)
    {
        $expr = $this->_em->createQueryBuilder()->expr();

        $andX = $expr->andX(
            $expr->eq('ba.deleted', 0),
            $expr->like('ba.title', ":character"),
            $expr->eq('fp.published', true),
            $expr->eq('fpr.published', true)
        );

        return $this->createQueryBuilder('ba')
            ->leftJoin('ba.forwardPlans', 'fp')
            ->leftJoin('fp.forwardPlans', 'fpr')
            ->where($andX)
            ->orderBy('ba.title', 'ASC')
            ->getQuery()
            ->setParameter('character', "" . strtolower($character) . "%")
            ->execute();
    }
}

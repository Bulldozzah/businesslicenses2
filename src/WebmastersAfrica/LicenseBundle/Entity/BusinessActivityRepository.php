<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Business Activity Repository
 *
 * Custom Repository
 */
class BusinessActivityRepository extends EntityRepository
{
    /**
     * Filter Business License based on
     *
     * @param string $status        // published
     * @param string $business_type // type of business
     * @param string $location      // current location

     * @return void
     */
    public function filterBusinessActivity($status, $business_type, $location)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('g.deleted', ':deleted'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted')
        );

        if (!empty($business_type) || $business_type != 0) {
            $andX->add($expr->eq('g.id', ":business_type"));
            $params['business_type'] = $business_type;
        }

        if (!empty($location) || $business_type != 0) {
            $andX->add($expr->eq('l.location', ":location"));
            $params['location'] = $location;
        }
        $params['status'] = $status;
        $params['deleted'] = 0;


        $qb = $this->createQueryBuilder('u')
            ->innerJoin('u.businesstypes', 'g')
            ->innerJoin('u.licenses', 'l')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC')
            ->getQuery();
        return $qb->execute();
    }

    public function filterBusinessActivityByBusinessType($business_type)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('g.deleted', ':deleted')
        );
        $andX->add($expr->eq('g.id', ':business_type'));
        $params['deleted'] = 0;
        $params['business_type'] = $business_type;

        $qb = $this->createQueryBuilder('u')
            ->innerJoin('u.businesstypes', 'g')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC')
            ->getQuery();
        return $qb->getResult();
    }

    public function filterBusinessActivityByLocation($location)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted')
        );
        $andX->add($expr->eq('l.location_id', ':location'));
        $params['status'] = 'published';
        $params['deleted'] = 0;
        $params['location'] = $location;

        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.licenses', 'l')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function filterBusinessActivityByIndustry($industry)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('i.deleted', ':deleted')
        );
        $andX->add($expr->eq('i.id', ':industry'));
        $params['deleted'] = 0;
        $params['industry'] = $industry;

        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->leftJoin('u.businesstypes', 'l')
            ->leftJoin('l.industries', 'i')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findAllBusinessActivity()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('u.deleted', ':deleted')
        );

        return $this->createQueryBuilder('u')
            ->where($andX)
            ->setParameter('deleted', 0)
            ->getQuery()
            ->getResult();
    }

    public function getBusinessActivitiesWithLicenses()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', 0),
            $expr->eq('l.status', 1)
        );

        return $this->createQueryBuilder('u')
            ->leftJoin('u.licenses', 'l')
            ->where($andX)
            ->setParameter('deleted', 0)
            ->getQuery()
            ->getResult();
    }
}

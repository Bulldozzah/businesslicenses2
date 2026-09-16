<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BusinessLicenseRepository extends EntityRepository
{

    /**
     * Get 8 Business Licenses
     *
     * @return void
     */
    public function findAllOrderByViews()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->neq('bl.name', ':empty'),
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.deleted', 0),
            $expr->isNotNull('ag.id'),
            $expr->eq('l.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->leftJoin('bl.location', 'l')
            ->leftJoin('bl.agency', 'ag')
            ->setFirstResult(0)
            ->setMaxResults(4)
            ->getQuery()
            ->setParameters(
                [
                    'empty' => '',
                    'pub' => 1
                ]
            )
            ->execute();
    }

    public function findStage()
    {
        return $this->createQueryBuilder('bl')
            ->addSelect('w')
            ->join('bl.stage', 'w')
            ->getQuery()
            ->execute();
    }

    public function getBusinessLicenseInMyAgency($stage)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.id', ':stage'),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.stage', 'w')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'stage' => $stage
                ]
            )
            ->execute();
    }
    public function getBusinessLicensesInMyAgencies($agencies)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->in('ba.id', ':agencies'),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.agency', 'ba')
            ->where($andX)
            ->orderBy('bl.id', 'ASC')
            ->getQuery()
            ->setParameters(
                [
                    'agencies' => $agencies
                ]
            )
            ->execute();
    }
    public function getUnpublishedBusinessLicensesInMyAgencies($agencies)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->in('ba.id', ':agencies'),
            $expr->eq('bl.status', 4),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.agency', 'ba')
            ->where($andX)
            ->orderBy('bl.id', 'ASC')
            ->getQuery()
            ->setParameters(
                [
                    'agencies' => $agencies
                ]
            )
            ->execute();
    }
    // should be workflow not in my agency all
    public function getBusinessLicenseInMyAgencyAll($stage, $agency = [])
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.id', ':stage'),
            $expr->eq('bl.deleted', 0)
        );

        if ($agency) {
            $andAgency = $expr->andX(
                $expr->in('bl.agency', $agency)
            );
            $andX->add($andAgency);
        }

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.stage', 'w')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameter('stage', $stage)
            ->execute();
    }

    /**
     * Get all published business licenses
     *
     * @return void
     */
    public function findAllPublished()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 1
                ]
            )
            ->execute();
    }

    /**
     * Get all published business licenses for agency
     *
     * @return void
     */
    public function findAllPublishedLicensesForAgency($agency_list)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.status', ':pub'),
            $expr->in('bl.agency_id', $agency_list),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 1
                ]
            )
            ->execute();
    }

    public function getLicenseOnlyInMyAgencyList($license_id, $agency_list)
    {
        $expr = $this->_em->createQueryBuilder()->expr();

        $andX = $expr->andX(
            $expr->in('bl.agency', ':agency'),
            $expr->eq('bl.id', ":license_id"),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->setParameters(
                [
                    'license_id' => $license_id,
                    'agency' => $agency_list
                ]
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all the published Business Licenses for my agency
     *
     * @return void
     */
    public function findMyLicenses($agency, $id)
    {
        $expr = $this->_em->createQueryBuilder()->expr();

        $andX = $expr->andX(
            $expr->eq('bl.deleted', ':empty'),
            $expr->eq('bl.status', ':pub'),
            $expr->in('bl.agency_id', ':agency'),
            $expr->eq('bl.stage', ':stage'),
            $expr->eq('l.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.location', 'l')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->setParameters(
                [
                    'empty' => 0,
                    'pub' => 1,
                    'agency' => $agency,
                    'stage' => $id
                ]
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all unpublished business licenses
     *
     * @return void
     */
    public function findAllUnPublished()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 4
                ]
            )
            ->execute();
    }

    /**
     * Get all unpublished business licenses
     *
     * @return void
     */
    public function findAllUnpublishedLicensesForAgency($agency_list)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.status', ':pub'),
            $expr->in('bl.agency_id', $agency_list),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 4
                ]
            )
            ->execute();
    }


    /**
     * Get all license in review
     *
     * @return void
     */
    public function findAllApplicationInReview()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->orX(
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.deleted', 0),
            $expr->eq('bl.status', ':pub_2')
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 3,
                    'pub_2' => 5
                ]
            )
            ->execute();
    }

    /**
     * Get all license in review in agency
     *
     * @return void
     */
    public function findAllApplicationInReviewForAgency($agency_list)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->in('bl.agency_id', $agency_list),
            $expr->eq('bl.deleted', 0)
        );
        $orX = $expr->orX(
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.status', ':pub_2')
        );

        $andX->add($orX);


        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 3,
                    'pub_2' => 5
                ]
            )->getResult();
    }

    /**
     * Get all license saved as draft
     *
     * @return void
     */
    public function findAllSavedAsDraft()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 2
                ]
            )
            ->execute();
    }

    /**
     * Get all license saved as draft for agency
     *
     * @return void
     */
    public function findAllSavedAsDraftForAgency($agency_list)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.status', ':pub'),
            $expr->in('bl.agency_id', $agency_list),
            $expr->eq('bl.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 2
                ]
            )
            ->execute();
    }

    /**
     * Find Distinct license keywords
     *
     * @return void
     */
    public function findDistinctLicenceKeywords()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->neq('bl.name', ':empty'),
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.deleted', 0),
            $expr->eq('l.deleted', 0),
            $expr->isNotNull('bl.agency')
        );

        return $this->createQueryBuilder('bl')
            ->distinct('bl.keywords')
            ->leftJoin('bl.location', 'l')
            ->where($andX)
            ->getQuery()
            ->setParameters(
                [
                    'empty' => 0,
                    'pub' => 1
                ]
            )
            ->execute();
    }

    public function findPublishedLicensesByIndustry($industry)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('i.id', ':industry'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['industry'] = $industry;


        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function findPublishedLicensesByBusinessType($businesstype)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('b.id', ':businesstype'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['businesstype'] = $businesstype;


        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findPublishedLicensesByBusinessActivity($activities)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->in('u.id', ':activities'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['activities'] = $activities;


        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findPublishedLicensesByLocationBusinessActivity($location, $activities)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->in('u.id', ':activities'));
        $andX->add($expr->eq('lo.id', ':location'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['activities'] = $activities;
        $params['location'] = $location;


        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findPublishedLicensesByIndustryBusinessActivity($industry, $activities)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->in('u.id', ':activities'));
        $andX->add($expr->eq('i.id', ':industry'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['activities'] = $activities;
        $params['industry'] = $industry;


        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('l.location', 'lo')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function findPublishedLicensesByLocationBusinessType($location, $businesstype)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('lo.id', ':location'));
        $andX->add($expr->eq('b.id', ':businesstype'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['location'] = $location;
        $params['businesstype'] = $businesstype;


        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('l.location', 'lo')
            ->leftJoin('u.businesstypes', 'b')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function findPublishedLicensesByBusinessTypeBusinessActivity($businesstype, $activities)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->in('u.id', ':activities'));
        $andX->add($expr->eq('b.id', ':businesstype'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['activities'] = $activities;
        $params['businesstype'] = $businesstype;


        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('l.location', 'lo')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }


    public function findPublishedLicensesByIndustryLocation($industry, $location)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('i.id', ':industry'));
        $andX->add($expr->eq('lo.id', ':location'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['industry'] = $industry;
        $params['location'] = $location;

        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function findPublishedByLicenseByIndustryBusinessTypes($industry, $businesstypes)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('i.id', ':industry'));
        $andX->add($expr->eq('b.id', ':businesstypes'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['industry'] = $industry;
        $params['businesstypes'] = $businesstypes;

        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findPublishedByLicenseByBusinessTypes($businesstypes)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('b.id', ':businesstypes'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['businesstypes'] = $businesstypes;

        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findPublishedLicenseByIndustryLocationBusinessType($industry, $location, $businesstypes)
    {

        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('i.id', ':industry'));
        $andX->add($expr->eq('b.id', ':businesstypes'));
        $andX->add($expr->eq('lo.id', ':location'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['industry'] = $industry;
        $params['location'] = $location;
        $params['businesstypes'] = $businesstypes;

        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findPublishedLicenseByIndustryLocationBusinessTypeBusinessActivity($industry, $location, $businesstypes, $businessactivity)
    {

        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('i.id', ':industry'));
        $andX->add($expr->eq('b.id', ':businesstypes'));
        $andX->add($expr->eq('lo.id', ':location'));
        $andX->add($expr->in('u.id', ':activity'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['industry'] = $industry;
        $params['location'] = $location;
        $params['businesstypes'] = $businesstypes;
        $params['activity'] = $businessactivity;

        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function findPublishedLicenseByLocationBusinessTypeBusinessActivity($location, $businesstypes, $businessactivity)
    {

        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('b.id', ':businesstypes'));
        $andX->add($expr->eq('lo.id', ':location'));
        $andX->add($expr->in('u.id', ':activity'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['location'] = $location;
        $params['businesstypes'] = $businesstypes;
        $params['activity'] = $businessactivity;

        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findPublishedLicenseByIndustryBusinessTypeBusinessActivity($industry, $businesstypes, $businessactivity)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency'),
            $expr->eq('lo.deleted', 0)
        );
        $andX->add($expr->eq('i.id', ':industry'));
        $andX->add($expr->eq('b.id', ':businesstypes'));
        $andX->add($expr->in('u.id', ':activity'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['industry'] = $industry;
        $params['businesstypes'] = $businesstypes;
        $params['activity'] = $businessactivity;

        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
    public function findPublishedLicensesByLocationIndustryBusinessActivity($location, $industry, $businessactivity)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency'),
            $expr->eq('lo.deleted', 0)
        );
        $andX->add($expr->eq('i.id', ':industry'));
        $andX->add($expr->eq('lo.id', ':location'));
        $andX->add($expr->in('u.id', ':activity'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['industry'] = $industry;
        $params['location'] = $location;
        $params['activity'] = $businessactivity;

        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('u.businesstypes', 'b')
            ->leftJoin('b.industries', 'i')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * Get all published license per location
     *
     * @param string $location // location
     *
     * @return void
     */
    public function findPublishedByLocation($location)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.deleted', 0),
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.location', ':location'),
            $expr->eq('l.deleted', 0),
            $expr->isNotNull('bl.agency')
        );

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.location', 'l')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 1,
                    'location' => $location
                ]
            )
            ->execute();
    }
    /**
     * Get all published license per location
     *
     * @param string $location // location
     *
     * @return void
     */
    public function findPublishedByLocationCategory($location)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.deleted', 0),
            $expr->eq('bl.status', ':pub'),
            $expr->eq('c.id', ':location'),
            $expr->eq('l.deleted', 0),
            $expr->isNotNull('bl.agency')
        );

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.location', 'l')
            ->leftJoin('l.parentLocation', 'c')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'pub' => 1,
                    'location' => $location
                ]
            )
            ->execute();
    }

    /**
     * search by keywords
     *
     * @param string $location
     * @param string $search_term
     *
     * @return void
     */
    public function advancedSearch($activities = null, $location = null, $search_term = null)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $params = [
            'published' => 1
        ];
        $andX = $andX = $expr->andX(
            $expr->eq('l.status', ':published'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        if (!is_null($search_term)) {
            $orX = $expr->orX(
                $expr->like('l.name', ':search_term'),
                $expr->like('l.description', ':search_term'),
                $expr->like('l.keywords', ':search_term')
            );
            $params['search_term'] = "%" . $search_term . "%";
            $andX->add($orX);
        }



        if (!is_null($activities)) {
            $andX->add($expr->in('b.id', ':activities'));
            $params['activities'] = $activities['ids'];
        }

        if (!is_null($location)) {
            $orX = $expr->orX($expr->eq('l.universal', ':universal'));
            $orX->add($expr->eq('l.location_id', ':location'));
            $params['universal'] = 1;
            $params['location'] = $location;
            $andX->add($orX);
        }

        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.activities', 'b')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->orderBy('l.name', 'ASC')
            ->setParameters($params);
        return $qb->getQuery()->getResult();
    }

    public function advancedKeywordsSearch($search_term)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('bl.status', ':published')
        );

        $orX = $expr->orX(
            $expr->like('bl.name', ":name"),
            $expr->like('bl.description', ":description"),
            $expr->like('bl.keywords', ":keywords"),
            $expr->like('bl.purpose', ":purpose"),
            $expr->like('bl.requirements', ":requirements")
        );

        $params['name'] = "%" . $search_term . "%";
        $params['description'] = "%" . $search_term . "%";
        $params['keywords'] = "%" . $search_term . "%";
        $params['purpose'] = "%" . $search_term . "%";
        $params['requirements'] = "%" . $search_term . "%";
        $params['published'] = 1;

        $andX->add($orX);

        $qb = $this->createQueryBuilder('bl')
            ->leftJoin('bl.agency', 'a')
            ->leftJoin('bl.activities', 'ac')
            ->leftJoin('ac.businesstypes', 'bt')
            ->leftJoin('bt.industries', 'i')
            ->leftJoin('bl.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('bl.name');
        return $qb->getQuery()->getResult();
    }


    public function filterBusinessActivityByLocation($location)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];
        $andX = $expr->andX(
            $expr->eq('l.status', ':status'),
            $expr->eq('u.deleted', ':deleted'),
            $expr->eq('l.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->isNotNull('l.agency')
        );
        $andX->add($expr->eq('l.location_id', ':location'));
        $params['status'] = 1;
        $params['deleted'] = 0;
        $params['location'] = $location;

        $qb = $this->createQueryBuilder('l')
            ->select('u')
            ->leftJoin('l.activities', 'u')
            ->leftJoin('l.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('u.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getDashLicensesWithAgencies()
    {
        $expr = $this->_em->createQueryBuilder()->expr();

        $params = [];
        $params['deleted'] = 0;
        $params['agency_id'] = 0;

        $andX = $expr->andX(
            $expr->eq('bl.deleted', ':deleted'),
            $expr->eq('bl.agency_id', ':agency_id'),
            $expr->eq('lo.deleted', 0)
        );

        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->orderBy('bl.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findLicensesByBusinessActivities($activity)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.deleted', ':deleted'),
            $expr->eq('lo.deleted', 0),
            $expr->in('bl.id', array_values($activity))
        );
        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.location', 'lo')
            ->where($andX)
            ->setParameter('deleted', 0)
            ->orderBy('bl.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLicensesByBusinessActivity($activity)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('bl.deleted', ':deleted'),
            $expr->eq('bl.status', ':status'),
            $expr->in('ba.id', ":activity")
        );
        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.activities', 'ba')
            ->where($andX)
            ->setParameters(['status' => 1, 'deleted' => 0, 'activity' => $activity])
            ->orderBy('bl.id', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function getDashLicensesWithoutAgencies()
    {
        $expr = $this->_em->createQueryBuilder()->expr();

        $andX = $expr->andX(
            $expr->eq('bl.status', ':deleted')
        );

        return $this->createQueryBuilder('bl')
            ->where($andX)
            ->setParameter('deleted', 1)
            ->orderBy('bl.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function getDashLicensesPublishedWithAgencies($published)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];

        $andX = $expr->andX(
            $expr->eq('bl.deleted', ':deleted'),
            $expr->eq('bl.agency_id', ':agency'),
            $expr->eq('lo.deleted', 0)
        );

        if ($published) {
            $andX->add(
                $expr->eq('bl.status', ':status')
            );

            $params['status'] = 1;
        } else {
            $andX->add(
                $expr->neq('bl.status', ':status')
            );

            $params['status'] = 1;
        }

        $params['deleted'] = 0;
        $params['agency'] = 0;
        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->getQuery()
            ->getResult();
    }


    public function getDashLicensesPublishedWithoutAgencies($published)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $params = [];

        $andX = $expr->andX(
            $expr->eq('bl.deleted', ':deleted'),
            $expr->eq('bl.agency_id', ':agency'),
            $expr->eq('lo.deleted', 0)
        );

        if ($published) {
            $andX->add(
                $expr->eq('bl.status', ':status')
            );

            $params['status'] = 1;
        } else {
            $andX->add(
                $expr->neq('bl.status', ':status')
            );

            $params['status'] = 'published';
        }

        $params['deleted'] = 0;
        $params['agency'] = 0;
        return $this->createQueryBuilder('bl')
            ->leftJoin('bl.location', 'lo')
            ->where($andX)
            ->setParameters($params)
            ->getQuery()
            ->getResult();
    }

    public function findLicensesPerWorkflow($id)
    {
        $expr = $this->_em->createQueryBuilder()->expr();

        $andX = $expr->andX(
            $expr->eq('bl.deleted', ':empty'),
            $expr->eq('bl.deleted', ':empty'),
            $expr->eq('bl.status', ':pub'),
            $expr->eq('bl.stage', ':stage')
        );

        return $this->createQueryBuilder('bl')
            > leftJoin('bl.location', 'lo')
            ->where($andX)
            ->orderBy('bl.views', 'DESC')
            ->setParameters(
                [
                    'empty' => 0,
                    'pub' => 1,
                    'stage' => $id
                ]
            )
            ->getQuery()
            ->getResult();
    }
}

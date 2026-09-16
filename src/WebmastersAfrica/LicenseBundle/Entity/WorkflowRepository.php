<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;
// use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;

class WorkflowRepository extends EntityRepository
{
    public function getFirstStage($stages)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->notin('w.id', ":stages")
        );

        return $this->createQueryBuilder('w')
            ->where($andX)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(1)
            ->setParameter("stages", $stages)
            ->getQuery()
            ->getResult();
    }

    public function getStagesForThisUser($user)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        return $this->_em->createQueryBuilder()
            ->addSelect('w.id')
            ->from('WebmastersAfricaLicenseBundle:Workflow', 'w')
            ->leftJoin('w.accessGroups', 'g')
            ->leftJoin('g.users', 'u')
            ->where(
                $expr->andX(
                    $expr->eq('w.deleted', 0),
                    $expr->eq('w.published', 1),
                    $expr->eq('u.id', $user)
                )
            )
            ->orderBy('w.nextStage', 'ASC')->getQuery()->getResult();
    }
    public function findAllWorkflowStageQuery()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', ":published"),
            $expr->eq('w.deleted', 0)
        );

        return $this->createQueryBuilder('w')
            ->where($andX)
            ->orderBy('w.id', 'ASC')
            ->setParameter("published", 1);
    }
    public function getLastStage($stages)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->in('w.id', ":stages")
        );

        return $this->createQueryBuilder('w')
            ->where($andX)
            ->orderBy('w.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter("stages", $stages)
            ->getQuery()
            ->getResult();
    }

    public function getAllStages()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0)
        );

        return $this->createQueryBuilder('w')
            ->where($andX)
            ->orderBy('w.orderLevel', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getStagesInArray($my_stages)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->in('w.id', ":my_stages")
        );

        return $this->createQueryBuilder('w')
            ->where($andX)
            ->orderBy('w.orderLevel', 'DESC')
            ->setParameter('my_stages', $my_stages)
            ->getQuery()
            ->getResult();
    }

    public function getNextStage($current_stage)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->gt('w.orderLevel', ':current_stage')
        );

        return $this->createQueryBuilder('w')
            ->where($andX)
            ->setParameter('current_stage', $current_stage)
            ->orderBy('w.orderLevel', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
    public function getPreviousStage($current_stage)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->lt('w.orderLevel', ':current_stage')
        );

        return $this->createQueryBuilder('w')
            ->where($andX)
            ->setParameter('current_stage', $current_stage)
            ->orderBy('w.orderLevel', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function getTheCurrentStage($current_stage)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->eq('w.id', ':current_stage')
        );

        return $this->createQueryBuilder('w')
            ->where($andX)
            ->setParameter('current_stage', $current_stage)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function getLicensesUnderMyAgency($agency)
    {
        if (!$agency || empty($agency)) {
            return false;
        }
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->in('a.id', $agency)
        );
        return $this->createQueryBuilder('w')
            ->leftJoin('w.licenses', 'l')
            ->leftJoin('l.agency', 'a')
            ->where($andX)
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUsersWorkflow($stage, $my_agencies)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->eq('w.id', ":stage"),
            $expr->in('a.id', ":agencies")
        );
        return $this->createQueryBuilder('w')
            ->leftJoin('w.agency', 'a')
            ->where($andX)
            ->setParameter(["agencies" => $my_agencies, 'stage' => $stage ])
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findAllUserWorkflow($my_agencies)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->in('a.id', ":agencies")
        );
        return $this->createQueryBuilder('w')
            ->leftJoin('w.agency', 'a')
            ->where($andX)
            ->setParameter("agencies", $my_agencies)
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findLicenseWithoutAgency($stage)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->eq('w.id', $stage),
            $expr->eq('l.agency', ":agencies")
        );
        return $this->createQueryBuilder('w')
            ->leftJoin('w.licenses', 'l')
            ->where($andX)
            ->setParameter("agencies", 0)
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDeletedLicensesPerStage($stage)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('w.published', 1),
            $expr->eq('w.deleted', 0),
            $expr->eq('w.id', ":stage"),
            $expr->in('a.id', ":agencies")
        );
        return $this->createQueryBuilder('w')
            ->leftJoin('w.licenses', 'l')
            ->leftJoin('l.agency', 'a')
            ->where($andX)
            ->setParameters(["agencies" => $my_agencies, 'stage' => $stage])
            ->orderBy('w.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

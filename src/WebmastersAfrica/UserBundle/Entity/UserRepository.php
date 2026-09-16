<?php

namespace WebmastersAfrica\UserBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getLockedUsersWithAgencies($locked, $count = true)
    {
        $params = [];
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.locked', ':locked'),
            $expr->eq('a.id', ':id')
        );

        $params['locked'] = $locked;
        $params['id'] = 0;
        $qb = $this->createQueryBuilder('p')
            ->join('p.agencies', 'a')
            ->where($andX);
        if ($count) {
            $qb->setMaxResults(5);
        }
        $result = $qb->setParameters($params)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
        return $result;
    }

    public function getUsersWithAgencies()
    {
        $params = [];
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.locked', ':locked'),
            $expr->eq('a.id', ':id')
        );
        $qb = $this->createQueryBuilder('p')
            ->join('p.agencies', 'a')
            ->where($andX);
        $result = $qb->setParameters($params)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
        return $result;
    }

    public function getLockedUsersWithoutAgencies($locked, $count = true)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.locked', ':locked')
        );

        $qb = $this->createQueryBuilder('p')
            ->where($andX);
        if ($count) {
            $qb->setMaxResults(5);
        }
        $result = $qb->setParameter('locked', $locked)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->getResult();
        return $result;
    }

    public function getAllUsers()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->isNull('u.agency_id')
        );
        return $this->createQueryBuilder('u')
            ->where($andX)
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getAllAdminUsers()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('u.isAdmin', 1),
            $expr->eq('u.enabled', 1)
        );
        return $this->createQueryBuilder('u')
            ->where($andX)
            ->orderBy('u.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getAllAdminUsersOnly()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('u.isAdmin', 1),
            $expr->eq('u.enabled', 1)
        );
        return $this->createQueryBuilder('u')
            ->where($andX)
            ->orderBy('u.id', 'DESC');
    }

    public function getAllAdminUsersAgency()
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('u.isAdmin', 1),
            $expr->eq('u.enabled', 1)
        );
        return $this->createQueryBuilder('u')
            ->where($andX)
            ->leftJoin('u.agencies', 'a')
            ->orderBy('u.id', 'DESC');
    }

    public function searchUsers($search_term)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $orX = $expr->orX(
            $expr->like('us.first_name', ":first_name"),
            $expr->like('us.last_name', ":last_name"),
            $expr->like('us.username', ":username"),
            $expr->like('us.email', ":email"),
            $expr->like('ba.title', ':agency_title'),
            $expr->like('ba.acronym', ':acronym')
        );

        return $this->createQueryBuilder('us')
            ->leftJoin('us.agencies', 'ba')
            ->where($orX)
            ->orderBy('us.id', 'DESC')
            ->getQuery()
            ->setParameters(
                [
                    'first_name' => '%' . $search_term . "%",
                    'last_name' => '%' . $search_term . "%",
                    'username' => '%' . $search_term . "%",
                    'email' => '%' . $search_term . "%",
                    'agency_title' => '%' . $search_term . "%",
                    'acronym' => '%' . $search_term . '%'
                ]
            )->getResult();
    }
}

<?php

namespace WebmastersAfrica\PressBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FaqRespository extends EntityRepository
{
    /**
     * Search in Faqs as per user
     * 
     * @param int $default
     * 
     * @return void
     */
    public function searchFaqAsRequested($q, $site)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('f.published', ':published'),
            $expr->eq('f.site', ':site'),
            $expr->orX(
                $expr->like('f.question', ':question'),
                $expr->like('f.answer', ':answer')
            )
        );

        return $this->createQueryBuilder('f')
        ->where($andX)
        ->orderBy('f.question', 'DESC')
        ->setParameters(
            [
                'question' => "%" . $q . "%",
                'answer' => "%" . $q . "%",
                'published' => 1,
                'site' => $site
            ]
        )
        ->getQuery()
        ->getResult();
    }
}
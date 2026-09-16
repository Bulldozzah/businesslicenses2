<?php

namespace WebmastersAfrica\PressBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PageRepository extends EntityRepository
{
    /**
     * Get all Published Pages
     * 
     * @return void
     */
    public function findAllPublished($site)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.published', 1),
            $expr->eq('p.deleted', 0),
            $expr->isNull('p.parent'),
            $expr->eq('p.site', ':site')
        );

        return $this->createQueryBuilder('p')
            ->where($andX)
            ->orderBy('p.page_order', 'ASC')
            ->setParameter('site', $site)
            ->getQuery()
            ->execute();
    }

    /**
     * Get The Details of the Current Page
     * @param string $title // title of the page
     *
     * @return mixed
     */
    public function findTheCurrentPage($title, $site)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.published', ':published'),
            $expr->like('p.page_title', ':title'),
            $expr->eq('p.site', ":site")
        );

        return $this->createQueryBuilder('p')
        ->where($andX)
        ->setParameters(
            [
                'published' => 1, 'title' => "%" . $title ."%", 'site' => $site
            ]
        )
        ->getQuery()
        ->execute();
    }

}
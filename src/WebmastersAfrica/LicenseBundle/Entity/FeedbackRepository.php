<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class FeedbackRepository extends EntityRepository
{
    /**
     * Find all Business Location not deleted
     *
     * @return void
     */
    public function findUnRepliedAll()
    {
        return $this->findBy(array('replied' => 0), array('id' => 'DESC'));
    }

    public function findRepliedAll()
    {
    	  return $this->findBy(array('replied' => 1), array('id' => 'DESC'));
    }
}
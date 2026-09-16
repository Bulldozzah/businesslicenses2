<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebmastersAfrica\LicenseBundle\Entity\SearchContent
 *
 * @ORM\Table(name="searchcontent")
 * @ORM\Entity
 */
class SearchContent
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer $search_id
     *
     * @ORM\Column(name="search_id", type="integer")
     */
    private $search_id;

    /**
     * @var integer $license_id
     *
     * @ORM\Column(name="license_id", type="integer")
     */
    private $license_id;
	
	/**
	* @ORM\ManyToOne (targetEntity="\WebmastersAfrica\LicenseBundle\Entity\Search", inversedBy="content")
	* @ORM\JoinColumn(name="search_id", referencedColumnName="id")
	*/
	protected $search;
	
	/**
	* @ORM\ManyToOne (targetEntity="\WebmastersAfrica\LicenseBundle\Entity\BusinessLicense", inversedBy="content")
	* @ORM\JoinColumn(name="license_id", referencedColumnName="id")
	*/
	protected $license;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set search_id
     *
     * @param integer $searchId
     */
    public function setSearchId($searchId)
    {
        $this->search_id = $searchId;
    }

    /**
     * Get search_id
     *
     * @return integer 
     */
    public function getSearchId()
    {
        return $this->search_id;
    }

    /**
     * Set license_id
     *
     * @param integer $licenseId
     */
    public function setLicenseId($licenseId)
    {
        $this->license_id = $licenseId;
    }

    /**
     * Get license_id
     *
     * @return integer 
     */
    public function getLicenseId()
    {
        return $this->license_id;
    }

    /**
     * Set search
     *
     * @param WebmastersAfrica\LicenseBundle\Entity\Searches $search
     */
    public function setSearch(\WebmastersAfrica\LicenseBundle\Entity\Search $search)
    {
        $this->search = $search;
    }

    /**
     * Get search
     *
     * @return WebmastersAfrica\LicenseBundle\Entity\Searches 
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set license
     *
     * @param WebmastersAfrica\LicenseBundle\Entity\License $license
     */
    public function setLicense(\WebmastersAfrica\LicenseBundle\Entity\BusinessLicense $license)
    {
        $this->license = $license;
    }

    /**
     * Get license
     *
     * @return WebmastersAfrica\LicenseBundle\Entity\BusinessLicense 
     */
    public function getLicense()
    {
        return $this->license;
    }
}

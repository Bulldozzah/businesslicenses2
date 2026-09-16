<?php

namespace WebmastersAfrica\UserBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="site_setting")
 * @ORM\Entity
 */
class Setting
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Gedmo\Locale
     */
    private $locale;

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="site_title", type="string", length=255)
     */
    private $siteTitle;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="site_description", type="text")
     */
    private $siteDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="site_owner", type="string", length=255)
     */
    private $siteOwner;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="site_address", type="string", length=255)
     */
    private $siteAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="footer", type="text")
     */
    private $footer;

    /**
     * @var string
     *
     * @ORM\Column(name="site_email_title", type="string", length=255)
     */
    private $siteEmailTitle;

    /**
     * @var integer
     *
     * @ORM\Column(name="closing_days", type="integer")
     */
    private $closingDays;

    /**
     * @var string
     *
     * @ORM\Column(name="site_email_address", type="string", length=255)
     */
    private $siteEmailAddress;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @ORM\Column(name="site_keywords", type="text")
     */
    private $siteKeywords;


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
     * Set siteTitle
     *
     * @param string $siteTitle
     * @return Setting
     */
    public function setSiteTitle($siteTitle)
    {
        $this->siteTitle = $siteTitle;
    
        return $this;
    }

    /**
     * Get siteTitle
     *
     * @return string 
     */
    public function getSiteTitle()
    {
        return $this->siteTitle;
    }

    /**
     * Set siteDescription
     *
     * @param string $siteDescription
     * @return Setting
     */
    public function setSiteDescription($siteDescription)
    {
        $this->siteDescription = $siteDescription;
    
        return $this;
    }

    /**
     * Get siteDescription
     *
     * @return string 
     */
    public function getSiteDescription()
    {
        return $this->siteDescription;
    }

    /**
     * Set siteOwner
     *
     * @param string $siteOwner
     * @return Setting
     */
    public function setSiteOwner($siteOwner)
    {
        $this->siteOwner = $siteOwner;
    
        return $this;
    }

    public function setClosingDays($closingDays)
		{
			$this->closingDays = $closingDays;
			return $this;
		}

		public function getClosingDays()
		{
			return $this->closingDays;
		}
    /**
     * Get siteOwner
     *
     * @return string 
     */
    public function getSiteOwner()
    {
        return $this->siteOwner;
    }

    /**
     * Set siteAddress
     *
     * @param string $siteAddress
     * @return Setting
     */
    public function setSiteAddress($siteAddress)
    {
        $this->siteAddress = $siteAddress;
    
        return $this;
    }

    /**
     * Get siteAddress
     *
     * @return string 
     */
    public function getSiteAddress()
    {
        return $this->siteAddress;
    }

    /**
     * Set footer
     *
     * @param string $footer
     * @return Setting
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
    
        return $this;
    }

    /**
     * Get footer
     *
     * @return string 
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set siteEmailTitle
     *
     * @param string $siteEmailTitle
     * @return Setting
     */
    public function setSiteEmailTitle($siteEmailTitle)
    {
        $this->siteEmailTitle = $siteEmailTitle;
    
        return $this;
    }

    /**
     * Get siteEmailTitle
     *
     * @return string 
     */
    public function getSiteEmailTitle()
    {
        return $this->siteEmailTitle;
    }

    /**
     * Set siteEmailAddress
     *
     * @param string $siteEmailAddress
     * @return Setting
     */
    public function setSiteEmailAddress($siteEmailAddress)
    {
        $this->siteEmailAddress = $siteEmailAddress;
    
        return $this;
    }

    /**
     * Get siteEmailAddress
     *
     * @return string 
     */
    public function getSiteEmailAddress()
    {
        return $this->siteEmailAddress;
    }

    /**
     * Set siteKeywords
     *
     * @param string $siteKeywords
     * @return Setting
     */
    public function setSiteKeywords($siteKeywords)
    {
        $this->siteKeywords = $siteKeywords;
    
        return $this;
    }

    /**
     * Get siteKeywords
     *
     * @return string 
     */
    public function getSiteKeywords()
    {
        return $this->siteKeywords;
    }
}

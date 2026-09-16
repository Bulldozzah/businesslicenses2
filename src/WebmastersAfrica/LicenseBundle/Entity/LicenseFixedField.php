<?php

namespace WebmastersAfrica\LicenseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * WebmastersAfrica\LicenseBundle\Entity\Licensetemplate
 *
 * @ORM\Table(name="licensefixedfield")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\LicenseBundle\Entity\LicenseFixedFieldRepository")
 * @Gedmo\Loggable
 */
class LicenseFixedField implements Translatable
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
     * @Gedmo\Locale
     */
    private $locale;

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
    
    /**
     * @var integer $fieldid
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="fieldid", type="integer")
     */
    private $fieldid;

    /**
     * @var string $fieldlabel
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="fieldlabel", type="string", length=255)
     */
    private $fieldlabel;

    /**
     * @var integer $showed
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="showed", type="boolean", nullable=true)
     */
    private $showed;
    
    
    /**
     * @var integer $required
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="required", type="boolean", nullable=true)
     */
    private $required;


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
     * Set fieldid
     *
     * @param integer $fieldid
     * @return LicenseFixedField
     */
    public function setFieldid($fieldid)
    {
        $this->fieldid = $fieldid;
    
        return $this;
    }

    /**
     * Get fieldid
     *
     * @return integer 
     */
    public function getFieldid()
    {
        return $this->fieldid;
    }

    /**
     * Set fieldlabel
     *
     * @param string $fieldlabel
     * @return LicenseFixedField
     */
    public function setFieldlabel($fieldlabel)
    {
        $this->fieldlabel = $fieldlabel;
    
        return $this;
    }

    /**
     * Get fieldlabel
     *
     * @return string 
     */
    public function getFieldlabel()
    {
        return $this->fieldlabel;
    }

    /**
     * Set showed
     *
     * @param boolean $showed
     * @return LicenseFixedField
     */
    public function setShowed($showed)
    {
        $this->showed = $showed;
    
        return $this;
    }

    /**
     * Get showed
     *
     * @return boolean 
     */
    public function getShowed()
    {
        return $this->showed;
    }

    /**
     * Set required
     *
     * @param boolean $required
     * @return LicenseFixedField
     */
    public function setRequired($required)
    {
        $this->required = $required;
    
        return $this;
    }

    /**
     * Get required
     *
     * @return boolean 
     */
    public function getRequired()
    {
        return $this->required;
    }
}
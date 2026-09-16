<?php

namespace WebmastersAfrica\PressBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pages
 *
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="WebmastersAfrica\PressBundle\Entity\PageRepository")
 * @Gedmo\Loggable
 */
class Page implements Translatable
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
     * @Gedmo\Versioned
     * @ORM\Column(name="page_title", type="string", length=255)
     */
    private $page_title;

    /**
     * @var string
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="page_breadcrumb_title", type="string", length=255)
     */
    private $page_breadcrumb_title;

    /**
     * @var text
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="page_content", type="text", nullable=true)
     */
    private $page_content;

    /**
     * @var text
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="seo_keywords", type="text", nullable=true)
     */
    private $seo_keywords;

    /**
     * @var text
     *
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     * @ORM\Column(name="seo_description", type="text", nullable=true)
     */
    private $seo_description;

    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="page_layout", type="integer")
     */
    private $page_layout;

    /**
     * @var integer
     *
     * @ORM\Column(name="menu_dropdown", type="integer")
     */
    // private $menu_dropdown;

    /**
     * @var string
     * 
     * @Gedmo\Versioned
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var integer
     * @Gedmo\Versioned
     * @ORM\Column(name="page_order", type="integer")
     */
    private $page_order;

    /**
     * @ORM\OneToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @var boolean
     * @Gedmo\Versioned
     * 
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published;

    /**
     * @var boolean
     * @Gedmo\Versioned
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @Gedmo\Versioned
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="pages")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     */
    protected $menu;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Versioned
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var datetime $contentChanged
     *
     * @ORM\Column(name="content_changed", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field={"page_title", "page_breadcrumb_title", "page_content", "seo_keywords", "seo_description", "page_layout", "page_order", "published", "deleted", "menu"})
     */
    private $contentChanged;


    /**
     * @var User $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var User $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @var User $contentChangedBy
     *
     * @Gedmo\Timestampable(on="change", field={"page_title", "page_breadcrumb_title", "page_content", "seo_keywords", "seo_description", "page_layout", "page_order", "published", "deleted", "menu"})
     * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
     */
    private $contentChangedBy;

    /**
     * @var integer $site
     *
     * @ORM\Column(name="site", type="integer")
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", unique=true)
     */

    private $slug;

    public function __construct()
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
        $this->deleted = false;
    }

    /**
     * Get Entity Identifier
     *
     * @return String 
     */
    public function __toString()
    {
        return $this->page_title;
    }


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
     * Set page_title
     *
     * @param string $pageTitle
     * @return Page
     */
    public function setPageTitle($pageTitle)
    {
        $this->page_title = $pageTitle;

        return $this;
    }

    /**
     * Get page_title
     *
     * @return string 
     */
    public function getPageTitle()
    {
        return $this->page_title;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite($site)
    {
        $this->site = $site;
        return $this;
    }
    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Set page_breadcrumb_title
     *
     * @param string $pageBreadcrumbTitle
     * @return Page
     */
    public function setPageBreadcrumbTitle($pageBreadcrumbTitle)
    {
        $this->page_breadcrumb_title = $pageBreadcrumbTitle;

        return $this;
    }

    /**
     * Get page_breadcrumb_title
     *
     * @return string 
     */
    public function getPageBreadcrumbTitle()
    {
        return $this->page_breadcrumb_title;
    }

    /**
     * Set page_content
     *
     * @param string $pageContent
     * @return Page
     */
    public function setPageContent($pageContent)
    {
        $this->page_content = $pageContent;

        return $this;
    }

    /**
     * Get page_content
     *
     * @return string 
     */
    public function getPageContent()
    {
        return $this->page_content;
    }

    /**
     * Set seo_keywords
     *
     * @param string $seoKeywords
     * @return Page
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->seo_keywords = $seoKeywords;

        return $this;
    }

    /**
     * Get seo_keywords
     *
     * @return string 
     */
    public function getSeoKeywords()
    {
        return $this->seo_keywords;
    }

    /**
     * Set seo_description
     *
     * @param string $seoDescription
     * @return Page
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seo_description = $seoDescription;

        return $this;
    }

    /**
     * Get seo_description
     *
     * @return string 
     */
    public function getSeoDescription()
    {
        return $this->seo_description;
    }

    /**
     * Set page_layout
     *
     * @param integer $pageLayout
     * @return Page
     */
    public function setPageLayout($pageLayout)
    {
        $this->page_layout = $pageLayout;

        return $this;
    }

    /**
     * Get page_layout
     *
     * @return integer 
     */
    public function getPageLayout()
    {
        return $this->page_layout;
    }

    /**
     * Set page_order
     *
     * @param integer $pageOrder
     * @return Page
     */
    public function setPageOrder($pageOrder)
    {
        $this->page_order = $pageOrder;

        return $this;
    }

    /**
     * Get page_order
     *
     * @return integer 
     */
    public function getPageOrder()
    {
        return $this->page_order;
    }

    /**
     * Set parent
     *
     * @param \WebmastersAfrica\PressBundle\Entity\Page $parent
     * @return Parent
     */
    public function setParent(\WebmastersAfrica\PressBundle\Entity\Page $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \WebmastersAfrica\PressBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Page
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Page
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set menu
     *
     * @param \WebmastersAfrica\PressBundle\Entity\Menu $menu
     * @return Page
     */
    public function setMenu(\WebmastersAfrica\PressBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \WebmastersAfrica\PressBundle\Entity\Menu 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Page
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Page
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set contentChanged
     *
     * @param \DateTime $contentChanged
     * @return Page
     */
    public function setContentChanged($contentChanged)
    {
        $this->contentChanged = $contentChanged;

        return $this;
    }

    /**
     * Get contentChanged
     *
     * @return \DateTime 
     */
    public function getContentChanged()
    {
        return $this->contentChanged;
    }

    /**
     * Set createdBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $createdBy
     * @return Page
     */
    public function setCreatedBy(\WebmastersAfrica\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User 
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $updatedBy
     * @return Page
     */
    public function setUpdatedBy(\WebmastersAfrica\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set contentChangedBy
     *
     * @param \WebmastersAfrica\UserBundle\Entity\User $contentChangedBy
     * @return Page
     */
    public function setContentChangedBy(\WebmastersAfrica\UserBundle\Entity\User $contentChangedBy = null)
    {
        $this->contentChangedBy = $contentChangedBy;

        return $this;
    }

    /**
     * Get contentChangedBy
     *
     * @return \WebmastersAfrica\UserBundle\Entity\User 
     */
    public function getContentChangedBy()
    {
        return $this->contentChangedBy;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Page
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}

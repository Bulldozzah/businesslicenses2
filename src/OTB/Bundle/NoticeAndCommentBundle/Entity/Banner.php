<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OTB\Bundle\NoticeAndCommentBundle\Entity\Banner
 *
 * @ORM\Table(name="regulation_banners")
 * @ORM\Entity(repositoryClass="OTB\Bundle\NoticeAndCommentBundle\Entity\BannerRepository")
 * @Gedmo\Loggable
 */
class Banner implements Translatable
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
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     */
    private $title;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @Gedmo\Translatable
     * @Gedmo\Versioned
     */
    private $description;

    /**
     * @ORM\Column(name="image", type="text")
     **/
    private $image;

     /**
     * @var text $image
     *
     * @Gedmo\Versioned
     *
     * @Assert\File(
     *   mimeTypes = {
     *       "image/png",
     *        "image/jpeg",
     *        "image/jpg",
     *   }
     * )
     * @Gedmo\Versioned
     */
    private $file;

    /**
     * @var integer $deleted
     *
     * @Gedmo\Versioned
     * @ORM\Column(name="deleted", type="integer")
     */
    private $deleted;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @Gedmo\Versioned
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


    public function __construct()
    {
        $this->created = new \DateTime("now");
        $this->updated = new \DateTime("now");
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param text $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }


    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function upload($fileName)
    {
        // the file property can be empty if the field is not required
        if (null ===  $this->file) {
            return;
        }

        // set the path property to the filename where you've saved the file
        $this->image =  $fileName;

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }


    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }
    

    /**
     * Get image
     *
     * @return text
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set deleted
     *
     * @param integer $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Get deleted
     *
     * @return integer
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}

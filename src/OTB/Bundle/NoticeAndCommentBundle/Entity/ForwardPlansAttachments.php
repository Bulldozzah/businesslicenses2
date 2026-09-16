<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlansAttachment
 *
 * @ORM\Table(name="forward_plans_attachments")
 * @ORM\Entity
 */
class ForwardPlansAttachments implements Translatable
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
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255)
   */
  private $name;

  /**
   * Set name
   *
   * @param string $name
   *
   * @return Document
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @Gedmo\Locale
   */
  private $locale;

  public function setTranslatableLocale($locale)
  {
    $this->locale = $locale;
  }


  /**
   * @Assert\File(maxSize="6000000")
   */
  private $file;

  /**
   * @var string $filepath
   *
   * @ORM\Column(name="filepath", type="string", length=255)
   */
  private $filepath;



  /**
   * @var integer $filesize
   *
   * @ORM\Column(name="filesize", type="integer")
   */
  private $filesize;


  /**
   * @ORM\ManyToOne(targetEntity="ForwardPlans", inversedBy="attachments")
   * @ORM\JoinColumn(name="forward_plan_id", referencedColumnName="id")
   */
  protected $forwardPlan;

  /**
   * @var datetime $created
   *
   * @Gedmo\Timestampable(on="create")
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
   * @Gedmo\Timestampable(on="change", field={"name", "filepath", "type_id", "issuing_body", "filesize", "license_id", "license"})
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
   * @Gedmo\Timestampable(on="change", field={"name", "filepath", "type_id", "issuing_body", "filesize", "license_id", "license"})
   * @ORM\ManyToOne(targetEntity="WebmastersAfrica\UserBundle\Entity\User")
   * @ORM\JoinColumn(name="content_changed_by", referencedColumnName="id")
   */
  private $contentChangedBy;

  public function __construct()
  {
    $this->created = new \DateTime("now");
    $this->updated = new \DateTime("now");
    $this->filesize = 600000;
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
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set filepath
   *
   * @param string $filepath
   * @return LicenseDownload
   */
  public function setFilepath($filepath)
  {
    $this->filepath = $filepath;

    return $this;
  }

  /**
   * Get filepath
   *
   * @return string
   */
  public function getFilepath()
  {
    return $this->filepath;
  }


  /**
   * Set filesize
   *
   * @param integer $filesize
   * @return LicenseDownload
   */
  public function setFilesize($filesize)
  {
    $this->filesize = $filesize;

    return $this;
  }

  /**
   * Get filesize
   *
   * @return integer
   */
  public function getFilesize()
  {
    return $this->filesize;
  }


  /**
   * Set license
   *
   * @param \OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans $license
   * @return ForwardPlan
   */
  public function setForwardPlan(\OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans $forwardPlan = null)
  {
    $this->forwardPlan = $forwardPlan;

    return $this;
  }

  /**
   * Get license
   *
   * @return \OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans
   */
  public function getForwardPlan()
  {
    return $this->forwardPlan;
  }

  /**
   * Set created
   *
   * @param \DateTime $created
   * @return LicenseDownload
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
   * @return LicenseDownload
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
   * @return LicenseDownload
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
   * @return LicenseDownload
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
   * @return LicenseDownload
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
   * @return LicenseDownload
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

  public function getAbsolutePath()
  {
    return null === $this->filepath
      ? null
      : $this->getUploadRootDir() . '/' . $this->filepath;
  }

  public function getWebPath()
  {
    return null === $this->filepath
      ? null
      : $this->getUploadDir() . '/' . $this->filepath;
  }

  protected function getUploadRootDir()
  {
    // the absolute directory path where uploaded
    // documents should be save
   return getcwd() . "/". $this->getUploadDir();
  }

  protected function getUploadDir()
  {
    // get rid of the __DIR__ so it doesn't screw up
    // when displaying uploaded doc/image in the view.
    return 'uploads/documents';
  }

    public function upload()
    {
    // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

    // use the original file name here but you should
    // sanitize it at least to avoid any security issues

    // move takes the target directory and then the
    // target filename to move to
        $fileName = $this->getFile()->getClientOriginalName();
        $pos = strrpos($fileName, ".");
        $ext = substr($fileName, $pos);
        $fp = substr($fileName, 0, $pos);
        $dir = rtrim($this->getUploadRootDir(), '/\\') . DIRECTORY_SEPARATOR;

        while (file_exists($dir . $fileName)) {
            $fileName = $fp . "_" . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYX'), 0, 10) . $ext;
        }

        $this->getFile()->move(
            $this->getUploadRootDir(),
            $fileName
        );
        // set the path property to the filename where you've saved the file
        $this->filepath = $fileName;
    // clean up the file property as you won't need it anymore
        $this->file = null;
    }
}

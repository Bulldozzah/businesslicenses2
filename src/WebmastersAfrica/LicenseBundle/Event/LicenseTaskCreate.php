<?php

namespace WebmastersAfrica\LicenseBundle\Event;

use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use Symfony\Component\EventDispatcher\Event;
use WebmastersAfrica\TaskBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

/**
 * Class Create Task Controller
 * 
 * @package WebmastersAfrica\LicenseBundle\Events
 */
class LicenseTaskCreate extends Event
{
  /**
   * @var License
   *  */
  protected $license;

  /**
   * @var mixed
   */
  protected $task;

  /**
   * Constructor Function
   * 
   * @param BusinessLicense $license // Business License Class
   * 
   * @return void
   */
  public function __construct(BusinessLicense $license)
  {
    $this->license =  $license;
  }

  /**
   * Create task function
   * 
   * @return void
   */
  public function setTask(EntityManager $em): self
  {
    $task_info = $this->license->stage;
    $task = new Task();
    $task->setTaskDescription($task_info->getTaskDescription());
    $task->setTaskStartDate(date('Y-m-d H:i:s'));
    $task->setTaskEndDate(date('Y-m-d', strtotime($task_info->getNumberOfDays())));
    $task->setTaskStatus("pending");
    $task->setLicense($this->license);
    $task->setAssignee($task_info->getAssignee());

    $em->persist($task);
    $em->flush();

    $this->task = $task;
  }

  public function getTask()
  {
    return $this->task;
  }
}

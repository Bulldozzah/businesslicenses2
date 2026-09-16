<?php

namespace WebmastersAfrica\LicenseBundle\Subscriber;

use WebmastersAfrica\LicenseBundle\Event\LicenseTaskCreate;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class Task Create Subscriber
 * @package WebmastersAfrica\LicenseBundle\Subscriber
 */
class LicenseTaskSubscriber implements EventSubscriberInterface
{
  use LoggerAwareTrait;

  /**
   * LicenseTakSubscribe constructor.
   * @param LoggerInterface $logger
   */
  public function __construct(LoggerInterface $logger)
  {
    $this->setLogger($logger);
  }

  /**
   * @return array
   */
  public static function getSubscribedEvents()
  {
    return [
      "task.registered" => [
        ["writeLog", -10],
      ]
    ];
  }

  /**
   * @param UserRegisteredEvent $event
   */
  public function writeLog(LicenseTaskCreate $event): void
  {
    $this->logger->info(
      sprintf(
        'Created a new task: %s',
        $event->getTaskDescription()
      )
    );
  }
}

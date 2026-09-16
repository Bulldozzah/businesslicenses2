<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebmastersAfrica\AdminBundle\Session\Handler;

use Doctrine\ORM\EntityManager;

/**
 * Session handler using the existing doctrine orm connection to read and write 
 * data.
 *
 * Session data is a binary string that can contain non-printable characters 
 * like the null byte. For this reason this handler base64 encodes the data to 
 * be able to save it in a character column.
 *
 * This version of the SessionHandler does NOT implement locking. So concurrent
 * requests to the same session can result in data loss due to race conditions.
 */
class RepositoryHandler implements \SessionHandlerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager instance
     */
    private $em;

    /**
     * @var string  Entity name to use
     */
    private $entityName;

    /**
     * Constructor.
     *
     * @param EntityManager $em
     * @param string        $entityName  The name of the entity to use
     */
    public function __construct(EntityManager $em, string $entityName)
    {
        $this->entityName = $entityName;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->em
            ->createQuery("DELETE FROM $this->entityName u WHERE u.id = :id")
            ->setParameter("id", $sessionId)
            ->execute();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        $this->em
            ->createQuery("DELETE FROM $this->entityName u WHERE u.access < :old")
            ->setParameter("old", (new \DateTime())->sub(new \DateInterval("PT" . ((int) $lifetime) . "S")))
            ->execute();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        if (isset($_GET['session_id'])) {
            $session = $this->findById($_GET['session_id']);
            if (!is_null($session)) return base64_decode($session->getData());

            $session = $this->findById($sessionId);
            return !is_null($session) ? base64_decode($session->getData()) : '';
        } else {
            $session = $this->findById($sessionId);
            return !is_null($session) ? base64_decode($session->getData()) : '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $data)
    {
        $session = $this->findById($sessionId);
        if (is_null($session))
            $session = (new $this->entityName)->setId($sessionId);

        $this->em->persist(
            $session
                ->setAccess(new \DateTime())
                ->setData(base64_encode($data))
        );

        $this->em->flush();
        return true;
    }

    /**
     * Used when reading session data from the DB
     * 
     * @param   sessionId
     */
    private function findById($sessionId)
    {
        return $this->em
            ->createQuery("SELECT u FROM $this->entityName u WHERE u.id = :id")
            ->setParameter("id", $sessionId)
            ->getOneOrNullResult();
    }
}

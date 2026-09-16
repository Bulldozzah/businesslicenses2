<?php

namespace WebmastersAfrica\TaskBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    /**
     * Get 5 reset tasks
     *
     * @param mixed $user // user
     * @param string $status // status
     *
     * @return void
     */
    public function getRecentTasks($user, $status)
    {
        $expr = $this->_em->createQueryBuilder()->expr();
        $andX = $expr->andX(
            $expr->eq('p.assigned_to', ":user"),
            $expr->neq('p.taskStatus', ':status')
        );

        $task_count =  $this->createQueryBuilder('p')
            ->where($andX)
            ->setMaxResults(5)
            ->orderBy('p.id', 'desc')
            ->getQuery()
            ->setParameters(
                [
                    'user' => $user,
                    'status' => $status
                ]
            )
            ->getResult();
        if ($task_count < 0) {
            $expr = $this->_em->createQueryBuilder()->expr();
            $andX = $expr->andX(
                $expr->eq('p.assigned_to', ":user"),
                $expr->neq('p.taskStatus', ':status')
            );

            $task_count =  $this->createQueryBuilder('p')
                ->where($andX)
                ->setMaxResults(5)
                ->orderBy('p.id', 'desc')
                ->getQuery()
                ->setParameters(
                    [
                        'user' => $user,
                        'status' => $status
                    ]
                )
                ->getResult();
        }
        return $task_count;
    }
}

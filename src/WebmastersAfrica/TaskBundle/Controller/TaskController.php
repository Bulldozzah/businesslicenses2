<?php

namespace WebmastersAfrica\TaskBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WebmastersAfrica\TaskBundle\Entity\Task;
use WebmastersAfrica\TaskBundle\Form\TaskType;
use Symfony\Component\HttpFoundation\Response;
use WebmastersAfrica\LicenseBundle\Entity\LicenseField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;
use WebmastersAfrica\TaskBundle\Entity\DeclineTask;
use WebmastersAfrica\LicenseBundle\Entity\ApplicationHistory;

/**
 * Task controller.
 *
 */
class TaskController extends Controller
{

    /**
     * Lists all Task entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaTaskBundle:Task a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            5 /*limit per page*/
        );

        return $this->render(
            'WebmastersAfricaTaskBundle:Task:index.html.twig',
            array(
                'pagination' => $pagination,
            )
        );
    }

    /**
     * Creates a new Task entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Task();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setAssigner(
                $this->get('security.context')->getToken()->getUser()
            );
            $em->persist($entity);
            $em->flush();

            $user = $em->getRepository(
                'WebmastersAfricaUserBundle:User'
            )->find(
                $this->get('security.context')->getToken()->getUser()->getId()
            );
            $settings = $em->getRepository(
                'WebmastersAfricaUserBundle:Setting'
            )->find(1);

            $message = \Swift_Message::newInstance()
                ->setSubject("New Task")
                ->setFrom(
                    array(
                        $settings->getSiteEmailAddress()
                        => $settings->getSiteEmailTitle()
                    )
                )
                ->setTo($user->getEmail())
                ->setBody(
                    "Hi, 
                    You received the following task on http://" . $_SERVER['HTTP_HOST'] . "
                    " . $entity->getTaskDescription() . "

                    Yours,
                    eRegistry Team.",
                    'text/html'
                );
            $this->get('mailer')->send($message);

            return $this->redirect($this->generateUrl('managetasks'));
        }

        return $this->render(
            'WebmastersAfricaTaskBundle:Task:new.html.twig',
            array(
                'entity' => $entity,
                'form'   => $form->createView(),
            )
        );
    }

    /**
     * Creates a form to create a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Task $entity)
    {
        $form = $this->createForm(
            new TaskType(),
            $entity,
            array(
                'action' => $this->generateUrl('managetasks_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Task entity.
     *
     */
    public function newAction()
    {
        $entity = new Task();
        $form   = $this->createCreateForm($entity);

        return $this->render(
            'WebmastersAfricaTaskBundle:Task:new.html.twig',
            array(
                'entity' => $entity,
                'form'   => $form->createView(),
            )
        );
    }

    /**
     * Finds and displays a Task entity.
     *
     *
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaTaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                WHERE p.showed = :showed
                ORDER BY p.order ASC'
        )->setParameter("showed", true);
        $customfields = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }

        return $this->render(
            'WebmastersAfricaTaskBundle:Task:show.html.twig',
            array(
                'entity'      => $entity,
                'showedfields' => $showedfields,
                'customfields' => $customfields
            )
        );
    }

    /**
     * Displays a form to edit an existing Task entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaTaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'WebmastersAfricaTaskBundle:Task:edit.html.twig',
            array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Task $entity)
    {
        $form = $this->createForm(
            new TaskType(),
            $entity,
            array(
                'action' => $this->generateUrl('managetasks_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Task entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaTaskBundle:Task')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('managetasks'));
        }

        return $this->render(
            'WebmastersAfricaTaskBundle:Task:edit.html.twig',
            array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }
    /**
     * Deletes a Task entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(
                'WebmastersAfricaTaskBundle:Task'
            )->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Task entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('managetasks'));
    }

    /**
     * Creates a form to delete a Task entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managetasks_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function individualTaskCountAction()
    {
        $task_filter = $this->myTask();
        return new Response(
            json_encode(
                array(
                    "task_count" => count($task_filter)
                )
            )
        );
    }

    public function myTaskListAction(Request $request)
    {
        $entity = $this->myTask();
        return $this->render(
            'WebmastersAfricaTaskBundle:Task:my_task.html.twig',
            array(
                'pagination' => $entity,
            )
        );
    }

    public function myTask()
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository(Task::class)->findBy(
            [
                "assignee" => $this->getUser()->getId(),
                'taskStatus' => 'pending'
            ]
        );
        return $result;
    }

    public function startTaskAction(Request $request, $id, $stage_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user_groups = $user->getGroups();
        $access = false;

        $entity = $em->getRepository(
            'WebmastersAfricaTaskBundle:Task'
        )->findOneBy(['id' => $id, 'stage' => $stage_id]);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }
        if ($entity->getAssignee() == $this->get('security.context')->getToken()->getUser()) {
            $access = true;
        }

        if ($access == false) {
            if ($entity->getLicense()->getStage()->getAssignee() == $user) {
                $entity->setTaskStatus('completed');
                $em->flush();
            }
            $this->get('session')->getFlashBag()->add(
                'success',
                'Access Denied'
            );
            return new RedirectResponse($this->generateUrl('my_task'));
        }

        // $deleteForm = $this->createDeleteForm($id);
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                WHERE p.showed = :showed
                ORDER BY p.order ASC'
        )->setParameter("showed", true);
        $customfields = $query->getResult();
        
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }

        return $this->render(
            'WebmastersAfricaTaskBundle:Task:show.html.twig',
            array(
                'entity'      => $entity,
                'customfields' => $customfields,
                'showedfields' => $showedfields
            )
        );
    }

    public function startDeclineTaskAction(Request $request, $id, $stage_id)
    {
        // check if user has pending task on this stage
        // if none create one
        $task = $this->isSelfAssignedTask($id);
        $em = $this->getDoctrine()->getManager();
        $license = $em->getRepository(BusinessLicense::class)->find(['id' => $id]);
        if (!$license) {
            $this->createNotFoundException("Business License Not Found");
        }
        if ($stage_id != $license->getStage()->getId()) {
            $this->createNotFoundException("Business License Not Found");
        }
        return $this->render(
            'WebmastersAfricaTaskBundle:Task:reject.html.twig',
            array(
                'id' => $id,
                'stage_id' => $stage_id
            )
        );
    }

    public function isApplicationAlreadyRejected(BusinessLicense $license)
    {
        $user = ($license->getCreatedBy()) ? $license->getCreatedBy()->getId() : $this->getUser()->getId();

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->findOneBy(['license' => $license->getId(), 'taskStatus' => 'pending', 'assignee' => $user, 'stage' => $license->getStage()->getId(), 'decline' => 1]);
        return $task ? true : false;
    }

    // can and will be redefined 
    public function declineTaskAction(Request $request, $id, $stage_id)
    {
        $em = $this->getDoctrine()->getManager();
        $workflow = $em->getRepository(Workflow::class)->find($stage_id);
        if ($id == $request->get('license')) {
            error_log("check if license is equal to id");
            if ($this->checkIfItsAReloadOfTheSameDeclineAction($id, $stage_id)) {
                // error log move to agency then
                error_log("Move to agency then");
                $license = $em->getRepository(BusinessLicense::class)->find($id);
                if (!$workflow || !$license) {
                    $this->get('session')->getFlashBag()->add(
                        'success',
                        'Application Moved to agency submission'
                    );
                    return $this->redirect($this->generateUrl('managelicenses', ['id' => $stage_id]));
                }
                if (!$workflow->getPreviousStage()) {
                    return $this->redirect($this->generateUrl('managelicenses', ['id' => $stage_id]));
                }
                $previous_stage_workflow = $workflow->getPreviousStage();
                $license = $this->updateLicenseDeclineToNextStage($license, $previous_stage_workflow);
                $this->createDeclineMovementHistory($license, $previous_stage_workflow);
                $this->createDeclineTaskToAgency($request, $license);
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Application sent back to ' . $workflow->getPreviousStage()
                );
                error_log("This application is already rejected more than once");
                return $this->redirect($this->generateUrl('managelicenses', ['id', $workflow->getId()]));
            }

            error_log("start the process");
            if (!empty($request->request->get('decline_reasons'))) {
                $taskData = $this->isSelfAssignedTask($id);
                $task = $taskData['task'];
                if (!$task) {
                    throw $this->createNotFoundException('Unable to find Task entity.');
                }
                error_log("get the status");
                $em = $this->getDoctrine()->getManager();
                $license = $em->getRepository(BusinessLicense::class)->findOneBy(['id' => $task->getLicense()->getId()]);

                if (!$license) {
                    throw $this->createNotFoundException('Business License Not Found.');
                }
                $workflow = $em->getRepository(Workflow::class)->findOneBy(['id' => $license->getStage()]);
                error_log("get the license first");
                error_log($this->isApplicationAlreadyRejected($license));
                if ($this->isApplicationAlreadyRejected($license)) {
                    // move to agency
                    error_log("This application is already rejected more than once");
                    if ($workflow->getPreviousStage()) {
                        $previous_stage_workflow = $workflow->getPreviousStage();
                        $license = $this->updateLicenseDeclineToNextStage($license, $previous_stage_workflow);
                        $this->createDeclineMovementHistory($license, $previous_stage_workflow);
                        $this->createDeclineTaskToAgency($request, $license);
                        $this->get('session')->getFlashBag()->add(
                            'success',
                            'Application sent back to ' . $workflow->getPreviousStage()
                        );
                    }
                    error_log("license id");
                    error_log($workflow->getId());
                    return $this->redirect($this->generateUrl('managelicenses', ['id' => $workflow->getId()]));
                }
                error_log("this application is not rejected");
                error_log("My Current Workflow Stage is");
                error_log($workflow->getTitle());

                if (!$workflow) {
                    throw $this->createNotFoundException('Business License Not Found.');
                }

                error_log("search the workflow");
                // error_log($workflow->getPreviousStage()->getId());
                error_log("search the stage to previousStage");
                if ($workflow->getPreviousStage()) {
                    if ($workflow->getPreviousStage()->getId() != $workflow->getId()) {
                        $previous_stage_workflow = $em->getRepository(Workflow::class)->findOneBy(['id' => $workflow->getPreviousStage()->getId()]);
                        error_log("The title" . $previous_stage_workflow->getTitle());
                        if (!$previous_stage_workflow) {
                            throw $this->createNotFoundException('Workflow Previous Stage Not Set');
                        }
                        error_log("handle and move application");
                        $license = $this->updateLicenseDeclineToNextStage($license, $previous_stage_workflow);
                        $movement = $this->createDeclineMovementHistory($license, $previous_stage_workflow);
                    }
                }
                error_log("you are through");
                error_log("update the task");
                $task = $this->updateTaskToComplete($task);
                error_log("task complete");
                $complete_task = $this->createDeclineTask($request, $license);
                error_log("create the decline task");
                // send email
                if (!empty($workflow->getPreviousStage()) && $workflow->getPreviousStage()->getNotificationsUser()->count() > 0) {
                    $this->sendTaskEmail($workflow->getPreviousStage());
                }
                error_log("sent emails");
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Task Successfully Completed| Moved to ' . $workflow->getPreviousStage()
                );
                // redirect user to task list
                if ($taskData['selfAssigned']) {
                    $checkPermission = $this->checkPermissionForThisWorkflowStage($workflow->getPreviousStage());
                    if ($checkPermission) {
                        return $this->redirect($this->generateUrl('managelicenses', ['id' => $workflow->getPreviousStage()->getId()]));
                    } else {
                        return $this->redirect($this->generateUrl('managelicenses', ['id' => $workflow->getId()]));
                    }
                }
                return $this->redirect($this->generateUrl('my_task'));
            }
        }
        return $this->redirect($this->generateUrl("managetask_reject"));
    }

    public function createDeclineMovementHistory(BusinessLicense $license, Workflow $nextStage)
    {
        $em = $this->getDoctrine()->getManager();
        $history = new ApplicationHistory();
        $history->setUser($this->get('security.context')->getToken()->getUser());
        $history->setCurrentStage($license->getStage());
        $history->setPreviousStage($nextStage);
        $message = "License Details Rejected";
        $history->setActionType($message);
        $history->setApplication($license);
        $em->persist($history);
        $em->flush();
    }

    public function createDeclineTask(Request $request, BusinessLicense $license)
    {
        $em = $this->getDoctrine()->getManager();
        $task  = new Task();
        $task_assignee = ($license->getCreatedBy()) ? $license->getCreatedBy() : $this->getUser();
        $create_task = false;
        // no one to assign so move
        error_log("no one to assign");
        if ($license->getCreatedBy()) {
            // check if the creator has access the stage and assign them the task
            $permissions = $this->checkPermissionsForThisLicense($license);
            if ($permissions) {
                $create_task = true;
            } else {
                $create_task = false;
            }
        }
        if ($create_task) {
            $task->setTaskDescription($request->request->get('decline_reasons'));
            $task->setTaskStartDate(
                date('Y-m-d H:i:s')
            );
            $task->setTaskStatus("pending");
            $task->setLicense($license);
            $task->setAssignedBy($this->getUser());
            $task->setAssignee($task_assignee);
            $task->setDecline(1);
            $task->setAssigner($this->getUser());
            $task->setStage($license->getStage());
            $task->setTaskEndDate(date('Y-m-d H:i:s'));

            $em->persist($task);
            $em->flush();

            return $task;
        }

        return false;
    }
    public function createDeclineTaskToAgency(Request $request, BusinessLicense $license)
    {
        $em = $this->getDoctrine()->getManager();
        $task  = new Task();
        $task_assignee = ($license->getCreatedBy()) ? $license->getCreatedBy() : $this->getUser();
        $create_task = false;
        // no one to assign so move
        $task->setTaskDescription($request->request->get('decline_reasons'));
        $task->setTaskStartDate(
            date('Y-m-d H:i:s')
        );
        $task->setTaskStatus("complete");
        $task->setLicense($license);
        $task->setAssignedBy($this->getUser());
        $task->setAssignee($task_assignee);
        $task->setDecline(1);
        $task->setAssigner($this->getUser());
        $task->setStage($license->getStage());
        $task->setTaskEndDate(date('Y-m-d H:i:s'));

        $em->persist($task);
        $em->flush();

        return $task;
    }

    public function unpublishTaskAction(Request $request, $id, $stage_id)
    {
        $em = $this->getDoctrine()->getManager();
        $businessLicense = $em->getRepository(BusinessLicense::class)->findOneBy(['id' => $id]);
        if (!$businessLicense) {
            throw $this->createNotFoundException('Unable to find Business License entity.');
        }
        $workflow = $em->getRepository(Workflow::class)->findOneBy(['type' => 'unpublish']);
        $businessLicense->setStage($workflow);
        $businessLicense->setStatus(4);
        $em->persist($businessLicense);
        $em->flush();
        $createMovementHistory = $this->setApplicationHistory($businessLicense, $businessLicense->getStage());
        $this->get('session')->getFlashBag()->add(
            'update_application',
            'Application Successfully Unpublished!' . $businessLicense->getStage()
        );
        return $this->redirect(
            $this->generateUrl(
                'managelicenses',
                ['id' => $workflow->getId()]
            )
        );
    }

    public function publishTaskAction(Request $request, $id, $stage_id)
    {
        $em = $this->getDoctrine()->getManager();
        $businessLicense = $em->getRepository(BusinessLicense::class)->findOneBy(['id' => $id]);
        if (!$businessLicense) {
            throw $this->createNotFoundException('Unable to find Business License entity.');
        }
        $workflow = $em->getRepository(Workflow::class)->findOneBy(['type' => 'publish']);
        $businessLicense->setStage($workflow);
        $businessLicense->setStatus(1);
        $em->persist($businessLicense);
        $em->flush();
        $this->setApplicationHistory($businessLicense, $businessLicense->getStage());
        $this->get('session')->getFlashBag()->add(
            'update_application',
            'Application Successfully Published!' . $businessLicense->getStage()
        );
        return $this->redirect(
            $this->generateUrl(
                'managelicenses',
                ['id' => $workflow->getId()]
            )
        );
    }

    public function approveTaskAction(Request $request, $id, $stage_id)
    {
        if ($this->checkIfItsAReloadOfTheSameAction($id, $stage_id)) {
            // $this->get('session')->getFlashBag()->add(
            //     'update_application',
            //     'Task Successfully Completed'
            // );
            // redirect user to task list
            $this->redirect($this->generateUrl('managelicenses', ['id' => $stage_id]));
        }
        $em = $this->getDoctrine()->getManager();
        $taskData = $this->isSelfAssignedTask($id);
        $task = $taskData['task'];

        if (!$task) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }
        $task = $this->updateTaskToComplete($task);
        $businessLicense = $em->getRepository(
            BusinessLicense::class
        )->findOneBy(
            ['id' => $task->getLicense()->getId()]
        );
        if (!$businessLicense) {
            throw $this->createNotFoundException('Business License not found');
        }
        $workflowRepository = $em->getRepository(Workflow::class);

        $currentStage = $businessLicense->getStage();
        $nextStep = $workflowRepository->findOneBy(['id' => $businessLicense->getStage()]);
        if ($nextStep->getId() == $currentStage->getId() && $nextStep->getNextStage()->getType() == "publish") {
            $license = $this->updateLicenseToNextStage($businessLicense, $nextStep->getNextStage(), true);
            $this->setApplicationHistory($license, $businessLicense->getStage());
            $my_task = $this->myTask();
            $this->get('session')->getFlashBag()->add(
                'update_application',
                'Task Successfully Approved!' . $license->getStage()
            );
            error_log("Am about to send the email 1");
            if ($nextStep->getNotificationsUser()->count() > 0) {
                $this->sendTaskEmail($nextStep);
                error_log("Email send");
            }

            if ($taskData['selfAssigned']) {
                return $this->redirect(
                    $this->generateUrl(
                        'managelicenses',
                        ['id' => $license->getStage()->getId()]
                    )
                );
            }
            return $this->render(
                'WebmastersAfricaTaskBundle:Task:my_task.html.twig',
                array(
                    'pagination' => $my_task,
                )
            );
        } else if ($currentStage->getType() == 'publish') {
            $license = $this->updateLicenseToNextStage($businessLicense, $currentStage, true);
            $this->get('session')->getFlashBag()->add(
                'update_application',
                'Task Successfully Approved!' . $license->getStage()
            );
            error_log("Am about to send the email");
            if ($currentStage->getNotificationsUser()->count() > 0) {
                error_log("send an email");
                $this->sendTaskEmail($currentStage);
            }
            if ($taskData['selfAssigned']) {
                $checkPermission = $this->checkPermissionForThisWorkflowStage($license->getStage());
                if ($checkPermission) {
                    return $this->redirect(
                        $this->generateUrl(
                            'managelicenses',
                            ['id' => $license->getStage()->getId()]
                        )
                    );
                } else {
                    return $this->redirect(
                        $this->generateUrl(
                            'managelicenses',
                            ['id' => $$currentStage->getId()]
                        )
                    );
                }
            }
            return $this->render(
                'WebmastersAfricaTaskBundle:Task:my_task.html.twig',
                array(
                    'pagination' => $my_task,
                )
            );
        } else {
            $nextStep = $nextStep->getNextStage();
            $license = $this->updateLicenseToNextStage($businessLicense, $nextStep);
            $createMovementHistory = $this->setApplicationHistory($license, $currentStage);
            $this->createTask($license);
            error_log("Am about to send the email 2");
            if ($nextStep->getNotificationsUser()->count() > 0) {
                $this->sendTaskEmail($nextStep);
                error_log("Am about to send the email 2");
            }
        }
        $this->get('session')->getFlashBag()->add(
            'update_application',
            'Task Successfully Completed| Application moved to ' . $license->getStage()
        );
        if ($taskData['selfAssigned']) {
            $checkPermission = $this->checkPermissionForThisWorkflowStage($license->getStage());
            if ($checkPermission) {
                $redirect_stage = $currentStage->getNextStage()->getId();
            } else {
                $redirect_stage = $currentStage->getId();
            }
            return $this->redirect($this->generateUrl('managelicenses', ['id' => $redirect_stage]));
        }

        $my_task = $this->myTask();
        return $this->render(
            'WebmastersAfricaTaskBundle:Task:my_task.html.twig',
            array(
                'pagination' => $my_task,
            )
        );
    }

    protected function setApplicationHistory($license, $previousStage)
    {
        $em = $this->getDoctrine()->getManager();
        $history = new ApplicationHistory();
        $history->setUser($this->get('security.context')->getToken()->getUser());
        $history->setCurrentStage($license->getStage());
        $history->setPreviousStage($previousStage);
        $message = "License Details Approved";
        $history->setActionType($message);
        $history->setUser($this->get('security.context')->getToken()->getUser());
        $history->setApplication($license);
        $em->persist($history);
        $em->flush();
    }
    public function updateLicenseToNextStage(BusinessLicense $license, Workflow $nextStage, $publish = false)
    {
        $em = $this->getDoctrine()->getManager();
        $license->setStage($nextStage);
        $status = ($publish) ? 1 : 3;
        $license->setStatus($status);
        $em->persist($license);
        $em->flush();

        return $license;
    }

    protected function checkPermissionForThisWorkflowStage(Workflow $stage)
    {
        $em = $this->getDoctrine()->getManager();
        $accessGranted = false;

        $permissions = $em->getRepository(Workflow::class)->findOneBy(["id" => $stage->getId()]);
        $stage_permissions = $permissions->getAccessGroups()->getValues();
        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("BRRA_AGENCY_EDIT")')))) {
            $accessGranted = true;
        }
        $logged_in_user_permissions = $this->get('security.context')->getToken()->getUser()->getGroups()->getValues();

        foreach ($logged_in_user_permissions as $key) {
            foreach ($stage_permissions as $value) {
                if ($key->getId() == $value->getId() && $key->getName() == $value->getName()) {
                    $accessGranted = true;
                }
            }
        }
        return $accessGranted;
    }

    public function updateLicenseDeclineToNextStage(BusinessLicense $license, Workflow $nextStage, $publish = false)
    {
        $em = $this->getDoctrine()->getManager();
        $license->setStage($nextStage);
        $status = 5;
        $license->setStatus($status);
        $em->persist($license);
        $em->flush();

        return $license;
    }

    public function updateTaskToComplete(Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $task->setTaskStatus("complete");
        $em->persist($task);
        $em->flush();
        return $task;
    }

    protected function isSelfAssignedTask($id)
    {
        $em = $this->getDoctrine()->getManager();
        $taskData = array();
        $license = $em->getRepository(BusinessLicense::class)->findOneBy(['id' => $id]);
        $user = $this->get('security.context')->getToken()->getUser();
        $task = $em->getRepository(Task::class)->findOneBy(['license' => $license->getId(), "assignee" => $user->getId(), "assigner" => $user->getId(), "stage" => $license->getStage()->getId()]);
        $taskData['selfAssigned'] = true;
        if (!$task) {
            // create task without notification and return user to complete task
            $taskData['selfAssigned'] = true;
            $task = $em->getRepository(Task::class)->findOneBy(['license' => $license->getId(), "assignee" => $user->getId(), "stage" => $license->getStage()->getId()]);
            if (!$task) {
                $taskData['task'] = $task = $this->createUserAssignedTask($license, $user);
                $taskData['selfAssigned'] = true;
            } else {
                $taskData['task'] = $task;
                $taskData['selfAssigned'] = false;
            }
        } else {
            $taskData['task'] = $task;
        }
        return $taskData;
    }

    protected function checkIfItsAReloadOfTheSameDeclineAction($license, $stage_id)
    {
        $task = false;
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->findOneBy(
            ['stage' => $stage_id, 'assignee' => $this->getUser()->getId(), 'license' => $license, 'taskStatus' => 'complete', "decline" => 1]
        );
        if (!$task) {
            $task = $em->getRepository(Task::class)->findOneBy(['stage' => $stage_id, 'license' => $license, 'taskStatus' => 'complete', 'decline' => 1]);
        }
        return $task ? true : false;
    }

    protected function checkIfItsAReloadOfTheSameAction($license, $stage_id)
    {
        $task = false;
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->findOneBy(
            ['stage' => $stage_id, 'assignee' => $this->getUser()->getId(), 'license' => $license, 'taskStatus' => 'complete']
        );
        if (!$task) {
            $task = $em->getRepository(Task::class)->findOneBy(['stage' => $stage_id, 'license' => $license, 'taskStatus' => 'complete']);
        }
        return $task ? true : false;
    }

    protected function createUserAssignedTask($license, $user)
    {
        $em = $this->getDoctrine()->getManager();
        $task_info = $license->getStage();
        $task_description = ($task_info->getTaskDescription()) ? $task_info->getTaskDescription() : "License Details review";
        $my_task = new Task();
        $my_task->setTaskDescription($task_description);
        $my_task->setTaskStartDate(date('Y-m-d H:i:s'));
        $my_task->setTaskEndDate(date('Y-m-d H:i:s'));
        $my_task->setTaskStatus("complete");
        $my_task->setLicense($license);
        $my_task->setStage($task_info);
        $my_task->setAssigner($this->getUser());
        $my_task->setAssignee($this->getUser());
        $em->persist($my_task);
        $em->flush();
        return $my_task;
    }


    public function declineTaskCreation($license, $stage_id)
    {

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->find($id);
        $task->setTaskStatus("complete");
        $em->persist($task);
        $em->flush();
        $task_info = $license->getStage();
        $user = ($license->getCreatedBy()) ? $license->getCreatedBy() : $this->getUser();
        $task = new Task();
        $task->setTaskDescription(
            "The Following license has been sent back to you with corrections. <a href='/index.php/managetasks/my-task/list' class='btn btn-lg btn-success'>$license->getTitle()</a>"
        );
        $task->setTaskStartDate(
            date('Y-m-d H:i:s')
        );

        $task->setTaskStatus("pending");
        $task->setLicense($license);
        $task->setAssignee($task_info->getAssignee());

        $task->setAssigner($this->getUser());
        $task->setAssignedTo($user);
        $task->setStage($task_info);
        $em->persist($task);
        $em->flush();

        if ($task_info->getSendEmails()) {
            $this->sendTaskEmail($license->getStage());
        }

        return true;
    }
    public function createTask($license)
    {
        // lazy loading is causing me to do alot of doctrine, to be refined
        $em = $this->getDoctrine()->getManager();
        $stage = $em->getRepository(Workflow::class)->findOneBy(['id' => $license->getStage()->getId()]);
        if (!$stage) {
            return false;
        }
        if (!$stage->getAssignee()) {
            return false;
        }

        $task_info = $license->getStage();
        $task = new Task();
        $task->setTaskDescription($task_info->getTaskDescription());
        $task->setTaskStartDate(
            date('Y-m-d H:i:s')
        );
        $task->setTaskStatus("pending");
        $task->setLicense($license);
        $task->setAssignee($task_info->getAssignee());

        $task->setAssigner($this->getUser());
        $task->setStage($task_info);

        $em->persist($task);
        $em->flush();

        if ($task_info->getSendEmails()) {
            $this->sendTaskEmail($license->getStage());
        }

        return true;
    }

    public function sendEmailTestAction(Workflow $stage)
    {
        $results = $this->sendTaskEmail($stage);
    }

    public function sendTaskEmail(Workflow $entity)
    {
        
        $em = $this->getDoctrine()->getManager();
        // if ($entity->getAssignee() < 1) {
        //     return false;
        // }
        $settings = $em->getRepository(
            'WebmastersAfricaUserBundle:Setting'
        )->find(1);
        $users = $entity->getNotificationsUser()->getValues();
        foreach ($users as $key) {
            $message = \Swift_Message::newInstance()
                ->setSubject($settings->getSiteEmailTitle())
                // ->setFrom($settings->getSiteEmailAddress())
                ->setFrom($this->container->getParameter('mailer_user'))
                ->setTo($key->getEmail())
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        'WebmastersAfricaTaskBundle:Task:send_email_notification.html.twig',
                        array(
                            'server' => $_SERVER['HTTP_HOST'],
                            'first_name' => $key->getFirstName()
                        )
                    )
                );
            $response = $this->get('mailer')->send($message);
        }
        return true;
    }

    protected function checkPermissionsForThisLicense(BusinessLicense $license)
    {
        $em = $this->getDoctrine()->getManager();
        $accessGranted = false;

        $permissions = $em->getRepository(Workflow::class)->findOneBy(["id" => $license->getStage()->getId()]);
        $stage_permissions = $permissions->getAccessGroups()->getValues();
        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("BRRA_AGENCY_EDIT")')))) {
            $accessGranted = true;
        }
        $logged_in_user_permissions = $license->getCreatedBy()->getGroups()->getValues();

        foreach ($logged_in_user_permissions as $key) {
            foreach ($stage_permissions as $value) {
                if ($key->getId() == $value->getId() && $key->getName() == $value->getName()) {
                    $accessGranted = true;
                }
            }
        }
        return $accessGranted;
    }
}

<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;
use WebmastersAfrica\LicenseBundle\Form\WorkflowType;
use WebmastersAfrica\UserBundle\Entity\User;

/**
 * Workflow controller.
 *
 * @Route("/license/workflow")
 */
class WorkflowController extends Controller
{

    /**
     * Lists all Workflow entities.
     *
     * @Route("/", name="license_workflow")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $em->getRepository(
                Workflow::class
            )->findAll(),
            $this->get('request')->query->get('page', 1),
            20 /*limit per page*/
        );


        return array(
            'pagination' => $pagination
        );
    }
    /**
     * Creates a new Workflow entity.
     *
     * @Route("/", name="license_workflow_create")
     * @Method("POST")
     * @Template("WebmastersAfricaLicenseBundle:Workflow:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Workflow();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('license_workflow'));
        }

        return array(
            'entity' => $entity,
            'form'  => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Workflow entity.
     *
     * @param Workflow $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Workflow $entity)
    {
        $form = $this->createForm(
            new WorkflowType(),
            $entity,
            array(
                'action' => $this->generateUrl('license_workflow_create'),
                'method' => 'POST',
            )
        );

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Create',
                'attr' => array(
                    'class' => 'w3-right w3-button w3-green w3-round-large',
                    'style' => "padding: 10px 30px 30px 30px;"
                )
            )
        );

        return $form;
    }

    /**
     * Displays a form to create a new Workflow entity.
     *
     * @Route("/new", name="license_workflow_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Workflow();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'  => $form->createView(),
        );
    }

    /**
     * Finds and displays a Workflow entity.
     *
     * @Route("/{id}", name="license_workflow_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:Workflow')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'     => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Workflow entity.
     *
     * @Route("/{id}/edit", name="license_workflow_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(User::class)->getAllAdminUsers();
        $entity = $em->getRepository(Workflow::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'     => $entity,
            'edit_form'  => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a Workflow entity.
     *
     * @param Workflow $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Workflow $entity)
    {
        $form = $this->createForm(
            new WorkflowType(),
            $entity,
            array(
                'action' => $this->generateUrl('license_workflow_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array(
            'label' => 'Update',
            'attr' => array(
                'class' => 'w3-right w3-center w3-button w3-blue w3-round-large pull-right',
                'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
            )
        ));

        return $form;
    }
    /**
     * Edits an existing Workflow entity.
     *
     * @Route("/{id}", name="license_workflow_update")
     * @Method("PUT")
     * @Template("WebmastersAfricaLicenseBundle:Workflow:edit.html.twig")
     * 
     * @return mixed
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:Workflow'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Workflow entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect(
                $this->generateUrl('license_workflow')
            );
        }

        return array(
            'entity'     => $entity,
            'edit_form'  => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Workflow entity.
     *
     * @Route("/{id}", name="license_workflow_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaLicenseBundle: Workflow')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Workflow entity .');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('license_workflow'));
    }

    /**
     * Creates a form to delete a Workflow entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('license_workflow_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}

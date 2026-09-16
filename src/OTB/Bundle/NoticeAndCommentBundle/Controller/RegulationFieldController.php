<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField;
use OTB\Bundle\NoticeAndCommentBundle\Form\RegulationFieldType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RegulationField controller.
 *
 */
class RegulationFieldController extends Controller
{

    /**
     * Lists all RegulationField entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM NoticeCommentBundle:RegulationField a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10000/*limit per page*/
        );

        return $this->render('NoticeCommentBundle:RegulationField:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $entity = $em->getRepository('NoticeCommentBundle:RegulationField')->find($batch_item);
                $em->remove($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('manageregulationcustomfields'));
    }

    /**
     * Creates a new RegulationField entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new RegulationField();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('manageregulationcustomfields'));
        }

        return $this->render('NoticeCommentBundle:RegulationField:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a RegulationField entity.
     *
     * @param RegulationField $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(RegulationField $entity)
    {
        $form = $this->createForm(new RegulationFieldType(), $entity, array(
            'action' => $this->generateUrl('manageregulationcustomfields_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new RegulationField entity.
     *
     */
    public function newAction()
    {
        $entity = new RegulationField();
        $form   = $this->createCreateForm($entity);

        return $this->render('NoticeCommentBundle:RegulationField:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a RegulationField entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:RegulationField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegulationField entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('NoticeCommentBundle:RegulationField:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing RegulationField entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:RegulationField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegulationField entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('NoticeCommentBundle:RegulationField:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a RegulationField entity.
     *
     * @param RegulationField $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(RegulationField $entity)
    {
        $form = $this->createForm(new RegulationFieldType(), $entity, array(
            'action' => $this->generateUrl('manageregulationcustomfields_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('update', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing RegulationField entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:RegulationField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegulationField entity.');
        }

        $originalFieldchoices = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($entity->getFieldchoices() as $choice) {
            $originalFieldchoices->add($choice);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            // remove the relationship between the tag and the Task
            foreach ($originalFieldchoices as $choice) {
                if (false === $entity->getFieldchoices()->contains($choice)) {
                    // remove the Task from the Tag
                    $entity->getFieldchoices()->removeElement($choice);

                    $choice = $em->getRepository('NoticeCommentBundle:RegulationFieldChoice')->find($choice->getId());
                    $em->remove($choice);
                }
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('manageregulationcustomfields'));
        }

        return $this->render('NoticeCommentBundle:RegulationField:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a RegulationField entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NoticeCommentBundle:RegulationField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find RegulationField entity.');
        }

        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'delete',
            'delete'
        );

        return $this->redirect($this->generateUrl('manageregulationcustomfields'));
    }

    /**
     * Creates a form to delete a RegulationField entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manageregulationcustomfields_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}

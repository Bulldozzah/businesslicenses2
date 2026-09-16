<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\LicenseBundle\Entity\LicenseField;
use WebmastersAfrica\LicenseBundle\Form\LicenseFieldType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * LicenseField controller.
 *
 */
class LicenseFieldController extends Controller
{

    /**
     * Lists all LicenseField entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaLicenseBundle:LicenseField a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10000/*limit per page*/
        );

        return $this->render('WebmastersAfricaLicenseBundle:LicenseField:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach($batch_items as $batch_item)
        {
            if($batch_select == "delete")
            {
                $entity = $em->getRepository('WebmastersAfricaLicenseBundle:LicenseField')->find($batch_item);
                $em->remove($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('managefields'));
    }
    
    /**
     * Creates a new LicenseField entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new LicenseField();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('managefields'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:LicenseField:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a LicenseField entity.
    *
    * @param LicenseField $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(LicenseField $entity)
    {
        $form = $this->createForm(new LicenseFieldType(), $entity, array(
            'action' => $this->generateUrl('managefields_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new LicenseField entity.
     *
     */
    public function newAction()
    {
        $entity = new LicenseField();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaLicenseBundle:LicenseField:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a LicenseField entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:LicenseField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LicenseField entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:LicenseField:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing LicenseField entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:LicenseField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LicenseField entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:LicenseField:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a LicenseField entity.
    *
    * @param LicenseField $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(LicenseField $entity)
    {
        $form = $this->createForm(new LicenseFieldType(), $entity, array(
            'action' => $this->generateUrl('managefields_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing LicenseField entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:LicenseField')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find LicenseField entity.');
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

                    $choice = $em->getRepository('WebmastersAfricaLicenseBundle:LicenseFieldChoice')->find($choice->getId());
                    $em->remove($choice);
                }
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('managefields'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:LicenseField:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a LicenseField entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaLicenseBundle:LicenseField')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find LicenseField entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        //}

        return $this->redirect($this->generateUrl('managefields'));
    }

    /**
     * Creates a form to delete a LicenseField entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managefields_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\LicenseBundle\Entity\BusinessType;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Form\BusinessTypeType;

/**
 * BusinessType controller.
 *
 */
class BusinessTypeController extends Controller
{

    /**
     * Lists all BusinessType entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaLicenseBundle:BusinessType a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10000/*limit per page*/
        );

        return $this->render('WebmastersAfricaLicenseBundle:BusinessType:index.html.twig', array(
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
                $businesstype = $em->getRepository(
                    BusinessLicense::class
                )->findPublishedByLicenseByBusinessTypes($batch_item);
                if ($businesstype) {
                    $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this business Type. Has Licenses associated to it');
                } else {
                    $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessType')->find($batch_item);
                    $em->remove($entity);

                    $this->get('session')->getFlashBag()->add(
                        'delete',
                        'delete'
                    );
                }
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('managebusinesstypes'));
    }

    /**
     * Creates a new BusinessType entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BusinessType();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
				$this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );
            return $this->redirect($this->generateUrl('managebusinesstypes'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:BusinessType:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a BusinessType entity.
    *
    * @param BusinessType $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(BusinessType $entity)
    {
        $form = $this->createForm(new BusinessTypeType(), $entity, array(
            'action' => $this->generateUrl('managebusinesstypes_create'),
            'method' => 'POST',
        ));

         $form->add('submit', 'submit', array('label' => 'Create', 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                , 'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )));


        return $form;
    }

    /**
     * Displays a form to create a new BusinessType entity.
     *
     */
    public function newAction()
    {
        $entity = new BusinessType();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessType:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BusinessType entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessType:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing BusinessType entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessType entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessType:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BusinessType entity.
    *
    * @param BusinessType $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BusinessType $entity)
    {
        $form = $this->createForm(new BusinessTypeType(), $entity, array(
            'action' => $this->generateUrl('managebusinesstypes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

         $form->add('update', 'submit', array('label' => 'Update', 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                , 'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )));


        return $form;
    }
    /**
     * Edits an existing BusinessType entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessType')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessType entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('managebusinesstypes'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:BusinessType:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BusinessType entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessType')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BusinessType entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        //}

        return $this->redirect($this->generateUrl('managebusinesstypes'));
    }

    /**
     * Creates a form to delete a BusinessType entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managebusinesstypes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

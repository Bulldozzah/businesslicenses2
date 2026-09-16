<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\LicenseBundle\Entity\BusinessLocation;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Form\BusinessLocationType;

/**
 * BusinessLocation controller.
 *
 */
class BusinessLocationController extends Controller
{

    /**
     * Lists all BusinessLocation entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaLicenseBundle:BusinessLocation a WHERE a.deleted <> 1";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10000/*limit per page*/
        );

        return $this->render('WebmastersAfricaLicenseBundle:BusinessLocation:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    public function indexDeletedLocationsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $locations = $em->getRepository(BusinessLocation::class)->findBy(
            ['deleted' => 1]
        );

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $locations,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10000/*limit per page*/
        );

        return $this->render('WebmastersAfricaLicenseBundle:BusinessLocation:deleted_index.html.twig', array('pagination' => $pagination));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $deleted = false;

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "unpublish") {
                $businessLicense = $em->getRepository(BusinessLicense::class)->findPublishedByLocation($batch_item);
                if (!$businessLicense) {
                    $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocation')->find($batch_item);
                    $entity->setDeleted(1);

                    $this->get('session')->getFlashBag()->add(
                        'unpublish_location',
                        'Your have successfully unpublished the item(s)'
                    );
                } else {
                    $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this jurisdiction. Has Licenses associated to it.');
                }
            }

            if ($batch_select == "publish") {
                $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocation')->find($batch_item);
                $entity->setDeleted(0);

                $this->get('session')->getFlashBag()->add(
                    'publish_location',
                    'Your have successfully published the item(s)'
                );
                $deleted = true;
            }
        }
        $em->flush();
        if ($deleted) {
            return $this->redirect($this->generateUrl('managelocations'));
        }
        

        return $this->redirect($this->generateUrl('managelocations'));
    }

    /**
     * Creates a new BusinessLocation entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BusinessLocation();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //Check if location has been set to default, if it has then set everything else to not default
            if ($entity->getIsDefault()) {
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery(
                    'SELECT p
                        FROM WebmastersAfricaLicenseBundle:BusinessLocation p
                        WHERE p.id <> :id'
                )->setParameter('id', $entity->getId());
                $otherlocations = $query->getResult();
                foreach ($otherlocations as $otherlocation) {
                    $em = $this->getDoctrine()->getManager();
                    $otherlocation->setIsDefault(0);
                    $em->persist($otherlocation);
                    $em->flush();
                }
            }
            $this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );
            return $this->redirect($this->generateUrl('managelocations'));
        }
        return $this->render('WebmastersAfricaLicenseBundle:BusinessLocation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a BusinessLocation entity.
     *
     * @param BusinessLocation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessLocation $entity)
    {
        $form = $this->createForm(new BusinessLocationType(), $entity, array(
            'action' => $this->generateUrl('managelocations_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )));

        return $form;
    }

    /**
     * Displays a form to create a new BusinessLocation entity.
     *
     */
    public function newAction()
    {
        $entity = new BusinessLocation();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessLocation:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BusinessLocation entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLocation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessLocation:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing BusinessLocation entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLocation entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessLocation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a BusinessLocation entity.
     *
     * @param BusinessLocation $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessLocation $entity)
    {
        $form = $this->createForm(new BusinessLocationType(), $entity, array(
            'action' => $this->generateUrl('managelocations_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('update', 'submit', array('label' => 'Update'));
        return $form;
    }
    /**
     * Edits an existing BusinessLocation entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLocation entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            //Check if location has been set to default, if it has then set everything else to not default
            if ($entity->getIsDefault()) {
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery(
                    'SELECT p
                        FROM WebmastersAfricaLicenseBundle:BusinessLocation p
                        WHERE p.id <> :id'
                )->setParameter('id', $entity->getId());
                $otherlocations = $query->getResult();
                foreach ($otherlocations as $otherlocation) {
                    $em = $this->getDoctrine()->getManager();
                    $otherlocation->setIsDefault(0);
                    $em->persist($otherlocation);
                    $em->flush();
                }
            }

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('managelocations'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:BusinessLocation:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BusinessLocation entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLocation entity.');
        }

        $entity->setDeleted(1);
        $em->persist($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'delete',
            'delete'
        );

        //}

        return $this->redirect($this->generateUrl('managelocations'));
    }

    /**
     * Creates a form to delete a BusinessLocation entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managelocations_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add(
                'submit',
                'submit',
                array(
                    'label' => 'Delete',
                    'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )
                )
            )
            ->getForm();
    }
}

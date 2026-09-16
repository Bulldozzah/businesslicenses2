<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\LicenseBundle\Entity\BusinessActivity;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Form\BusinessActivityType;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * BusinessActivity controller.
 *
 */
class BusinessActivityController extends Controller
{

    /**
     * Lists all BusinessActivity entities.
     *
     */
    public function indexAction()
    {

        $em    = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(BusinessActivity::class)->findAll();

        return $this->render('WebmastersAfricaLicenseBundle:BusinessActivity:index.html.twig', array(
            'business_activity' => $entity,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete")  {
                $businessLicense = $em->getRepository(BusinessLicense::class)->findLicensesByBusinessActivity($batch_item);
                if (!$businessLicense) {
                    $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessActivity')->find($batch_item);
                    $em->remove($entity);

                    $this->get('session')->getFlashBag()->add(
                        'delete',
                        'delete'
                    );
                } else {
                    $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this business Activity. Has Licenses associated to it');
                }
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('manageactivities'));
    }

    /**
     * Creates a new BusinessActivity entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BusinessActivity();
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

            return $this->redirect($this->generateUrl('manageactivities'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:BusinessActivity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a BusinessActivity entity.
    *
    * @param BusinessActivity $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(BusinessActivity $entity)
    {
        $form = $this->createForm(new BusinessActivityType(), $entity, array(
            'action' => $this->generateUrl('manageactivities_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BusinessActivity entity.
     *
     */
    public function newAction()
    {
        $entity = new BusinessActivity();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessActivity:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BusinessActivity entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessActivity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessActivity entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessActivity:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing BusinessActivity entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessActivity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessActivity entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessActivity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BusinessActivity entity.
    *
    * @param BusinessActivity $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BusinessActivity $entity)
    {
        $form = $this->createForm(new BusinessActivityType(), $entity, array(
            'action' => $this->generateUrl('manageactivities_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BusinessActivity entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessActivity')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessActivity entity.');
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

            return $this->redirect($this->generateUrl('manageactivities'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:BusinessActivity:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BusinessActivity entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessActivity')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BusinessActivity entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        //}

        return $this->redirect($this->generateUrl('manageactivities'));
    }

    /**
     * Creates a form to delete a BusinessActivity entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manageactivities_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

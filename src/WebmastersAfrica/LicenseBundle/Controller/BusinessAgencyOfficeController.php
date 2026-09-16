<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use WebmastersAfrica\LicenseBundle\Form\BusinessAgencyOfficeType;

/**
 * BusinessAgencyOffice controller.
 *
 * @Route("/businessagencyoffice")
 */
class BusinessAgencyOfficeController extends Controller
{

    /**
     * Lists all BusinessAgencyOffice entities.
     *
     * @Route("/", name="manageagencies_office")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($agency_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(BusinessAgencyOffice::class)->findBy(['agency' => $agency_id]);

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new BusinessAgencyOffice entity.
     *
     * @Route("/", name="manageagencies_office_create")
     * @Method("POST")
     * @Template("WebmastersAfricaLicenseBundle:BusinessAgencyOffice:new.html.twig")
     */
    public function createAction(Request $request, $agency_id)
    {
        $entity = new BusinessAgencyOffice();
        $form = $this->createCreateForm($entity, $agency_id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $agency = $em->getRepository(BusinessAgency::class)->findOneBy(['id' => $agency_id]);
            $entity->setAgency($agency);
            if ($request->files->get('businessagencyoffice')['file']) {
                $entity->upload();
            }
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );

            return $this->redirect($this->generateUrl('manageagencies_show', array('id' => $agency_id)));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'agency_id' => $agency_id
        );
    }

    /**
     * Creates a form to create a BusinessAgencyOffice entity.
     *
     * @param BusinessAgencyOffice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessAgencyOffice $entity, $agency_id)
    {
        $form = $this->createForm(new BusinessAgencyOfficeType(), $entity, array(
            'action' => $this->generateUrl('manageagencies_office_create', ['agency_id' => $agency_id]),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BusinessAgencyOffice entity.
     *
     * @Route("/new", name="manageagencies_office_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($agency_id)
    {
        $entity = new BusinessAgencyOffice();
        $form   = $this->createCreateForm($entity, $agency_id);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'agency' => $agency_id
        );
    }

    /**
     * Finds and displays a BusinessAgencyOffice entity.
     *
     * @Route("/{id}", name="manageagencies_office_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessAgencyOffice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessAgencyOffice entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity
        );
    }

    /**
     * Displays a form to edit an existing BusinessAgencyOffice entity.
     *
     * @Route("/{id}/edit", name="manageagencies_office_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id, $agency_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessAgencyOffice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessAgencyOffice entity.');
        }

        $editForm = $this->createEditForm($entity, $agency_id);
        $deleteForm = $this->createDeleteForm($id, $agency_id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'agency' => $agency_id
        );
    }

    /**
     * Creates a form to edit a BusinessAgencyOffice entity.
     *
     * @param BusinessAgencyOffice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessAgencyOffice $entity, $agency_id)
    {
        $form = $this->createForm(new BusinessAgencyOfficeType(), $entity, array(
            'action' => $this->generateUrl(
                'manageagencies_office_update',
                array(
                    'agency_id' => $agency_id,
                    'id' => $entity->getId()
                )
            ),
            'method' => 'PUT',
        ));

        $form->add('update', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BusinessAgencyOffice entity.
     *
     * @Route("/{id}", name="manageagencies_office_update")
     * @Method("PUT")
     * @Template("WebmastersAfricaLicenseBundle:BusinessAgencyOffice:edit.html.twig")
     */
    public function updateAction(Request $request, $id, $agency_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessAgencyOffice')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessAgencyOffice entity.');
        }

        $deleteForm = $this->createDeleteForm($id, $agency_id);
        $editForm = $this->createEditForm($entity, $agency_id);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $agency = $em->getRepository(BusinessAgency::class)->findOneBy(['id' => $agency_id]);
            $entity->setAgency($agency);
            if ($request->files->get('businessagencyoffice')['file']) {
                $entity->upload();
            }
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('manageagencies_show', array('id' => $agency_id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'agency' => $agency_id
        );
    }
    /**
     * Deletes a BusinessAgencyOffice entity.
     *
     * @Route("/{id}", name="manageagencies_office_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id, $agency_id)
    {
        $form = $this->createDeleteForm($id, $agency_id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessAgencyOffice')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BusinessAgencyOffice entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('manageagencies_show', array('id' => $agency_id)));
    }

    /**
     * Creates a form to delete a BusinessAgencyOffice entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, $agency_id)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'manageagencies_office_delete',
                    array(
                        'id' => $id, 'agency_id' => $agency_id
                    )
                )
            )
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }


    public function batchAction(Request $request, $agency_id)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $office = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessAgencyOffice')->find($batch_item);
                $em->remove($office);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        return $this->redirect($this->generateUrl('manageagencies_show', ['id' => $agency_id]));
    }
}

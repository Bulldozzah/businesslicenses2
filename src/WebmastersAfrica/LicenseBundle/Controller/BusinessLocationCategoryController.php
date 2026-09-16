<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLocationCategory;
use WebmastersAfrica\LicenseBundle\Form\BusinessLocationCategoryType;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLocation;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;

/**
 * BusinessLocationCategory controller.
 *
 * @Route("/businesslocationcategory")
 */
class BusinessLocationCategoryController extends Controller
{

    /**
     * Lists all BusinessLocationCategory entities.
     *
     * @Route("/", name="managebusinesslocation_category")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocationCategory')->findBy([], ['published' => 'DESC']);

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new BusinessLocationCategory entity.
     *
     * @Route("/", name="businesslocationcategory_create")
     * @Method("POST")
     * @Template("WebmastersAfricaLicenseBundle:BusinessLocationCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new BusinessLocationCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('managebusinesslocationcategory'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a BusinessLocationCategory entity.
     *
     * @param BusinessLocationCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessLocationCategory $entity)
    {
        $form = $this->createForm(new BusinessLocationCategoryType(), $entity, array(
            'action' => $this->generateUrl('businesslocationcategory_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BusinessLocationCategory entity.
     *
     * @Route("/new", name="businesslocationcategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new BusinessLocationCategory();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a BusinessLocationCategory entity.
     *
     * @Route("/{id}", name="businesslocationcategory_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocationCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLocationCategory entity.');
        }

       // $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity
         //   'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing BusinessLocationCategory entity.
     *
     * @Route("/{id}/edit", name="businesslocationcategory_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocationCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLocationCategory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a BusinessLocationCategory entity.
    *
    * @param BusinessLocationCategory $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BusinessLocationCategory $entity)
    {
        $form = $this->createForm(new BusinessLocationCategoryType(), $entity, array(
            'action' => $this->generateUrl('businesslocationcategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BusinessLocationCategory entity.
     *
     * @Route("/{id}", name="businesslocationcategory_update")
     * @Method("PUT")
     * @Template("WebmastersAfricaLicenseBundle:BusinessLocationCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocationCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLocationCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('managebusinesslocationcategory'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a BusinessLocationCategory entity.
     *
     * @Route("/{id}", name="businesslocationcategory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocationCategory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BusinessLocationCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('businesslocationcategory'));
    }

    /**
     * Creates a form to delete a BusinessLocationCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('businesslocationcategory_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $deleted = false;

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "unpublish") {
                $businessLicense = $em->getRepository(BusinessLicense::class)->findPublishedByLocationCategory($batch_item);
                if (!$businessLicense) {
                    $entity = $em->getRepository(BusinessLocationCategory::class)->find($batch_item);
                    $entity->setPublished(0);

                    $this->get('session')->getFlashBag()->add(
                        'unpublish_category',
                        'Your have successfully unpublished the item(s)'
                    );
                } else {
                    $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t unpublish this jurisdiction category. Has Licenses associated to it.');
                }
            }

            if ($batch_select == "publish") {
                $entity = $em->getRepository(BusinessLocationCategory::class)->find($batch_item);
                $entity->setPublished(1);

                $this->get('session')->getFlashBag()->add(
                    'publish_category',
                    'Your have successfully published the item(s)'
                );
                $deleted = true;
            }
        }
        $em->flush();
        if ($deleted) {
            return $this->redirect($this->generateUrl('managebusinesslocationcategory'));
        }
        

        return $this->redirect($this->generateUrl('managebusinesslocationcategory'));
    }
}

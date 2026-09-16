<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WebmastersAfrica\PressBundle\Entity\BusinessStartup;
use WebmastersAfrica\PressBundle\Form\BusinessStartupType;

/**
 * BusinessStartup controller.
 *
 * @Route("/businessstartup")
 */
class BusinessStartupController extends Controller
{

    /**
     * Lists all BusinessStartup entities.
     *
     * @Route("/", name="businessstartup")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(
            BusinessStartup::class
        )->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new BusinessStartup entity.
     *
     * @Route("/", name="businessstartup_create")
     * @Method("POST")
     * @Template("WebmastersAfricaPressBundle:BusinessStartup:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new BusinessStartup();
        $slug = $this->get('cocur_slugify');
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setSlug(
                $slug->slugify(
                    $request->get('startup')['name']
                )
            );
            $this->get('session')->getFlashBag()->add(
                'procedure_create',
                'create'
            );
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('manage_procedures', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a BusinessStartup entity.
     *
     * @param BusinessStartup $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessStartup $entity)
    {
        $form = $this->createForm(
            new BusinessStartupType(),
            $entity,
            array(
                'action' => $this->generateUrl('manage_procedures_create'),
                'method' => 'POST',
            )
        );

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Create',
                'attr' => array('class'=>'w3-right w3-button w3-hover-teal w3-round-large w3-blue w3-medium',
                                'style'=>'padding: 10px 30px 30px 30px; margin-right:15px;')
            )
        );

        return $form;
    }

    /**
     * Displays a form to create a new BusinessStartup entity.
     *
     * @Route("/new", name="businessstartup_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new BusinessStartup();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a BusinessStartup entity.
     *
     * @Route("/{id}", name="manage_procedures_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            'WebmastersAfricaPressBundle:BusinessStartup'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessStartup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing BusinessStartup entity.
     *
     * @Route("/{id}/edit", name="businessstartup_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            'WebmastersAfricaPressBundle:BusinessStartup'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessStartup entity.');
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
     * Creates a form to edit a BusinessStartup entity.
     *
     * @param BusinessStartup $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessStartup $entity)
    {
        $form = $this->createForm(
            new BusinessStartupType(),
            $entity,
            array(
                'action' => $this->generateUrl(
                    'manage_procedures_update',
                    array('id' => $entity->getId())
                ),
                'method' => 'PUT',
            )
        );

        $form->add('submit',
                    'submit',
                    array('label' => 'Update',
                            'attr' => array('class'=>'w3-right w3-button w3-hover-teal w3-round-large w3-green w3-medium',
                                            'style'=>'padding: 10px 30px 30px 30px; margin-right:15px;')
                        )
                );

        return $form;
    }
    /**
     * Edits an existing BusinessStartup entity.
     *
     * @Route("/{id}", name="manage_procedures_update")
     * @Method("PUT")
     * @Template("WebmastersAfricaPressBundle:BusinessStartup:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
    		$slug = $this->get('cocur_slugify');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(BusinessStartup::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessStartup entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'procedure_update',
                'update'
            );
            return $this->redirect($this->generateUrl('manage_procedures'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a BusinessStartup entity.
     *
     * @Route("/{id}", name="businessstartup_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(BusinessStartup::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BusinessStartup entity.');
            }

            $em->remove($entity);
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add(
            'delete',
            'delete'
        );

        return $this->redirect($this->generateUrl('manage_procedures'));
    }

    /**
     * Creates a form to delete a BusinessStartup entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manage_procedures_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "publish") {
                $entity = $em->getRepository(
                    'WebmastersAfricaPressBundle:BusinessStartup'
                )->find($batch_item);
                $entity->setIsPublished(1);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            } else if ($batch_select == "unpublish") {
                $entity = $em->getRepository(
                    'WebmastersAfricaPressBundle:BusinessStartup'
                )->find($batch_item);
                $entity->setIsPublished(0);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            } else if ($batch_select == "delete") {
                $entity = $em->getRepository(
                    'WebmastersAfricaPressBundle:BusinessStartup'
                )->find($batch_item);
                $entity->setDeleted(1);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('manage_procedures'));
    }
}

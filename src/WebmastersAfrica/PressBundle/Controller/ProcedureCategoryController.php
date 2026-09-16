<?php

namespace WebmastersAfrica\PressBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WebmastersAfrica\PressBundle\Entity\ProcedureCategory;
use WebmastersAfrica\PressBundle\Form\ProcedureCategoryType;
use Symfony\Component\HttpFoundation\Request;

/**
 * ProcedureCategory controller.
 *
 * @Route("/manage_procedure_category")
 */
class ProcedureCategoryController extends Controller
{

    /**
     * Lists all ProcedureCategory entities.
     *
     * @Route("/", name="manage_procedure_category")
     * @Method("GET")
     * @Template("WebmastersAfricaPressBundle:ProcedureCategory:index_2.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(
            ProcedureCategory::class
        )->findBy(['delete' => 0]);

        return $this->render(
            "WebmastersAfricaPressBundle:ProcedureCategory:index_2.html.twig",
            array(
                'entities' => $entities,
            )
        );
    }

    /**
     * Creates a new ProcedureCategory entity.
     *
     * @Route("/", name="procedure_category_create")
     * @Method("POST")
     * @Template("WebmastersAfricaPressBundle:ProcedureCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new ProcedureCategory();
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
            return $this->redirect(
                $this->generateUrl(
                    'manage_procedure_category'
                )
            );
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ProcedureCategory entity.
     *
     * @param ProcedureCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ProcedureCategory $entity)
    {
        $form = $this->createForm(
            new ProcedureCategoryType(),
            $entity,
            array(
                'action' => $this->generateUrl('manage_procedure_category_create'),
                'method' => 'POST',
            )
        );

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Create',
                'attr' => array(
                    "class" => "w3-button w3-green w3-round-large w3-hover-green",
                    'style' => "padding: 10px 30px 30px 30px;"
                )
            )
        );

        return $form;
    }

    /**
     * Displays a form to create a new ProcedureCategory entity.
     *
     * @Route("/new", name="manage_procedure_category_new")
     * @Method("GET")
     * @Template("WebmastersAfricaPressBundle:ProcedureCategory:new.html.twig")
     */
    public function newAction()
    {
        $entity = new ProcedureCategory();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProcedureCategory entity.
     *
     * @Route("/{id}", name="manage_procedure_category_show")
     * @Method("GET")
     * @Template("WebmastersAfricaPressBundle:ProcedureCategory:show.html.twig")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(ProcedureCategory::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find ProcedureCategory entity.'
            );
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing ProcedureCategory entity.
     *
     * @Route("/{id}/edit", name="manage_business_procedure_edit")
     * @Method("GET")
     * @Template("WebmastersAfricaPressBundle:ProcedureCategory:edit.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            ProcedureCategory::class
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find ProcedureCategory entity.'
            );
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            "WebmastersAfricaPressBundle:ProcedureCategory:edit.html.twig",
            array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a ProcedureCategory entity.
     *
     * @param ProcedureCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ProcedureCategory $entity)
    {
        $form = $this->createForm(
            new ProcedureCategoryType(),
            $entity,
            array(
                'action' => $this->generateUrl(
                    'manage_procedure_category_update',
                    array('id' => $entity->getId())
                ),
                'method' => 'PUT',
            )
        );

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Update',
                'attr' => array(
                    "class" => "w3-right w3-button w3-indigo w3-round-large",
                    'style' => "padding: 10px 30px 30px 30px;"
                )
            )
        );

        return $form;
    }

    /**
     * Edits an existing ProcedureCategory entity.
     *
     * @Route("/{id}", name="ProcedureCategory_update")
     * @Method("PUT")
     * @Template("WebmastersAfricaPressBundle:ProcedureCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            ProcedureCategory::class
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find ProcedureCategory entity.'
            );
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
            return $this->redirect(
                $this->generateUrl('manage_procedure_category')
            );
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ProcedureCategory entity.
     *
     * @Route("/{id}", name="manage_procedure_category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(
                ProcedureCategory::class
            )->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProcedureCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ProcedureCategory'));
    }

    /**
     * Creates a form to delete a ProcedureCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('manage_procedure_category_delete', array('id' => $id))
            )
            ->setMethod('DELETE')
            ->add(
                'submit',
                'submit',
                array(
                    'label' => 'Delete',
                    'attr' => array(
                        'class' => "w3-left w3-button w3-red w3-round-large",
                        'style' => "padding: 10px 30px 30px 30px;"
                    )
                )
            )
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
                    ProcedureCategory::class
                )->find($batch_item);
                $entity->setPublish(true);
                $em->persist($entity);
            } else if ($batch_select == "unpublish") {
                $entity = $em->getRepository(
                    ProcedureCategory::class
                )->find($batch_item);
                $entity->setPublish(false);
                $em->persist($entity);
            } else if ($batch_select == "delete") {
                $entity = $em->getRepository(
                    ProcedureCategory::class
                )->find($batch_item);
                $entity->setDelete(true);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('manage_procedure_category'));
    }
}

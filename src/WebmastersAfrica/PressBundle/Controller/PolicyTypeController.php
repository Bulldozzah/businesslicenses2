<?php

namespace WebmastersAfrica\PressBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WebmastersAfrica\PressBundle\Entity\PolicyType;
use WebmastersAfrica\PressBundle\Form\PolicyTypeType;
use Symfony\Component\HttpFoundation\Request;

/**
 * PolicyType controller.
 *
 * @Route("/manage_policy_category")
 */
class PolicyTypeController extends Controller
{

    /**
     * Lists all PolicyType entities.
     *
     * @Route("/", name="manage_policy_category")
     * @Method("GET")
     * @Template("WebmastersAfricaPressBundle:PolicyType:index.html.twig")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(
            PolicyType::class
        )->findAll();

        return $this->render(
            "WebmastersAfricaPressBundle:PolicyType:index.html.twig",
            array(
                'entities' => $entities,
            )
        );
    }

    /**
     * Creates a new PolicyType entity.
     *
     * @Route("/", name="procedure_category_create")
     * @Method("POST")
     * @Template("WebmastersAfricaPressBundle:PolicyType:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new PolicyType();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect(
                $this->generateUrl(
                    'manage_policy_category'
                )
            );
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a PolicyType entity.
     *
     * @param PolicyType $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PolicyType $entity)
    {
        $form = $this->createForm(
            new PolicyTypeType(),
            $entity,
            array(
                'action' => $this->generateUrl('manage_policy_category_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PolicyType entity.
     *
     * @Route("/new", name="manage_policy_category_new")
     * @Method("GET")
     * @Template("WebmastersAfricaPressBundle:PolicyType:new.html.twig")
     */
    public function newAction()
    {
        $entity = new PolicyType();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a PolicyType entity.
     *
     * @Route("/{id}", name="manage_policy_category_show")
     * @Method("GET")
     * @Template("WebmastersAfricaPressBundle:PolicyType:show.html.twig")
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(PolicyType::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find PolicyType entity.'
            );
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing PolicyType entity.
     *
     * @Route("/{id}/edit", name="manage_business_procedure_edit")
     * @Method("GET")
     * @Template("WebmastersAfricaPressBundle:PolicyType:edit.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            PolicyType::class
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find PolicyType entity.'
            );
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            "WebmastersAfricaPressBundle:PolicyType:edit.html.twig",
            array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Creates a form to edit a PolicyType entity.
     *
     * @param PolicyType $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PolicyType $entity)
    {
        $form = $this->createForm(
            new PolicyTypeType(),
            $entity,
            array(
                'action' => $this->generateUrl(
                    'manage_policy_category_update',
                    array('id' => $entity->getId())
                ),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing PolicyType entity.
     *
     * @Route("/{id}", name="PolicyType_update")
     * @Method("PUT")
     * @Template("WebmastersAfricaPressBundle:PolicyType:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            PolicyType::class
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find PolicyType entity.'
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
                $this->generateUrl('manage_policy_category')
            );
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a PolicyType entity.
     *
     * @Route("/{id}", name="manage_policy_category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(
                PolicyType::class
            )->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PolicyType entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('PolicyType'));
    }

    /**
     * Creates a form to delete a PolicyType entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('manage_policy_category_delete', array('id' => $id))
            )
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
                    PolicyType::class
                )->find($batch_item);
                $entity->setPublish(true);
                $em->persist($entity);
            } elseif ($batch_select == "unpublish") {
                $entity = $em->getRepository(
                    PolicyType::class
                )->find($batch_item);
                $entity->setPublish(false);
                $em->persist($entity);
            } elseif ($batch_select == "delete") {
                $entity = $em->getRepository(
                    PolicyType::class
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

        return $this->redirect($this->generateUrl('manage_policy_category'));
    }
}

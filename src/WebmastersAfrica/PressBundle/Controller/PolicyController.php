<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WebmastersAfrica\PressBundle\Entity\Policy;
use WebmastersAfrica\PressBundle\Form\PolicyType;

/**
 * Policy controller.
 *
 * @Route("/policy")
 */
class PolicyController extends Controller
{

    /**
     * Lists all BusinessStartup entities.
     *
     * @Route("/", name="policy")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(
            Policy::class
        )->FindBy(['published' => true], ['id' => 'ASC']);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new BusinessStartup entity.
     *
     * @Route("/", name="manage_policy_create")
     * @Method("POST")
     * @Template("WebmastersAfricaPressBundle:Policy:deleted.html.twig")
     */
    public function indexDeletedAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository(
            Policy::class
        )->FindBy(['deleted' => true], ['id' => 'ASC']);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new BusinessStartup entity.
     *
     * @Route("/", name="manage_policy_create")
     * @Method("POST")
     * @Template("WebmastersAfricaPressBundle:Policy:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $slug = $this->get('cocur_slugify');
        $entity = new Policy();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setSlug($slug->slugify($entity->getTitle()));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('manage_policy', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a BusinessStartup entity.
     *
     * @param Policy $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Policy $entity)
    {
        $form = $this->createForm(
            new PolicyType(),
            $entity,
            array(
                'action' => $this->generateUrl('manage_policy_create'),
                'method' => 'POST',
            )
        );

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Create',
                'attr' => array(
                    'class'=>'w3-right w3-button w3-hover-teal w3-round-large w3-blue w3-medium',
                    'style'=>'padding: 10px 30px 30px 30px; margin-right:15px;'
                )
            )
        );

        return $form;
    }

    /**
     * Displays a form to create a new BusinessStartup entity.
     *
     * @Route("/new", name="manage_policy_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Policy();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a BusinessStartup entity.
     *
     * @Route("/{id}", name="manage_policy_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            'WebmastersAfricaPressBundle:Policy'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Policy entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Policy entity.
     *
     * @Route("/{id}/edit", name="manage_policy_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(
            'WebmastersAfricaPressBundle:Policy'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Policy entity.');
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
     * Creates a form to edit a Policy entity.
     *
     * @param Policy $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Policy $entity)
    {
        $form = $this->createForm(
            new PolicyType(),
            $entity,
            array(
                'action' => $this->generateUrl(
                    'manage_policy_update',
                    array('id' => $entity->getId())
                ),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update',
                                            'attr' => array('class'=>'w3-right w3-button w3-hover-teal w3-round-large w3-blue w3-medium',
                                            'style'=>'padding: 10px 30px 30px 30px; margin-right:15px;')));

        return $form;
    }
    /**
     * Edits an existing Policy entity.
     *
     * @Route("/{id}", name="manage_policy_update")
     * @Method("PUT")
     * @Template("WebmastersAfricaPressBundle:Policy:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(Policy::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Policy entity.');
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
            return $this->redirect($this->generateUrl('manage_policy'));
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
     * @Route("/{id}", name="manage_policy_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(Policy::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Policy entity.');
            }

            $em->remove($entity);
            $em->flush();
        }
        $this->get('session')->getFlashBag()->add(
            'delete',
            'delete'
        );

        return $this->redirect($this->generateUrl('manage_policy'));
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
            ->setAction($this->generateUrl('manage_policy_delete', array('id' => $id)))
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
                    'WebmastersAfricaPressBundle:Policy'
                )->find($batch_item);
                $entity->setPublished(true);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            } elseif ($batch_select == "unpublish") {
                $entity = $em->getRepository(
                    'WebmastersAfricaPressBundle:Policy'
                )->find($batch_item);
                $entity->setPublished(false);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            } elseif ($batch_select == "delete") {
                $entity = $em->getRepository(
                    'WebmastersAfricaPressBundle:Policy'
                )->find($batch_item);
                $entity->setPublished(false);
                $entity->setDeleted(true);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }
        return $this->redirect($this->generateUrl('manage_policy'));
    }
    public function batchDeletedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        foreach ($batch_items as $batch_item) {
            if ($batch_select == "publish") {
                $entity = $em->getRepository(
                    'WebmastersAfricaPressBundle:Policy'
                )->find($batch_item);
                $entity->setPublished(true);
                $entity->setDeleted(false);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            }
        }
        return $this->redirect($this->generateUrl('manage_policy_deleted'));
    }
}

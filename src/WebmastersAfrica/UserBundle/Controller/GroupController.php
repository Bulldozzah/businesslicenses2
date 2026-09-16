<?php

namespace WebmastersAfrica\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\UserBundle\Entity\Group;
use WebmastersAfrica\UserBundle\Form\GroupType;

/**
 * Group controller.
 *
 */
class GroupController extends Controller
{

    /**
     * Lists all Group entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaUserBundle:Group a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10000 /*limit per page*/
        );

        return $this->render(
            'WebmastersAfricaUserBundle:Group:index.html.twig',
            array(
                'pagination' => $pagination,
            )
        );
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $entity = $em->getRepository('WebmastersAfricaUserBundle:Group')->find($batch_item);
                $em->remove($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('managegroups'));
    }

    /**
     * Creates a new Group entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Group();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($request->get("roles")) {
                $entity->setRoles($request->get("roles"));
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('managegroups'));
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p FROM WebmastersAfricaUserBundle:Role p ORDER BY p.title ASC'
        );
        $roles = $query->getResult();


        $this->get('session')->getFlashBag()->add(
            'create',
            'create'
        );


        return $this->render(
            'WebmastersAfricaUserBundle:Group:new.html.twig',
            array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'roles' => $roles,
            )
        );
    }

    /**
     * Creates a form to create a Group entity.
     *
     * @param Group $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Group $entity)
    {
        $form = $this->createForm(new GroupType(), $entity, array(
            'action' => $this->generateUrl('managegroups_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Group entity.
     *
     */
    public function newAction()
    {
        $entity = new Group();
        $form   = $this->createCreateForm($entity);


        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p FROM WebmastersAfricaUserBundle:Role p ORDER BY p.rolecode ASC'
        );
        $roles = $query->getResult();

        return $this->render('WebmastersAfricaUserBundle:Group:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'roles' => $roles,
        ));
    }

    /**
     * Finds and displays a Group entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:Group')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaUserBundle:Group:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Group entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:Group')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p FROM WebmastersAfricaUserBundle:Role p ORDER BY p.rolecode ASC'
        );
        $roles = $query->getResult();

        $choosenroles = $entity->getRoles();

        return $this->render('WebmastersAfricaUserBundle:Group:edit.html.twig', array(
            'entity'      => $entity,
            'roles' => $roles,
            'choosenroles' => $choosenroles,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Group entity.
     *
     * @param Group $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Group $entity)
    {
        $form = $this->createForm(new GroupType(), $entity, array(
            'action' => $this->generateUrl('managegroups_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', "attr" => ['class' => "w3-right"]));
        return $form;
    }
    /**
     * Edits an existing Group entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:Group')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            if ($request->get("roles")) {
                $entity->setRoles($request->get("roles"));
            }
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('managegroups'));
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p FROM WebmastersAfricaUserBundle:Role p ORDER BY p.title ASC'
        );
        $roles = $query->getResult();

        $choosenroles = $entity->getRoles();

        $this->get('session')->getFlashBag()->add(
            'update',
            'update'
        );

        return $this->render('WebmastersAfricaUserBundle:Group:edit.html.twig', array(
            'entity'      => $entity,
            'roles' => $roles,
            'choosenroles' => $choosenroles,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Group entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaUserBundle:Group')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Group entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        }

        return $this->redirect($this->generateUrl('managegroups'));
    }

    /**
     * Creates a form to delete a Group entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managegroups_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', "attr" => ['class' => "w3-right", "style" => "margin-top:-30px;"]))
            ->getForm();
    }
}

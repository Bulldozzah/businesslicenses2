<?php

namespace WebmastersAfrica\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\UserBundle\Entity\User;
use WebmastersAfrica\UserBundle\Form\UserType;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all Administrative User entities.
     *
     */
    public function indexAction()
    {
        //If a user has logged in  and is part of agency, prepare an agency filter
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());

        $agencies = $user->getAgencies();

        $agencies_filter = "";
        $agencies_filter_ids = "";

        $count = 0;
        $agencysize = sizeof($agencies);
        $accessGranted = false;
        foreach ($agencies as $agency) {
            $count++;
            if ($count == $agencysize) {
                $agencies_filter = $agencies_filter . " a.agency_id = " . $agency->getId();
                $agencies_filter_ids = $agencies_filter_ids . " a.id = " . $agency->getId();
            } else {
                $agencies_filter = $agencies_filter . " a.agency_id = " . $agency->getId() . " OR";
                $agencies_filter_ids = $agencies_filter_ids . " a.id = " . $agency->getId() . " OR";
            }
        }

        $em    = $this->get('doctrine.orm.entity_manager');
        $logged_in_user_permissions = $this->get('security.context')->getToken()->getUser()->getGroups()->getValues();
        foreach ($logged_in_user_permissions as $key) {
            if ($key->getId() == 11) {
                $accessGranted = true;
            }
        }
        if ($accessGranted) {
            $dql   = "SELECT a FROM WebmastersAfricaUserBundle:User a WHERE a.locked = 0 and a.isAdmin = 1";
        } else {
            if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
                if ($agencies_filter_ids == "") {
                    $dql   = "SELECT u FROM WebmastersAfricaUserBundle:User u JOIN u.agencies a  WHERE u.locked = 0 AND u.isAdmin = 1";
                } else {
                    $dql   = "SELECT u FROM WebmastersAfricaUserBundle:User u JOIN u.agencies a  WHERE u.locked = 0 and u.isAdmin = 1 AND (" . $agencies_filter_ids . ")";
                }
                $query = $em->createQuery($dql)->getResult();
            } else {
                $dql   = "SELECT a FROM WebmastersAfricaUserBundle:User a WHERE a.locked = 0 and a.isAdmin = 1";
            }
        }

        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10000 /*limit per page*/
        );

        return $this->render(
            'WebmastersAfricaUserBundle:User:index.html.twig',
            array(
                'pagination' => $pagination,
                'user_heading' => 'Administrative Users',
                'add_user' => true
            )
        );
    }

    /**
     * Lists all User entities.
     *
     */
    public function publicUsersAction()
    {
        //If a user has logged in  and is part of agency, prepare an agency filter
        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(User::class)->findBy(['locked' => 0, 'isAdmin' => false], ['id' => 'asc']);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10000 /*limit per page*/
        );
        return $this->render(
            'WebmastersAfricaUserBundle:User:front_end_index.html.twig',
            array(
                'pagination' => $pagination,
                'user_heading' => 'List of Users',
                'add_user' => false
            )
        );
    }

    /**
     * Search Users
     *
     * @param Request $request
     */
    public function searchUserDetailsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $search_term = $request->request->get("search");
        $search_result = $em->getRepository(User::class)->searchUsers($search_term);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $search_result,
            $this->get('request')->query->get('page', 1),
            10000
        );

        return $this->render(
            'WebmastersAfricaUserBundle:User:index.html.twig',
            array(
                'pagination' => $pagination,
                'user_heading' => 'Administrative Users',
                'add_user' => true
            )
        );
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "enable") {
                $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($batch_item);
                $entity->setEnabled(true);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            } else if ($batch_select == "disable") {
                $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($batch_item);
                $entity->setEnabled(false);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            } else if ($batch_select == "delete") {
                $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($batch_item);
                $entity->setLocked(true);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('manage_admins'));
    }

    public function batchFrontEndUsersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "enable") {
                $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($batch_item);
                $entity->setEnabled(true);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            } else if ($batch_select == "disable") {
                $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($batch_item);
                $entity->setEnabled(false);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('manageusers'));
    }

    /**
     * Logs for user activities.
     *
     */
    public function logAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM Gedmo\Loggable\Entity\LogEntry a";
        $query = $em->createQuery($dql);
        $query->setMaxResults(1000);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            1000 /*limit per page*/
        );

        return $this->render('WebmastersAfricaUserBundle:User:log.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Creates a new User entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if ($entity->getPassword()) {
                $encoder_service = $this->get('security.encoder_factory');
                $encoder = $encoder_service->getEncoder($entity);
                $encoded_pass = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($encoded_pass);
            }
            $entity->setIsAdmin(1);
            $em->persist($entity);
            $em->flush();


            $this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );

            return $this->redirect($this->generateUrl('manage_admins'));
        }

        return $this->render('WebmastersAfricaUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('manageusers_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaUserBundle:User:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaUserBundle:User:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaUserBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('manageusers_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $postdata = $request->get('webmastersafrica_userbundle_user');
            if ($postdata['password']) {
                $encoder_service = $this->get('security.encoder_factory');
                $encoder = $encoder_service->getEncoder($entity);
                $encoded_pass = $encoder->encodePassword($postdata['password'], $entity->getSalt());
                $entity->setPassword($encoded_pass);
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );
            return $this->redirect($this->generateUrl('manage_admins'));
        }

        return $this->render('WebmastersAfricaUserBundle:User:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        }

        return $this->redirect($this->generateUrl('manage_admins'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manageusers_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}

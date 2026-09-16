<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\PressBundle\Entity\NewsletterSubscriber;
use WebmastersAfrica\PressBundle\Form\NewsletterSubscriberType;

/**
 * NewsletterSubscriber controller.
 *
 */
class NewsletterSubscriberController extends Controller
{

    /**
     * Lists all NewsletterSubscriber entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $em    = $this->get('doctrine.orm.entity_manager');
        $query = $em->getRepository(NewsletterSubscriber::class)->findBy([], ['confirmed' => 'DESC']);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10000/*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:NewsletterSubscriber:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Lists all NewsletterSubscriber entities.
     *
     */
    public function indexfilterAction($status)
    {
        $em = $this->getDoctrine()->getManager();

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaPressBundle:NewsletterSubscriber a WHERE a.confirmed = ".$status;
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            100/*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:NewsletterSubscriber:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach($batch_items as $batch_item)
        {
            if($batch_select == "confirm")
            {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:NewsletterSubscriber')->find($batch_item);
                $entity->setConfirmed(true);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            }
            else if($batch_select == "unconfirm")
            {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:NewsletterSubscriber')->find($batch_item);
                $entity->setConfirmed(false);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
            }
            else if($batch_select == "delete")
            {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:NewsletterSubscriber')->find($batch_item);
                $em->remove($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('managesubscribers'));
    }
    
    /**
     * Creates a new NewsletterSubscriber entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new NewsletterSubscriber();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('managesubscribers'));
        }

        return $this->render('WebmastersAfricaPressBundle:NewsletterSubscriber:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a NewsletterSubscriber entity.
    *
    * @param NewsletterSubscriber $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(NewsletterSubscriber $entity)
    {
        $form = $this->createForm(new NewsletterSubscriberType(), $entity, array(
            'action' => $this->generateUrl('managesubscribers_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new NewsletterSubscriber entity.
     *
     */
    public function newAction()
    {
        $entity = new NewsletterSubscriber();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaPressBundle:NewsletterSubscriber:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a NewsletterSubscriber entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:NewsletterSubscriber')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterSubscriber entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:NewsletterSubscriber:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing NewsletterSubscriber entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:NewsletterSubscriber')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterSubscriber entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:NewsletterSubscriber:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a NewsletterSubscriber entity.
    *
    * @param NewsletterSubscriber $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(NewsletterSubscriber $entity)
    {
        $form = $this->createForm(new NewsletterSubscriberType(), $entity, array(
            'action' => $this->generateUrl('managesubscribers_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing NewsletterSubscriber entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:NewsletterSubscriber')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find NewsletterSubscriber entity.');
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

            return $this->redirect($this->generateUrl('managesubscribers'));
        }

        return $this->render('WebmastersAfricaPressBundle:NewsletterSubscriber:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a NewsletterSubscriber entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaPressBundle:NewsletterSubscriber')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find NewsletterSubscriber entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        //}

        return $this->redirect($this->generateUrl('managesubscribers'));
    }

    /**
     * Creates a form to delete a NewsletterSubscriber entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managesubscribers_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\PressBundle\Entity\Newsletter;
use WebmastersAfrica\PressBundle\Form\NewsletterType;

/**
 * Newsletter controller.
 *
 */
class NewsletterController extends Controller
{

    /**
     * Lists all Newsletter entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $query = $em->getRepository(Newsletter::class)->findBy([], ['deleted' => 'ASC']);
       
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            50000/*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:Newsletter:index.html.twig', array(
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
            if($batch_select == "delete")
            {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Newsletter')->find($batch_item);
                $entity->setDeleted(true);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('managenewsletters'));
    }

    /**
     * Creates a new Newsletter entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Newsletter();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($entity);
            $em->flush();

            if($entity->getSent())
            {
                $query = $em->createQuery(
                    'SELECT p
                        FROM WebmastersAfricaPressBundle:NewsletterSubscriber p
                        WHERE p.confirmed = :confirmed'
                )->setParameter('confirmed', true);
                $subscribers = $query->getResult();
                $receivers = null;
                foreach($subscribers as $subscriber)
                {
                    if (!filter_var(trim($subscriber->getEmail()), FILTER_VALIDATE_EMAIL)) {
                        error_log("Invalid Email: ".trim($subscriber->getEmail()));
                    }
                    elseif(strpos($subscriber->getEmail(), '..') !== false)
                    {
                      error_log("Invalid Email. Double dots: ".trim($subscriber->getEmail()));
                    }
                    else
                    {
                        $receivers[trim($subscriber->getEmail())] = $subscriber->getName();
                    }
                }

                $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);

                $message = \Swift_Message::newInstance()
                    ->setSubject($entity->getSubject())
                    ->setFrom(array($settings->getSiteEmailAddress() => $settings->getSiteEmailTitle()))
                    ->setBcc($receivers)
                    ->setBody($entity->getContent(), 'text/html');
                $this->get('mailer')->send($message);
            }

            return $this->redirect($this->generateUrl('managenewsletters'));
        }

        return $this->render('WebmastersAfricaPressBundle:Newsletter:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Newsletter entity.
    *
    * @param Newsletter $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Newsletter $entity)
    {
        $form = $this->createForm(new NewsletterType(), $entity, array(
            'action' => $this->generateUrl('managenewsletters_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Newsletter entity.
     *
     */
    public function newAction()
    {
        $entity = new Newsletter();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaPressBundle:Newsletter:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Newsletter entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:Newsletter:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Newsletter entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:Newsletter:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Newsletter entity.
    *
    * @param Newsletter $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Newsletter $entity)
    {
        $form = $this->createForm(new NewsletterType(), $entity, array(
            'action' => $this->generateUrl('managenewsletters_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Newsletter entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Newsletter')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Newsletter entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if($entity->getSent())
            {
                $query = $em->createQuery(
                    'SELECT p
                        FROM WebmastersAfricaPressBundle:NewsletterSubscriber p
                        WHERE p.confirmed = :confirmed'
                )->setParameter('confirmed', true);
                $subscribers = $query->getResult();
                foreach($subscribers as $subscriber)
                {
                    $message = \Swift_Message::newInstance()
                        ->setSubject($entity->getSubject())
                        ->setFrom('inquiries@businesslicences.go.ug')
                        ->setTo($subscriber->getEmail())
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaPressBundle:Newsletter:newsletter.txt.twig',
                                array('content' => $entity->getContent())
                            )
                        )
                        ->setContentType("text/html");

                    $this->get('mailer')->send($message);
                }
            }

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('managenewsletters'));
        }

        return $this->render('WebmastersAfricaPressBundle:Newsletter:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Newsletter entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaPressBundle:Newsletter')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Newsletter entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        }

        return $this->redirect($this->generateUrl('managenewsletters'));
    }

    /**
     * Creates a form to delete a Newsletter entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managenewsletters_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

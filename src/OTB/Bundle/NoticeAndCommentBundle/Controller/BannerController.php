<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use OTB\Bundle\NoticeAndCommentBundle\Entity\Banner;
use OTB\Bundle\NoticeAndCommentBundle\Form\BannerType;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Banner controller.
 *
 * @Route("/managebanners")
 */
class BannerController extends Controller
{
    /**
     * Lists all Banner entities.
     *
     * @Route("/", name="managebanners")
     * @Template()
     */
    public function indexAction()
    {

        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM NoticeCommentBundle:Banner a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10 /*limit per page*/
        );
        return $this->render('NoticeCommentBundle:Banner:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }


    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $entity = $em->getRepository('NoticeCommentBundle:Banner')->find($batch_item);
                $em->remove($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('regulations_managebanners'));
    }


    /**
     * Creates a new banner entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Banner();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        
        if ($form->isValid()) {
            $entity->setDeleted(0);

            //Manage file uploads
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $file = $request->files->get('webmasters_contentbundle_bannertype')['file'];

            // Generate a unique name for the file before saving it
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->container->getParameter('banners_directory'),
                $fileName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $entity->upload($fileName);


            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('regulations_managebanners'));
        }

        return $this->render('NoticeCommentBundle:Banner:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Banner entity.
     *
     * @param Banner $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Banner $entity)
    {
        $form = $this->createForm(new BannerType(), $entity, array(
            'action' => $this->generateUrl('regulations_managebanners_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Banner entity.
     *
     */
    public function newAction()
    {
        $entity = new Banner();
        $form   = $this->createCreateForm($entity);

        return $this->render('NoticeCommentBundle:Banner:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    /**
     * Finds and displays a Banner entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('NoticeCommentBundle:Banner:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Banner entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('NoticeCommentBundle:Banner:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Banners entity.
     *
     * @param News $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Banner $entity)
    {
        $form = $this->createForm(new BannerType(), $entity, array(
            'action' => $this->generateUrl('regulations_managebanners_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('update', 'submit', array('label' => 'Update'));
        $form->add(
                'file',
                'file',
                array(
                    'label' => 'banners.image',
                    'required' => false,
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                        'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            );

        return $form;
    }
    /**
     * Edits an existing News entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            //Manage file uploads
            if ($request->files->get("webmasters_contentbundle_bannertype")['file']) {
                $file = $request->files->get('webmasters_contentbundle_bannertype')['file'];

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move(
                    $this->container->getParameter('banners_directory'),
                    $fileName
                );

                // Update the 'brochure' property to store the PDF file name
                // instead of its contents
                $entity->upload($fileName);
            }
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('regulations_managebanners'));
        }

        return $this->render(
            'NoticeCommentBundle:Banner:edit.html.twig',
            array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }
    /**
     * Deletes a News entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NoticeCommentBundle:Banner')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Banner entity.');
        }

        $em->remove($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'delete',
            'delete'
        );

        return $this->redirect($this->generateUrl('regulations_managebanners'));
    }

    /**
     * Creates a form to delete a Banner entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('regulations_managebanners_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete',
                                            'attr' => array('class'=>'w3-left w3-button w3-hover-teal w3-round-large w3-red w3-medium',
                                            'style'=>'padding: 10px 30px 30px 30px; margin-right:15px;')))
            ->getForm();
    }
}

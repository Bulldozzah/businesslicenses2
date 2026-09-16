<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use WebmastersAfrica\PressBundle\Entity\Page;
use WebmastersAfrica\PressBundle\Form\PageType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Page controller.
 *
 */
class PageController extends Controller
{

    /**
     * Lists all Page entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Page::class)->findBy(['published' => true, 'deleted' => 0, 'site' => 2], ['page_order' => 'ASC']);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            5000/*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:Page:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Lists all deleted Page entities.
     *
     */
    public function indexdeletedAction()
    {
        $em    = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Page::class)->findBy(['deleted' => 1, 'site' => 2]);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            5000/*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:Page:indexDeleted.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    /**
     * Lists all deleted Page entities.
     *
     */
    public function indexUnpublishedAction()
    {
        $em    = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Page::class)->findBy(['published' => false, 'deleted' => false, 'site' => 2]);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            5000/*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:Page:indexUnpublished.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        $published = false;
        $unpublished = false;
        $deleted = false;

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "index_unpublish") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($batch_item);
                $entity->setPublished(false);
                $entity->setDeleted(false);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
                $unpublished = true;
            } else if ($batch_select == "un_publish") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($batch_item);
                $entity->setPublished(true);
                $entity->setDeleted(false);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
                $published = true;
            } else if ($batch_select == "unpublish") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($batch_item);
                $entity->setPublished(false);
                $entity->setDeleted(false);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
                $unpublished = true;
            } else if ($batch_select == "delete_publish") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($batch_item);
                $entity->setDeleted(false);
                $entity->setPublished(true);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'update'
                );
                $deleted = true;
            } else if ($batch_select == "index_delete") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($batch_item);
                $entity->setDeleted(true);
                $entity->setPublished(false);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
                $unpublished = true;
            } else if ($batch_select == "un_delete") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($batch_item);
                $entity->setDeleted(true);
                $entity->setPublished(false);
                $em->persist($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
                $published = true;
            } else if ($batch_select == 'delete_permanently_delete') {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($batch_item);
                $em->remove($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
                $deleted = true;
            }
        }

        $em->flush();
        if ($published) {
            return $this->redirect($this->generateUrl('managepages_unpublished'));
        }

        if ($unpublished) {
            return $this->redirect($this->generateUrl('managepages'));
        }
        if ($deleted) {
            return $this->redirect($this->generateUrl('managepages_deleted'));
        }
    }

    /**
     * Creates a new Page entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Page();
        $slug = $this->get('cocur_slugify');
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setSite(2);
            $entity->setSlug(
                $slug->slugify(
                    $entity->getPageTitle()
                )
            );
            if (!$request->get('published')) {
                $entity->setPublished(false);
                $entity->setDeleted(false);
            }
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );

            return $this->redirect($this->generateUrl('managepages'));
        }

        return $this->render('WebmastersAfricaPressBundle:Page:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Page entity.
     *
     * @param Page $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Page $entity)
    {
        $form = $this->createForm(new PageType(), $entity, array(
            'action' => $this->generateUrl('managepages_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Page entity.
     *
     */
    public function newAction()
    {
        $slug = $this->get('cocur_slugify');
        $em = $this->getDoctrine()->getManager();
        /**$entity = $em->getRepository(Page::class)->findAll();
        foreach ($entity as $page) {
            $page->setSlug(
                $slug->slugify(
                    $page->getPageTitle()
                )
            );
            $em->flush();
        }
        die; **/
        $entity = new Page();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaPressBundle:Page:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Page entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:Page:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Page entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:Page:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Page entity.
     *
     * @param Page $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Page $entity)
    {
        $form = $this->createForm(new PageType(), $entity, array(
            'action' => $this->generateUrl('managepages_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Page entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $slug = $this->get('cocur_slugify');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setSlug(
                $slug->slugify(
                    $entity->getPageTitle()
                )
            );
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('managepages'));
        }
        return $this->render('WebmastersAfricaPressBundle:Page:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Page entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }
        $entity->setDeleted(true);
        $em->persist($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'delete',
            'delete'
        );

        return $this->redirect($this->generateUrl('managepages'));
    }

    /**
     * Restores a Page entity.
     *
     */
    public function restoreAction(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }
        $entity->setDeleted(false);
        $em->persist($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'update',
            'update'
        );

        return $this->redirect($this->generateUrl('managepages'));
    }

    /**
     * Creates a form to delete a Page entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managepages_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function updatePageOrderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $updated = false;
        $positions = $request->get('positions');
        foreach ($positions as $position) {
            $index = $position[0];
            $newPosition = $position[1];
            $page = $em->getRepository(Page::class)->find($index);
            if ($page) {
                $page->setPageOrder($newPosition);
                $em->persist($page);
                $updated = true;
            }
        }
        $em->flush();
        if ($updated) {
            return new Response(
                json_encode(
                    [
                        'success' => true,
                        'message' => 'updated successfully'
                    ]
                )
            );
        } else {
            return new Response(
                json_encode(
                    [
                        'success' => false,
                        'message' => 'Something went wrong'
                    ]
                )
            );
        }
    }
}

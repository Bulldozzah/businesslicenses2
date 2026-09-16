<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\PressBundle\Entity\Faq;
use WebmastersAfrica\PressBundle\Form\FaqType;
use Symfony\Component\HttpFoundation\Response;


/**
 * Faq controller.
 *
 */
class NcFaqController extends Controller
{

    /**
     * Lists all Faq entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->getDoctrine()->getManager();
        $pagination = $em->getRepository(Faq::class)->findBy(['deleted' => 0, 'site' => 1], ['orderLevel' => 'ASC', 'published' => 'DESC']);
        return $this->render('WebmastersAfricaPressBundle:Faq:nc_index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        $publish = $delete = $unpublish = false;

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "publish") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Faq')->find($batch_item);
                $entity->setPublished(true);
                $em->persist($entity);
                $publish = true;
            } else if ($batch_select == "unpublish") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Faq')->find($batch_item);
                $entity->setPublished(false);
                $em->persist($entity);
                $unpublish = true;
            } else if ($batch_select == "delete") {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Faq')->find($batch_item);
                $em->remove($entity);
                $delete = true;
            }
        }
        if ($publish) {
            $this->get('session')->getFlashBag()->add(
                'publish',
                'Publish Succesfull'
            );
        }
        if ($delete) {
            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        }
        if ($unpublish) {
            $this->get('session')->getFlashBag()->add(
                'unpublish',
                'unpublish'
            );
        }

        $em->flush();

        return $this->redirect($this->generateUrl('managencfaq'));
    }

    /**
     * Creates a new Faq entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Faq();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setEmail("-");
            $entity->setSite(1);
            $entity->setDeleted(false);
            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('managencfaq'));
        }

        return $this->render('WebmastersAfricaPressBundle:Faq:nc_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Faq entity.
     *
     * @param Faq $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Faq $entity)
    {
        $form = $this->createForm(new FaqType(), $entity, array(
            'action' => $this->generateUrl('managencfaq_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create',
                                            'attr' => array('class'=>'w3-right w3-button w3-hover-teal w3-round-large w3-blue w3-medium',
                                            'style'=>'padding: 10px 30px 30px 30px; margin-right:15px;')));

        return $form;
    }

    /**
     * Displays a form to create a new Faq entity.
     *
     */
    public function newAction()
    {
        $entity = new Faq();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaPressBundle:Faq:nc_new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Faq entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Faq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:Faq:nc_show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Faq entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Faq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:Faq:nc_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Faq entity.
     *
     * @param Faq $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Faq $entity)
    {
        $form = $this->createForm(new FaqType(), $entity, array(
            'action' => $this->generateUrl('managencfaq_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Faq entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Faq')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $faq_data = $request->request->get("webmastersafrica_pressbundle_faq");

        $em = $this->getDoctrine()->getManager();
        $entity->setQuestion($faq_data['question']);
        $entity->setAnswer($faq_data['answer']);
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('managencfaq'));

        return $this->render('WebmastersAfricaPressBundle:Faq:nc_edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Faq entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaPressBundle:Faq')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Faq entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        }

        return $this->redirect($this->generateUrl('managencfaq'));
    }

    /**
     * Creates a form to delete a Faq entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managencfaq_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete',
                                            'attr' => array('class'=>'w3-left w3-button w3-hover-teal w3-round-large w3-red w3-medium',
                                            'style'=>'padding: 10px 30px 30px 30px; margin-right:15px;')))
            ->getForm();
    }

    public function updateOrderLevelAction(Request $request)
    {
	     	$em = $this->getDoctrine()->getManager();
	    	$updated = false;
	      $positions = $request->get('positions');
	      foreach ($positions as $position) {
	          	$index = $position[0];
	          	$newPosition = $position[1];
	          	$faq = $em->getRepository(Faq::class)->find($index);
	          	if ($faq) {
	          		$faq->setOrderLevel($newPosition);
	          		$em->persist($faq);
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

<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\LicenseBundle\Entity\Feedback;
use WebmastersAfrica\LicenseBundle\Form\FeedbackType;

/**
 * Feedback controller.
 *
 */
class FeedbackController extends Controller
{

    /**
     * Lists all Feedback entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $feedback = $em->getRepository(Feedback::class)->findUnRepliedAll();

        return $this->render('WebmastersAfricaPressBundle:Feedback:index.html.twig', array(
            'pagination' => $feedback,
        ));
    }

    public function repliedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $feedback = $em->getRepository(Feedback::class)->findRepliedAll();

        return $this->render('WebmastersAfricaPressBundle:Feedback:index_replied.html.twig', array(
            'pagination' => $feedback,
        ));
    }
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $feedback = $em->getRepository(Feedback::class)->find($id);

        return $this->render('WebmastersAfricaPressBundle:Feedback:show.html.twig', array(
            'pagination' => $feedback,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $entity = $em->getRepository('WebmastersAfricaLicenseBundle:Feedback')->find($batch_item);
                $em->remove($entity);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('managefeedback'));
    }

    /**
     * Displays a form to edit an existing Feedback entity.
     *
     */
    // public function editAction($id)
    // {
    //     $em = $this->getDoctrine()->getManager();

    //     $entity = $em->getRepository('WebmastersAfricaLicenseBundle:Feedback')->find($id);

    //     if (!$entity) {
    //         throw $this->createNotFoundException('Unable to find Feedback entity.');
    //     }

    //     $editForm = $this->createEditForm($entity);
    //     $deleteForm = $this->createDeleteForm($id);

    //     return $this->render('WebmastersAfricaPressBundle:Feedback:edit.html.twig', array(
    //         'entity'      => $entity,
    //         'edit_form'   => $editForm->createView(),
    //         'delete_form' => $deleteForm->createView(),
    //     ));
    // }



    /**
     * Displays a form to edit an existing Feedback entity.
     *
     */
    public function replyAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:Feedback')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feedback entity.');
        }

        $defaultData = array('message' => 'Feedback');
        $form = $this->createFormBuilder($defaultData)
            ->add('message',
                 'textarea',
                  array("data" => "", 'required' => true,
                  'label_attr' => array(
                                    "class" => "label-required"),
              )
            )
            ->add(
                'send',
                'submit',
                array('label' => 'Send', 'translation_domain' => 'WebmastersAfricaPressBundle')
            )
            ->getForm();

        return $this->render('WebmastersAfricaPressBundle:Feedback:reply.html.twig', array(
            'entity'      => $entity,
            'reply_form'   => $form->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing Feedback entity.
     *
     */
    public function sendAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository(
            'WebmastersAfricaUserBundle:Setting'
        )->find(1);

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:Feedback')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feedback entity.');
        }

        $form = $request->request->get("form");
        if (!filter_var($entity->getEmail(), FILTER_VALIDATE_EMAIL)) {
             $this->get('session')->getFlashBag()->add(
                'email_error',
                'Email Invalid'
            );
            return $this->redirect($this->generateUrl('managefeedback'));
        }
        $message = \Swift_Message::newInstance()
            ->setSubject('Feedback From Licensing Portal')
            ->setFrom($settings->getSiteEmailAddress())
            ->setTo($entity->getEmail())
            ->setBody($form['message'], 'text/html');
        $this->get('mailer')->send($message);
        $entity->setReplied(true);
        $entity->setReplyMessage($form['message']);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
                'email_success',
                'Reply Sent Successfully'
            );
        return $this->redirect($this->generateUrl('managefeedback'));
    }

    /**
     * Creates a form to edit a Feedback entity.
     *
     * @param Feedback $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Feedback $entity)
    {
        $form = $this->createForm(new FeedbackType(), $entity, array(
            'action' => $this->generateUrl('managefeedback_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Feedback entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:Feedback')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Feedback entity.');
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

            return $this->redirect($this->generateUrl('managefeedback'));
        }

        return $this->render('WebmastersAfricaPressBundle:Feedback:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Feedback entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaLicenseBundle:Feedback')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Feedback entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        }

        return $this->redirect($this->generateUrl('managefeedback'));
    }

    /**
     * Creates a form to delete a Feedback entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managefeedback_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}

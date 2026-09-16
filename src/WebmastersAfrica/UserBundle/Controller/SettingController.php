<?php

namespace WebmastersAfrica\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\UserBundle\Entity\Setting;
use WebmastersAfrica\UserBundle\Form\SettingType;

/**
 * Setting controller.
 *
 */
class SettingController extends Controller
{

    /**
     * Lists all Setting entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('WebmastersAfricaUserBundle:Setting')->findAll();

        return $this->render(
            'WebmastersAfricaUserBundle:Setting:index.html.twig',
            array(
                'entities' => $entities,
            )
        );
    }
    /**
     * Creates a new Setting entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Setting();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('setting_edit', array('id' => $entity->getId())));
        }

        return $this->render('WebmastersAfricaUserBundle:Setting:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Setting entity.
     *
     * @param Setting $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Setting $entity)
    {
        $form = $this->createForm(new SettingType(), $entity, array(
            'action' => $this->generateUrl('setting_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Setting entity.
     *
     */
    public function newAction()
    {
        $entity = new Setting();
        $form   = $this->createCreateForm($entity);

        return $this->render(
            'WebmastersAfricaUserBundle:Setting:new.html.twig',
            array(
                'entity' => $entity,
                'form'   => $form->createView(),
            )
        );
    }

    public function adminLoginAction(Request $request)
    {
        $csrf = $this->get('form.csrf_provider');
        $csrf_token = $csrf->generateCsrfToken($intention);
        return $this->render(
            'WebmastersAfricaUserBundle:User:admin_login.html.twig',
            [
                'error' =>  '',
                'csrf_token' => $csrf_token
            ]
        );
    }

    /**
     * Finds and displays a Setting entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Setting entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaUserBundle:Setting:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Setting entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Setting entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaUserBundle:Setting:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a Setting entity.
     *
     * @param Setting $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Setting $entity)
    {
        $form = $this->createForm(new SettingType(), $entity, array(
            'action' => $this->generateUrl('setting_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Setting entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Setting entity.');
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
            return $this->redirect($this->generateUrl('setting_edit', array('id' => $id)));
        }

        return $this->render('WebmastersAfricaUserBundle:Setting:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Setting entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Setting entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('setting'));
    }

    /**
     * Creates a form to delete a Setting entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('setting_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}

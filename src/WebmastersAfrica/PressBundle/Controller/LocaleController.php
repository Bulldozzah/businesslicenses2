<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\PressBundle\Entity\Locale;
use WebmastersAfrica\PressBundle\Form\LocaleType;

/**
 * Locale controller.
 *
 */
class LocaleController extends Controller
{
    public function setlocaleAction($locale)
    {
        $session = $this->getRequest()->getSession();
        $session->set('locale', $locale);
        $this->get('session')->set('_locale', $locale);
        $this->get('session')->set('_locale_2', $locale);

        $request = $this->getRequest();
        $request->setLocale($locale);
        
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach($batch_items as $batch_item)
        {
            if($batch_select == "enable")
            {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($batch_item);
                $entity->setEnabled(true);
                $em->persist($entity);
            }
            else if($batch_select == "disable")
            {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($batch_item);
                $entity->setEnabled(false);
                $em->persist($entity);
            }
            else if($batch_select == "delete")
            {
                $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($batch_item);
                $em->remove($entity);

                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('locale'));
    }

    /**
     * Lists all Locale entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaPressBundle:Locale a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            5/*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:Locale:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }
    /**
     * Creates a new Locale entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Locale();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            //Check if location has been set to default, if it has then set everything else to not default
            if($entity->getIsDefault())
            {
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery(
                    'SELECT p
                        FROM WebmastersAfricaPressBundle:Locale p
                        WHERE p.id <> :id'
                )->setParameter('id', $entity->getId());
                $otherlocales = $query->getResult();
                foreach($otherlocales as $otherlocale)
                {
                    $em = $this->getDoctrine()->getManager();
                    $otherlocale->setIsDefault(0);
                    $em->persist($otherlocale);
                    $em->flush();
                }
            }

            return $this->redirect($this->generateUrl('locale'));
        }

        return $this->render('WebmastersAfricaPressBundle:Locale:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a Locale entity.
    *
    * @param Locale $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Locale $entity)
    {
        $form = $this->createForm(new LocaleType(), $entity, array(
            'action' => $this->generateUrl('locale_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Locale entity.
     *
     */
    public function newAction()
    {
        $entity = new Locale();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaPressBundle:Locale:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Locale entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:Locale:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing Locale entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaPressBundle:Locale:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Locale entity.
     *
     */
    public function editfilesAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        $my_file1 = dirname(__FILE__).'/../../AdminBundle/Resources/translations/WebmastersAfricaAdminBundle.'.$entity->getLocaleCode().'.yml';
        $handle1 = fopen($my_file1, 'r');
        $locale_admin = fread($handle1,filesize($my_file1));
        if (file_exists($my_file1)) {
            //Awesome
        }
        else
        {
            $file = dirname(__FILE__).'/../../AdminBundle/Resources/translations/WebmastersAfricaAdminBundle.en.yml';
            $newfile = $my_file1;

            if (!copy($file, $newfile)) {
                echo "failed to copy $file...\n. Check write permissions on translations folders.";
                exit;
            }
            else
            {
                $handle1 = fopen($my_file1, 'r');
                $locale_admin = fread($handle1,filesize($my_file1));
            }
        }

        $my_file2 = dirname(__FILE__).'/../../LicenseBundle/Resources/translations/WebmastersAfricaLicenseBundle.'.$entity->getLocaleCode().'.yml';
        $handle2 = fopen($my_file2, 'r');
        $locale_license = fread($handle2,filesize($my_file2));
        if (file_exists($my_file2)) {
            //Awesome
        }
        else
        {
            $file = dirname(__FILE__).'/../../LicenseBundle/Resources/translations/WebmastersAfricaLicenseBundle.en.yml';
            $newfile = $my_file2;

            if (!copy($file, $newfile)) {
                echo "failed to copy $file...\n. Check write permissions on translations folders.";
                exit;
            }
            else
            {
                $handle2 = fopen($my_file2, 'r');
                $locale_license = fread($handle2,filesize($my_file2));
            }
        }

        $my_file3 = dirname(__FILE__).'/../../MessageBundle/Resources/translations/WebmastersAfricaMessageBundle.'.$entity->getLocaleCode().'.yml';
        $handle3 = fopen($my_file3, 'r');
        $locale_message = fread($handle3,filesize($my_file3));
        if (file_exists($my_file3)) {
            //Awesome
        }
        else
        {
            $file = dirname(__FILE__).'/../../MessageBundle/Resources/translations/WebmastersAfricaMessageBundle.en.yml';
            $newfile = $my_file3;

            if (!copy($file, $newfile)) {
                echo "failed to copy $file...\n. Check write permissions on translations folders.";
                exit;
            }
            else
            {
                $handle3 = fopen($my_file3, 'r');
                $locale_message = fread($handle3,filesize($my_file3));
            }
        }

        $my_file4 = dirname(__FILE__).'/../../PressBundle/Resources/translations/WebmastersAfricaPressBundle.'.$entity->getLocaleCode().'.yml';
        $handle4 = fopen($my_file4, 'r');
        $locale_press = fread($handle4,filesize($my_file4));
        if (file_exists($my_file4)) {
            //Awesome
        }
        else
        {
            $file = dirname(__FILE__).'/../../PressBundle/Resources/translations/WebmastersAfricaPressBundle.en.yml';
            $newfile = $my_file4;

            if (!copy($file, $newfile)) {
                echo "failed to copy $file...\n. Check write permissions on translations folders.";
                exit;
            }
            else
            {
                $handle4 = fopen($my_file4, 'r');
                $locale_press = fread($handle4,filesize($my_file4));
            }
        }

        $my_file5 = dirname(__FILE__).'/../../TaskBundle/Resources/translations/WebmastersAfricaTaskBundle.'.$entity->getLocaleCode().'.yml';
        $handle5 = fopen($my_file5, 'r');
        $locale_tasks = fread($handle5,filesize($my_file5));
        if (file_exists($my_file5)) {
            //Awesome
        }
        else
        {
            $file = dirname(__FILE__).'/../../TaskBundle/Resources/translations/WebmastersAfricaTaskBundle.en.yml';
            $newfile = $my_file5;

            if (!copy($file, $newfile)) {
                echo "failed to copy $file...\n. Check write permissions on translations folders.";
                exit;
            }
            else
            {
                $handle5 = fopen($my_file5, 'r');
                $locale_tasks = fread($handle5,filesize($my_file5));
            }
        }

        $my_file6 = dirname(__FILE__).'/../../UserBundle/Resources/translations/WebmastersAfricaUserBundle.'.$entity->getLocaleCode().'.yml';
        $handle6 = fopen($my_file6, 'r');
        $locale_user = fread($handle6,filesize($my_file6));
        if (file_exists($my_file6)) {
            //Awesome
        }
        else
        {
            $file = dirname(__FILE__).'/../../UserBundle/Resources/translations/WebmastersAfricaUserBundle.en.yml';
            $newfile = $my_file6;

            if (!copy($file, $newfile)) {
                echo "failed to copy $file...\n. Check write permissions on translations folders.";
                exit;
            }
            else
            {
                $handle6 = fopen($my_file6, 'r');
                $locale_user = fread($handle6,filesize($my_file6));
            }
        }

        return $this->render('WebmastersAfricaPressBundle:Locale:editfiles.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'locale_admin' => $locale_admin,
            'locale_license' => $locale_license,
            'locale_message' => $locale_message,
            'locale_press' => $locale_press,
            'locale_tasks' => $locale_tasks,
            'locale_user' => $locale_user,
        ));
    }

    /**
    * Creates a form to edit a Locale entity.
    *
    * @param Locale $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Locale $entity)
    {
        $form = $this->createForm(new LocaleType(), $entity, array(
            'action' => $this->generateUrl('locale_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }


    
    /**
     * Edits an existing Locale entity.
     *
     */
    public function updatefilesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
        }

        $my_file1 = dirname(__FILE__).'/../../AdminBundle/Resources/translations/WebmastersAfricaAdminBundle.'.$entity->getLocaleCode().'.yml';
        $handle1 = fopen($my_file1, 'w') or die('Cannot open file:  '.$my_file1);

        $my_file2 = dirname(__FILE__).'/../../LicenseBundle/Resources/translations/WebmastersAfricaLicenseBundle.'.$entity->getLocaleCode().'.yml';
        $handle2 = fopen($my_file2, 'w') or die('Cannot open file:  '.$my_file2);

        $my_file3 = dirname(__FILE__).'/../../MessageBundle/Resources/translations/WebmastersAfricaMessageBundle.'.$entity->getLocaleCode().'.yml';
        $handle3 = fopen($my_file3, 'w') or die('Cannot open file:  '.$my_file3);

        $my_file4 = dirname(__FILE__).'/../../PressBundle/Resources/translations/WebmastersAfricaPressBundle.'.$entity->getLocaleCode().'.yml';
        $handle4 = fopen($my_file4, 'w') or die('Cannot open file:  '.$my_file4);

        $my_file5 = dirname(__FILE__).'/../../TaskBundle/Resources/translations/WebmastersAfricaTaskBundle.'.$entity->getLocaleCode().'.yml';
        $handle5 = fopen($my_file5, 'w') or die('Cannot open file:  '.$my_file5);

        $my_file6 = dirname(__FILE__).'/../../UserBundle/Resources/translations/WebmastersAfricaUserBundle.'.$entity->getLocaleCode().'.yml';
        $handle6 = fopen($my_file6, 'w') or die('Cannot open file:  '.$my_file6);

        fwrite($handle1, $request->request->get('locale_admin'));
        fwrite($handle2, $request->request->get('locale_license'));
        fwrite($handle3, $request->request->get('locale_message'));
        fwrite($handle4, $request->request->get('locale_press'));
        fwrite($handle5, $request->request->get('locale_task'));
        fwrite($handle6, $request->request->get('locale_user'));

        return $this->redirect($this->generateUrl('locale'));
    }
    
    /**
     * Edits an existing Locale entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locale entity.');
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

            //Check if location has been set to default, if it has then set everything else to not default
            if($entity->getIsDefault())
            {
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery(
                    'SELECT p
                        FROM WebmastersAfricaPressBundle:Locale p
                        WHERE p.id <> :id'
                )->setParameter('id', $entity->getId());
                $otherlocales = $query->getResult();
                foreach($otherlocales as $otherlocale)
                {
                    $em = $this->getDoctrine()->getManager();
                    $otherlocale->setIsDefault(0);
                    $em->persist($otherlocale);
                    $em->flush();
                }
            }

            return $this->redirect($this->generateUrl('locale'));
        }

        return $this->render('WebmastersAfricaPressBundle:Locale:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Locale entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaPressBundle:Locale')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Locale entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        }

        return $this->redirect($this->generateUrl('locale'));
    }

    /**
     * Creates a form to delete a Locale entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('locale_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

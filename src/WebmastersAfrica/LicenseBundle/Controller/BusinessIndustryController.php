<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Form\BusinessIndustryType;
use OTB\Bundle\NoticeAndCommentBundle\Entity\SubscriberList;
use Symfony\Component\HttpFoundation\Response;

/**
 * BusinessIndustry controller.
 *
 */
class BusinessIndustryController extends Controller
{

    /**
     * Lists all BusinessIndustry entities.
     *
     */
    public function indexAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaLicenseBundle:BusinessIndustry a";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1)/*page number*/,
            10000/*limit per page*/

        );

        return $this->render('WebmastersAfricaLicenseBundle:BusinessIndustry:index.html.twig', array(
            'pagination' => $pagination,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "unpublish") {
                $business_licenes = $em->getRepository(BusinessLicense::class)->findPublishedLicensesByIndustry($batch_item);
                if ($business_licenes) {
                    $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this industry. Has Licenses associated to it');
                } else {
	                  $this->get('session')->getFlashBag()->add(
	                    'delete',
	                    'delete'
	                  );
                  $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessIndustry')->find($batch_item);
                  $em->remove($entity);
                }
            }
        }

        $em->flush();

        return $this->redirect($this->generateUrl('manageindustries'));
    }

    /**
     * Creates a new BusinessIndustry entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new BusinessIndustry();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
						$this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );
            return $this->redirect($this->generateUrl('manageindustries'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:BusinessIndustry:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
    * Creates a form to create a BusinessIndustry entity.
    *
    * @param BusinessIndustry $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(BusinessIndustry $entity)
    {
        $form = $this->createForm(new BusinessIndustryType(), $entity, array(
            'action' => $this->generateUrl('manageindustries_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create', 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                , 'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )));

        return $form;
    }

    /**
     * Displays a form to create a new BusinessIndustry entity.
     *
     */
    public function newAction()
    {
        $entity = new BusinessIndustry();
        $form   = $this->createCreateForm($entity);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessIndustry:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a BusinessIndustry entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessIndustry')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessIndustry entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessIndustry:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to edit an existing BusinessIndustry entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessIndustry')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessIndustry entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('WebmastersAfricaLicenseBundle:BusinessIndustry:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a BusinessIndustry entity.
    *
    * @param BusinessIndustry $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(BusinessIndustry $entity)
    {
        $form = $this->createForm(new BusinessIndustryType(), $entity, array(
            'action' => $this->generateUrl('manageindustries_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('update', 'submit', array('label' => 'Update', 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                , 'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )));

        return $form;
    }
    /**
     * Edits an existing BusinessIndustry entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessIndustry')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business Industry entity.');
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

            return $this->redirect($this->generateUrl('manageindustries'));
        }

        return $this->render('WebmastersAfricaLicenseBundle:BusinessIndustry:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a BusinessIndustry entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessIndustry')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find BusinessIndustry entity.');
            }

            $em->remove($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        //}

        return $this->redirect($this->generateUrl('manageindustries'));
    }

    /**
     * Creates a form to delete a BusinessIndustry entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manageindustries_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    public function addUserIndustrySubscriptionAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new SubscriberList();
        $userDetails = $this->container->get('security.context')->getToken()->getUser();
        if ($userDetails == 'anon.') {
            return new Response(json_encode(['success' => false, 'message' => '<p class="alert alert-danger"><a href="/login">Login To Subscribe to this industry</a></p>']));
        } else {
            $industry = $request->get('industry');
            $user = $userDetails->getId();
            $industryDetails = $em->getRepository(BusinessIndustry::class)->find($industry);
            if ($industry && $userDetails) {
                $entity = $em->getRepository(SubscriberList::class)->findOneBy(["user" => $userDetails->getId(), "industry" => $industry]);
                if ($entity) {
                		$industryList = $em->getRepository(SubscriberList::class)->find($entity->getId());
                    $em->remove($industryList);
                    $em->flush();
                    $domain = "www.businesslicenses.gov.zm";
                    $domain = str_replace("www.", "", $domain);
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Sector Unsubscription Successful')
                        ->setFrom('info@' . $domain)
                        ->setTo($userDetails->getEmail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaLicenseBundle:BusinessIndustry:industryunSub.html.twig',
                                array('path' => $request->getSchemeAndHttpHost(),'industry' => $industryDetails)
                            )
                        );
                    $this->get('mailer')->send($message);
                    return new Response(json_encode(['success' => true, 'sub' => false, 'message' => 'Successfully unsubscribed']));
                } else {
                    $entity = new SubscriberList();
                    $entity->setUser($user);
                    $entity->setIndustry($em->getRepository(BusinessIndustry::class)->find($industry));
                    $em->persist($entity);
                    $em->flush();
                    // an event to send emails.
                    $domain = "www.businesslicenses.gov.zm";
                    $domain = str_replace("www.", "", $domain);
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Sector Subscription Successful')
                        ->setFrom('info@' . $domain)
                        ->setTo($userDetails->getEmail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaLicenseBundle:BusinessIndustry:industrySub.html.twig',
                                array('path' => $request->getSchemeAndHttpHost(), 'industry' => $industryDetails)
                            )
                        );
                    $this->get('mailer')->send($message);
                    return new Response(json_encode(['success' => true, 'sub' => true, 'message' => 'Subscribed successfully']));
                }
            } else {
                return new Response(json_encode(['success' => false, 'message' => 'Unable to subscribe please try again later']));
            }
        }
    }
}

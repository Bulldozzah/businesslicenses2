<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Form\BusinessAgencyType;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WebmastersAfrica\LicenseBundle\Entity\SubscriberList;
use Symfony\Component\HttpFoundation\Response;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation;
use OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlanMainCategory;
use Symfony\Component\HttpFoundation\File\File;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;

/**
 * BusinessAgency controller.
 *
 */
class BusinessAgencyController extends Controller
{

    /**
     * Lists all BusinessAgency entities.
     *
     */
    public function indexAction()
    {
        //If a user has logged in  and is part of agency, prepare an agency filter
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
        $my_ageny = [];
        $agencies = $user->getAgencies();

        $agencies_filter = "";
        $agencies_filter_ids = "";

        $count = 0;
        $agencysize = sizeof($agencies);
        foreach ($agencies as $agency) {
            $my_ageny[] = $agency->getId();
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
        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
            if ($agencies_filter_ids == "") {
                $dql   = "SELECT a FROM WebmastersAfricaLicenseBundle:BusinessAgency a WHERE a.deleted = 1 OR  a.deleted = 0";
            } else {
                $dql   = "SELECT a FROM WebmastersAfricaLicenseBundle:BusinessAgency a WHERE (" . $agencies_filter_ids . ") AND a.deleted = 0";
            }
        } else {
            $dql   = "SELECT a FROM WebmastersAfricaLicenseBundle:BusinessAgency a WHERE a.deleted = 0";
        }
        $query = $em->createQuery($dql)->getResult();

        return $this->render('WebmastersAfricaLicenseBundle:BusinessAgency:index.html.twig', array(
            'pagination' => $query,
        ));
    }

    public function indexDeletedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $agencies = $em->getRepository(BusinessAgency::class)->findBy(['deleted' => 1]);
        return $this->render('WebmastersAfricaLicenseBundle:BusinessAgency:indexDeleted.html.twig', array(
            'agencies' => $agencies,
        ));
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        $unsuccessdelete = true;
        $publish = false;
        $count = 0;
        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $businessLicense = $em->getRepository(BusinessLicense::class)->findByAgency($batch_item);
                if (!$businessLicense) {
                    $regulation = $em->getRepository(Regulation::class)->findByAgency($batch_item);
                    if (!$regulation) {
                        $forwardPlans = $em->getRepository(ForwardPlanMainCategory::class)->findByAgency($batch_item);
                        if (!$forwardPlans) {
                            $entity = $em->getRepository(
                                'WebmastersAfricaLicenseBundle:BusinessAgency'
                            )->find($batch_item);
                            $entity->setDeleted(1);
                            $em->persist($entity);
                            $em->flush();
                            $unsuccessdelete = false;
                        } else {
                            $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this Issuing Authority. Has Forward Plans associated to it');
                        }
                    } else {
                        $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this Issuing Authority. Has Regulations associated to it');
                    }
                } else {
                    $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this Issuing Authority. Has Licenses associated to it');
                }
            } else if ($batch_select == 'publish') {
                $agency = $em->getRepository(BusinessAgency::class)->find($batch_item);
                $agency->setDeleted(0);
                $em->persist($agency);
                $em->flush();
                $count = $count + 1;
                $publish = true;
            }
        }
        if (!$unsuccessdelete) {
            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
        }
        if ($publish) {
            $this->get('session')->getFlashBag()->add(
                'publish',
                $count . " item(s) published"
            );
            return $this->redirect($this->generateUrl('manageagencies_deleted'));
        }

        return $this->redirect($this->generateUrl('manageagencies'));
    }

    public function upload(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();

        $pos = strrpos($fileName, ".");
        $ext = substr($fileName, $pos);
        $fp = substr($fileName, 0, $pos);
        $dir = rtrim($this->container->getParameter('documents_directory'), '/\\') . DIRECTORY_SEPARATOR;

        while (file_exists($dir . $fileName)) {
            $fileName = $fp . "_" . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYX'), 0, 10) . $ext;
        }

        $file->move(
            $this->container->getParameter('documents_directory'),
            $fileName
        );
        return $fileName;
    }

    /**
     * Creates a new BusinessAgency entity.
     *
     */
    public function createAction(Request $request)
    {
        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("AGENCY_CREATE")')))) {
            $slug = $this->get('cocur_slugify');
            $entity = new BusinessAgency();
            $form = $this->createCreateForm($entity);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $entity->setSlug(
                    $slug->slugify(
                        $request->get('webmastersafrica_licensebundle_businessagency')['title']
                    )
                );
                if ($entity->getUsers()->contains($this->getUser())) {
                    error_log("this user is in this list");
                } else {
                    $entity->addUser($this->getUser());
                    error_log("this user is not in this list");
                }
                if ($request->files->get('webmastersafrica_licensebundle_businessagency')['file']) {
                    $fileName = $this->upload(
                        $request->files->get(
                            'webmastersafrica_licensebundle_businessagency'
                        )['file']
                    );
                    $entity->upload($fileName);
                }
                $em->persist($entity);
                $em->flush();
                $this->get('session')
                ->getFlashBag()
                ->add(
                    'agency_create_success',
                    'Issuing Authority was created successfully'
                );
                
                $workflows = $em->getRepository(Workflow::class)->findAll();
                if ($workflows) {
                    foreach ($workflows as $workflow) {
                        $workflow->addAgency($entity);
                        $em->persist($workflow);
                    }
                    $em->flush();
                }
                return $this->redirect($this->generateUrl('manageagencies'));
            }
            $this->get('session')
                ->getFlashBag()
                ->add(
                    'agency_create_unsuccess',
                    'Issuing Authority creation unsuccessful'
                );

            return $this->render(
                'WebmastersAfricaLicenseBundle:BusinessAgency:new.html.twig',
                array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                )
            );
        } else {
            return $this->redirect($this->generateUrl('manageagencies'));
        }
    }

    /**
     * Creates a form to create a BusinessAgency entity.
     *
     * @param BusinessAgency $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessAgency $entity)
    {
        $form = $this->createForm(
            new BusinessAgencyType(),
            $entity,
            array(
                'action' => $this->generateUrl('manageagencies_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BusinessAgency entity.
     *
     */
    public function newAction()
    {
        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("AGENCY_CREATE")')))) {
            $entity = new BusinessAgency();
            $form   = $this->createCreateForm($entity);

            return $this->render(
                'WebmastersAfricaLicenseBundle:BusinessAgency:new.html.twig',
                array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                )
            );
        }

        return $this->redirect($this->generateUrl('manageagencies'));
    }

    /**
     * Finds and displays a BusinessAgency entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessAgency')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessAgency entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessAgency:show.html.twig',
            array(
                'entity'      => $entity,
                'agency' => $id,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing BusinessAgency entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessAgency'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Business Agency.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessAgency:edit.html.twig',
            array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );

        return $this->redirect($this->generateUrl('manageagencies'));
    }

    /**
     * Creates a form to edit a BusinessAgency entity.
     *
     * @param BusinessAgency $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessAgency $entity)
    {
        $form = $this->createForm(
            new BusinessAgencyType(),
            $entity,
            array(
                'action' => $this->generateUrl('manageagencies_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing BusinessAgency entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $slug = $this->get('cocur_slugify');

        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessAgency'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessAgency entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        $originalOffices = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($entity->getAgencyoffices() as $office) {
            $originalOffices->add($office);
        }

        if ($editForm->isValid()) {
            // remove the relationship between the tag and the Task
            foreach ($originalOffices as $office) {
                if (false === $entity->getAgencyoffices()->contains($office)) {
                    $entity->getRequirements()->removeElement($office);
                    $agencyoffice = $em->getRepository(
                        'WebmastersAfricaLicenseBundle:BusinessAgencyOffice'
                    )->find($office->getId());
                    $em->remove($agencyoffice);
                } else {
                    error_log("Google missing");
                }
            }

            if ($request->files->get('webmastersafrica_licensebundle_businessagency')['file']) {
                $fileName = $this->upload(
                    $request->files->get(
                        'webmastersafrica_licensebundle_businessagency'
                    )['file']
                );
                $entity->upload($fileName);
            }

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('manageagencies'));
        }
        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessAgency:edit.html.twig',
            array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            )
        );
    }
    /**
     * Deletes a BusinessAgency entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessAgency'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessAgency entity.');
        }
        $businessLicense = $em->getRepository(BusinessLicense::class)->findByAgency($entity->getId());
        $regulation = $em->getRepository(Regulation::class)->findByAgency($entity->getId());
        $forwardPlans = $em->getRepository(ForwardPlanMainCategory::class)->findByAgency($entity->getId());

        if ($businessLicense) {
            $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this Issuing Authority. Has Licenses associated to it');
            return $this->redirect($this->generateUrl('manageagencies'));
        } else if ($regulation) {
            $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this Issuing Authority. Has Regulation associated to it');
            return $this->redirect($this->generateUrl('manageagencies'));
        } else if ($forwardPlans) {
            $this->get('session')->getFlashBag()->add('unsuccessdelete', 'You Can\'t delete this Issuing Authority. Has Forward Plans associated to it');
            return $this->redirect($this->generateUrl('manageagencies'));
        }

        $entity->setDeleted(1);
        $em->persist($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'delete',
            'delete'
        );
        //}

        return $this->redirect($this->generateUrl('manageagencies'));
    }

    /**
     * Creates a form to delete a BusinessAgency entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl('manageagencies_delete', array('id' => $id))
            )
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function getAgencyList()
    {
        $em = $this->getDoctrine()->getManager();
        $agency = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessAgency'
        )->getActiveAgencies();
    }


    public function addUserAgencySubscriptionAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new SubscriberList();
        $userDetails = $this->container->get('security.context')->getToken()->getUser();
        if ($userDetails == 'anon.') {
            return new Response(json_encode(['success' => false, 'message' => '<p class="alert alert-danger"><a href="/login">Login To Subscribe to this agency</a></p>']));
        } else {
            $agency = $request->get('agency');
            $user = $userDetails->getId();
            $agencyDetails = $em->getRepository(BusinessAgency::class)->find($agency);
            if ($agency && $userDetails) {
                $entity = $em->getRepository(SubscriberList::class)->findOneBy(["user" => $userDetails->getId(), "agency" => $agency]);
                if ($entity) {
                    $agencyRemove = $em->getRepository(SubscriberList::class)->find($entity->getId());
                    $em->remove($agencyRemove);
                    $em->flush();
                    $domain = "www.businesslicenses.gov.zm";
                    $domain = str_replace("www.", "", $domain);
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Agency Unsubscription Successful')
                        ->setFrom('info@' . $domain)
                        ->setTo($userDetails->getEmail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaLicenseBundle:BusinessAgency:agencyUnsub.html.twig',
                                array('path' => $request->getSchemeAndHttpHost(), 'agency' => $agencyDetails)
                            )
                        );
                    $this->get('mailer')->send($message);
                    return new Response(json_encode(['success' => true, 'sub' => false, 'message' => 'Successfully unsubscribed']));
                } else {
                    $entity = new SubscriberList();
                    $entity->setUser($user);
                    $entity->setAgency($em->getRepository(BusinessAgency::class)->find($agency));
                    $em->persist($entity);
                    $em->flush();
                    // an event to send emails.
                    $domain = "www.businesslicenses.gov.zm";
                    $domain = str_replace("www.", "", $domain);
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Agency Subscription Successful')
                        ->setFrom('info@' . $domain)
                        ->setTo($userDetails->getEmail())
                        ->setContentType("text/html")
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaLicenseBundle:BusinessAgency:agencySub.html.twig',
                                array('path' => $request->getSchemeAndHttpHost(), 'agency' => $agencyDetails)
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

    public function updateSlugForAgencyTitleAction()
    {
        $em = $this->getDoctrine()->getManager();
        $slug = $this->get('cocur_slugify');
        $entity = $em->getRepository(BusinessAgency::class)->findAll();
        $count = 0;
        foreach ($entity as $key) {
            $entity_agency = $em->getRepository(BusinessAgency::class)->findOneBy(['id' => $key->getId()]);
            $em = $this->getDoctrine()->getManager();
            $entity_agency->setSlug(
                $slug->slugify($entity_agency->getTitle())
            );
            $em->persist($entity_agency);
            $em->flush();
            $em->clear();
            $count += 1;
        }

        $request = $this->container->get('request');
        $routeURL = $request->getSchemeAndHttpHost();
        return new Response(json_encode(['count' => $count]));
    }
}

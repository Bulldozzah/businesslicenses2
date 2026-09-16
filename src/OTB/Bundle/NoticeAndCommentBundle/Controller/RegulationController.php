<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation;
use OTB\Bundle\NoticeAndCommentBundle\Form\RegulationType;
use Symfony\Component\HttpFoundation\Response;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Comment;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Like;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Notification;
use Doctrine\Common\Collections\ArrayCollection;
use WebmastersAfrica\LicenseBundle\Entity\SubscriberList;
use OTB\Bundle\NoticeAndCommentBundle\Entity\SubscriberList as SubscriberListIndustry;
use WebmastersAfrica\UserBundle\Entity\User;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;
use OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFixedFields;
use OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationField;
use OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationFieldData;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Position;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use OTB\Bundle\NoticeAndCommentBundle\Entity\EmailQueue;

/**
 * Regulation controller.
 *
 * @Route("/manageregulations")
 */
class RegulationController extends Controller
{

    public function __construct()
    {
        // $this->_em = $this->container->get('doctrine')->getManager();
    }

    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="manageregulations")
     *@Method("GET")
     *@Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $my_agency = [];
        $agencies = $user->getAgencies();
        $count = 0;
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $entities = $em->getRepository('NoticeCommentBundle:Regulation')->findRegulationsByAgency($my_agency);

        return array(
            'entities' => $entities,
        );
    }

    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulations_list")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:unpublished_regulations.html.twig")
     *
     *@return array
     */
    public function indexUnpublishedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $my_agency = [];
        $agencies = $user->getAgencies();
        $count = 0;
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $entities = $em->getRepository('NoticeCommentBundle:Regulation')->findUnpublishedRegulationsByAgency($my_agency);
        return array(
            'entities' => $entities,
        );
    }

    public function publishRegulationAction(Request $request, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $regulation = $em->getRepository(Regulation::class)->findOneBy(['id' => $regulation_id, 'deleted' => 1]);
        if (!$regulation) {
            return new Response(['success' => false, 'message' => 'Consultation Not Found']);
        }
        if ($regulation->getId() != $request->get('regulation_id')) {
            return new Response(json_encode(['success' => false, 'message' => 'Consultation Not Found']));
        }
        $regulation->setPublished(0);
        $em->persist($regulation);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'publish',
            'Regulation Published Successfully'
        );
        return new Response(json_encode(['success' => true, 'message' => 'Consultation Published Successfully']));
    }

    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulations_list")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:open_regulations.html.twig")
     *
     *@return array
     */
    public function openForConsultationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $my_agency = [];
        $agencies = $user->getAgencies();
        $count = 0;
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $entities = $em->getRepository('NoticeCommentBundle:Regulation')->findOpenRegulationsByAgency($my_agency);
        return array(
            'entities' => $entities,
        );
    }
    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulations_list")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:completed_regulations.html.twig")
     *
     *@return array
     */
    public function completeConsultationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $my_agency = [];
        $agencies = $user->getAgencies();
        $count = 0;
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $entities = $em->getRepository('NoticeCommentBundle:Regulation')->findCompletedRegulationsByAgency($my_agency);
        return array(
            'entities' => $entities,
        );
    }
    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulations_list")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:internal_regulations.html.twig")
     *
     *@return array
     */
    public function internalConsultationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $my_agency = [];
        $agencies = $user->getAgencies();
        $count = 0;
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $entities = $em->getRepository('NoticeCommentBundle:Regulation')->findInternalRegulationsByAgency($my_agency);
        return array(
            'entities' => $entities,
        );
    }

    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulations_list")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:completing_regulations.html.twig")
     *
     *@return array
     */
    public function completingConsultationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $my_agency = [];
        $agencies = $user->getAgencies();
        $count = 0;
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $idList = $this->calculateDaysRemainingBeforeCLosing(
            $em->getRepository('NoticeCommentBundle:Regulation')->findOpenRegulationsByAgency($my_agency)
        );

        if ($idList) {
            $entities = $em->getRepository(Regulation::class)->findBy(array('id' => array_keys($idList)), array('id' => 'DESC'));
        } else {
            $entities = [];
        }

        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();

        return array(
            'entities' => $entities,
            'closing_date_value' => $closing_date_value
        );
    }



    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulations_list")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:list.html.twig")
     *
     *@return array
     */
    public function frontendListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NoticeCommentBundle:Regulation');
        $paginate  = $this->get('knp_paginator');
        $pagination = $paginate->paginate(
            $entity->getListOfFrontendRegulations(),
            $request->query->getInt('page', 1),
            10
        );
        $agencies = $em->getRepository(BusinessAgency::class)->getAgenciesWithRegulations();
        $industries = $em->getRepository(BusinessIndustry::class)->getIndustriesWithRegulations();
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();
        return array(
            'all_regulations' => $pagination,
            "keywords" => "",
            'closing_date_value' => $closing_date_value,
            'agencies' => $agencies,
            'industries' => $industries
        );
    }


    public function batchDeleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        $published = false;
        $unpublished = false;
        $count = 0;

        foreach ($batch_items as $batch_item) {
            $entity = $em->getRepository(Regulation::class)->find($batch_item);
            if ($entity) {
                if ($batch_select == "unpublish") {
                    $entity->setPublished(0);
                    $unpublished = true;
                    $count = $count + 1;
                } else if ($batch_select == 'publish') {
                    $entity->setPublished(1);
                    $published = true;
                    $count = $count + 1;
                }
            }
        }

        $em->flush();

        if ($published) {
            $this->get('session')->getFlashBag()->add(
                'publish',
                $count . ' Consultation(s) Published Successfully'
            );
            return $this->redirect($this->generateUrl('regulations'));
        } else {
            $this->get('session')->getFlashBag()->add(
                'publish',
                $count . ' Consultation(s) Unpublished Successfully'
            );
            return $this->redirect($this->generateUrl('regulations_unpublished'));
        }
    }

    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulations_completed")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:closed.html.twig")
     *
     *@return array
     */
    public function frontendClosedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NoticeCommentBundle:Regulation');
        $paginate  = $this->get('knp_paginator');
        $pagination = $paginate->paginate(
            $entity->getAllRegulationsClosedForConsultation(),
            $request->query->getInt('page', 1),
            10
        );
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();
        $agencies = $em->getRepository(BusinessAgency::class)->getAgenciesWithRegulations();
        $industries = $em->getRepository(BusinessIndustry::class)->getIndustriesWithRegulations();
        return array(
            'closed_regulations' => $pagination,
            "keywords" => "",
            'closing_date_value' => $closing_date_value,
            'agencies' => $agencies,
            'industries' => $industries
        );
    }

    /**
     *Lists all Ongoing Regulations.
     *
     *@Route("/", name="regulations_open")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:open.html.twig")
     *
     *@return array
     */
    public function frontendOpenAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NoticeCommentBundle:Regulation');
        $paginate  = $this->get('knp_paginator');
        $pagination = $paginate->paginate(
            $entity->getAllRegulationsOpenForConsultation(),
            $request->query->getInt('page', 1),
            10
        );
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $agencies = $em->getRepository(BusinessAgency::class)->getAgenciesWithRegulations();
        $industries = $em->getRepository(BusinessIndustry::class)->getIndustriesWithRegulations();
        $closing_date_value = (int) $settings->getClosingDays();
        return array(
            'open_regulations' => $pagination,
            "keywords" => "",
            'closing_date_value' => $closing_date_value,
            'agencies' => $agencies,
            'industries' => $industries
        );
    }

    /**
     *Creates a new Regulation entity.
     *
     *@Route("/", name="regulations_create")
     *@Method("POST")
     *@Template("NoticeCommentBundle:Regulation:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $slug = $this->get('cocur_slugify');
        $sent_notification = false;
        $entity = new Regulation();
        $em = $this->getDoctrine()->getManager();
        $buildFormFields = [];

        $fixedfields = $em->getRepository(
            RegulationFixedFields::class
        )->findBy(['showed' => true]);
        $customfields = $em->getRepository(
            RegulationField::class
        )->findBy(['showed' => true]);

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
            $buildFormFields[$field->getId()] = [
                'showed' => $field->getShowed(),
                'required' => $field->getRequired(),
                'label' => $field->getFieldLabel()
            ];
        }
        $form = $this->createCreateForm($entity, $buildFormFields);
        $form->handleRequest($request);
        $form_errors = "";
        if ($form->isSubmitted() && $form->isValid()) {
            if ($request->files->get('noticeandcommentbundle_regulation')['file']) {
                $fileName = $this->upload(
                    $request->files->get(
                        'noticeandcommentbundle_regulation'
                    )['file']
                );
                $entity->upload($fileName);
            }

            if ($request->get('noticeandcommentbundle_regulation')['published']) {
                $entity->setPublished(1);
                $date = new \DateTime(null, new \DateTimeZone('Africa/Lusaka'));
                $entity->setPublishDate(date($date->format('Y-m-d H:i:sP')));
                $send_notification = true;
            }
            $entity->setSlug(
                $slug->slugify(
                    $request->get('noticeandcommentbundle_regulation')['title']
                )
            );
            $em->persist($entity);
            $em->flush();

            $fielddatas = $request->request->get("custom");
            foreach ($customfields as $customfield) {
                $newfielddatas = new RegulationFieldData();
                $newfielddatas->setFieldId($customfield->getId());
                $newfielddatas->setField($customfield);
                $newfielddatas->setRegulation($entity);
                $newfielddatas->setRegulationId($entity->getId());
                $newfielddatas->setFielddata($fielddatas[$customfield->getId()]);
                $em->persist($newfielddatas);
                $em->flush();
            }

            $this->get('session')->getFlashBag()->add(
                'create',
                'Consultation Created Successfully'
            );
            // to refine using events listeners
            try {
                if ($send_notification) {
                    $subscribed = $em->getRepository(SubscriberList::class)->findBy(["agency" => $entity->getAgency()->getId()]);
                    if ($subscribed) {
                        foreach ($subscribed as $key) {
                            $user = $em->getRepository(User::class)->findOneBy(['id' => $key->getUser()]);
                            if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                                $domain = "www.businesslicenses.gov.zm";
                                $domain = str_replace("www.", "", $domain);
                                $message = \Swift_Message::newInstance()
                                    ->setSubject('New Regulation Available for Comments')
                                    ->setFrom('info@' . $domain)
                                    ->setTo($user->getEmail())
                                    ->setContentType("text/html")
                                    ->setBody(
                                        $this->renderView(
                                            'WebmastersAfricaLicenseBundle:BusinessAgency:newRegulation.html.twig',
                                            array('user' => $user->getFirstName(), 'path' => $request->getSchemeAndHttpHost(), 'agency' => $entity->getAgency(), 'regulation' => $entity)
                                        )
                                    );
                                $this->get('mailer')->send($message);
                            }
                        }
                    }
                }

                $industry_subscribed = $em->getRepository(SubscriberListIndustry::class)->findBy(['industry' => $entity->getIndustry()->getId()]);
                if ($industry_subscribed) {
                    foreach ($subscribed as $key) {
                        $user = $em->getRepository(User::class)->findOneBy(['id' => $key->getUser()]);
                        if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                            $domain = "www.businesslicenses.gov.zm";
                            $domain = str_replace("www.", "", $domain);
                            $message_2 = \Swift_Message::newInstance()
                                ->setSubject('New Regulation Available for Comments')
                                ->setFrom('info@' . $domain)
                                ->setTo($user->getEmail())
                                ->setContentType("text/html")
                                ->setBody(
                                    $this->renderView(
                                        'WebmastersAfricaLicenseBundle:BusinessIndustry:newRegulation.html.twig',
                                        array('user' => $user->getFirstName(), 'path' => $request->getSchemeAndHttpHost(), 'industry' => $entity->getIndustry(), 'regulation' => $entity)
                                    )
                                );
                            $this->get('mailer')->send($message_2);
                        }
                    }
                }
            } catch (\Exception $e) {
                return $this->redirect($this->generateUrl('regulations'));
            }
            return $this->redirect($this->generateUrl('regulations'));
        } else {
            $form_errors = true;
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'form_errors' => $form_errors,
            'showedfields' => $showedfields,
            'customfields' => $customfields
        );
    }

    /**
     * Finds and displays a Agency Regulations.
     *
     * @Method("GET")
     * @Template("NoticeCommentBundle:Regulation:regulation_agency_list.html.twig")
     */
    public function showAgencyRegulationsAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $security_context = $this->container->get('security.context');
        $entity = $em->getRepository(BusinessAgency::class)
            ->findOneBy(['slug' => $slug]);
        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find Regulation entity.'
            );
        }

        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $subscribed = $em->getRepository(SubscriberList::class)->findOneBy(["user" => $security_context->getToken()->getUser()->getId(), "agency" => $entity->getId()]);
        } else {
            $subscribed = false;
        }


        return array(
            'agency' => $entity,
            'keywords' => "",
            'closing_date_value' => $closing_date_value,
            'subscribed' => $subscribed
        );
    }
    /**
     * Finds and displays a Industry Regulations.
     *
     * @Method("GET")
     * @Template("NoticeCommentBundle:Regulation:regulation_industry_list.html.twig")
     */
    public function showIndustryRegulationsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $security_context = $this->container->get('security.context');
        $entity = $em->getRepository(BusinessIndustry::class)
            ->findOneBy(['id' => $id]);
        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find the Business Industry.'
            );
        }

        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $subscribed = $em->getRepository(SubscriberListIndustry::class)->findOneBy(["user" => $security_context->getToken()->getUser()->getId(), "industry" => $entity->getId()]);
        } else {
            $subscribed = false;
        }

        return array(
            'industry' => $entity,
            'keywords' => "",
            'closing_date_value' => $closing_date_value,
            'subscribed' => $subscribed
        );
    }

    protected function checkValidity(Regulation $regulation)
    {
        $validator = $this->get('validator');
        $errors = $validator->validate($regulation);
        if (count($errors) > 0) {
            return (string) $errors;
        }
    }

    /**
     *Creates a form to create a Regulation entity.
     *
     *@param Regulation $entity The entity
     *
     *@return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Regulation $entity, $buildForm = [])
    {
        $form = $this->createForm(
            new RegulationType(),
            $entity,
            array(
                'action' => $this->generateUrl('regulations_create'),
                'method' => 'POST',
            )
        );
        if ($buildForm) {
            if ($buildForm[2]['showed']) {
                if ($buildForm[2]['required']) {
                    $form->add(
                        'description',
                        null,
                        array(
                            'label' => $buildForm[2]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'description',
                        null,
                        array(
                            'label' => $buildForm[2]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'description',
                    'hidden',
                    array(
                        'label' => $buildForm[2]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildForm[15]['showed']) {
                if ($buildForm[15]['required']) {
                    $form->add(
                        'expectedOutcome',
                        'textarea',
                        array(
                            'label' => $buildForm[15]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'expectedOutcome',
                        null,
                        array(
                            'label' => $buildForm[15]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'expectedOutcome',
                    'hidden',
                    array(
                        'label' => $buildForm[15]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildForm[5]['showed']) {
                if ($buildForm[5]['required']) {
                    $form->add(
                        'regulationType',
                        'choice',
                        array(
                            'label' => $buildForm[5]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'choices' => array(
                                1 => "Law",
                                2 => "By Law",
                                3 => "Instructions",
                                4 => "Decision",
                                5 => "Codes and Standards",
                                6 => "Forward Planning",
                                7 => "RIA",
                                8 => "Policy",
                                9 => "Other"
                            ),
                            'required' => true
                        )
                    );
                } else {
                    $form->add(
                        'regulationType',
                        'choice',
                        array(
                            'label' => $buildForm[5]['label'],
                            'choices' => array(
                                1 => "Law",
                                2 => "By Law",
                                3 => "Instructions",
                                4 => "Decision",
                                5 => "Codes and Standards",
                                6 => "Forward Planning",
                                7 => "RIA",
                                8 => "Policy",
                                9 => "Other"
                            ),
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'regulationType',
                    'hidden',
                    array(
                        'label' => $buildForm[5]['label'],
                        'required' => false
                    )
                );
            }

            $form->add(
                'stage',
                'choice',
                array(
                    'label' => $buildForm[5]['label'],
                    'choices'  => array(
                        1 => 'open',
                        2 => 'close'
                    ),
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'required' => true,
                    'constraints' => new NotBlank()
                )
            );

            if ($buildForm[6]) {
                $form->add(
                    'closingDate',
                    'text',
                    array(
                        'label' => $buildForm[6]['label'],
                        'label_attr' => array(
                            "class" => "label-required"
                        ),
                        'required' => true
                    )
                );
            }

            if ($buildForm[7]['showed']) {
                if ($buildForm[7]['required']) {
                    $form->add(
                        'specificInstructions',
                        null,
                        array(
                            'label' => $buildForm[7]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'specificInstructions',
                        null,
                        array(
                            'label' => $buildForm[7]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'specificInstructions',
                    'hidden',
                    array(
                        'label' => $buildForm[7]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildForm[8]['showed']) {
                if ($buildForm[8]['required']) {
                    $form->add(
                        'supportingMaterials',
                        null,
                        array(
                            'label' => $buildForm[8]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'supportingMaterials',
                        null,
                        array(
                            'label' => $buildForm[8]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'supportingMaterials',
                    'hidden',
                    array(
                        'label' => $buildForm[8]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildForm[9]['showed']) {
                if ($buildForm[9]['required']) {
                    $form->add(
                        'keywords',
                        null,
                        array(
                            'label' => $buildForm[9]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'attr' => array('placeholder' => "Separate Keywords with commas(,)"),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'keywords',
                        null,
                        array(
                            'label' => $buildForm[9]['label'],
                            'attr' => array('placeholder' => "Separate Keywords with commas(,)"),
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'keywords',
                    'hidden',
                    array(
                        'label' => $buildForm[9]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildForm[10]['showed']) {
                if ($buildForm[10]['required']) {
                    $form->add(
                        'tags',
                        null,
                        array(
                            'label' => $buildForm[10]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'attr' => array('placeholder' => "Separate Tags with commas(,)"),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'tags',
                        null,
                        array(
                            'label' => $buildForm[10]['label'],
                            'attr' => array('placeholder' => "Separate Tags with commas(,)"),
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'tags',
                    'hidden',
                    array(
                        'label' => $buildForm[10]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildForm[14]['showed']) {
                if ($buildForm[14]['required']) {
                    $form->add(
                        'offlineConsultations',
                        null,
                        array(
                            'label' => $buildForm[14]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'offlineConsultations',
                        null,
                        array(
                            'label' => $buildForm[14]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'offlineConsultations',
                    'hidden',
                    array(
                        'label' => $buildForm[14]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildForm[11]['showed']) {
                $form->add(
                    'file',
                    'file',
                    array(
                        'label' => $buildForm[11]['label']
                    )
                );
            }
        }

        $form->add(
            'fileSize',
            'number',
            array(
                'label' => 'File Size Limit',
                'label_attr' => array(
                    "class" => "label-required"
                ),
                'required' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Range(
                        array(
                            'min'        => 1,
                            'max'        => 800,
                            'minMessage' => 'You must be at least {{ limit }} Mb',
                            'maxMessage' => 'Maximum upload file size is {{ limit }}Mbs',
                            'invalidMessage' => 'This value should be a valid number.'
                        )
                    )
                )
            )
        );

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Create',
                'attr' => array(
                    'class' => 'w3-button w3-blue w3-round-medium',
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            )
        );

        return $form;
    }

    /**
     *Displays a form to create a new Regulation entity.
     *
     *@Route("/new", name="regulations_new")
     *@Method("GET")
     *@Template()
     */
    public function newAction()
    {
        $entity = new Regulation();
        $em = $this->getDoctrine()->getManager();
        $agency_list = [];

        $user = $this->getUser();
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $agency_list[] = $agency->getId();
        }
        $_SESSION['agencyid'] = $agency_list;
        $_SESSION['agency'] = true;

        $customfields = $em->getRepository(RegulationField::class)->findBy(['showed' => true], ['order' => 'ASC']);
        $fixedfields = $em->getRepository(RegulationFixedFields::class)->findBy(['showed' => true]);

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
            $buildFormFields[$field->getId()] = [
                'showed' => $field->getShowed(),
                'required' => $field->getRequired(),
                'label' => $field->getFieldLabel()
            ];
        }

        $form   = $this->createCreateForm($entity, $buildFormFields);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'showedfields' => $showedfields,
            'form_errors' => "",
            'customfields' => $customfields,
        );
    }

    /**
     *Finds and displays a Regulation entity.
     *
     *@Route("/{id}", name="regulations_show", requirements={"id": "\d+"})
     *@Method("GET")
     *@Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Regulation')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find Regulation entity.'
            );
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     *Finds and displays a Regulation entity.
     *
     *@Route("/{id}/regulation", name="regulation_show")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:regulation.html.twig")
     */
    public function frontendShowAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $open_for_comments = true;

        $entity = $em->getRepository(
            'NoticeCommentBundle:Regulation'
        )->findOneBy(
            ['slug' => $slug, 'published' => 1]
        );

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find Regulations you are looking for.'
            );
        }

        if ($entity->getConsultationStage() == "Final" or $entity->getConsultationStage() == "closed") {
            $open_for_comments = false;
        }

        return array(
            'regulation' => $entity,
            'open_for_comments' => $open_for_comments,
            "keywords" => ""
        );
    }

    /**
     *Finds and displays a Regulation entity.
     *
     *@Route("/{id}/comments", name="regulation_comments")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:regulation_comments.html.twig")
     */
    public function regulationCommentsAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $open_for_comments = true;
        $user_logged_in = false;

        $entity = $em->getRepository(
            'NoticeCommentBundle:Regulation'
        )->findOneBy(['slug' => $slug, 'deleted' => 0]);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find Regulation entity.'
            );
        }
        if ($entity->getConsultationStage() == "final" or $entity->getConsultationStage() == "Closed") {
            $open_for_comments = false;
        }

        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_logged_in = true;
            $subscribed_button = true;
            $subscribed_agency = $em->getRepository(SubscriberList::class)->findOneBy(["user" => $security_context->getToken()->getUser()->getId(), "agency" => $entity->getAgency()->getId()]);

            $subscribed_industry = $em->getRepository(SubscriberListIndustry::class)->findOneBy(
                [
                    "user" => $security_context->getToken()->getUser()->getId(),
                    "industry" => $entity->getIndustry()->getId()
                ]
            );
        } else {
            $subscribed_button = false;
            $subscribed_industry = false;
            $subscribed_agency = false;
        }

        $fixedfields = $em->getRepository(RegulationFixedFields::class)->findBy(['showed' => true]);

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }
        $fielddatas = $em->getRepository(
            RegulationFieldData::class
        )->findFieldData($entity->getId());
        $regulationfields = $em->getRepository(RegulationField::class)->findFieldData(true, $entity->getId());

        // this field not in use but can take the risk of deleting it now // check later
        $subscribed = "";
        return array(
            'regulation' => $entity,
            'open_for_comments' => $open_for_comments,
            'user_logged_in' => $user_logged_in,
            "keywords" => "",
            'subscribed' => $subscribed,
            'subscribed_button' => $subscribed_button,
            'showedfields' => $showedfields,
            'fielddatas' =>  $fielddatas,
            'regulationfields' => $regulationfields,
            'subscribed_industry' => $subscribed_industry,
            'subscribed_agency' => $subscribed_agency
        );
    }

    public function getCommentsListAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $open_for_comments = false;
        $user_logged_in = false;
        $entity = $em->getRepository('NoticeCommentBundle:Regulation')->findOneBy(
            [
                'slug' => $slug,
                'deleted' => 0
            ]
        );

        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_logged_in = true;
        }
        if ($entity->getStage() == 2) {
            $open_for_comments = false;
        }
        $commentsArray = [];


        if (!$entity) {
            return new Response(
                json_encode(
                    $commentsArray
                )
            );
        }
        foreach ($entity->getComments() as $comment) {
            if ($comment->getPublish()) {
                if ($comment->getDeleted() == 0) {
                    if ($comment->getHidden()) {
                        //$my_comment = "This comment is hidden for violating our comment policy";
                    } else {
                        $my_comment = $comment->getComment();
                        $commentsArray[] = [
                            'id' => $comment->getId(),
                            "parent" => (!empty($comment->getParent()) ? $comment->getParent()->getId() : null),
                            'created' => date_format($comment->getCreateAt(), "Y/m/d H:i:s"),
                            'modified' => date_format($comment->getUpdatedAt(), "Y/m/d H:i:s"),
                            'content' => $my_comment,
                            "creator" => $comment->getUser(),
                            "pings" => [],
                            'file_url' => (!empty($comment->getDocument()) ? "/uploads/documents/" . $comment->getDocument() : null),
                            'file' => $comment->getDocument(),
                            "file_mime_type" => $this->getMimeType($comment->getDocument()),
                            'fullname' => (!empty($comment->getUser()) ? $this->getUserDetails($comment->getUser()) : "GUEST USER"),
                            "profile_picture_url" => "https://viima-app.s3.amazonaws.com/media/public/defaults/user-icon.png",
                            "created_by_current_user" => $this->isCreatedByCurrentUser(
                                $comment->getUser()
                            ),
                            'upvote_count' => count($comment->getLikes()),
                            'user_has_upvoted' => $this->userHasUpvoted($comment),
                            "is_new" => false,
                            "created_by_admin" => (bool) $comment->getIsAdmin(),
                            "marked_abusive" => (bool) $comment->getIsAbusive(),
                            "hidden" => (bool) $comment->getHidden(),
                            'user_logged_in' => $user_logged_in,
                            'admin_upvote_count' => $comment->getUpvoteCount(),
                            'share_link' => $comment->getRegulation()->getSlug()
                        ];
                    }
                } // else if ($comment->getDeleted() == 1 && $comment->getChildren()->count() > 0) {
                //     $my_comment = "This comment is unvailable";
                //     $commentsArray[] = [
                //         'id' => $comment->getId(),
                //         "parent" => (!empty($comment->getParent()) ? $comment->getParent()->getId() : null),
                //         'created' => date_format($comment->getCreateAt(), "Y/m/d H:i:s"),
                //         'modified' => date_format($comment->getUpdatedAt(), "Y/m/d H:i:s"),
                //         'content' => $my_comment,
                //         "creator" => $comment->getUser(),
                //         "pings" => [],
                //         'file_url' => (!empty($comment->getDocument()) ? "/uploads/documents/" . $comment->getDocument() : null),
                //         'file' => $comment->getDocument(),
                //         "file_mime_type" => $this->getMimeType($comment->getDocument()),
                //         'fullname' => (!empty($comment->getUser()) ? $this->getUserDetails($comment->getUser()) : "GUEST USER"),
                //         "profile_picture_url" => "https://viima-app.s3.amazonaws.com/media/public/defaults/user-icon.png",
                //         "created_by_current_user" => $this->isCreatedByCurrentUser(
                //             $comment->getUser()
                //         ),
                //         'upvote_count' => count($comment->getLikes()),
                //         'user_has_upvoted' => $this->userHasUpvoted($comment),
                //         "is_new" => false,
                //         "created_by_admin" => (bool) $comment->getIsAdmin(),
                //         "marked_abusive" => (bool) $comment->getIsAbusive(),
                //         "hidden" => (bool) $comment->getHidden(),
                //         'user_logged_in' => $user_logged_in,
                //         'admin_upvote_count' => $comment->getUpvoteCount(),
                //         'share_link' => $comment->getRegulation()->getSlug()
                //     ];
                // }
            }
        }
        $result = base64_encode(json_encode(['success' => true, 'comments' => $commentsArray]));
        return new Response(
            $result
        );
    }

    public function userHasUpvoted($comment)
    {
        $em = $this->getDoctrine()->getManager();
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_id = $security_context->getToken()->getUser()->getId();
            $has_likes = $em->getRepository(Like::class)->findBy(['comment' => $comment, 'regulation' => $comment->getRegulation(), 'user' => $user_id]);
            if ($has_likes) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    public function getMimeTypeBeforeSaving($document)
    {
        if ($document) {
            return mime_content_type(
                $document
            );
        }
    }


    public function getMimeType($document)
    {
        if ($document) {
            return mime_content_type(
                $this->container->getParameter(
                    'documents_directory'
                ) . "/" . $document
            );
        }
        return false;
    }

    /**
     * Is comment created by current User
     *
     * @param mixed $user // userId
     *
     * @return boolean
     */
    public function isCreatedByCurrentUser($user)
    {
        if ($user) {
            $security_context = $this->container->get('security.context');
            if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user_id = $security_context->getToken()->getUser()->getId();
                return $user_id == $user->getId();
            }
        }

        return false;
    }

    /**
     *Get User details
     *
     *@param mixed $user //integer
     *
     *@return string
     */
    public function getUserDetails($user)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($user);
        if ($user->getAliasName()) {
            return $user->getAliasName();
        }
        return $user->getFullName();
    }

    public function updateCommentAction(Request $request, $comment_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->findOneBy(['id' => $comment_id]);
        $result = $this->validateCommentIsForUser($comment);
        $user_logged_in = false;
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_logged_in = true;
        }
        if ($result == true) {
            if (empty($request->get('file_url'))) {
                $comment->setDocument(null);
            }
            $date = new \DateTime(null, new \DateTimeZone('Africa/Lusaka'));
            $comment->setComment($request->get('content'));
            $em->persist($comment);
            $em->flush();
            $comment = base64_encode(
                json_encode([
                    'success' => true,
                    'comment' => [
                        'id' => $comment->getId(),
                        'parent' => (!empty($comment->getParent()) ? $comment->getParent()->getId() : null),
                        'created' => date_format($comment->getCreateAt(), "Y/m/d H:i:s"),
                        'modified' => date_format($comment->getUpdatedAt(), "Y/m/d H:i:s"),
                        'content' => $comment->getComment(),
                        'creator' => $comment->getUser(),
                        'pings' => [],
                        'fullname' => (!empty($comment->getUser()) ? $this->getUserDetails($comment->getUser()) : "GUEST USER"),
                        "profile_picture_url" => "https://viima-app.s3.amazonaws.com/media/public/defaults/user-icon.png",
                        "created_by_current_user" => $this->isCreatedByCurrentUser(
                            $comment->getUser()
                        ),
                        'file_url' => (!empty($comment->getDocument()) ? "/uploads/documents/" . $comment->getDocument() : null),
                        'file' => $comment->getDocument(),
                        "file_mime_type" => $this->getMimeType($comment->getDocument()),
                        'upvote_count' => (!empty($comment->getLikes()) ? count($comment->getLikes()) : 0),
                        'user_has_upvoted' => false,
                        "is_new" => false,
                        "created_by_admin" => $comment->getIsAdmin(),
                        "marked_abusive" => (bool) $comment->getIsAbusive(),
                        "hidden" => (bool) $comment->getHidden(),
                        'user_logged_in' => $user_logged_in,
                        'admin_upvote_count' => $comment->getUpvoteCount(),
                        'share_link' => $comment->getRegulation()->getSlug()
                    ]
                ])
            );
            return new Response(
                $comment
            );
        }
        return $result;
    }

    public function deleteCommentAction(Request $request, $comment_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->findOneBy(['id' => $comment_id]);
        $result = $this->validateCommentIsForUser($comment);
        if ($result == true) {
            $comment->setDeleted(1);
            $comment->setPublish(1);
            $em->persist($comment);
            $em->flush();
            return new Response(json_encode(['success' => true, 'message' => 'Deleted successfully']));
        }
        return $result;
    }

    public function validateCommentIsForUser(Comment $comment)
    {
        if (!$comment) {
            return new Response(['success' => false, 'message' => 'Comment Not found']);
        }
        $security_context = $this->container->get('security.context');
        if (!$security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new Response(['success' => false, 'message' => 'Comment Deletion unsuccessful']);
        }

        if (!$this->isCreatedByCurrentUser($comment->getUser())) {
            return new Response(['success' => false, 'message' => 'Comment Deletion unsuccessful']);
        }
        return true;
    }

    /**
     *Create a comment endpoint
     *
     *@param Request $request
     *
     *@return string
     */
    public function createCommentAction(Request $request, $regulation_id)
    {
        $my_comment = "";
        $user_id = 0;
        $em = $this->getDoctrine()->getManager();
        $entity = new Comment();
        $is_a_reply = false;
        $is_user_login = false;
        $send_notification = false;
        $main_parent = false;
        $security_context = $this->container->get('security.context');
        $regulation = $em->getRepository(
            'NoticeCommentBundle:Regulation'
        )->find($regulation_id);

        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_id = $this->getUser()->getId();
            if ($this->getUser()->getAliasName()) {
                $username = $this->getUser()->getAliasName();
            } else {
                $username = $this->getUser()->getUsername();
            }
        }
        if ($request->request->get('content') || $request->files->get('file')) {
            if ($request->files->get('file')) {
                $regulation_extensions = explode(',', $regulation->getFileType());
                $regulation_extensions = preg_split('/(\s*,*\s*)*,+(\s*,*\s*)*/', $regulation->getFileType());
                if (!in_array($this->getFileExtension($request->files->get('file')), $regulation_extensions)) {
                    return new Response(
                        json_encode(
                            [
                                'success' => false,
                                'message' => 'This are the file uploads allowed:-' . implode(",", $regulation_extensions)
                            ]
                        )
                    );
                }
                $fileName = $this->upload($request->files->get('file'));
                $entity->setDocument($fileName);
            }
            if ($user_id) {
                $entity->setUser($security_context->getToken()->getUser());
                $is_user_login = true;
            }

            if ($request->request->get('content')) {
                $my_comment = $request->request->get('content');
            }
            $review_comment_flag = $regulation->getIsReviewComments();
            if ($review_comment_flag) {
                $entity->setPublish(0);
                $entity->setPendingReview(1);
            } else {
                $entity->setPublish(1);
                $entity->setPendingReview(0);
            }

            if (!is_null($request->request->get('parent')) && !empty($request->request->get('parent'))) {
                $comment = $em->getRepository(
                    Comment::class
                )->find(
                    $request->request->get('parent')
                );
                $parentComment = $em->getRepository(Comment::class)->find($request->request->get('parent'));
                if (!$parentComment) {
                    $entity->setParentRight($comment);
                } else {
                    if ($parentComment->getParent()) {
                        $entity->setParentRight($parentComment->getParentRight());
                    } else {
                        $entity->setParentRight($comment);
                    }
                }

                $entity->setParent($comment);
                $entity->setPublish(1);
                $review_comment_flag = false;
                $is_a_reply = true;
            } else {
                $main_parent = true;
            }
            $entity->setRegulation($regulation);
            $entity->setComment($my_comment);
            $entity->setIsAdmin(0);
            $em->persist($entity);
            $em->flush();

            if ($is_a_reply && $comment->getUser() != $this->getUser() && !is_null($comment->getUser())) {
                $em = $this->getDoctrine()->getManager();
                $notification = new Notification();
                $notification->setRecipient($comment->getUser()->getId());
                $notification->setReference("regulation");
                $notification->setReferenceId($regulation->getSlug());
                $notification->setIsRead(0);
                if ($is_user_login) {
                    $notification->setSender($user_id);
                    $notification->setMessage("$username, has replied to your comment.");
                } else {
                    $notification->setMessage("Guest User, has replied to your comment.");
                }
                $em->persist($notification);
                $em->flush();
                $em->clear();
                error_log($comment->getUser()->getIsReceiveReplyEmail());
                if ($comment->getUser()->getIsReceiveReplyEmail()) {
                    $emailQueue = new EmailQueue;
                    $emailQueue->setIsSent(0);
                    $emailQueue->setComment($entity->getId());
                    $em->persist($emailQueue);
                    $em->flush();
                }
            }
            if (!$review_comment_flag) {
                if ($entity->getHidden()) {
                    $my_comment = "This comment is hidden for violating our comment policy";
                } else {
                    $my_comment = $entity->getComment();
                }
                $commentsArray = [
                    'id' => $entity->getId(),
                    'parent' => (!empty($entity->getParent()) ? $entity->getParent()->getId() : null),
                    'created' => date_format($entity->getCreateAt(), "Y/m/d H:i:s"),
                    'modified' => date_format($entity->getUpdatedAt(), "Y/m/d H:i:s"),
                    'content' => $my_comment,
                    "creator" => $entity->getUser(),
                    "pings" => [],
                    'file_url' => (!empty($entity->getDocument()) ? "/uploads/documents/" . $entity->getDocument() : null),
                    'file' => (!empty($entity->getDocument()) ? $entity->getDocument() : null),
                    "file_mime_type" => $this->getMimeType($entity->getDocument()),
                    'fullname' => (!empty($entity->getUser()) ? $this->getUserDetails($entity->getUser()) : "GUEST USER"),
                    "profile_picture_url" => "https://viima-app.s3.amazonaws.com/media/public/defaults/user-icon.png",
                    "created_by_current_user" => $this->isCreatedByCurrentUser(
                        $entity->getUser()
                    ),
                    'upvote_count' => (!empty($entity->getLikes()) ? count($entity->getLikes()) : 0),
                    'user_has_upvoted' => false,
                    "is_new" => true,
                    "created_by_admin" => $entity->getIsAdmin(),
                    "marked_abusive" => (bool) $entity->getIsAbusive(),
                    "hidden" => (bool) $entity->getHidden(),
                    'share_link' => $entity->getRegulation()->getSlug()
                ];
                return new Response(
                    json_encode(
                        [
                            'success' => true,
                            'comment' => $commentsArray
                        ]
                    )
                );
            } else {
                return new Response(
                    json_encode(
                        [
                            'success' => false,
                            'message' => "Comment Posted For Review. Thank you for contributing!"
                        ]
                    )
                );
            }
        } else {
            return new Response(
                json_encode(
                    ['error' => false]
                )
            );
        }
    }

    public function createCommentEnabledAction(Request $request, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $open_for_comments = true;

        $entity = $em->getRepository(
            'NoticeCommentBundle:Regulation'
        )->find($regulation_id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find Regulation entity.'
            );
        }
        if ($entity->getConsultationStage() == "Final" or $entity->getConsultationStage() == "Closed") {
            $open_for_comments = false;
        }
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_logged_in = true;
        } else {
            $user_logged_in = false;
        }
        if ($entity->getIsLoginRequired()) {
            $required_to_login = true;
        } else {
            $required_to_login = false;
        }

        $isReviewComments = ($entity->getIsReviewComments()) ? false : true;
        $isAttachmentEnabled = ($entity->getIsAttachmentEnabled() && $user_logged_in) ? true : false;
        return new Response(
            json_encode(
                [
                    "status" => true,
                    "open" => $open_for_comments,
                    "user_logged_in" => $user_logged_in,
                    "review_comments" => $isReviewComments,
                    'enable_attachment' => $isAttachmentEnabled,
                    'required_to_login' => $required_to_login,
                    'file_size' => $entity->getFileSize()
                ]
            )
        );
    }

    public function getFileTypeMapping($file_type)
    {
        $file_mapping = [];
        if ($file_type == 'docx') {
            $file_mapping['files'] = [
                'application/msword', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                'application/vnd.ms-word.document.macroEnabled.12', 'application/vnd.ms-word.template.macroEnabled.12'
            ];
            $file_mapping['file_type'] = 'docx';
        } else if ($file_type == 'pdf') {
            $file_mapping['files'] = [
                'application/pdf', 'application/x-pdf'
            ];
            $file_mapping['file_type'] = 'pdf';
        } else if ($file_type == 'txt') {
            $file_mapping['files'] = 'txt';
            $file_mapping['file_type'] = 'txt';
        } else if ($file_type == 'image') {
            $file_mapping['files'] = [
                'image/png', 'image/jpeg', 'image/gif', 'image/jpg', 'image/psd', 'image/svg', 'image/'
            ];
        } else if ($file_type['pptx']) {
            $file_mapping['files'] = [
                'application/vnd.ms-powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.openxmlformats-officedocument.presentationml.template', 'application/vnd.openxmlformats-officedocument.presentationml.slideshow', 'application/vnd.ms-powerpoint.addin.macroEnabled.12', 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', 'application/vnd.ms-powerpoint.template.macroEnabled.12', 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
            ];
        } else if ($file_type['xls']) {
            $file_mapping['files'] = [
                'application/vnd.ms-excel', 'application/vnd.ms-excel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.spreadsheetml.template', 'application/vnd.ms-excel.sheet.macroEnabled.12', 'application/vnd.ms-excel.template.macroEnabled.12', 'application/vnd.ms-excel.addin.macroEnabled.12', 'application/vnd.ms-excel.sheet.binary.macroEnabled.12'
            ];
        }
        return $file_mapping;
    }



    /**
     *Displays a form to edit an existing Regulation entity.
     *
     *@Route("/{id}/edit", name="regulations_edit")
     *@Method("GET")
     *@Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Regulation')->find($id);
        $agency_list = [];
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Regulation entity.');
        }

        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
            $_SESSION['agency'] = true;
            $user = $this->getUser();
            $agencies = $user->getAgencies();
            foreach ($agencies as $agency) {
                $agency_list[] = $agency->getId();
            }
            $_SESSION['agencyid'] = $agency_list;
        } else {
            $_SESSION['agency'] = false;
        }

        $customfields = $em->getRepository(RegulationField::class)->findBy(['showed' => true], ['order' => 'ASC']);
        $fixedfields = $em->getRepository(RegulationFixedFields::class)->findBy(['showed' => true]);

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
            $buildFormFields[$field->getId()] = [
                'showed' => $field->getShowed(),
                'required' => $field->getRequired(),
                'label' => $field->getFieldLabel()
            ];
        }


        $editForm = $this->createEditForm($entity, $buildFormFields);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'form_errors' => false,
            'showedfields' => $showedfields,
            'customfields' => $customfields
        );
    }

    /**
     *Creates a form to edit a Regulation entity.
     *
     *@param Regulation $entity The entity
     *
     *@return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Regulation $entity, $buildForm)
    {
        $form = $this->createForm(
            new RegulationType(),
            $entity,
            array(
                'action' => $this->generateUrl(
                    'regulations_update',
                    array(
                        'id' => $entity->getId()
                    )
                ),
                'method' => 'PUT',
            )
        );

        if ($buildForm) {
            if ($buildForm[2]['showed']) {
                if ($buildForm[2]['required']) {
                    $form->add(
                        'description',
                        null,
                        array(
                            'label' => $buildForm[2]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'description',
                        null,
                        array(
                            'label' => $buildForm[2]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'description',
                    'hidden',
                    array(
                        'label' => $buildForm[2]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildForm[15]['showed']) {
                if ($buildForm[15]['required']) {
                    $form->add(
                        'expectedOutcome',
                        'textarea',
                        array(
                            'label' => $buildForm[15]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'expectedOutcome',
                        null,
                        array(
                            'label' => $buildForm[15]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'expectedOutcome',
                    'hidden',
                    array(
                        'label' => $buildForm[15]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildForm[5]['showed']) {
                if ($buildForm[5]['required']) {
                    $form->add(
                        'regulationType',
                        'choice',
                        array(
                            'label' => $buildForm[5]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'choices' => array(
                                1 => "Law",
                                2 => "By Law",
                                3 => "Instructions",
                                4 => "Decision",
                                5 => "Codes and Standards",
                                6 => "Forward Planning",
                                7 => "RIA",
                                8 => "Policy",
                                9 => "Other"
                            ),
                            'required' => true
                        )
                    );
                } else {
                    $form->add(
                        'regulationType',
                        'choice',
                        array(
                            'label' => $buildForm[5]['label'],
                            'choices' => array(
                                1 => "Law",
                                2 => "By Law",
                                3 => "Instructions",
                                4 => "Decision",
                                5 => "Codes and Standards",
                                6 => "Forward Planning",
                                7 => "RIA",
                                8 => "Policy",
                                9 => "Other"
                            ),
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'regulationType',
                    'hidden',
                    array(
                        'label' => $buildForm[5]['label'],
                        'required' => false
                    )
                );
            }

            $form->add(
                'stage',
                'choice',
                array(
                    'label' => $buildForm[5]['label'],
                    'choices'  => array(
                        1 => 'open',
                        2 => 'close'
                    ),
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'required' => true,
                    'constraints' => new NotBlank()
                )
            );

            if ($buildForm[6]) {
                $form->add(
                    'closingDate',
                    'text',
                    array(
                        'label' => $buildForm[6]['label'],
                        'label_attr' => array(
                            "class" => "label-required"
                        ),
                        'required' => true
                    )
                );
            }

            if ($buildForm[7]['showed']) {
                if ($buildForm[7]['required']) {
                    $form->add(
                        'specificInstructions',
                        null,
                        array(
                            'label' => $buildForm[7]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'specificInstructions',
                        null,
                        array(
                            'label' => $buildForm[7]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'specificInstructions',
                    'hidden',
                    array(
                        'label' => $buildForm[7]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildForm[8]['showed']) {
                if ($buildForm[8]['required']) {
                    $form->add(
                        'supportingMaterials',
                        null,
                        array(
                            'label' => $buildForm[8]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'supportingMaterials',
                        null,
                        array(
                            'label' => $buildForm[8]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'supportingMaterials',
                    'hidden',
                    array(
                        'label' => $buildForm[8]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildForm[9]['showed']) {
                if ($buildForm[9]['required']) {
                    $form->add(
                        'keywords',
                        null,
                        array(
                            'label' => $buildForm[9]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'attr' => array('placeholder' => "Separate Keywords with commas(,)"),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'keywords',
                        null,
                        array(
                            'label' => $buildForm[9]['label'],
                            'attr' => array('placeholder' => "Separate Keywords with commas(,)"),
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'keywords',
                    'hidden',
                    array(
                        'label' => $buildForm[9]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildForm[10]['showed']) {
                if ($buildForm[10]['required']) {
                    $form->add(
                        'tags',
                        null,
                        array(
                            'label' => $buildForm[10]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'attr' => array('placeholder' => "Separate Tags with commas(,)"),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'tags',
                        null,
                        array(
                            'label' => $buildForm[10]['label'],
                            'attr' => array('placeholder' => "Separate Tags with commas(,)"),
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'tags',
                    'hidden',
                    array(
                        'label' => $buildForm[10]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildForm[14]['showed']) {
                if ($buildForm[14]['required']) {
                    $form->add(
                        'offlineConsultations',
                        null,
                        array(
                            'label' => $buildForm[14]['label'],
                            'label_attr' => array(
                                "class" => "label-required"
                            ),
                            'required' => true,
                            'constraints' => new NotBlank()
                        )
                    );
                } else {
                    $form->add(
                        'offlineConsultations',
                        null,
                        array(
                            'label' => $buildForm[14]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'offlineConsultations',
                    'hidden',
                    array(
                        'label' => $buildForm[14]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildForm[11]['showed']) {
                $form->add(
                    'file',
                    'file',
                    array(
                        'label' => $buildForm[11]['label']
                    )
                );
            }
        }

        $form->add(
            'fileSize',
            'number',
            array(
                'label' => 'File Size Limit',
                'label_attr' => array(
                    "class" => "label-required"
                ),
                'required' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Range(
                        array(
                            'min'        => 1,
                            'max'        => 800,
                            'minMessage' => 'Upload must be least {{ limit }} Mb',
                            'maxMessage' => 'Maximum upload file size is {{ limit }}Mbs',
                            'invalidMessage' => 'This value should be a valid number.'
                        )
                    )
                )
            )
        );


        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Update',
                'attr' => array(
                    'class' => 'w3-button w3-blue w3-round-medium',
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            )
        );

        return $form;
    }
    /**
     *Edits an existing Regulation entity.
     *
     *@Route("/{id}", name="regulations_update")
     *@Method("PUT")
     *@Template("NoticeCommentBundle:Regulation:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $slug = $this->get('cocur_slugify');
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Regulation')->find($id);

        $closing_date_before = $entity->getClosingDate();
        $published = (bool) $entity->getPublished();
        $send_notification = false;
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Regulation entity.');
        }
        $customfields = $em->getRepository(RegulationField::class)->findBy(['showed' => true]);
        $fixedfields = $em->getRepository(RegulationFixedFields::class)->findBy(['showed' => true]);

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
            $buildFormFields[$field->getId()] = [
                'showed' => $field->getShowed(),
                'required' => $field->getRequired(),
                'label' => $field->getFieldLabel()
            ];
        }
        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, $buildFormFields);
        $editForm->handleRequest($request);
        $supportingAttachments = new ArrayCollection();
        $form_errors = "";
        foreach ($entity->getSupportingAttachments() as $key) {
            $supportingAttachments->add($key);
        }
        if ($editForm->isValid()) {
            $file = $request->files->get(
                'noticeandcommentbundle_regulation'
            )['file'];
            $closingStatus = $request->get('noticeandcommentbundle_regulation')['stage'];
            if ($closingStatus == 2) {
                $date = new \DateTime(null, new \DateTimeZone('Africa/Lusaka'));
                if ($request->get('noticeandcommentbundle_regulation')['closingDate'] != $closing_date_before) {
                    $entity->setClosingDate($request->get('noticeandcommentbundle_regulation')['closingDate']);
                    $entity->setCheckClosing(1);
                    $entity->setConsultationStage(1);
                } else {
                    $entity->setClosingDate(date($date->format('m/d/Y')));
                    $entity->setCheckClosing(2);
                    $entity->setConsultationStage(2);
                    $entity->setPublishDate(date($date->format('m/d/Y')));
                }
            }
            if ($request->get('noticeandcommentbundle_regulation')['published']) {
                $entity->setPublished(1);
                $date = new \DateTime(null, new \DateTimeZone('Africa/Lusaka'));
                $entity->setPublishDate(date($date->format('m/d/Y')));
                if ($request->get('noticeandcommentbundle_regulation')['published'] != $published) {
                    $send_notification = true;
                }
            } else {
                $entity->setPublished(0);
            }
            if ($closingStatus == 1 && $request->get('noticeandcommentbundle_regulation')['closingDate'] != $entity->getClosingDate()) {
                $entity->setClosingDate($request->get('noticeandcommentbundle_regulation')['closingDate']);
                $entity->setCheckClosing(1);
                $entity->setConsultationStage(1);
            }
            if (!is_null($file)) {
                $entity->upload($this->upload($file));
            }
            foreach ($supportingAttachments as $values) {
                if (false === $entity->getSupportingAttachments()->contains($values)) {
                    $entity->getSupportingAttachments()->removeElement($values);
                    $downs = $em->getRepository(RegulationAttachments::class)->find($values->getId());
                    $em->remove($downs);
                }
            }
            $em->persist($entity);
            $em->flush();
            $fielddatas = $request->request->get("custom");

            foreach ($customfields as $customfield) {
                $currentfielddatas = $em->getRepository(RegulationFieldData::class)->findBy(['field_id' => $customfield->getId(), 'regulation_id' => $entity->getId()]);
                if ($currentfielddatas) {
                    foreach ($currentfielddatas as $currentfielddata) {
                        $currentfielddata->setFielddata($fielddatas[$customfield->getId()]);
                    }
                } else {
                    $newfielddatas = new RegulationFieldData();
                    $newfielddatas->setFieldId($customfield->getId());
                    $newfielddatas->setField($customfield);
                    $newfielddatas->setRegulation($entity);
                    $newfielddatas->setRegulationId($entity->getId());
                    $newfielddatas->setFielddata($fielddatas[$customfield->getId()]);
                    $em->persist($newfielddatas);
                    $em->flush();
                }
            }

            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );
            if ($send_notification) {
                $subscribed = $em->getRepository(SubscriberList::class)->findBy(["agency" => $entity->getAgency()->getId()]);
                if ($subscribed) {
                    foreach ($subscribed as $key) {
                        $user = $em->getRepository(User::class)->findOneBy(['id' => $key->getUser()]);
                        if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                            $domain = "www.businesslicenses.gov.zm";
                            $domain = str_replace("www.", "", $domain);
                            $message = \Swift_Message::newInstance()
                                ->setSubject('New Regulation Available for Comments')
                                ->setFrom('info@' . $domain)
                                ->setTo($user->getEmail())
                                ->setContentType("text/html")
                                ->setBody(
                                    $this->renderView(
                                        'WebmastersAfricaLicenseBundle:BusinessAgency:newRegulation.html.twig',
                                        array('user' => $user->getFirstName(), 'path' => $request->getSchemeAndHttpHost(), 'agency' => $$entity->getAgency(), 'regulation' => $entity)
                                    )
                                );
                            $this->get('mailer')->send($message);
                        }
                    }
                }
            }

            $industry_subscribed = $em->getRepository(SubscriberListIndustry::class)->findBy(['industry' => $entity->getIndustry()->getId()]);
            if ($industry_subscribed) {
                foreach ($subscribed as $key) {
                    $user = $em->getRepository(User::class)->findOneBy(['id' => $key->getUser()]);
                    if (filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
                        $domain = "www.businesslicenses.gov.zm";
                        $domain = str_replace("www.", "", $domain);
                        $message = \Swift_Message::newInstance()
                            ->setSubject('New Regulation Available for Comments')
                            ->setFrom('info@' . $domain)
                            ->setTo($user->getEmail())
                            ->setContentType("text/html")
                            ->setBody(
                                $this->renderView(
                                    'WebmastersAfricaLicenseBundle:BusinessIndustry:newRegulation.html.twig',
                                    array('user' => $user->getFirstName(), 'path' => $request->getSchemeAndHttpHost(), 'industry' => $entity->getIndustry(), 'regulation' => $entity)
                                )
                            );
                        $this->get('mailer')->send($message);
                    }
                }
            }
            return $this->redirect($this->generateUrl('regulations'));
        } else {
            $form_errors = true;
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'form_errors' => $form_errors,
            'showedfields' => $showedfields,
            'customfields' => $customfields
        );
    }

    public function getFileExtension(UploadedFile $file)
    {
        return $file->guessExtension();
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
     *Deletes a Regulation entity.
     *
     *@Route("/{id}", name="regulations_delete")
     *@Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(
                'NoticeCommentBundle:Regulation'
            )
                ->find($id);

            if (!$entity) {
                throw $this->createNotFoundException(
                    'Unable to find Regulation entity.'
                );
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('regulations'));
    }

    /**
     *Creates a form to delete a Regulation entity by id.
     *
     *@param mixed $id The entity id
     *
     *@return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('regulations_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr' => array(
                    'class' => 'w3-left w3-button w3-hover-teal w3-round-large w3-red w3-medium',
                    'style' => 'padding: 10px 30px 30px 30px; margin-right:15px;'
                )
            ))
            ->getForm();
    }

    /**
     *Generate a unique file name
     *
     *@return string
     */
    private function _generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     *Get Top Regulation
     *
     *@return void
     */
    private function _getRegulationWithTopComments($em)
    {
        $entities = $em->getRegulationsWithCommentsGreaterThenTen();
        $regulation = [];
        if (count($entities) > 0) {
            for ($i = 0; $i < count($entities); $i++) {
                $regulation[$entities[$i]['id']] = $entities[$i]['comment_count'];
            }
            $regulation_id = array_keys(
                $regulation,
                max($regulation)
            );
            return $em->find($regulation_id[0]);
        } else {
            return [];
        }
    }
    /**
     *Get Top Regulation
     *
     *@return void
     */
    private function _getRegulationsWithTopComments($em)
    {
        $entities = $em->getRegulationsWithCommentsGreaterThenTen();
        $regulation = [];
        if (count($entities) > 0) {
            for ($i = 0; $i < count($entities); $i++) {
                $regulation[$entities[$i]['id']] = $entities[$i]['comment_count'];
            }
            return array_keys($regulation);
        } else {
            return [];
        }
    }

    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulation_due_seven")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:trending.html.twig")
     *
     *@return array
     */
    public function getTopTrendingRegulationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Regulation::class);
        $pagination = [];
        $regulations = $this->getFindUniqueTrendingRegulations($entity);
        $paginate  = $this->get('knp_paginator');
            $pagination = $paginate->paginate(
                $entity->findByTitle(array_values($regulations)),
                $request->query->getInt('page', 1),
                10
            ); 
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();
        $agencies = $em->getRepository(BusinessAgency::class)->getAgenciesWithRegulations();
        $industries = $em->getRepository(BusinessIndustry::class)->getIndustriesWithRegulations();
        return array(
            'trending_regulations' => $pagination,
            'closing_date_value' => $closing_date_value,
            "keywords" => "",
            'agencies' => $agencies,
            'industries' => $industries
        );
    }

    public function getFindUniqueTrendingRegulations($entity)
    {
        $trending = array();
        $regulation_top_comments = $this->_getRegulationsWithTopComments($entity);
        $recent_regulation = $entity->findOneBy(['checkClosing' => 1, 'isPublic' => 1, 'published' => 1, 'deleted' => 0], ['id' => 'desc']);
        $comment_entity = $entity->getRegulationsWithLargestExpression();
        if ($regulation_top_comments) {
            $trending = $regulation_top_comments;
        }
        if ($recent_regulation) {
            $trending[] = $recent_regulation->getId();
        }
        if ($comment_entity) {
            $trending[] = $comment_entity[0]['id'];
        }
        if (is_array($trending)) {
            $regulations = $entity->findById(array_values(array_unique($trending)));
            $regulation_title = [];
            foreach ($regulations as $regulation) {
                $regulation_title[$regulation->getSlug()] = $regulation->getTitle();
            }

            return $regulation_title;
        }
    }
    public function getTopRegulationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Regulation::class);
        $regulations = [];
        $regulations = $this->getFindUniqueTrendingRegulations($entity);
        return new Response(
            json_encode(
                $regulations
            )
        );
    }

    public function countRegulationsPerStageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Regulation::class);
        $all_regulations = $entity->getListOfFrontendRegulations();
        $open_regulations = $entity->getAllRegulationsOpenForConsultation();

        return new Response(
            json_encode(
                array(
                    'all' => count($all_regulations),
                    'open' => count($entity->getAllRegulationsOpenForConsultation()),
                    'published' => count($all_regulations),
                    'closed' => count(
                        $entity->getAllRegulationsClosedForConsultation()
                    ),
                    'seven_all' => count(
                        $this->calculateDaysRemainingBeforeCLosing($open_regulations)
                    ),
                    'trending_all' => count(
                        $this->getFindUniqueTrendingRegulations($entity)
                    )
                )
            )
        );
    }

    public function countRegulationsPerStage()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Regulation::class);
        $all_regulations = $entity->getListOfActiveRegulations();

        return $all_regulations;
    }


    protected function agencyList()
    {
        $em = $this->getDoctrine()->getManager();
        $agencies = $em->getRepository(BusinessAgency::class)->getActiveAgencies();
        return $agencies;
    }
    /**
     *Get the list of agencies
     *
     *@return Response
     */
    public function getAgencyListAction()
    {
        $agencies = $this->agencyList();

        $agency_list = [];
        foreach ($agencies as $agency) {
            $agency_list[$agency->getId()] = $agency->getTitle();
        }
        return new Response(
            json_encode(
                $agency_list
            )
        );
    }

    /**
     *Get the list of industries
     *
     *@return Response
     */
    public function getIndustryListAction()
    {
        $em = $this->getDoctrine()->getManager();
        $industries = $em->getRepository(BusinessIndustry::class)
            ->findAllActiveBusinessIndustry();
        $industry_list = [];
        foreach ($industries as $industry) {
            $industry_list[$industry->getId()] = $industry->getName();
        }

        return new Response(
            json_encode(
                $industry_list
            )
        );
    }

    /**
     *Get List of Unique Keywords
     *
     *@return Response
     */
    public function getListOfUniqueKeywordsAction()
    {

        $keywords_list = $this->createUniqueKeywordsArray();
        return new Response(
            json_encode(
                $keywords_list
            )
        );
    }

    protected function createUniqueKeywordsArray()
    {
        $em = $this->getDoctrine()->getManager();
        $keywords = $em->getRepository(Regulation::class)
            ->getListOfAllKeywords();
        $keywords_list = [];
        foreach ($keywords as $keyword => $values) {
            $_keywords = explode(',', $values['keywords']);
            if (strlen($values['keywords']) > 0) {
                foreach ($_keywords as $key => $value) {
                    $keywords_list[] = $value;
                }
            }
        }
        $keyword_list = array_merge($keywords_list, $this->businessAgencyName());
        return array_unique($keyword_list);
    }
    /**
     *Search Regulations Based on Agency, Keyword, Industry
     *
     *@param Request $request
     *
     *@Route("/regulation/search/result", name="regulations_completed")
     *@Method("POST")
     *@Template("NoticeCommentBundle:Regulation:search_result.html.twig")
     *@return array
     */
    public function advanceSearchRegulationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $agency = $request->get('agency');
        $industry = $request->get('industry');
        $keyword = trim($request->get('keywords'));
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();


        $entity = $em->getRepository(Regulation::class);
        $paginate  = $this->get('knp_paginator');

        if ($agency && $industry == "0" && $keyword == "") {
            $regulation_list = $em->getRepository(Regulation::class)
                ->searchByAgency($agency);
        } else if ($industry && $agency == "0" && $keyword == "") {
            $regulation_list = $em->getRepository(Regulation::class)
                ->searchByIndustry($industry);
        } else if ($keyword && $industry == "0" && $agency == "0") {
            $regulation_list = $em->getRepository(Regulation::class)->searchByDetails($keyword);
        } else if ($agency && $keyword && $industry == "0") {
            $regulation_list = $em->getRepository(Regulation::class)->searchByAgencyKeyword($agency, $keyword);
        } else if ($agency && $industry && $keyword == "") {
            $regulation_list = $em->getRepository(Regulation::class)->searchByAgencyIndustry($agency, $industry);
        } else if ($industry && $keyword && $agency == "0") {
            $regulation_list = $em->getRepository(Regulation::class)->searchByIndustryKeyword($industry, $keyword);
        } else if ($industry && $keyword && $agency) {
            $regulation_list = $em->getRepository(Regulation::class)->searchByAgencyIndustryKeyword($agency, $industry, $keyword);
        } else if ($agency == "0" && $industry == "0" && empty($keyword)) {
            $regulation_list = $em->getRepository(Regulation::class)->getListOfFrontendRegulations();
        } else {
            $regulation_list = $em->getRepository(Regulation::class)->getListOfFrontendRegulations();
        }

        $agencies = $em->getRepository(BusinessAgency::class)->getAgenciesWithRegulations();
        $industries = $em->getRepository(BusinessIndustry::class)->getIndustriesWithRegulations();
        $pagination = $paginate->paginate(
            $regulation_list,
            $request->query->getInt('page', 1),
            10
        );
        return array(
            'all_regulations' => $pagination,
            "keywords" => $keyword,
            "agency" => $agency,
            "industry" => $industry,
            'closing_date_value' => $closing_date_value,
            'agencies' => $agencies,
            'industries' => $industries,
            'search_agency' => $agency,
            'search_industry' => $industry,
            'count_regulation_list' => count($regulation_list)

        );
    }

    /**
     *Search Regulations Based on Agency, Keyword, Industry
     *
     *@param Request $request
     *
     *@Route("/regulation/quick-search/result", name="regulations_completed")
     *@Method("POST")
     *@Template("NoticeCommentBundle:Regulation:search_result.html.twig")
     *@return array
     */
    public function searchRegulationsAction(Request $request)
    {
        $search_key = $request->get('search_key');
        $em = $this->getDoctrine()->getManager();
        $paginate  = $this->get('knp_paginator');
        $entity = $em->getRepository(Regulation::class);
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();
        $result = $entity->searchByDetails($search_key);
        $pagination = $paginate->paginate(
            $result,
            $request->query->getInt('page', 1),
            10
        );
        $agencies = $em->getRepository(BusinessAgency::class)->getAgenciesWithRegulations();
        $industries = $em->getRepository(BusinessIndustry::class)->getIndustriesWithRegulations();
        return array(
            'all_regulations' => $pagination,
            "keywords" => $search_key,
            "agency" => $agencies,
            "industry" => "",
            'closing_date_value' => $closing_date_value,
            'agencies' => $agencies,
            'industries' => $industries,
            'search_agency' => "",
            'search_industry' => "",
            'count_regulation_list' => count($result)
        );
    }

    public function suggestSearchTermsForQuickSearchAction()
    {
        $em = $this->getDoctrine()->getManager();
        $regulations = $em->getRepository(Regulation::class)->findAll();

        foreach ($regulations as $entity) {
            $regulation_title[] = $entity->getTitle();
        }

        $agencies = $em->getRepository(BusinessAgency::class)->findAll();

        foreach ($agencies as $entity) {
            $agency_title[] = $entity->getTitle();
        }

        $industries = $em->getRepository(BusinessIndustry::class)->findAll();
        foreach ($industries as $entity) {
            $industry_title[] = $entity->getName();
        }

        $keyword_list = $this->createUniqueKeywordsArray();
	var_dump($Keyword_list);die;
        $agencies_regulations_industries = array_merge(
            $regulation_title,
            $agency_title,
            $industry_title,
            $keyword_list
        );
        return new Response(
            json_encode(
                $agencies_regulations_industries
            )
        );
    }

    public function countRegulationsPerIndustryAction()
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Regulation::class)
            ->getRegulationsCountPerIndustry();

        return new Response(
            json_encode(
                $result
            )
        );
    }

    public function regulationsDueInSevenDaysApiAction()
    {
        $em = $this->getDoctrine()->getManager();
        $regulations = $em->getRepository(Regulation::class)->getListOfActiveRegulations();
        $regulation_list = array_slice($this->calculateDaysRemainingBeforeCLosing($regulations), 0, 3);
        return new Response(
            json_encode($regulation_list)
        );
    }

    /**
     *Lists all Regulation entities.
     *
     *@Route("/", name="regulation_due_seven")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:closing.html.twig")
     *
     *@return array
     */
    public function regulationsDueInSevenDaysAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $regulations = $em->getRepository(Regulation::class);
        $paginate  = $this->get('knp_paginator');
        $regulation_filter = $this->calculateDaysRemainingBeforeCLosing(
            $regulations->getAllRegulationsOpenForConsultation()
        );
        $ids = [];
        foreach ($regulation_filter as $regulation => $key) {
            $ids[] = $regulation;
        }
        $pagination = $paginate->paginate(
            $regulations->findById($ids),
            $request->query->getInt('page', 1),
            10
        );
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();
        $agencies = $em->getRepository(BusinessAgency::class)->getAgenciesWithRegulations();
        $industries = $em->getRepository(BusinessIndustry::class)->getIndustriesWithRegulations();
        return array(
            'closing_regulations' => $pagination,
            'closing_date_value' => $closing_date_value,
            "keywords" => "",
            'agencies' => $agencies,
            'industries' => $industries
        );
    }

    public function calculateDaysRemainingBeforeCLosing($regulations)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int) $settings->getClosingDays();
        $regulation_list = [];
        $publish_date = new \Datetime();
        if ($regulations) {
            foreach ($regulations as $regulation) {
                $closing_date = $regulation->getClosingDate();
                if (!is_object($closing_date)) {
                    $closing_date = date_create($closing_date);
                }
                $date_diff = date_diff($closing_date, $publish_date);
                if ($date_diff) {
                    $days = (int) $date_diff->format('%a');
                    if ($days > 0 && $days <= $closing_date_value) {
                        $regulation_list[$regulation->getId()]
                            = $regulation->getTitle();
                    }
                }
            }
            return $regulation_list;
        } else {
            return [];
        }
    }

    public function regulationsWithLargeNumberCommentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $regulation = $em->getRepository(Regulation::class);
        $high_comments = $regulation->getRegulationsWithLargeNumberOfComments();
        $entity = $regulation->findOneBy(['id' => $high_comments[0]['id']]);
        return $this->redirect($this->generateUrl('manage_comment_list', ['id' => $entity->getId()]));
    }
    public function commentWithLargeNumberExpressionsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $regulation = $em->getRepository(Regulation::class);
        $high_expression = $regulation->getCommentWithLargestExpression();
        return $this->redirect(
            $this->generateUrl(
                'comments_show',
                [
                    'id' => $high_expression[0]['id']
                ]
            )
        );
    }
    public function updateCLosingRegulationsToClosedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $regulations = $em->getRepository(Regulation::class);
        $count = 0;
        $check_for_closing = $regulations->getCheckForClosingRegulations();
        foreach ($check_for_closing as $closing) {
            if ($this->updateClosingRegulations($closing)) {
                $count += 1;
            }
        }
        return new Response(
            json_encode(
                ["count" => $count]
            )
        );
    }

    public function updateClosingRegulations($closing)
    {
        $em = $this->getDoctrine()->getManager();
        if (!is_object($closing->getClosingDate())) {
            $closing_date = new \DateTime(
                $closing->getClosingDate()
            );
        } else {
            $closing_date = $closing->getClosingDate();
        }
        $closing_date = new \DateTime($closing_date->format('Y-m-d') . ' 23:59:00');
        $regulation = $em->getRepository(
            Regulation::class
        )->find($closing->getId());
        $current_date = new \Datetime();
        if ($current_date > $closing_date) {
            $regulation->setConsultationStage(2);
            $regulation->setCheckClosing(2);
            $em->persist($regulation);
            $em->flush();
            return true;
        }
        return false;
    }

    public function closedRegulationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $regulations = $em->getRepository(Regulation::class)
            ->getAllRegulationsClosedForConsultationSideMenu();
        $regulation_list = [];
        if ($regulations) {
            foreach ($regulations as $regulation) {
                $regulation_list[$regulation->getSLug()] = $regulation->getTitle();
            }
        }
        return new Response(
            json_encode($regulation_list)
        );
    }

    public function newsAction()
    {
        $html = "<html><body>News Items Comming Soon</body></html>";
        return new Response(
            $html
        );
    }

    public function statePositionAction(Request $request, $slug)
    {

        $em = $this->getDoctrine()->getManager();
        $regulation = $em->getRepository(Regulation::class)->findOneBy(['slug' => $slug, 'deleted' => 0]);
        if (!$regulation) {
            return new Response(json_encode(['success' => false, 'message' => 'Consultation Missing']));
        }
        $position = new Position();
        $my_position = $request->get('position');
        if ($my_position == 'option1') {
            $position->setPosition(1);
        } elseif ($my_position == 'option2') {
            $position->setPosition(2);
        } elseif ($my_position == 'option3') {
            $position->setPosition(3);
        }
        $position->setRegulation($regulation);
        $em->persist($position);
        $em->flush();

        return new response(json_encode(['success' => true, 'message' => 'Thank you for voting']));
    }

    /**
     *Lists all Comments for this regulation.
     *
     *@Route("/", name="comment_list")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:comments_list.html.twig")
     *
     *@return array
     */
    public function commentListAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $regulation = $em->getRepository(Regulation::class)->find($id);
        if (!$regulation) {
            $this->createNotFoundException("Regulation Can't be found");
        }
        $comments = $em->getRepository(Comment::class)->getRegulationComments($regulation->getId());
        return array(
            'entity' => $regulation,
            'comments' => $comments,
            "keywords" => ""
        );
    }

    /**
     *Lists all Comments for this regulation.
     *
     *@Route("/", name="comment_list")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:comments_list_unpublished.html.twig")
     *
     *@return array
     */
    public function unpublishedCommentListAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $regulation = $em->getRepository(Regulation::class)->find($id);

        return array(
            'entity' => $regulation,
            "keywords" => ""
        );
    }

    public function commentCountAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $position = [];
        $regulation = $em->getRepository(Regulation::class)
            ->getCategorizedCommentsPerRegulation($id);
        foreach ($regulation as $key => $value) {
            $position[$value['id']] = $value['position'];
        }
        $position = array_count_values($position);
        if (array_key_exists(1, $position)) {
            //
        } else {
            $position[1] = 0;
        }
        if (array_key_exists(2, $position)) {
            //
        } else {
            $position[2] = 0;
        }

        if (array_key_exists(3, $position)) {
            //
        } else {
            $position[3] = 0;
        }
        return new Response(
            json_encode($position)
        );
    }

    protected function businessAgencyName()
    {
        $em = $this->getDoctrine()->getManager();
        $business_rep = $em->getRepository(
            BusinessLicense::class
        );
        $business_name = $business_rep->findAllPublished();
        $business_names = [];
        foreach ($business_name as $business) {
            $business_names[] = $business->getName();
        }
        $business_keywords = $business_rep->findAllPublished();


        foreach ($business_keywords as $name) {
            $business_names[] = $name->getName();
        }
        return $business_names;
    }


    public function businessAgencyNameAction(Request $request)
    {
        $business_names = $this->businessAgencyName();

        return new Response(
            json_encode(
                $business_names
            )
        );
    }
    /**
     *Lists all Comments for this regulation.
     *
     *@Route("/", name="print_regulation_details")
     *@Method("GET")
     *@Template("NoticeCommentBundle:Regulation:regulation_print.html.twig")
     *
     *@return array
     */
    public function printRegulationDetailsAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $fixedfields = $em->getRepository(RegulationFixedFields::class)->findBy(['showed' => true]);

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }
        $entity = $em->getRepository(Regulation::class)->findOneBy(['published' => true, 'isPublic' => true, 'slug' => $slug]);
        if (!$entity) {
            return $this->createNotFoundException('Consultation Details Not Found');
        }

        $fielddatas = $em->getRepository(
            RegulationFieldData::class
        )->findFieldData($entity->getId());
        $regulationfields = $em->getRepository(RegulationField::class)->findFieldData(true, $entity->getId());

        return array(
            'entity' => $entity,
            'showedfields' => $showedfields,
            'fielddatas' =>  $fielddatas,
            'regulationfields' => $regulationfields

        );
    }
}

<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\PressBundle\Entity\Page;
use WebmastersAfrica\PressBundle\Entity\Banner;
use WebmastersAfrica\PressBundle\Entity\Faq;
use WebmastersAfrica\PressBundle\Entity\NewsletterSubscriber;
use WebmastersAfrica\LicenseBundle\Entity\Search;
use WebmastersAfrica\LicenseBundle\Entity\Feedback;
use WebmastersAfrica\LicenseBundle\Entity\SearchContent;
use WebmastersAfrica\PressBundle\Form\PageType;
use WebmastersAfrica\PressBundle\Form\FaqType;
use Symfony\Component\HttpFoundation\Response;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Choice;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLocation;
use WebmastersAfrica\LicenseBundle\Entity\BusinessType;
use WebmastersAfrica\LicenseBundle\Entity\BusinessActivity;
use WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry;
use WebmastersAfrica\PressBundle\Entity\ProcedureCategory;
use WebmastersAfrica\PressBundle\Entity\Policy;
use WebmastersAfrica\UserBundle\Entity\User;
use WebmastersAfrica\UserBundle\Form\UpdateUserType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WebmastersAfrica\LicenseBundle\Entity\SubscriberList;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLocationCategory;

/**
 * Page controller.
 *
 */
class PressController extends Controller
{

    public function pressviewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find($id);

        if (!$entity || $entity->getPublished() == false || $entity->getDeleted() == true) {
            throw $this->createNotFoundException('Page Not Found.');
        }

        $this->get('session')->set('pageid', $id);

        return $this->render('WebmastersAfricaPressBundle:Press:pressview.html.twig', array(
            'entity' => $entity
        ));
    }

    public function noticePressviewAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(
            Page::class
        );
        $entity = $page->findOneBy(['slug' => $slug, 'site' => 1]);

        if (!$entity || $entity->getPublished() == false || $entity->getDeleted() == true) {
            throw $this->createNotFoundException('Page Not Found.');
        }

        $this->get('session')->set('pageid', $slug);

        return $this->render('WebmastersAfricaPressBundle:Press:notice_pressview.html.twig', array(
            'page' => $entity,
        ));
    }

    public function subscriptionconfirmAction(Request $request, $skey)
    {
        $confirmed = false;

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:NewsletterSubscriber p
                WHERE p.skey = :skey'
        )->setParameter("skey", $skey);
        $subscribers = $query->getResult();

        if (sizeof($subscribers) > 0) {
            $confirmed = true;
            foreach ($subscribers as $subscriber) {
                $subscriber->setConfirmed("1");
                $em->persist($subscriber);
                $em->flush();
            }
            $message = \Swift_Message::newInstance()
                ->setSubject('Newsletter Subscription')
                ->setFrom('e-registry@brra.org.zm')
                ->setTo($subscriber->getEmail())
                ->setBody(
                    $this->renderView(
                        'WebmastersAfricaPressBundle:Press:subscriptionsuccess.txt.twig',
                        array('name' => $subscriber->getName(), 'confirmationUrl' => $request->getSchemeAndHttpHost())
                    )
                )
                ->setContentType("text/html");
            $this->get('mailer')->send($message);
        } else {
            $confirmed = false;
        }

        return $this->render('WebmastersAfricaPressBundle:Press:subscriptionconfirmed.html.twig', array(
            'confirmed' => $confirmed
        ));
    }

    public function subscriptioncheckAction(Request $request)
    {
        $subscription = false;
        $entity = new NewsletterSubscriber();

        $collectionConstraint = new Collection(array(
            'subscribername' => new NotBlank(array('message' => 'Enter your name')),
            'subscriberemail' => new Email(array('message' => 'Invalid email address')),
        ));

        $defaultData = array('message' => 'Newsletter');
        $form = $this->createFormBuilder($defaultData, array(
            'constraints' => $collectionConstraint
        ))
            ->add('subscribername', 'text', array('required' => true))
            ->add('subscriberemail', 'email', array('required' => true))
            ->getForm();
        $form->handleRequest($request);

        if (true) {
            $formdata = $request->request->get("form");
            $name = $formdata['subscribername'];
            $email = $formdata['subscriberemail'];
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT p
                    FROM WebmastersAfricaPressBundle:NewsletterSubscriber p
                    WHERE p.email = :email'
            )->setParameter("email", $email);
            $subscribers = $query->getResult();
            if (sizeof($subscribers) > 0) {
                return $this->redirect($this->generateUrl('subscription_exists'));
            } else {
                $entity->setName($name);
                $entity->setEmail($email);
                $entity->setOrganisation("None");
                $entity->setSkey(md5(date("d-m-Y g:i:s")));
                $entity->setConfirmed("0");
                $em->persist($entity);
                $em->flush();

                $subscription = true;
            }
            if ($subscription) {
                $confirmationUrl = "<a href='" . $this->getRequest()->getHost() . $this->generateUrl('subscription_confirm', array("skey" => $entity->getSkey())) . "'>Confirmation Service Here</a>";
                $message = \Swift_Message::newInstance()
                    ->setSubject('Newsletter Subscription')
                    ->setFrom('e-registry@brra.org.zm')
                    ->setTo($formdata['subscriberemail'])
                    ->setBody(
                        $this->renderView(
                            'WebmastersAfricaPressBundle:Press:subscriptionconfirmation.txt.twig',
                            array('name' => $request->request->get("subscribername"), 'email' => $formdata['subscriberemail'], 'confirmationUrl' => $confirmationUrl)
                        )
                    )
                    ->setContentType("text/html");
                $this->get('mailer')->send($message);
                return $this->redirect($this->generateUrl('subscription_success'));
            } else {
                return $this->redirect($this->generateUrl('subscription_fail'));
            }
        } else {
            return $this->render('WebmastersAfricaPressBundle:Press:subscriptionfail.html.twig', array('form'   => $form->createView()));
        }
    }

    public function unSubscriptionCheckAction(Request $request)
    {
        $subscription = false;

        $defaultData = array('message' => 'Newsletter');
        $form = $this->createFormBuilder()->add(
            'subscriberemail',
            'email',
            array(
                'required' => true, 'label' => '*',
                'attr' => array(
                    'placeholder' => 'Enter your email address'
                )
            )
        )
            ->add('submit', 'submit', array('label' => 'Submit', 'attr' => array('class' => 'btn btn-danger btn-lg')))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $formdata = $request->request->get("form");
                $email = $formdata['subscriberemail'];
                $em = $this->getDoctrine()->getManager();
                $query = $em->createQuery(
                    'SELECT p
	                    FROM WebmastersAfricaPressBundle:NewsletterSubscriber p
	                    WHERE p.email = :email'
                )->setParameter("email", $email);
                $subscribers = $query->getResult();
                if ($subscribers[0]) {
                    $em->remove($subscribers[0]);
                    $em->flush();
                    $subscription = true;
                } else {
                    $subscription = false;
                }
                if ($subscription) {
                    return $this->redirect($this->generateUrl('unsubscription_success'));
                } else {
                    $this->get('session')->getFlashBag()->add(
                        'subscription_failed',
                        'The email address not found'
                    );
                    return $this->redirect($this->generateUrl('unsubscription_check'));
                }
            } else {
                return $this->render('WebmastersAfricaPressBundle:Press:unsubscription.html.twig', array('form'   => $form->createView()));
            }
        } else {
            return $this->render('WebmastersAfricaPressBundle:Press:unsubscription.html.twig', array('form'   => $form->createView()));
        }
    }

    public function addUserAgencySubscriptionAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new SubscriberList();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        } else {
            $agency = $request->get('batch_items');
            $user = $user->getId();
            if ($agency && $user) {
                for ($i = 0; $i < count($agency); $i++) {
                    if (empty($em->getRepository(SubscriberList::class)->findOneBy(["user" => $user, "agency" => $agency[$i]]))) {
                        $entity->setUser($user);
                        $entity->setAgency($em->getRepository(BusinessAgency::class)->find($agency[$i]));
                        $em->persist($entity);
                        $em->flush();
                        $entity = new SubscriberList();
                    }
                }
            }
        }

        return $this->redirect($this->generateUrl('nc_edit_profile') . "#profile");
    }

    public function removeUserAgencySubscriptionAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        } else {
            $agency = $request->get('batch_items_remove');
            $user = $user->getId();

            if ($agency && $user) {
                for ($i = 0; $i < count($agency); $i++) {
                    $entity = $em->getRepository(SubscriberList::class)->find(
                        $agency[$i]
                    );

                    if ($entity) {
                        $em->remove($entity);
                        $em->flush();
                    }
                }
            }
        }

        return $this->redirect($this->generateUrl('nc_edit_profile') . "#profile");
    }


    public function subscriptionsuccessAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Press:subscriptionsuccess.html.twig');
    }
    public function unsubscriptionsuccessAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Press:unsubscriptionsuccess.html.twig');
    }

    public function subscriptionfailAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Press:subscriptionfail.html.twig');
    }

    public function subscriptionexistsAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Press:subscriptionexists.html.twig');
    }

    public function newsAction()
    {
        $em    = $this->get('doctrine.orm.entity_manager');
        $dql   = "SELECT a FROM WebmastersAfricaPressBundle:News a WHERE a.published = 1 ORDER BY a.id DESC";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            5 /*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:Press:news.html.twig', array('pagination' => $pagination));
    }

    public function newsarticleAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebmastersAfricaPressBundle:News')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Page Not Found.');
        }
        $hits = (int) $entity->getHits() + 1;
        $entity->setHits($hits);
        $em->persist($entity);
        $em->flush();
        return $this->render('WebmastersAfricaPressBundle:Press:newsarticle.html.twig', array('entity' => $entity));
    }

    public function faqsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $paginator  = $this->get('knp_paginator');
        $entity  = $em->getRepository(Faq::class);
        if ($this->getRequest()->request->get('q')) {
            $query  = $entity->searchFaqAsRequested($this->getRequest()->request->get('q'), 1);
            $q = $this->getRequest()->request->get('q');
        } else {
            $query = $entity->findBy(['published' => 1, 'site' => 2, 'deleted' => 0], ['orderLevel' => 'ASC']);
            $q = "";
        }

        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            1000 /*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:Press:faqs.html.twig', array('pagination' => $pagination, 'q' => $q));
    }

    public function noticeFaqsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginator  = $this->get('knp_paginator');
        $pageContent = $em->getRepository(Page::class)->findTheCurrentPage("FAQ", 1);
        $entity  = $em->getRepository(Faq::class);
        if ($this->getRequest()->request->get('q')) {
            $query = $entity->searchFaqAsRequested($this->getRequest()->request->get('q'), 1);
            $q = $this->getRequest()->request->get('q');
        } else {
            $query = $entity->findBy(['published' => 1, 'site' => 1, 'deleted' => 0], ['orderLevel' => 'ASC']);
            $q = "";
        }

        $pagination = $paginator->paginate(
            $query,
            $this->get('request')->query->get('page', 1),
            10 /*limit per page*/
        );

        return $this->render('WebmastersAfricaPressBundle:Press:notice_faqs.html.twig', array('pagination' => $pagination, 'q' => $q, 'page' => $pageContent[0]));
    }

    public function businessProcedureAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $business_startup = $em->getRepository(
            ProcedureCategory::class
        )->findBy(['delete' => 0, 'publish' => 1]);
        return $this->render(
            'WebmastersAfricaPressBundle:Press:procedure.html.twig',
            array(
                'business_startup' => $business_startup
            )
        );
    }
    public function businessProcedureDetailsAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(
            'WebmastersAfricaPressBundle:BusinessStartup'
        )->findOneBy(['slug' => $slug]);
        if (!$entity) {
            throw $this->createNotFoundException(
                'Unable to find the procedure.'
            );
        }
        if ($entity->getIsPublished()) {
            $main_categories = $em->getRepository(
                'WebmastersAfricaPressBundle:ProcedureCategory'
            )->findBy(
                ['delete' => 0, 'publish' => 1]
            );

            return $this->render(
                'WebmastersAfricaPressBundle:Press:procedure_description.html.twig',
                array(
                    "procedure" => $entity,
                    "main_categories" => $main_categories
                )
            );
        } else {
            throw $this->createNotFoundException(
                'Unable to find the procedure.'
            );
        }
    }

    public function faqaskAction()
    {
        $entity = new Faq();

        $form = $this->createFormBuilder($entity)
            ->add(
                'name',
                'text',
                array(
                    'label' => 'feedback_name',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    "required" => false
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label' => 'feedback_email',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    "required" => true
                )
            )
            ->add(
                'organization',
                'text',
                array(
                    'label' => 'feedback_organization',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    "required" => false
                )
            )
            ->add(
                'question',
                'textarea',
                array(
                    'label' => 'feedback_question',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    "required" => true
                )
            )
            ->getForm();

        return $this->render('WebmastersAfricaPressBundle:Press:faqask.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'capture_error' => false,
            'id' => time()
        ));
    }
    public function noticeFaqaskAction()
    {
        $entity = new Faq();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($entity)
            ->add('name', 'text', array('label' => 'feedback_name', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('email', 'email', array('label' => 'feedback_email', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('organization', 'text', array('label' => 'feedback_organization', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('question', 'textarea', array('label' => 'feedback_question', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->getForm();

        return $this->render('WebmastersAfricaPressBundle:Press:notice_faqask.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'page' => $em->getRepository(Page::class)->findTheCurrentPage("FAQ", 1)[0],
            'capture_error' => false,
            'id' => time()
        ));
    }


    public function noticeFaqsaveAction(Request $request)
    {
        $em    = $this->getDoctrine()->getManager();
        $submitted = false;
        $entity = new Faq();
        $form = $this->createFormBuilder($entity)
            ->add('name', 'text', array('label' => 'feedback_name', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('email', 'email', array('label' => 'feedback_email', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('organization', 'text', array('label' => 'feedback_organization', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('question', 'textarea', array('label' => 'feedback_question', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $secretKey = "6LeYhb8UAAAAAPOz4ne8L0lXw5CORzTolFO6nKS9";
            if ($request->request->get('g-recaptcha-response')) {
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($request->request->get('g-recaptcha-response')));
                // Decode json data
                // Decode json data
                $responseData = json_decode($verifyResponse);


                // if($responseData["success"] == '1' && $responseData["score"] >= 0.5){
                if ($responseData->success) {
                    $entity->setAnswer("-");
                    $entity->setPublished(false);
                    $entity->setDeleted(false);
                    $entity->setSite(1);
                    $em->persist($entity);
                    $em->flush();

                    $domain = $_SERVER['HTTP_HOST'];
                    $domain = str_replace("www.", "", $domain);
                    $domain = "www.businesslicenses.gov.zm";
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Feedback From Zambia Notice & Comment Portal')
                        ->setFrom('info@' . $domain)
                        ->setTo($entity->getEmail())
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaPressBundle:Press:faq_sent.html.twig',
                                array('firstname' => $entity->getName())
                            )
                        );
                    $this->get('mailer')->send($message);
                    $submitted = true;

                    return $this->render('WebmastersAfricaPressBundle:Press:notice_faqsaved.html.twig');
                }
            }
        }

        if (!$submitted) {
            return $this->render('WebmastersAfricaPressBundle:Press:notice_faqask.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'capture_error' => true,
                'page' => $em->getRepository(Page::class)->findTheCurrentPage("FAQ", 1)[0],
            ));
        }

        return $this->render('WebmastersAfricaPressBundle:Press:notice_faqask.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'capture_error' => false,
            'page' => $em->getRepository(Page::class)->findTheCurrentPage("FAQ", 1)[0],
        ));
    }
    public function faqsaveAction(Request $request)
    {
        $em    = $this->getDoctrine()->getManager();
        $submitted = true;
        $entity = new Faq();
        $form = $this->createFormBuilder($entity)
            ->add('name', 'text', array('label' => 'feedback_name', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('email', 'email', array('label' => 'feedback_email', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('organization', 'text', array('label' => 'feedback_organization', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('question', 'textarea', array('label' => 'feedback_question', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $secretKey = $this->container->getParameter('recaptacha_server_side');
            if ($request->request->get('g-recaptcha-response')) {
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($request->request->get('g-recaptcha-response')));
                // Decode json data
                $responseData = json_decode($verifyResponse, true);

                if ($responseData["success"] == '1' && $responseData["score"] >= 0.5) {
                    // if ($responseData->success) {
                    $entity->setAnswer("-");
                    $entity->setPublished(false);
                    $entity->setDeleted(false);
                    $entity->setSite(2);
                    $em->persist($entity);
                    $em->flush();

                    $domain = $_SERVER['HTTP_HOST'];
                    $domain = str_replace("www.", "", $domain);
                    $domain = "www.businesslicenses.gov.zm";
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Zambia E-Registry Feedback')
                        ->setFrom('info@' . $domain)
                        ->setTo($entity->getEmail())
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaPressBundle:Press:faq_sent.html.twig',
                                array('firstname' => $entity->getName())
                            )
                        );
                    $this->get('mailer')->send($message);
                    return $this->render('WebmastersAfricaPressBundle:Press:faqsaved.html.twig');
                } else {
                    $submitted = false;
                }
            } else {
                $submitted = false;
            }
        }
        if (!$submitted) {
            return $this->render('WebmastersAfricaPressBundle:Press:faqask.html.twig', array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'capture_error' => true,
                'page' => $em->getRepository(Page::class)->findTheCurrentPage("FAQ", 2)[0],
            ));
        }

        return $this->render('WebmastersAfricaPressBundle:Press:faqask.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'capture_error' => false,
            'page' => $em->getRepository(Page::class)->findTheCurrentPage("FAQ", 2)[0],
        ));
    }

    public function contactusAction()
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessAgency p
                ORDER BY p.id DESC'
        );
        $agencies = $query->getResult();
        $agency_choices = [];
        $agency_choices[] = "Select an agency";
        foreach ($agencies as $agency) {
            $agency_choices[$agency->getId()] = $agency->getName();
        }

        $defaultData = array('message' => 'Contact Us');
        $form = $this->createFormBuilder($defaultData)
            ->add('firstname', 'text', array('required' => true))
            ->add('lastname', 'text', array('required' => true))
            ->add('email', 'email', array('required' => true))
            ->add('subject', 'text', array('required' => true))
            ->add('message', 'textarea', array("data" => "", 'required' => true))
            // ->add('captcha', 'genemu_captcha', array("mapped" => false, 'required' => true))
            ->getForm();
        return $this->render('WebmastersAfricaPressBundle:Press:contactus.html.twig', array('form'   => $form->createView(), 'agencies' => $agencies, 'capture_error' => false));
    }
    public function noticeContactUsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $defaultData = array('message' => 'Contact Us');
        $form = $this->createFormBuilder($defaultData)
            ->add('firstname', 'text', array('required' => true))
            ->add('lastname', 'text', array('required' => true))
            ->add('email', 'email', array('required' => true))
            ->add('subject', 'text', array('required' => true))
            ->add('message', 'textarea', array("data" => "", 'required' => true))
            ->getForm();

        return $this->render(
            'WebmastersAfricaPressBundle:Press:notices_contactus.html.twig',
            array(
                'form'   => $form->createView(), 'capture_error' => false,
                'page' => $em->getRepository(Page::class)->findTheCurrentPage("contact", 1)[0],
                'capture_error' => false
            )
        );
    }

    public function contactsendAction()
    {
        $request = $this->getRequest();
        $submitted = true;
        $em = $this->getDoctrine()->getManager();

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('firstname', 'text', array('required' => true))
            ->add('lastname', 'text', array('required' => true))
            ->add('email', 'email', array('required' => true))
            ->add('subject', 'text', array('required' => true))
            ->add('message', 'textarea', array('required' => true))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $secretKey = $this->container->getParameter('recaptacha_server_side');
            if ($request->request->get('g-recaptcha-response')) {
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($request->request->get('g-recaptcha-response')));
                // Decode json data
                $responseData = json_decode($verifyResponse, true);
                // if ($responseData->success) {
                if ($responseData["success"] == '1' && $responseData["score"] >= 0.5) {
                    // data is an array with "name", "email", and "message" keys
                    $data = $form->getData();
                    $em = $this->getDoctrine()->getManager();
                    $feedback = new Feedback();
                    $feedback->setType("0");
                    $feedback->setSubject("Contact Us");
                    $feedback->setFirstName($data["firstname"]);
                    $feedback->setLastName($data["lastname"]);
                    $feedback->setEmail($data["email"]);
                    $feedback->setMessage($data["message"]);
                    $feedback->setSite(2);
                    $feedback->setReplied(false);
                    $em->persist($feedback);
                    $em->flush();
                    $settings = $em->getRepository(
                        'WebmastersAfricaUserBundle:Setting'
                    )->find(1);
                    $domain = $_SERVER['HTTP_HOST'];
                    $domain = str_replace("www.", "", $domain);
                    $domain = "www.businesslicenses.gov.zm";
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Feedback From Licensing Portal')
                        ->setFrom('info@' . $domain)
                        ->setTo($settings->getSiteEmailAddress())
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaPressBundle:Press:email.txt.twig',
                                array('firstname' => $data["firstname"], 'lastname' => $data["lastname"], 'email' => $data["email"], 'message' => $data["message"])
                            )
                        );
                    $this->get('mailer')->send($message);

                    $message_2 = \Swift_Message::newInstance()
                        ->setSubject('Zambia E-Registry Feedback')
                        ->setFrom('info@' . $domain)
                        ->setTo($data["email"])
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaPressBundle:Press:email_acknowledgement.html.twig',
                                array('firstname' => $data["firstname"], 'lastname' => $data["lastname"])
                            )
                        );
                    $this->get('mailer')->send($message_2);


                    return $this->render('WebmastersAfricaPressBundle:Press:contactussent.html.twig');
                } else {
                    $submitted = false;
                }
            } else {
                $submitted = false;
            }
            if (!$submitted) {
                return $this->render('WebmastersAfricaPressBundle:Press:contactus.html.twig', array('form'   => $form->createView(), 'capture_error' => true));
            };

            return $this->render('WebmastersAfricaPressBundle:Press:contactus.html.twig', array('form'   => $form->createView(), 'capture_error' => false));
        }
    }

    public function noticeContactSendAction(Request $request)
    {
        $request = $this->getRequest();
        $submitted = true;
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository(Page::class)->findTheCurrentPage("Contact", 1)[0];

        $defaultData = array();
        $form = $this->createFormBuilder($defaultData)
            ->add('firstname', 'text', array())
            ->add('lastname', 'text', array('required' => true))
            ->add('email', 'email', array('required' => true))
            ->add('subject', 'text')
            ->add('message', 'textarea', array('required' => true))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isValid()) {
            $secretKey = $this->container->getParameter('recaptacha_server_side');
            if ($request->request->get('g-recaptcha-response')) {
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) . '&response=' . urlencode($request->request->get('g-recaptcha-response')));
                // Decode json data
                $responseData = json_decode($verifyResponse, true);

                // if ($responseData->success) {
                if ($responseData["success"] == '1' && $responseData["score"] >= 0.5) {
                    // data is an array with "name", "email", and "message" keys
                    $data = $form->getData();
                    $em = $this->getDoctrine()->getManager();

                    $feedback = new Feedback();
                    $feedback->setType("0");
                    $feedback->setSubject($data['subject']);
                    $feedback->setFirstName($data["firstname"]);
                    $feedback->setLastName($data["lastname"]);
                    $feedback->setEmail($data["email"]);
                    $feedback->setMessage($data["message"]);
                    $feedback->setSite(1);
                    $feedback->setReplied(false);

                    $em->persist($feedback);
                    $em->flush();
                    $settings = $em->getRepository(
                        'WebmastersAfricaUserBundle:Setting'
                    )->find(1);

                    $domain = $_SERVER['HTTP_HOST'];
                    $domain = "www.businesslicenses.gov.zm";
                    $domain = str_replace("www.", "", $domain);

                    $message = \Swift_Message::newInstance()
                        ->setSubject('Feedback From Zambia Notice & Comment Portal')
                        ->setFrom('info@' . $domain)
                        ->setTo($settings->getSiteEmailAddress())
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaPressBundle:Press:email.html.twig',
                                array('firstname' => $data["firstname"], 'lastname' => $data["lastname"], 'email' => $data["email"], 'message' => $data["message"])
                            )
                        );
                    $this->get('mailer')->send($message);

                    $message_2 = \Swift_Message::newInstance()
                        ->setSubject('Feedback From Zambia Notice & Comment Portal')
                        ->setFrom('info@' . $domain)
                        ->setTo($data["email"])
                        ->setBody(
                            $this->renderView(
                                'WebmastersAfricaPressBundle:Press:email_acknowledgement.html.twig',
                                array('firstname' => $data["firstname"], 'lastname' => $data["lastname"])
                            )
                        );
                    $this->get('mailer')->send($message_2);

                    return $this->render('WebmastersAfricaPressBundle:Press:notice_contactussent.html.twig');
                } else {
                    $submitted = false;
                }
            } else {
                $submitted = false;
            }
            if (!$submitted) {
                return $this->render('WebmastersAfricaPressBundle:Press:notices_contactus.html.twig', array('form'   => $form->createView(), 'page' => $page, 'capture_error' => true));
            };

            return $this->render('WebmastersAfricaPressBundle:Press:notices_contactus.html.twig', array('form'   => $form->createView(), 'page' => $page, 'capture_error' => false));
        }
    }

    public function browseLicensesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $agencies = [];
        $licenses = $em->getRepository(BusinessLicense::class)->findAllPublished();
        foreach ($licenses as $key) {
            $agencies[] = $key->getAgency();
        }
        if (count($agencies) > 0) {
            $agencies = array_unique($agencies);
        }
        return $this->render(
            'WebmastersAfricaPressBundle:Press:browselicenses.html.twig',
            array(
                'licenses' => $licenses,
                'agencies' => $agencies,
                'searchterm' => ""
            )
        );
    }

    public function browseAgencyLicensesAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $agency = $em->getRepository(BusinessAgency::class)->findOneBy(
            [
                'slug' => $slug
            ]
        );

        if (!$agency) {
            throw $this->createNotFoundException('Agency Not Found.');
        }

        return $this->render(
            'WebmastersAfricaPressBundle:Press:browseagencylicenses.html.twig',
            array(
                'agency' => $agency,
                'searchterm' => ""
            )
        );
    }

    public function browseLicensesPerLocationAction(BusinessLocation $id)
    {

        $em = $this->getDoctrine()->getManager();
        $licenses = $em->getRepository(BusinessLicense::class)
            ->findPublishedByLocation($id);
        $agencies = $em->getRepository(BusinessAgency::class)->getActiveAgencies();
        return $this->render(
            'WebmastersAfricaPressBundle:Press:browselicenses.html.twig',
            array(
                'licenses' => $licenses,
                'agencies' => $agencies,
                'searchterm' => ""
            )
        );
    }

    public function browseLicensesPerBusinessTypesAction(BusinessType $id)
    {
        $em = $this->getDoctrine()->getManager();
        $licenses = [];
        $license_ids = [];
        $agency_list_ids = [];
        foreach ($id->getActivities() as $activity) {
            foreach ($activity->getPublishedLicenses() as $license) {
                if (!in_array($license->getId(), $license_ids)) {
                    $licenses[] = $license;
                    if (!in_array($license->getAgency()->getId(), $agency_list_ids)) {
                        $agency_list_ids [] = $license->getAgency()->getId();
                    }
                }
                $license_ids[] = $license->getId();
            }
        }

        $agencies = $em->getRepository(BusinessAgency::class)->findBy(array('id' => $agency_list_ids), array('id' => 'DESC'));
        return $this->render(
            'WebmastersAfricaPressBundle:Press:browselicenses.html.twig',
            array(
                'licenses' => $licenses,
                'agencies' => $agencies,
                'searchterm' => ""
            )
        );
    }

    public function browseLocationsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locations = $em->getRepository(BusinessLocation::class)->findAllBusinessLocationCount();

        return $this->render(
            'WebmastersAfricaPressBundle:Press:listlocations.html.twig',
            array(
                'locations' => $locations,
                'searchterm' => ""
            )
        );
    }

    public function advancedsearchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // $agencies = $em->getRepository(
        //     BusinessAgency::class
        // )->getActiveAgencies();
        $speedE_frontend = [];
        $agencies = [];
        $businesslicence = $em->getRepository(
            BusinessLicense::class
        );
        $locationsCategory = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessLocationCategory'
        )->findAllBusinessLocationCategory();
        $industries = $em->getRepository(
            BusinessIndustry::class
        );
        $businesstypes = $em->getRepository(BusinessType::class)->findAllBusinessTypeCount();
        $businessactivities = $em->getRepository(BusinessActivity::class)->getBusinessActivitiesWithLicenses();
        $speedC = $this->getRequest()->request->get("speedC");
        $speedD = $this->getRequest()->request->get("speedD");
        $speedB = $this->getRequest()->request->get("speedB");
        error_log("Speeding");
	error_log($speedC);
        error_log($speedB);
        error_log($speedD);
        if ($request->get('speedE')) {
            $speedE = $request->get('speedE');
            $speedE_frontend = array_values($speedE);
        } else {
            $speedE[0] = "0";
        }
        if (is_null($request->get('speedD'))) {
            $speedD = "0";
        }
        if (is_null($request->get('speedC'))) {
            $speedC = "0";
        }
        if (is_null($request->get('speedB'))) {
            $speedB = "0";
        }

        $search_term = "";
        if ($request->get("keywords")) {
            $search_term = trim($request->get("keywords"));

            if ($request->get("jurisdiction_id") != "") {
                $entity = $businesslicence->advancedSearch(
                    null,
                    $request->get("jurisdiction_id"),
                    $search_term
                );
            } else {
                $entity = $em->getRepository(
                    BusinessLicense::class
                )->advancedKeywordsSearch(
                    $search_term
                );
            }

            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $entity,
                $this->get('request')->query->get('page', 1),
                100 /*limit per page*/
            );
            foreach ($entity as $key) {
                $agencies[] = $key->getAgency();
            }
            return $this->render(
                'WebmastersAfricaPressBundle:Press:advancedsearch.html.twig',
                array(
                    'locations' => $locationsCategory,
                    'industries' => $industries->getAllIndustriesWithLicenses(),
                    'businesstypes' => $businesstypes,
                    'pagination' => $pagination,
                    'agencies' => array_unique($agencies),
                    'speedB' => $speedB,
                    'speedC' => $speedC,
                    'speedD' => $speedD,
                    'speedE' => $speedE_frontend,
                    'industry' => $industries,
                    'businessactivities' => $businessactivities,
                    'businesstype' => $businesstypes,
                    'searchterm' => $search_term
                )
            );
        } elseif ($this->getRequest()->request->get("activity")) {
            $location = (!empty($request->request->get('jurisdiction_id'))) ? $request->request->get('jurisdiction_id') : null;
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $businesslicence->advancedSearch(
                    $this->getRequest()->request->get("activity"),
                    $location,
                    null
                ),
                $this->get('request')->query->get('page', 1),
                100 /*limit per page*/
            );
            foreach ($entity as $key) {
                $agencies[] = $key->getAgency();
            }
            return $this->render(
                'WebmastersAfricaPressBundle:Press:advancedsearch.html.twig',
                array(
                    'locations' => $locationsCategory,
                    'industries' => $industries->getAllIndustriesWithLicenses(),
                    'businesstypes' => $businesstypes,
                    'pagination' => $pagination,
                    'agencies' => array_unique($agencies),
                    'speedB' => $speedB,
                    'speedC' => $speedC,
                    'speedD' => $speedD,
                    'speedE' => $speedE_frontend,
                    'industry' => $industries,
                    'businessactivities' => $businessactivities,
                    'businesstype' => $businesstypes,
                    'searchterm' => $search_term
                )
            );
        } else {
            if ($speedD == "0" && $speedC == "0" && $speedE[0] == "0") {
                // speed B // location
                $entity = $businesslicence->findPublishedByLocation($speedB);
            } else if ($speedB == "0" && $speedD == "0" && $speedE[0] == "0") {
                // speed C industry
                $entity = $businesslicence->findPublishedLicensesByIndustry($speedC);
            } else if ($speedB == "0" && $speedC == "0" && $speedD != "0" && $speedE[0] == "0") {
                // find by speed D only business Type
                $entity = $businesslicence->findPublishedLicensesByBusinessType($speedD);
            } elseif ($speedB == "0" && $speedC == "0" && $speedD == "0" && $speedE[0] != "0") {
                // find by speed E only business activities
                $entity = $businesslicence->findPublishedLicensesByBusinessActivity($speedE);
            } else if ($speedC != "0" && $speedB != "0" && $speedD == "0" && $speedE[0] == "0") {
                // find By industry and Location
                $entity = $businesslicence->findPublishedLicensesByIndustryLocation($speedC, $speedB);
            } else if ($speedC != "0" && $speedD != "0" && $speedB == "0" && $speedE[0] == "0") {
                // find by industry and business Type
                $entity = $businesslicence->findPublishedByLicenseByIndustryBusinessTypes($speedC, $speedD);
            } else if ($speedB != "0" && $speedC == "0" && $speedD != "0" && $speedE[0] == "0") {
                // find by location and business type
                $entity = $businesslicence->findPublishedLicensesByLocationBusinessType($speedB, $speedD);
            } else if ($speedB != "0" && $speedD == "0" && $speedC == "0" && $speedE[0] != "0") {
                // find by location and activity
                $entity = $businesslicence->findPublishedLicensesByLocationBusinessActivity($speedB, $speedE);
            } else if ($speedB == "0" && $speedD == "0" && $speedC != "0" && $speedE[0] != "0") {
                // find by industries and business activity
                $entity = $businesslicence->findPublishedLicensesByIndustryBusinessActivity($speedC, $speedE);
            } else if ($speedB == "0" && $speedD != "0" && $speedC == "0" && $speedE[0] != "0") {
                // find by business type and businessactivity
                $entity = $businesslicence->findPublishedLicensesByBusinessTypeBusinessActivity($speedD, $speedE);
            } else if ($speedB != "0" && $speedC != "0" && $speedD == "0" && $speedE[0] != 0) {
                // find by location industry and activity
                $entity = $businesslicence->findPublishedLicensesByLocationIndustryBusinessActivity($speedB, $speedC, $speedE);
            } else if ($speedC != "0" && $speedD != "0" && $speedB != "0" && $speedE[0] == "0") {
                // find by industry location and business type
                $entity = $businesslicence->findPublishedLicenseByIndustryLocationBusinessType($speedC, $speedB, $speedD);
            } else if ($speedB != "0" && $speedC == "0" && $speedD != "0" && $speedE[0] != 0) {
                // find by location, business type and business activity
                $entity = $businesslicence->findPublishedLicenseByLocationBusinessTypeBusinessActivity($speedB, $speedD, $speedE);
            } else if ($speedC != "0" && $speedD != "0" && $speedB == "0" && $speedE[0] > "0") {
                // find all
                $entity = $businesslicence->findPublishedLicenseByIndustryBusinessTypeBusinessActivity($speedC, $speedD, $speedE);
            } else if ($speedC != "0" && $speedD != "0" && $speedB != "0" && $speedE[0] > "0") {
                // find all
                $entity = $businesslicence->findPublishedLicenseByIndustryLocationBusinessTypeBusinessActivity($speedC, $speedB, $speedD, $speedE);
            }
            if ($speedC == "0" && $speedD == "0" && $speedB == "0" && $speedE[0] == "0") {
                $entity = $businesslicence->findAllPublished();
            }
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $entity,
                $this->get('request')->query->get('page', 1),
                100 /*limit per page*/
            );
        }

        foreach ($entity as $key) {
            $agencies[] = $key->getAgency();
        }

        //die;
        return $this->render(
            'WebmastersAfricaPressBundle:Press:advancedsearch.html.twig',
            array(
                'locations' => $locationsCategory,
                'industries' => $industries->getAllIndustriesWithLicenses(),
                'businesstypes' => $businesstypes,
                'pagination' => $pagination,
                'agencies' => array_unique($agencies),
                'speedB' => $speedB,
                'speedC' => $speedC,
                'speedD' => $speedD,
                'speedE' => $speedE_frontend,
                'industry' => $industries,
                'businesstype' => $businesstypes,
                'businessactivities' => $businessactivities,
                'searchterm' => $search_term
            )
        );
    }

    public function listlicensesAction($pagination, $agencies)
    {
        return $this->render('WebmastersAfricaPressBundle:Press:listlicenses.html.twig', array('pagination' => $pagination, 'agencies' => $agencies));
    }

    public function listindustriesAction()
    {
        $industries = "";
        $industries .= "<select name='speedC' id='speedC'  onchange='getBusinesstypes(\"/index.php/browse/listbusinesstypes\", this.value)'>";
        $industries .= "<option value='0' selected='selected'>Select Industry</option>";

        $em = $this->getDoctrine()->getManager();
        $industry = $em->getRepository(BusinessIndustry::class)->getAllIndustriesWithLicenses();

        foreach ($industry as $entity) {
            $industries .= "<option value='" . $entity->getId() . "'>" . $entity->getName() . "</option>";
        }

        $industries .= "</select>";
        return new Response($industries);
    }

    public function listbusinesstypesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $businesstypes = '';
        $businesstypes .= "<select name='speedD' id='speedD' onchange='getBusinessactivities(\"/browse/listbusinessactivities\", this.value)'>";
        $businesstypes .= "<option value='0' selected='selected'>Select Business Type</option>";
        if ($request->get('industry') == "0") {
            $entities = $em->getRepository(BusinessType::class)->findAllBusinessTypeCount();
        } else {
            $industry = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessIndustry')->find($this->getRequest()->request->get("industry"));
            if (!$industry) {
                $businesstypes .= "</select>";
                return new Response($businesstypes);
            }
            $entities = $industry->getBusinesstypes();
        }

        foreach ($entities as $entity) {
            $businesstypes .= "<option value='" . $entity->getId() . "'>" . $entity->getName() . "</option>";
        }

        $businesstypes .= "</select>";
        return new Response($businesstypes);
    }

    public function newListBusinessTypesAction(Request $request)
    {
        $business_types = [];
        $em = $this->getDoctrine()->getManager();
        $industry = $em->getRepository(BusinessIndustry::class)->find($request->get('industry'));
        if ($industry) {
            foreach ($industry->getBusinesstypes() as $entity) {
                $business_types[] = ['id' => $entity->getId(), 'name' => $entity->getName()];
            }
        }

        return new Response(
            json_encode(
                $business_types
            )
        );
    }

    public function listbusinesactivitiesAction(Request $request)
    {
        $businessactivities = '';
        $businessactivities .= '<select id="speedE" name="speedE[]" multiple="multiple">';
        $businessactivities .= "<option value='0'>Select Business Activities</option>";
        $em = $this->getDoctrine()->getManager();
        if ($request->get('businesstype') == "0") {
            $entities = $em->getRepository(BusinessActivity::class)->getBusinessActivitiesWithLicenses();
        } else {
            $businessType = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessType');
            $businessType = $businessType->find($this->getRequest()->request->get("businesstype"));
            if (!$businessType) {
                $businessactivities .= "</select>";
                return new Response($businessactivities);
            }
            $entities = $businessType->getActivities();
        }

        foreach ($entities as $entity) {
            $businessactivities .= "<option value='" . $entity->getId() . "'>" . $entity->getName() . "</option>";
        }

        $businessactivities .= "</select>";
        return new Response($businessactivities);
    }

    public function getBusinessTypesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $business_types = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessType'
        )->findAllBusinessTypeCount();
        return $this->render(
            'WebmastersAfricaPressBundle:Press:listbusinesstypes.html.twig',
            array('businessTypes' => $business_types)
        );
    }

    public function adminLoginAction(Request $request)
    {
	return $this->redirect($this->generateUrl("admin_login"));
        $entity = new User();
        $has_errors = false;
        $form = $this->createFormBuilder($entity)
            ->add(
                'username',
                'text',
                array(
                    'label' => "Username",
                    'required' => true,
                    'attr' => array("class" => "form-control")
                )
            )
            ->add(
                'password',
                'password',
                array(
                    'label' => "Password",
                    'required' => true,
                    'attr' => array("class" => "form-control")
                )
            )
            ->getForm();
        return $this->render(
            "WebmastersAfricaPressBundle:Press:admin_login.html.twig",
            array('entity' => $entity, 'form' => $form->createView(), 'has_errors' => $has_errors)
        );
    }


    public function listActivitiesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $business_type = $em->getRepository(BusinessType::class);
        $cities = $em->getRepository(BusinessLocation::class);
        $industries = $em->getRepository(BusinessIndustry::class);
        $agencies = $em->getRepository(BusinessAgency::class);
        $speedC = $this->getRequest()->request->get("speedC");
        $speedD = $this->getRequest()->request->get("speedD");
        $speedB = $this->getRequest()->request->get("speedB");
        if ($speedD != 0 && $speedB != 0) {
            $business_type = $business_type->find(
                $this->getRequest()->request->get("speedD")
            );
            $city = $cities->find(
                $this->getRequest()->request->get("speedB")
            );

            $repository = $em->getRepository(BusinessActivity::class);
            $query = $repository->createQueryBuilder('u')
                ->innerJoin('u.businesstypes', 'g')
                ->innerJoin('u.licenses', 'l')
                ->where('g.id = :business_type_id')
                ->andWhere('u.deleted = 0')
                ->andWhere('g.deleted = 0')
                ->andWhere('l.status = :status')
                ->andWhere('l.location_id = :location')
                ->setParameter('business_type_id', $business_type->getId())
                ->setParameter('location', $city->getId())
                ->setParameter('status', "published")
                ->orderBy('u.name', 'ASC')
                ->getQuery();
            $entities = $query->getResult();
        } else {
            if ($speedD != 0 && $speedC != 0) {
                $business_type = $business_type->find(
                    $this->getRequest()->request->get("speedD")
                );

                $entities = $em->getRepository(
                    BusinessActivity::class
                )->filterBusinessActivityByBusinessType(
                    $business_type->getId()
                );
            } else {
                if ($speedD != 0) {
                    $business_type = $business_type->find(
                        $this->getRequest()->request->get("speedD")
                    );
                    $entities = $em->getRepository(
                        BusinessActivity::class
                    )->filterBusinessActivityByBusinessType(
                        $business_type->getId()
                    );
                }

                if ($speedB != 0) {
                    $city = $cities->find(
                        $this->getRequest()->request->get("speedB")
                    );
                    $entities = $em->getRepository(
                        BusinessActivity::class
                    )->filterBusinessActivityByLocation(
                        $city->getId()
                    );
                } else {
                    $city = null;
                }

                if (!empty($speedC) && $speedC != 0) {
                    $industry = $industries->find(
                        $this->getRequest()->request->get("speedC")
                    );

                    $entities = $em->getRepository(
                        BusinessActivity::class
                    )->filterBusinessActivityByIndustry(
                        $industry->getId()
                    );
                }

                if (empty($speedB) && empty($speedC) && empty($speedD)) {
                    return $this->redirect($this->generateUrl("browse_licenses"));
                }
            }
        }
        $this->get('session')->set('speedB', $speedB);
        $this->get('session')->set('speedC', $speedC);
        $this->get('session')->set('speedD', $speedD);

        return $this->render(
            'WebmastersAfricaPressBundle:Press:listactivities.html.twig',
            array(
                'activities' => $entities, 'city' => $city,
                'industries' => $industries->getAllIndustriesWithLicenses(),
                'locations' => $cities->findAllBusinessLocation(),
                'selected' => '',
                'agencies' => $agencies->getActiveAgencies(),
                'searchterm' => ""
            )
        );
    }

    public function searchbusinesstypeAction($city, $businesstype)
    {
        $em = $this->getDoctrine()->getManager();
        $businesstype_entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessType')->find($businesstype);
        $city_entity = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessLocation')->find($city);

        $entities = $businesstype_entity->getActivities();

        return $this->render('WebmastersAfricaPressBundle:Press:listactivities.html.twig', array('activities' => $entities, 'city' => $city_entity));
    }

    public function chooselocaleAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:Locale p
                WHERE p.enabled = 1
                ORDER BY p.title ASC'
        );
        $locales = $query->getResult();

        $current_locale = "";

        if ($this->getRequest()->getLocale()) {
            $current_locale = $this->getRequest()->getLocale();
        } else {
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQuery(
                'SELECT p
                    FROM WebmastersAfricaPressBundle:Locale p
                    WHERE p.enabled = :enabled AND
                    p.is_default = :default
                    ORDER BY p.title ASC'
            )->setParameter('enabled', true)->setParameter('default', true);
            $defaultlanguages = $query->getResult();
            foreach ($defaultlanguages as $language) {
                $session->set('locale', $language->getLocaleCode());
                $current_locale = $language->getLocaleCode();
            }
        }

        return $this->render('WebmastersAfricaPressBundle:Press:setlocale.html.twig', array('locales' => $locales, 'current_locale' => $current_locale));
    }


    public function setlocaleAction($locale)
    {
        $session = $this->getRequest()->getSession();
        $session->set('locale', $locale);
        $this->get('session')->set('_locale', $locale);

        $request = $this->getRequest();
        $request->setLocale($locale);

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $locationsCategory = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessLocationCategory'
        )->findAllBusinessLocationCategory();
        $industries = $em->getRepository(
            BusinessIndustry::class
        );
        $agencies = $em->getRepository(BusinessAgency::class);
        $businesstypes = $em->getRepository(BusinessType::class)->findAllBusinessTypeCount();
        $businessactivities = $em->getRepository(BusinessActivity::class)->getBusinessActivitiesWithLicenses();
        $industries = $em->getRepository(BusinessIndustry::class);
        $speedB = "";
        $speedC = "";
        $speedD = "";
        $speedE = [];
        $em = $this->getDoctrine()->getManager();
        return $this->render(
            'WebmastersAfricaPressBundle:Press:search.html.twig',
            array(
                'locations' => $locationsCategory,
                'industries' => $industries->getAllIndustriesWithLicenses(),
                'businesstypes' => $businesstypes,
                'agencies' => $agencies->getAgenciesWithLicensesOnly(),
                'speedB' => $speedB,
                'speedC' => $speedC,
                'speedD' => $speedD,
                'speedE' => [],
                'businessactivities' => $businessactivities,
                'industry' => $industries->getAllIndustriesWithLicenses(),
                'businesstype' => $businesstypes,
                'searchterm' => ""
            )
        );
    }

    public function searchsaveAction()
    {
        $em = $this->getDoctrine()->getManager();
        $searches = "";
        $subquery = "";
        if ($this->getRequest()->request->get("share") == "Share" || $this->getRequest()->request->get("share") == "Share your Search" || $this->getRequest()->request->get("share") == "share" || $this->getRequest()->request->get("share") == "Ação") {
            //
        } else {
            if ($this->get('security.context')->getToken()->getUser() != "anon.") {
                $query = $em->createQuery(
                    'SELECT p
                    FROM WebmastersAfricaLicenseBundle:Search p
                    WHERE p.userid = :user
                    ORDER BY p.name DESC'
                )->setParameter("user", $this->get('security.context')->getToken()->getUser()->getId());
                $searches = $query->getResult();
            }
        }

        $selected_license_ids = $this->getRequest()->request->get("sf_admin_batch_selection");
        $licsize = sizeof($selected_license_ids);
        $count = 0;
        foreach ($selected_license_ids as $license_id) {
            $count++;
            $subquery .= " p.id = " . $license_id;
            if ($count != $licsize) {
                $subquery .= " OR ";
            }
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            "SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLicense p
                WHERE " . $subquery . "
                ORDER BY p.name DESC"
        );
        $licenses = $query->getResult();

        $share = false;

        if ($this->getRequest()->request->get("share") == "Share" || $this->getRequest()->request->get("share") == "Share your Search" || $this->getRequest()->request->get("share") == "share" || $this->getRequest()->request->get("share") == "Ação") {
            $share = true;
        }

        return $this->render('WebmastersAfricaPressBundle:Press:searchsave.html.twig', array('searches' => $searches, 'licenseentities' => $licenses, 'share' => $share));
    }

    public function searchcommitAction()
    {
        $em = $this->getDoctrine()->getManager();
        $selected_license_ids = $this->getRequest()->request->get("sf_admin_batch_selection");

        $search = "";

        if ($this->getRequest()->request->get("existing") == "") {
            $search = new Search();
            $search->setName($this->getRequest()->request->get("title"));
            $search->setUserid($this->get('security.context')->getToken()->getUser()->getId());
            $em->persist($search);
            $em->flush();
        } else {
            $search = $em->getRepository('WebmastersAfricaLicenseBundle:Search')->find($this->getRequest()->request->get("existing"));
        }

        foreach ($selected_license_ids as $licenseid) {
            $license = $em->getRepository(
                BusinessLicense::class
            )->find($licenseid);
            $searchcontent = new SearchContent();
            $searchcontent->setLicense($license);
            $searchcontent->setSearch($search);
            $em->persist($searchcontent);
            $em->flush();
        }


        return $this->redirect($this->generateUrl('my_licenses'));
    }

    public function searchshareAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $search = $em->getRepository('WebmastersAfricaLicenseBundle:Search')->find($id);

        $licenses = "";

        $searchcontent = $search->getContent();

        foreach ($searchcontent as $content) {
            $licenses[] = $content->getLicense();
        }

        $share = true;

        return $this->render('WebmastersAfricaPressBundle:Press:searchsave.html.twig', array('searches' => "", 'licenseentities' => $licenses, 'share' => $share));
    }

    public function licenseviewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $license = $em->getRepository(
            BusinessLicense::class
        )->findOneBy(['id' => $id, 'deleted' => 0, 'status' => 1]);
        if (!$license) {
            throw $this->createNotFoundException("License not Found");
        }

        if ($license->getViews()) {
            $count = intval($license->getViews());
        } else {
            $count = 0;
        }
        $count++;

        $license->setViews($count);
        $em->persist($license);
        $em->flush();

        $showedfields = [];

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }

        $licensefields = $em->getRepository(
            'WebmastersAfricaLicenseBundle:LicenseField'
        )->findFieldData(true);

        $fielddatas = $em->getRepository(
            "WebmastersAfricaLicenseBundle:LicenseFieldData"
        )->findFieldData($id);
        return $this->render(
            'WebmastersAfricaPressBundle:Press:licenseview.html.twig',
            array(
                'license' => $license, 'showedfields' => $showedfields,
                'licensefields' => $licensefields,
                'fielddatas' => $fielddatas,
                'searchterm' => ""
            )
        );
    }

    public function licenseprintAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $license = $em->getRepository(
            BusinessLicense::class
        )->find($id);

        $showedfields = [];

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $licensefields = $query->getResult();


        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFieldData p
                WHERE p.license_id = :licenseid'
        )->setParameter("licenseid", $id);
        $fielddatas = $query->getResult();

        return $this->render(
            'WebmastersAfricaPressBundle:Press:licenseprint.html.twig',
            array(
                'license' => $license,
                'showedfields' => $showedfields,
                'licensefields' => $licensefields,
                'fielddatas' => $fielddatas
            )
        );
    }

    public function sharecommitAction()
    {
        $em = $this->getDoctrine()->getManager();
        $selected_license_ids = $this->getRequest()->request->get("sf_admin_batch_selection");

        $licenses = [];
        if (is_array($licenses)) {
            foreach ($selected_license_ids as $licenseid) {
                $license = $em->getRepository(BusinessLicense::class)
                    ->find($licenseid);
                $licenses[] = $license;
            }
        } else {
            $licenses[] = $em->getRepository(BusinessLicense::class)
                ->find($selected_license_ids);
        }
        if (!filter_var($this->getRequest()->request->get("email"), FILTER_VALIDATE_EMAIL)) {
            $this->get('session')->getFlashBag()->add(
                'email_error',
                'Email Invalid'
            );
            return $this->redirect($this->generateUrl('managefeedback'));
        }

        $domain = $_SERVER['HTTP_HOST'];
        $domain = str_replace("www.", "", $domain);
        $message = \Swift_Message::newInstance()
            ->setSubject('Shared Licenses From Licensing Portal')
            ->setFrom($this->container->getParameter('mailer_user'))
            ->setTo($this->getRequest()->request->get("email"))
            ->setContentType("text/html")
            ->setBody(
                $this->renderView(
                    'WebmastersAfricaPressBundle:Press:shareemail.txt.twig',
                    array('licenses' => $licenses)
                )
            );
        $this->get('mailer')->send($message);


        return $this->render('WebmastersAfricaPressBundle:Press:shared.html.twig');
    }

    public function searchdeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $search = $em->getRepository('WebmastersAfricaLicenseBundle:Search')->find($id);

        $searchcontents = $search->getContent();

        foreach ($searchcontents as $content) {
            $em->remove($content);
            $em->flush();
        }

        $em->remove($search);
        $em->flush();

        return $this->redirect($this->generateUrl('my_licenses'));
    }

    public function mylicensesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:Search p
                WHERE p.userid = :user
                ORDER BY p.name DESC'
        )->setParameter("user", $this->get('security.context')->getToken()->getUser()->getId());
        $searches = $query->getResult();

        return $this->render('WebmastersAfricaPressBundle:Press:mylicenses.html.twig', array('searches' => $searches));
    }

    public function editProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $dispatcher = $this->container->get('event_dispatcher');
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $postdata = $request->get('webmastersafrica_userbundle_user');
                if ($postdata['password']) {
                    $encoder_service = $this->get('security.encoder_factory');
                    $encoder = $encoder_service->getEncoder($user);
                    $encoded_pass = $encoder->encodePassword($user->getPassword(), $user->getSalt());
                    $user->setPassword($encoded_pass);
                }
                $em->persist($user);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                    'update',
                    'Account Details Updated Successfully'
                );
            }
        }

        return $this->render(
            'WebmastersAfricaPressBundle:Press:notice_profile.html.twig',
            array(
                'user' => $user,
                'form' => $editForm->createView()
            )
        );
    }

    public function checkIfSubscribedAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $my_list = [];
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            return new Response(
                json_encode(
                    [
                        'success' => false,
                        'message' => "Access Denied"
                    ]
                )
            );
        }
        $subscriberList = $em->getRepository(
            SubscriberList::class
        )->findBy(
            [
                'user' => $user->getId()
            ]
        );
        foreach ($subscriberList as $subscriber) {
            $my_list[] = $subscriber->getAgency()->getId();
        }

        return new Response(
            json_encode(
                [
                    'success' => true,
                    'list' => $my_list
                ]
            )
        );
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move(
            $this->container->getParameter('documents_directory'),
            $fileName
        );
        return $fileName;
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UpdateUserType(), $entity, array(
            'action' => $this->generateUrl('nc_edit_profile_post'),
            'method' => 'PUT',
        ));

        return $form;
    }

    public function editsProfileAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm();
        $form->setData($user);
        $form->add('firstName', 'text', array("label" => "First Name"));
        $form->add('lastName', 'text', array("label" => "Last Name"));

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('edit_profile');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
                $this->get('session')->getFlashBag()->add(
                    'update',
                    'Account Details Updated Successfully'
                );
                return $response;
            }
        }

        return $this->container->get('templating')->renderResponse(
            'WebmastersAfricaPressBundle:Press:editprofile.html.twig',
            array('form' => $form->createView())
        );
    }

    public function updateUserProfileAction(Request $request, $id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if (!$user) {
            return new Response(json_encode(['success' => false, 'message' => 'User logged out']));
        }
        if ($user->getId() == (int) $id) {
            $user_details = $em->getRepository(User::class)->find($user->getId());
            $alias_name = $request->get('alias_name');
            if ($alias_name) {
                // set new alias name for this user
                $alias_name = ($alias_name == "null" ? null : $alias_name);
                $user_details->setAliasName($alias_name);
                $this->get('session')->getFlashBag()->add(
                    'update_success',
                    'Alias name settings updated successfully'
                );
            }
            if ($request->get('turn_off_email')) {
                $user_details->setIsReceiveReplyEmail($request->get('turn_off_email'));
                $this->get('session')->getFlashBag()->add(
                    'update_success',
                    'Email settings updated successfully'
                );
            }
            $em->persist($user_details);
            $em->flush();

            return new Response(json_encode(['success' => true, 'alias_name' => $user_details->getAliasName()]));
        }
        return new Response(json_encode(['success' => false, 'message' => 'User logged out']));
    }

    public function viewmapAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessAgency'
        )->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Agency Not Found.');
        }

        return $this->render(
            'WebmastersAfricaPressBundle:Press:viewmap.html.twig',
            array(
                'agency' => $entity,
            )
        );
    }

    public function viewmapofficeAction($officeid)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessAgencyOffice'
        )->find($officeid);

        if (!$entity) {
            throw $this->createNotFoundException('Agency Not Found.');
        }

        return $this->render(
            'WebmastersAfricaPressBundle:Press:viewmapoffice.html.twig',
            array(
                'agency' => $entity,
            )
        );
    }

    public function newAdminLoginAction()
    {
        return $this->render(
            'WebmastersAfricaAdminBundle:Default:admin_login.html.twig'
        );
    }

    public function policyAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Policy::class)->findOneBy(['slug' => $slug]);
        if (!$entity) {
            throw $this->createNotFoundException('Policy not Found');
        }
        return $this->render(
            'WebmastersAfricaPressBundle:Press:notice_policy.html.twig',
            array('entity' => $entity)
        );
    }
}

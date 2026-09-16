<?php

namespace WebmastersAfrica\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebmastersAfrica\PressBundle\Entity\Locale;
use WebmastersAfrica\UserBundle\Entity\User;
use WebmastersAfrica\TaskBundle\Entity\Task;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLocation;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;
use WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry;
use WebmastersAfrica\LicenseBundle\Entity\Feedback;
use WebmastersAfrica\PressBundle\Entity\News;
use WebmastersAfrica\PressBundle\Entity\Newsletter;
use WebmastersAfrica\PressBundle\Entity\NewsletterSubscriber;
use WebmastersAfrica\LicenseBundle\Entity\BusinessType;
use WebmastersAfrica\LicenseBundle\Entity\BusinessActivity;
use WebmastersAfrica\UserBundle\Entity\Group;
use WebmastersAfrica\AdminBundle\Entity\Session;

class DefaultController extends Controller
{
    public function routerAction()
    {
        $url = $this->generateUrl('webmasters_africa_notice_homepage');
        $security = $this->container->get('security.context');
        if ($security->isGranted(array(new Expression('hasRole("EREGISTRY_DASHBOARD")')))) {
            $url = $this->generateUrl('webmasters_africa_admin_homepage');
            return $this->redirect($url, 301);
        }
        if ($security->isGranted(array(new Expression('hasRole("NOTICECOMMENTS_DASHBOARD")')))) {
            $url = $this->generateUrl('webmasters_africa_notice_homepage');
            return $this->redirect($url, 301);
        }
        return $this->redirect($url, 301);
    }

    public function dashboardAction()
    {
        //Get Default Language and Assign
        $em = $this->getDoctrine()->getManager();
        $locale = $em->getRepository(
            Locale::class
        )->findByLocale(1);
        if ($locale) {
            $session = $this->getRequest()->getSession();
            if ($this->get('session')->get('_locale_2')) {
                //
            } else {
                $session->set('locale', $locale->getLocaleCode());
                $this->get('session')->set('_locale', $locale->getLocaleCode());
                $this->get('session')->set('_locale_2', $locale->getLocaleCode());

                $request = $this->getRequest();
                $request->setLocale($locale->getLocaleCode());
            }
        }

        //If a user has logged in  and is part of agency, prepare an agency filter
        $user = $em->getRepository(User::class)->find(
            $this->get('security.context')->getToken()->getUser()->getId()
        );


        $agencies = $user->getAgencies();

        $agencies_filter = "";
        $agencies_filter_ids = "";

        $count = 0;
        $agencysize = sizeof($agencies);
        foreach ($agencies as $agency) {
            $count++;
            if ($count == $agencysize) {
                $agencies_filter = $agencies_filter . " p.agency_id = " . $agency->getId();
                $agencies_filter_ids = $agencies_filter_ids . " a.id = " . $agency->getId();
            } else {
                $agencies_filter = $agencies_filter . " p.agency_id = " . $agency->getId() . " OR";
                $agencies_filter_ids = $agencies_filter_ids . " a.id = " . $agency->getId() . " OR";
            }
        }

        $dashtasks = $em->getRepository(Task::class)->findBy(
            [
                "assignee" => $this->getUser()->getId(),
                'taskStatus' => 'pending'
            ],
            ['id' => 'DESC'],
            5
        );
        // if (count($dashtasks) < 1) {
        //     $dashtasks = $em->getRepository(Task::class)->findBy(
        //         [
        //             "assignee" => $this->getUser()->getId(),
        //             'taskStatus' => 'complete'
        //         ],
        //         ['id' => 'DESC'],
        //         5
        //     );
        // }
        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
        }

        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
            if ($agencies_filter_ids == "") {
                $dashusers = $em->getRepository(
                    User::class
                )->getLockedUsersWithAgencies(0);
            } else {
                $query = $em->createQuery(
                    'SELECT p
						FROM WebmastersAfricaUserBundle:User p
						JOIN p.agencies a
						WHERE p.locked = :locked AND (' . $agencies_filter_ids . ')
						ORDER BY p.id DESC'
                )->setParameter('locked', 0);
                $query->setMaxResults(5);
                $dashusers = $query->getResult();
            }
        } else {
            $dashusers = $em->getRepository(
                User::class
            )->getLockedUsersWithoutAgencies(0);
        }

        $dashlicenses = null;
        
        $dashlicenses = $em->getRepository(
            BusinessLicense::class
        )->getDashLicensesWithoutAgencies();


        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
				WHERE p.username = :username
                ORDER BY p.loggedAt DESC'
        )->setParameter('username', $user->getUsername());
        $query->setMaxResults(5);
        $dashupdates = $query->getResult();

        return $this->render(
            'WebmastersAfricaAdminBundle:Default:dashboard.html.twig',
            array(
                'dashtasks' => $dashtasks, 'dashlicenses' => $dashlicenses,
                'dashusers' => $dashusers, 'dashupdates' => $dashupdates
            )
        );
    }

    public function dashboardStatsBusinessTypesAction()
    {
        $webStats = [];
        $em = $this->getDoctrine()->getManager();

        $webStats['businesstypes'] = sizeof(
            $em->getRepository(
                BusinessType::class
            )->findAllBusinessType()
        );
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%BusinessType%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdbusinesstypes'] = sizeof($query->getResult());
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%BusinessType%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedbusinesstypes'] = sizeof($query->getResult());

        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsBusinessActivitiesAction()
    {
        $webStats = [];
        $em = $this->getDoctrine()->getManager();
        $webStats['activities'] = sizeof(
            $em->getRepository(
                BusinessActivity::class
            )->findAllBusinessActivity()
        );
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%BusinessActivity%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdactivities'] = sizeof($query->getResult());
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%BusinessActivity%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedactivities'] = sizeof($query->getResult());

        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }

    public function dashboardStatsIndustriesAction()
    {
        $webStats = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%BusinessIndustry%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdindustries'] = sizeof($query->getResult());
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%BusinessIndustry%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedindustries'] = sizeof($query->getResult());
        $webStats['industries'] = sizeof(
            $em->getRepository(
                BusinessIndustry::class
            )->findAllActiveBusinessIndustry()
        );

        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsLocationsAction()
    {
        $webStats = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $webStats['locations'] = sizeof(
            $em->getRepository(
                BusinessLocation::class
            )->findAllBusinessLocation()
        );
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%BusinessLocation%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdlocations'] = sizeof($query->getResult());
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%BusinessLocation%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedlocations'] = sizeof($query->getResult());
        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsAgenciesAction()
    {
        $webStats = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%BusinessAgency%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdagencies'] = sizeof($query->getResult());
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%BusinessAgency%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedagencies'] = sizeof($query->getResult());
        $agencies_filter_ids = $this->getAgencyFilters()['agencies_filter_ids'];

        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
            $agencies = sizeof(
                $em->getRepository(
                    BusinessAgency::class
                )->findAll()
            );
        } else {
            $agencies = sizeof(
                $em->getRepository(
                    BusinessAgency::class
                )->getActiveAgencies()
            );
        }
        $webStats['agencies'] = $agencies;
        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsUsersAction()
    {
        $webStats = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $agencies_filter_ids = $this->getAgencyFilters()['agencies_filter_ids'];
        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
            if ($agencies_filter_ids == "") {
                $users = sizeof(
                    $em->getRepository(
                        User::class
                    )->getLockedUsersWithAgencies(0, false)
                );
            } else {
                $query = $em->createQuery(
                    'SELECT p
						FROM WebmastersAfricaUserBundle:User p
						JOIN p.agencies a
						WHERE p.locked = 0 AND  (' . $agencies_filter_ids . ')'
                );
                $users = sizeof($query->getResult());
            }
        } else {
            $users = sizeof(
                $em->getRepository(
                    User::class
                )->getLockedUsersWithoutAgencies(0, false)
            );
        }
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%User%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdusers'] = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%User%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedusers'] = sizeof($query->getResult());
        $webStats['users'] = $users;
        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsGroupsAction()
    {
        $webStats = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $webStats['groups'] = sizeof(
            $em->getRepository(
                Group::class
            )->findAll()
        );
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%Group%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdgroups'] = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%Group%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedgroups'] = sizeof($query->getResult());

        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsSubscribersAction()
    {
        $webStats = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%Subscriber%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdsubscribers'] = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%Subscriber%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedsubscribers'] = sizeof($query->getResult());
        $webStats['subscribers'] = sizeof(
            $em->getRepository(
                NewsletterSubscriber::class
            )->findByConfirmed(true)
        );
        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsNewsAction()
    {
        $webStats = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $webStats['news'] = sizeof(
            $em->getRepository(
                News::class
            )->findByPublished(true)
        );
        $webStats['newsletters'] = sizeof(
            $em->getRepository(
                Newsletter::class
            )->findByDeleted(false)
        );

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%News')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['creatednews'] = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%News')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removednews'] = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%Newsletter%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['creatednewsletters'] = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%Newsletter%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removednewsletters'] = sizeof($query->getResult());
        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsLicensesAction()
    {
        $webStats = [];
        $agency_list = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%BusinessLicense%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdlicenses'] = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%BusinessLicense%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedlicenses'] = sizeof($query->getResult());
        foreach ($this->getUser()->getAgencies() as $agency) {
            $agency_list[] = $agency->getId();
        }
        if ($agency_list) {
            // get published license for current logged in user
            // $published_licenses = sizeof(
            //     $em->getRepository(
            //         BusinessLicense::class
            //     )->findAllPublished()
            // );
            $published_licenses = sizeof(
                $em->getRepository(BusinessLicense::class)->findAllPublishedLicensesForAgency($agency_list)
            );
            
            // get unpublished license for current logged in user
            // $unpublished_licenses = sizeof(
            //     $em->getRepository(
            //         BusinessLicense::class
            //     )->findAllUnPublished()
            // );
            $unpublished_licenses = sizeof(
                $em->getRepository(BusinessLicense::class)->findAllUnpublishedLicensesForAgency($agency_list)
            );
            // get review license for current logged in user
            // $in_review = sizeof(
            //     $em->getRepository(BusinessLicense::class)->findAllApplicationInReview()
            // );
            $in_review = sizeof(
                $em->getRepository(BusinessLicense::class)->findAllApplicationInReviewForAgency($agency_list)
            );
            // saved as draft for this stage
            // $draft = sizeof(
            //     $em->getRepository(BusinessLicense::class)->findAllSavedAsDraft()
            // );
            $draft = sizeof(
                $em->getRepository(BusinessLicense::class)->findAllSavedAsDraftForAgency($agency_list)
            );
        } else {
            $published_licenses = 0;
            $unpublished_licenses = 0;
            $in_review = 0;
            $draft = 0;
        }
        $webStats['in_review'] = $in_review;
        $webStats['draft'] = $draft;
        $webStats['published_licenses'] = $published_licenses;
        $webStats['unpublished_licenses'] = $unpublished_licenses;
        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }
    public function dashboardStatsFeedbackAction()
    {
        $webStats = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :logaction AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('logaction', 'remove')->setParameter('objectclass', '%Feedback%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['removedfeedback'] = sizeof($query->getResult());
        $query = $em->createQuery(
            'SELECT p
                FROM Gedmo\Loggable\Entity\LogEntry p
                WHERE p.action = :action AND p.objectClass LIKE :objectclass AND p.loggedAt LIKE :datetoday'
        )->setParameter('action', 'create')->setParameter('objectclass', '%Feedback%')->setParameter('datetoday', '%' . date('Y-m-d') . '%');
        $webStats['createdfeedback'] = sizeof($query->getResult());

        $webStats['feedback'] = sizeof(
            $em->getRepository(
                Feedback::class
            )->findAll()
        );
        return new Response(
            json_encode(
                [
                    'success' => true,
                    'webStats' => $webStats
                ]
            )
        );
    }

    public function getAgencyFilters()
    {
        $agency_filter = [];
        $user_id = $this->get('security.context')->getToken()->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(
            'WebmastersAfricaUserBundle:User'
        )->find(
            $user_id
        );

        $agencies = $user->getAgencies();

        $agencies_filter = "";
        $agencies_filter_ids = "";

        $count = 0;
        $agency_size = sizeof($agencies);
        foreach ($agencies as $agency) {
            $count++;
            if ($count == $agency_size) {
                $agencies_filter = $agencies_filter . " p.agency_id = " . $agency->getId();
                $agencies_filter_ids = $agencies_filter_ids . " a.id = " . $agency->getId();
            } else {
                $agencies_filter = $agencies_filter . " p.agency_id = " . $agency->getId() . " OR";
                $agencies_filter_ids = $agencies_filter_ids . " a.id = " . $agency->getId() . " OR";
            }
        }

        $agency_filter['agencies_filter'] = $agencies_filter;
        $agency_filter['agencies_filter_ids'] = $agencies_filter_ids;
        return $agency_filter;
    }

    public function searchAction()
    {
        $search_term = $this->get('request')->request->get('search');
        $trashed = $this->get('request')->request->get('_with_trashed');

        $em = $this->get('doctrine.orm.entity_manager');

        if ($trashed) {
            $query_string = 'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLicense p JOIN p.location lo
                WHERE lo.deleted = 1 AND p.name LIKE :name OR p.purpose LIKE :purpose OR p.description LIKE :description OR p.keywords LIKE :keywords';
        } else {
            $query_string = 'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLicense p JOIN p.location lo
                WHERE p.deleted = 0 AND lo.deleted = 0 AND (p.name LIKE :name OR p.purpose LIKE :purpose OR p.description LIKE :description OR p.keywords LIKE :keywords)';
        }

        $query = $em->createQuery($query_string)->setParameter('name', '%' . $search_term . '%')->setParameter('purpose', '%' . $search_term . '%')->setParameter('description', '%' . $search_term . '%')->setParameter('keywords', '%' . $search_term . '%');
        $licenses = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLocation p
                WHERE p.name LIKE :name'
        )->setParameter('name', '%'.$search_term.'%');
        $locations = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessAgency p
                WHERE p.title LIKE :title'
        )->setParameter('title', '%' . $search_term . '%');
        $business_agency = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessIndustry p
                WHERE p.name LIKE :name OR  p.description LIKE :description'
        )->setParameter('name', '%' . $search_term . '%')->setParameter('description', '%' . $search_term . '%');
        $industries = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessType p
                WHERE p.name LIKE :name OR p.description LIKE :description'
        )->setParameter('name', '%' . $search_term . '%')->setParameter('description', '%' . $search_term . '%');
        $businesstypes = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessActivity p
                WHERE p.name LIKE :name OR  p.description LIKE :description'
        )->setParameter('name', '%' . $search_term . '%')->setParameter('description', '%' . $search_term . '%');
        $activities = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:Feedback p
                WHERE p.subject LIKE :subject OR  p.message LIKE :message'
        )->setParameter('subject', '%' . $search_term . '%')->setParameter('message', '%' . $search_term . '%');
        $feedback = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:Faq p
                WHERE p.question LIKE :question OR  p.answer LIKE :answer'
        )->setParameter('question', '%' . $search_term . '%')->setParameter('answer', '%' . $search_term . '%');
        $faqs = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:Page p
                WHERE p.page_title LIKE :pagetitle OR  p.page_content LIKE :pagecontent'
        )->setParameter('pagetitle', '%' . $search_term . '%')->setParameter('pagecontent', '%' . $search_term . '%');
        $pages = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:News p
                WHERE p.title LIKE :title OR  p.article LIKE :article'
        )->setParameter('title', '%' . $search_term . '%')->setParameter('article', '%' . $search_term . '%');
        $news = $query->getResult();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:Newsletter p
                WHERE p.subject LIKE :subject OR p.content LIKE :content'
        )->setParameter('subject', '%' . $search_term . '%')->setParameter('content', '%' . $search_term . '%');
        $newsletters = $query->getResult();

        $users = $em->getRepository(User::class)->searchUsers($search_term);
        return $this->render(
            'WebmastersAfricaAdminBundle:Default:search.html.twig',
            array(
                'searchterm' => $search_term,
                'licenses' => $licenses,
                'business_agency' => $business_agency,
                'locations' => $locations,
                'industries' => $industries,
                'businesstypes' => $businesstypes,
                'activities' => $activities,
                'feedback' => $feedback,
                'faqs' => $faqs,
                'pages' => $pages, 'news' => $news,
                'newsletters' => $newsletters,
                'users' => $users,
                'trashed' => $trashed ? 1 : 0
            )
        );
    }


    public function javascriptsAction()
    {
        return $this->render('WebmastersAfricaAdminBundle:Default:javascripts.html.twig');
    }
    public function stylesheetsAction()
    {
        return $this->render('WebmastersAfricaAdminBundle:Default:stylesheets.html.twig');
    }
    public function sidebarAction($routename)
    {
        $session = $this->getRequest()->getSession();

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:Locale p
                WHERE p.enabled = :enabled
                ORDER BY p.title ASC'
        )->setParameter('enabled', true);
        $languages = $query->getResult();

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

        return $this->render('WebmastersAfricaAdminBundle:Default:sidebar.html.twig', array('routename' => $routename, 'languages' => $languages, 'currentlocale' => $current_locale));
    }
    public function topbarAction($routename)
    {

        return $this->render('WebmastersAfricaAdminBundle:Default:topbar.html.twig', array('routename' => $routename));
    }
    public function headerAction()
    {
        return $this->render('WebmastersAfricaAdminBundle:Default:header.html.twig');
    }

    public function checkAdminAction(Request $request)
    {

        $router = $this->container->get('router');
        $check_login = $this->container->get('security.context')
            ->isGranted('IS_AUTHENTICATED_FULLY');
        if ($check_login) {
            $role = $this->container->get('security.context')->isGranted(
                "ROLE_ADMIN"
            );
            if ($role) {
                return new RedirectResponse(
                    $router->generate(
                        'webmasters_africa_admin_homepage'
                    ),
                    307
                );
            } else {
                return new RedirectResponse(
                    $router->generate(
                        'homepage_notice_comments'
                    ),
                    307
                );
            }
        } else {
            $template = sprintf(
                'WebmastersAfricaAdminBundle:Default:admin_login.html.twig'
            );
            $data =  array('error' => "Invalid Username or Password");
            return $this->container->get('templating')->renderResponse(
                $template,
                $data
            );
        }
    }

    public function checkUserOnlineAction(Request $request)
    {
        return new Response(json_encode(['success' => true, 'online' => 1]));
    }
}

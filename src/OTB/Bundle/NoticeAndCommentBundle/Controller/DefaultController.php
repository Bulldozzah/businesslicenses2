<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation;
use Symfony\Component\HttpFoundation\Response;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\UserBundle\Entity\User;
use WebmastersAfrica\TaskBundle\Entity\Task;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use WebmastersAfrica\LicenseBundle\Entity\BusinessLocation;
use WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry;
use WebmastersAfrica\PressBundle\Entity\News;
use WebmastersAfrica\PressBundle\Entity\Newsletter;
use WebmastersAfrica\PressBundle\Entity\NewsletterSubscriber;
use WebmastersAfrica\LicenseBundle\Entity\BusinessType;
use WebmastersAfrica\LicenseBundle\Entity\BusinessActivity;
use WebmastersAfrica\UserBundle\Entity\Group;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Comment;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $agency_list = [];
        $dash_tasks = $em->getRepository(Task::class)->getRecentTasks(
            $this->get('security.context')->getToken()->getUser()->getId(),
            'completed'
        );
        $user = $em->getRepository(User::class)->find(
            $this->get('security.context')->getToken()->getUser()->getId()
        );
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $agency_list[] = $agency->getId();
        }
        $dash_licenses = $em->getRepository(
            Regulation::class
        )->getDashLicense($agency_list);

        $regulation_count = $em->getRepository(
            Regulation::class
        )->getListOfActiveRegulations();
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int)$settings->getClosingDays();

        return $this->render(
            'NoticeCommentBundle:Default:index.html.twig',
            array(
                'dash_tasks' => $dash_tasks,
                'dash_licenses' => $dash_licenses,
                'regulations_count' => count($regulation_count),
                'closing_date_value' => $closing_date_value
            )
        );
    }
    public function calculateDaysRemainingBeforeCLosing($regulations)
    {
        $em = $this->getDoctrine()->getManager();
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int)$settings->getClosingDays();
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

    public function getFindUniqueTrendingRegulations($entity)
    {
        $trending = array();
        $regulation_top_comments = $this->_getRegulationsWithTopComments($entity);
        $recent_regulation = $entity->findOneBy(['checkClosing' => 1, 'isPublic' =>1, 'published' => 1, 'deleted' => 0], ['id' => 'desc']);
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

    public function regulationStatisticsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Regulation::class);
        $all_regulations = $entity->getAllRegulations();
        $regulation_stats = array(
                    'all' => count($all_regulations),
                    'published' => count(
                        $entity->getPublishedRegulations()
                    ),
                    'unpublished' => count(
                        $entity->getAllUnpublishedRegulations()
                    ),
                    'open' => count($entity->getAllRegulationsOpenForConsultation()),
                    'closed' => count(
                        $entity->getAllRegulationsClosedForConsultation()
                    ),
                    'seven_all' => count($this->calculateDaysRemainingBeforeCLosing($entity->getAllRegulationsOpenForConsultation())),
                    'internal_consultations' => count($entity->getInternalConsultations()),
                    'trending_all' => $this->getFindUniqueTrendingRegulations($entity),
                    'public_consultations' => count($entity->getListOfFrontendRegulations())
                );
        return new Response(
            json_encode(
                $regulation_stats
            )
        );
    }



    public function siteStatisticsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $locations = sizeof(
            $em->getRepository(
                BusinessLocation::class
            )->findAllBusinessLocation()
        );
        $industries = sizeof(
            $em->getRepository(
                BusinessIndustry::class
            )->findAllActiveBusinessIndustry()
        );

        return new Response(
            json_encode(
                [
                    'locations' => $locations,
                    'industries' => $industries
                ]
            )
        );
    }

    public function siteStatistics_2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $business_types = sizeof(
            $em->getRepository(
                BusinessType::class
            )->findAllBusinessType()
        );
        $activities = sizeof(
            $em->getRepository(
                BusinessActivity::class
            )->findAllBusinessActivity()
        );
        return new Response(
            json_encode(
                [
                    'business_types' => $business_types,
                    'activities' => $activities
                ]
            )
        );
    }

    public function siteStatistics_3Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $agencies = sizeof(
            $em->getRepository(
                BusinessAgency::class
            )->getActiveAgencies()
        );
        $news = sizeof(
            $em->getRepository(
                News::class
            )->findByDeleted(0)
        );
        return new Response(
            json_encode(
                [
                    'agencies' => $agencies,
                    'news' => $news
                ]
            )
        );
    }

    public function siteStatistics_4Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $newsletters = sizeof(
            $em->getRepository(
                Newsletter::class
            )->findByDeleted(0)
        );
        $subscribers = sizeof(
            $em->getRepository(
                NewsletterSubscriber::class
            )->findAll()
        );
        return new Response(
            json_encode(
                [
                    'newsletters' => $newsletters,
                    'subscribers' => $subscribers
                ]
            )
        );
    }
    public function siteStatistics_5Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $groups = sizeof(
            $em->getRepository(
                Group::class
            )->findAll()
        );
        $users = sizeof(
            $em->getRepository(
                User::class
            )->getAllUsers()
        );

        $admin = sizeof(
            $em->getRepository(
                User::class
            )->getAllAdminUsers()
        );
        return new Response(
            json_encode(
                [
                    'users' => $users,
                    'admin' => $admin,
                    'groups' => $groups
                ]
            )
        );
    }

    public function numberOfCommentsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->getActiveComments();
        return new Response(
            json_encode(
                [
                    "number_of_comments" => sizeof($comments)
                ]
            )
        );
    }

    public function commentStatsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class);
        $regulation = $em->getRepository(Regulation::class);
        $high_expression = $regulation->getRegulationsWithLargestExpression();
        $regulation_name_expression = "";
        $regulation_like_expression = 0;
        $regulation_name_comments = "";
        $regulation_likes_comments = 0;
        $high_comments = $regulation->getRegulationsWithLargeNumberOfComments();
        $abusive_comments = $comments->getAbusiveComments();
        if (!empty($high_expression)) {
            $regulation_name_expression = $regulation->find(
                $high_expression[0]['id']
            )->getTitle();
            $regulation_like_expression = $high_expression[0]['likes_count'];
        }
        if (!empty($high_comments)) {
            $regulation_name_comments = $regulation->find(
                $high_comments[0]['id']
            )->getTitle();
            $regulation_likes_comments = $high_comments[0]['comment_count'];
        }

        return new Response(
            json_encode(
                [
                    "high_comments" => [
                        $regulation_name_comments,
                        $regulation_likes_comments
                    ],
                    "high_expression" => [
                        $regulation_name_expression,
                        $regulation_like_expression
                    ],
                    "abusive_comments" => sizeof($abusive_comments)
                ]
            )
        );
    }

    public function numberOfLicensesPerAgencyAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $regulations = $em->getRepository(
            Regulation::class
        )->getNumberOfLicensesPerAgency();
        $regulations_count = [];

        foreach ($regulations as $regulation) {
            $regulations_count[$regulation['title']]
                = $regulation['number_of_licenses'];
        }
        return new Response(
            json_encode($regulations_count)
        );
    }
}

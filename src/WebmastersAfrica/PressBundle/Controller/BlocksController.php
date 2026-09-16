<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Collection;
use WebmastersAfrica\PressBundle\Entity\Policy;
use WebmastersAfrica\PressBundle\Entity\Page;


class BlocksController extends Controller
{
    public function cssAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Blocks:css.html.twig');
    }

    public function noticeCssAction()
    {
        return $this->render(
            'WebmastersAfricaPressBundle:Blocks:notice_css.html.twig'
        );
    }

    public function jsAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Blocks:js.html.twig');
    }

    public function noticeJsAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Blocks:notice_js.html.twig');
    }
    public function bannerAction()
    {
        //Get statistics on location, business types and published licenses
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLocation p
                WHERE p.deleted = 0'
        );
        $locations = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessType p
                WHERE p.deleted = 0'
        );
        $business_types = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLicense p
                WHERE p.deleted = 0 AND  p.status = :published'
        )->setParameter("published", "published");
        $licenses = sizeof($query->getResult());

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:Banner p
                WHERE p.deleted = 0'
        );
        $banners = $query->getResult();

        return $this->render('WebmastersAfricaPressBundle:Blocks:banner.html.twig', array('locations' => $locations, 'business_types' => $business_types, 'licenses' => $licenses, 'banners' => $banners));
    }

    public function headerAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Blocks:header.html.twig');
    }

    public function noticeHeaderAction()
    {
        return $this->render(
            'WebmastersAfricaPressBundle:Blocks:notice_header.html.twig'
        );
    }

    public function pagemoreAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaPressBundle:Page p
                WHERE p.published = :published
                AND p.parent = :id
				AND p.deleted = 0
                ORDER BY p.page_order ASC'
        )->setParameter('id', $this->get('session')->get("pageid"))->setParameter('published', true);
        $pages = $query->getResult();

        return $this->render('WebmastersAfricaPressBundle:Blocks:pagemore.html.twig', array('pages' => $pages));
    }

    public function menuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository(Page::class)->findAllPublished(2);
        
        $route = $_SERVER['REQUEST_URI'];
        $route = str_replace("/index.php", "", $route);

        return $this->render('WebmastersAfricaPressBundle:Blocks:menu.html.twig', array('pages' => $pages, 'route' => $route));
    }

    public function noticeMenuAction()
    {
        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository(Page::class)->findAllPublished(1);

        $route = $_SERVER['REQUEST_URI'];

        $route = str_replace("/index.php", "", $route);

        return $this->render('WebmastersAfricaPressBundle:Blocks:notice_menu.html.twig', array('pages' => $pages, 'route' => $route));
    }


    public function footerAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Blocks:footer.html.twig');
    }

    public function noticeFooterAction()
    {
        $em = $this->getDoctrine()->getManager();
        $policy = $em->getRepository(Policy::class)->findBy(['published' => 1, 'deleted' => 0, 'site' => 1]);
        return $this->render(
            'WebmastersAfricaPressBundle:Blocks:notice_footer.html.twig',
            array(
                'policies' => $policy
            )
        );
    }

    public function sidebarAction()
    {
        return $this->render('WebmastersAfricaPressBundle:Blocks:sidebar.html.twig');
    }

    public function newslettersAction()
    {
        $collectionConstraint = new Collection(
            array(
                'subscribername' => new NotBlank(array('message' => 'Enter your name')),
                'subscriberemail' => new Email(array('message' => 'Invalid email address')),
            )
        );

        $defaultData = array('message' => 'Newsletter');
        $form = $this->createFormBuilder(
            $defaultData,
            array(
                'constraints' => $collectionConstraint
            )
        )
            ->add('subscribername', 'text', array('required' => true))
            ->add('subscriberemail', 'email', array('required' => true))
            ->getForm();

        return $this->render('WebmastersAfricaPressBundle:Blocks:newsletters.html.twig', array('form'   => $form->createView()));
    }

    public function commonbusinessesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessType p
                WHERE p.show_in_browse = :common
                ORDER BY p.name ASC'
        )->setParameter('common', true);
        $businesstypes = $query->getResult();


        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLocation p
                ORDER BY p.name ASC'
        );
        $cities = $query->getResult();

        $defaultcity = "";
        foreach ($cities as $city) {
            $defaultcity = $city;
        }

        return $this->render('WebmastersAfricaPressBundle:Blocks:commonbusinesses.html.twig', array('businesstypes' => $businesstypes, 'cities' => $cities, 'defaultcity' => $defaultcity));
    }

    public function mostviewedAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLicense p
                WHERE p.status = :status AND
                p.deleted = 0
                ORDER BY p.views DESC'
        )->setParameter('status', 1);
        $query->setMaxResults(5);
        $licenses = $query->getResult();
        return $this->render('WebmastersAfricaPressBundle:Blocks:mostviewed.html.twig', array('licenses' => $licenses));
    }

    public function recentupdatesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:BusinessLicense p
                WHERE p.status = :status AND p.deleted = 0
                ORDER BY p.id DESC'
        )->setParameter('status', 1);
        $query->setMaxResults(5);
        $licenses = $query->getResult();
        return $this->render('WebmastersAfricaPressBundle:Blocks:recentupdates.html.twig', array('licenses' => $licenses));
    }
}

<?php

namespace WebmastersAfrica\PressBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;
use WebmastersAfrica\LicenseBundle\Entity\BusinessActivity;
use WebmastersAfrica\PressBundle\Entity\BusinessStartup;
use WebmastersAfrica\PressBundle\Entity\ProcedureCategory;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Banner;

// use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function homeAction()
    {
        $industry = "";
        $businesstype = "";
        $businesstypes = "";
        //Get Default Language and Assign
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('WebmastersAfricaPressBundle:Locale');

        $locale = $repo->findOneBy(['is_default' => '1']);
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

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('WebmastersAfricaPressBundle:Page')->find(1);

        if (!$entity) {
            throw $this->createNotFoundException(
                'No Homepage Found. Check Database.'
            );
        }

        $license = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessLicense'
        );
        $licenses = $license->findAllOrderByViews();

        $pages = $em->getRepository(
            'WebmastersAfricaPressBundle:Page'
        )->findAllPublished(2);

        // Homepage "Jurisdictions" figure: all active jurisdictions (as BlocksController::bannerAction
        // and the upgraded app count them), not only those that already have a published licence.
        $locations = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessLocation'
        )->findAllBusinessLocation();
        
        $locationsCategory = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessLocationCategory'
        )->findAllBusinessLocationCategory();

        $business_types = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessType'
        )->findAllBusinessTypeCount();

        $banners = $em->getRepository(
            'WebmastersAfricaPressBundle:Banner'
        )->findAllBanners();

        $industry = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessIndustry'
        );

        $business_startup = $em->getRepository(
            ProcedureCategory::class
        )->findBy(['delete' => 0, 'publish' => 1]);
        // $industries = $industry->findAllActiveBusinessIndustry();
        $agencies = $em->getRepository(BusinessAgency::class);

        $route = $_SERVER['REQUEST_URI'];

        $route = str_replace("/index.php", "", $route);

        $speedB = "";
        $speedC = "";

        $businesstype = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessType'
        )->findAllBusinessTypeCount();
        $businessactivities = $em->getRepository(BusinessActivity::class)->getBusinessActivitiesWithLicenses();
        $licenses_count = count($license->findAllPublished());
        $industry = $industry->getAllIndustriesWithLicenses();
        return $this->render(
            'WebmastersAfricaPressBundle:Default:home.html.twig',
            array(
                'page' => $entity, 'licenses' => $licenses,
                'licenses_count' => $licenses_count,
                'pages' => $pages, 'route' => $route,
                'location_count' => count($locations),
                'locations' => $locationsCategory,
                'business_types' => count($business_types),
                'banners' => $banners,
                'industries' => $industry,
                'businesstypes' => $business_types,
                'agencies' => $agencies->getAgenciesWithLicensesOnly(),
                'speedB' => $speedB,
                'speedC' => $speedC,
                'speedD' => "",
                'speedE' => "",
                'industry' => $industry,
                'businesstype' => $business_types,
                'business_startup' => $business_startup,
                'businessactivities' => $businessactivities,
                'searchterm' => ""
            )
        );
    }

    /**
     * Home page for the notice and comment page
     *
     * @return void
     */
    public function noticeCommentAction()
    {
        $em = $this->getDoctrine()->getManager();
        $regulations = $em->getRepository(
            'NoticeCommentBundle:Regulation'
        )->getRegulationsForLandingPage();
        $settings = $em->getRepository('WebmastersAfricaUserBundle:Setting')->find(1);
        $closing_date_value = (int)$settings->getClosingDays();
        $industries = $em->getRepository('WebmastersAfricaLicenseBundle:BusinessIndustry')->getIndustriesWithMoreThan10Regulations();
        $banners = $em->getRepository(Banner::class)->findAllBanners();
        return $this->render(
            'WebmastersAfricaPressBundle:Default:notice_home.html.twig',
            [
                'regulations' => $regulations,
                'closing_date_value' => $closing_date_value,
                'industries' => $industries,
                'banners' => $banners,
                'banner_id' => $banners[0]->getId()
            ]
        );
    }
}

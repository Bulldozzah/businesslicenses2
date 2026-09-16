<?php

namespace WebmastersAfrica\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelpController extends Controller
{
    public function manualsAction()
    {
        return $this->render('WebmastersAfricaAdminBundle:Help:manuals.html.twig');
    }
    public function videosAction()
    {
        return $this->render('WebmastersAfricaAdminBundle:Help:videos.html.twig');
    }
}
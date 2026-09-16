<?php

namespace WebmastersAfrica\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;


class ReportsController extends Controller
{
    public function reportsAction(Request $request, $dash)
    {
        return $this->render(
            'WebmastersAfricaAdminBundle:Default:reports.html.twig',
            ['dash' => $dash]
        );
    }

    public function loginAction(Request $request)
    {
        $user = $this->validateRequest($request);
        if ( $user instanceof Response ) return $user;

        $time = time();
        $link = sprintf("%s/auth/login?key=%s&timestamp=%s&session_id=%s",
            $this->container->getParameter('app_report_url'), 
            hash('sha256', $this->container->getParameter('app_api_key').$time),
            $time,
            session_id()
        );
        return $this->redirect($link);
    }

    public function userAction(Request $request)
    {
        $user = $this->validateRequest($request);
        if ( $user instanceof Response ) return $user;

        return $this->json([
            'username'   => 'admin' == $user->getUsername() ? 'anon': $user->getUsername(),
            'first_name' => 'admin' == $user->getUsername() ? 'Anonymous': $user->getFirstName(),
            'last_name'  => 'admin' == $user->getUsername() ? 'User': $user->getLastName(),
            'dash_id'    => 1,
            'email'      => 'admin' == $user->getUsername() ? 'anon@otbafrica.com': $user->getEmail()
        ]);
    }

    public function groupAction(Request $request)
    {
        $user = $this->validateRequest($request);
        if ( $user instanceof Response ) return $user;

        $groups = [];
        foreach ($user->getGroups() as $group)
            $groups[] = $group->getId();

        return $this->json($groups);
    }

    private function validateRequest(Request $request) 
    {
        $timestamp = $request->get('timestamp');
        $key = $this->container->getParameter('app_api_key');

        if ( !($request->get('key') == hash('sha256', $key.$timestamp)) ) {
            return $this->json([
                'success' => false,
                'error'   => "User not authenticated"
            ], 403);
        }

        $user = $this->getUser();
        if ( is_null($user) ) {
            return $this->json([
                'success' => false,
                'error'   => "User not found"
            ], 404);
        }

        $security = $this->container->get('security.context');
        if ( !$security->isGranted([new Expression("hasRole('ACCESS_BACKEND')")]) ) {
            return $this->json([
                'success' => false,
                'error'   => "Access Denied"
            ], 403);
        }

        return $user;
    }

    private function json($content, $status=200, $headers=[]) {
        return new Response(json_encode($content), $status, array_merge(
            $headers, ['Content-Type' => 'application/json']
        ));
    }
}
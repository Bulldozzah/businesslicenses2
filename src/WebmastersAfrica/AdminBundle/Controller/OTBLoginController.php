<?php

namespace WebmastersAfrica\AdminBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\SecurityController as BaseController;
//Inherited from Resetting Controller
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

class OTBLoginController extends ContainerAware
{
    /**
     * Custom OTB Logging
     */
    public function checkAdminOTBAction(Request $request){
        /////////////////////////
        // Get our Security Context Object - [deprecated in 3.0]
            $security_context = $this->container->get('security.context');
            # e.g: $security_context->isGranted('ROLE_ADMIN');

            // Get our Token (representing the currently logged in user)
            $security_token = $security_context->getToken();
            # e.g: $security_token->getUser();
            # e.g: $security_token->isAuthenticated();
            # [Careful]             ^ "Anonymous users are technically authenticated"

            // Get our user from that security_token
            $user = $security_token->getUser();
            # e.g: $user->getEmail(); $user->isSuperAdmin(); $user->hasRole();

            // Check for Roles on the $security_context
            $isRoleAdmin = $security_context->isGranted('ROLE_ADMIN');
            //
            
        /////////////////////////
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
                        'otb_africa_admin_homepage'
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
    public function loginAction(Request $request)
    {
       //check if a user is logged in
       $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return new RedirectResponse($this->container->get('router')->generate('otb_africa_admin_homepage',
            array()
        ));
        }

        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            // TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
            $error = $error->getMessage();
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $csrfToken = $this->container->has('form.csrf_provider')
            ? $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate')
            : null;
        return $this->renderLogin(
            array(
                'last_username' => $lastUsername,
                'error'         => $error,
                'csrf_token' => $csrfToken,
            )
        );
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        $template = sprintf('WebmastersAfricaAdminBundle:Default:metronic_login.html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }
    /**
     * Admin reset password
     */
    public function resetPasswordAction(Request $request)
    {
        //Check supplied email exists in our system
        //either render login or send a token to user for password reset process
        $data = array();
        return $this->renderResetPassword($data);
    }
    /**
     * Render template to reset password
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderResetPassword(array $data){
     
        $template = sprintf('WebmastersAfricaAdminBundle:Default:metronic_reset_password.html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);

    }

    /**
     * Reset password process
     */
    public function sendPasswordResetCodeAction(Request $request){
        //error_log("Debug: Custom password reset process");
        //
        $username = $request->request->get('username');
        //error_log($username);

        /** @var $user UserInterface */
        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            return $this->container->get('templating')->renderResponse('WebmastersAfricaAdminBundle:Default:metronic_password_reset_usernotfound.html.'.$this->getEngine(), array('invalid_username' => $username));
        }
        //
        /*if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
            return $this->container->get('templating')->renderResponse('WebmastersAfricaAdminBundle:Default:metronic_password_alreadysent.html.'.$this->getEngine());
        }*/

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }
        //OTB patch - We call our own implementation
        $this->container->get('fos_user.mailer')->sendResettingBackendEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);
        return new RedirectResponse($this->container->get('router')->generate('admin_reset_password_sent_success',
            array('email' => $user->getEmail())
        ));
    }
    /**
     *  Add custom method for reset backend user passwords
     */
    public function resetAction(Request $request, $token)
    {
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->container->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->container->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);
        //error_log("Debug: Here i am");
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        //OTB patch - Quick fix for validator requiring photo on form submission.
        //set it to null but fix the validator
        //https://stackoverflow.com/questions/44908686/fosuserbundle-the-file-could-not-be-found-error-while-resetting-changing-pas
        //$user->setPhoto(null);
        $form->setData($user);
        //
        //error_log($user);

        if ('POST' === $request->getMethod()) {
            //error_log("Debug::: Post method successfult");
            $form->bind($request);

            if ($form->isValid()) {
                //error_log("Debug: Form is valid");
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

                $userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->container->get('router')->generate('admin_reset_password_was_successful');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
            //else{
            //    throw new NotFoundHttpException(sprintf('Whoops! Something is missing on reset email settings. Please check!'));
        
            //}
        }

        return $this->container->get('templating')->renderResponse('WebmastersAfricaAdminBundle:Default:reset.html.'.$this->getEngine(), array(
            'token' => $token,
            'form' => $form->createView(),
        ));
    }
    /**
     * Reset success template
     */
    public function adminResetAction(Request $request){

        return $this->container->get('templating')->renderResponse('WebmastersAfricaAdminBundle:Default:metronic_reset_success.html.'.$this->getEngine(), array(
            'message' => 'Password reset was successful',
        ));
    }
    /**
     * Render success page
     */
    public function passwordResetSuccessAction(Request $request){
        $email = $request->query->get('email');
        
        if (empty($email)) {
            // the user does not come from the sendEmail action
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:checkEmail.html.'.$this->getEngine(), array(
                'error' => 'email missing error',
            ));
        }
        //generate path for success
        return $this->container->get('templating')->renderResponse('WebmastersAfricaAdminBundle:Default:metronic_reset_sent.html.'.$this->getEngine(), array(
            'email' => $email,
        ));
      }
    /**
     * Tell the user to check his email provider
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->query->get('email');

        if (empty($email)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->container->get('router')->generate('admin_reset_backend_password'));
        }

        return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:checkEmail.html.'.$this->getEngine(), array(
            'email' => $email,
        ));
    }
     /**
     * Get the truncated email displayed when requesting the resetting.
     *
     * The default implementation only keeps the part following @ in the address.
     *
     * @param \FOS\UserBundle\Model\UserInterface $user
     *
     * @return string
     */
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }



    protected function getEngine()
    {
        return $this->container->getParameter('fos_user.template.engine');
    }
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}

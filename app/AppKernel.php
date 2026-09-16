<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new FOS\MessageBundle\FOSMessageBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new WebmastersAfrica\AdminBundle\WebmastersAfricaAdminBundle(),
            new WebmastersAfrica\UserBundle\WebmastersAfricaUserBundle(),
            new WebmastersAfrica\PressBundle\WebmastersAfricaPressBundle(),
            new WebmastersAfrica\LicenseBundle\WebmastersAfricaLicenseBundle(),
            new WebmastersAfrica\MessageBundle\WebmastersAfricaMessageBundle(),
            new WebmastersAfrica\TaskBundle\WebmastersAfricaTaskBundle(),
            new DefaultTemplate\PressBundle\DefaultTemplatePressBundle(),
            new OTB\Bundle\NoticeAndCommentBundle\NoticeCommentBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Cocur\Slugify\Bridge\Symfony\CocurSlugifyBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Liuggio\ExcelBundle\LiuggioExcelBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Bazinga\Bundle\FakerBundle\BazingaFakerBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (false === $this->booted) {
            $this->boot();
        }

        if ($this->isSessionKeyValid()) {
            $request = Request::createFromGlobals();
        }
        
        return $this->getHttpKernel()->handle($request, $type, $catch);
    }

    /**
     * Check if we are to start the session manually.
     *
     * @return  boolean
     */
    private function isSessionKeyValid()
    {
        if (!isset($_GET['session_id']) || !isset($_GET['key']) || !isset($_GET['timestamp'])) {
                return false;
        }

        $apiKey = $this->container->getParameter('app_api_key');
        $sessName = $this->container->get('session')->getName();

        if (isset($_COOKIE[$sessName]) || $_GET['key'] != hash('sha256', $apiKey.$_GET['timestamp'])) {
            return false;
        }
        
        $_COOKIE[$sessName] = $_GET['session_id'];
        return true;
    }
}

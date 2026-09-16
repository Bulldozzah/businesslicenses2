<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LicenseDownloadType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'licenses.download_name', 'translation_domain' => 'WebmastersAfricaLicenseBundle','required'    => false))
            ->add('issuing_body', 'text', array('label' => 'licenses.issuing_body', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
            ->add('file', 'file', array('label' => 'licenses.file', 'translation_domain' => 'WebmastersAfricaLicenseBundle','required'    => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\LicenseDownload'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_licensedownload';
    }
}

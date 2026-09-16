<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LicenseStatuteType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('issued_on')
            ->add('issued_by')
            ->add('statute_no')
            ->add('valid_from')
            ->add('valid_to')
            ->add('chapter')
            ->add('section')
            ->add('license_id')
            ->add('license')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\LicenseStatute'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_licensestatute';
    }
}

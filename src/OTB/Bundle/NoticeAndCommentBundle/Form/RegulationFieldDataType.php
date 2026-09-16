<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LicenseFieldDataType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fielddata')
            ->add('license_id')
            ->add('field_id')
            ->add('license')
            ->add('field');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\LicenseFieldData'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_licensefielddata';
    }
}

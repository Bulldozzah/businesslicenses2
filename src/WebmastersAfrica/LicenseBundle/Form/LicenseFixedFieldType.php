<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LicenseFixedFieldType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fieldlabel',
                 'text',
                  array(
                    'label' => 'fixed_license_fields.field_label', 
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
              )
            ->add('showed', null, array('label' => 'fixed_license_fields.showed', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
            ->add('required', null, array('label' => 'fixed_license_fields.required', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
            ->add('update', 'submit', array('label' => 'fixed_license_fields.update', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\LicenseFixedField'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_licensefixedfield';
    }
}

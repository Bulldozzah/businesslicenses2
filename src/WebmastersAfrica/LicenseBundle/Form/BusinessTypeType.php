<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BusinessTypeType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',
                 'text', 
                 array(
                    'label' => 'business_types.name',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
             )
            ->add('description',
                 'textarea', 
                 array(
                    'label' => 'business_types.description',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
             )
            ->add('industries',null,array('attr'=>array('class' => 'chzn-select', 'style' => 'width: 100%'),'label' => 'business_types.industries', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
            ->add('activities',null,array('attr'=>array('class' => 'chzn-select', 'style' => 'width: 100%'),'label' => 'business_types.activities', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
            ->add('show_in_browse','hidden',array('required' => false,'label' => 'business_types.is_common', 'translation_domain' => 'WebmastersAfricaLicenseBundle', "empty_data" => false ))
            ->add('update', 'submit', array('label' => 'business_types.update', 'translation_domain' => 'WebmastersAfricaLicenseBundle','attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\BusinessType'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_businesstype';
    }
}

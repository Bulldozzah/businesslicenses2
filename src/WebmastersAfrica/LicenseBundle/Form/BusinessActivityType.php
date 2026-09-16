<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BusinessActivityType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name', 
                'text', 
                array(
                    'label' => 'activities.name', 'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
            )
            ->add(
                'description', 
                'textarea', 
                array(
                    'label' => 'activities.description',
                    'label_attr' => array(
                            "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add(
                'licenses',
                null,
                array(
                    'attr'=> array(
                        'class' => 'chzn-select', 'style' => 'width: 100%'),
                    'label' => 'activities.licenses', 
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add(
                'businesstypes',
                null,
                array(
                    'attr'=>array(
                        'class' => 'chzn-select', 'style' => 'width: 100%'),'label' => 'activities.business_types', 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add(
                'update',
                 'submit',
                  array(
                    'label' => 'activities.update', 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
              )
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\BusinessActivity'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_businessactivity';
    }
}

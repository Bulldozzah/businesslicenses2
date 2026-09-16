<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BusinessLocationType extends AbstractType
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
                    'label' => 'locations.name',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
            )
            ->add(
                'parentLocation',
                null,
                array(
                    "label" => "Parent Location",
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
            )
            ->add(
                'is_default',
                'hidden',
                array(
                    'required' => false,
                    "empty_data" => false,
                    'label' => 'locations.is_default', 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add('submit', 'submit', array('label' => 'locations.update', 'translation_domain' => 'WebmastersAfricaLicenseBundle', 'label' => 'Create',
                    'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\BusinessLocation'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_businesslocation';
    }
}

<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BusinessLocationCategoryType extends AbstractType
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
                    'label' => 'Parent Location',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
            )
            ->add('published');
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\BusinessLocationCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_businesslocationcategory';
    }
}

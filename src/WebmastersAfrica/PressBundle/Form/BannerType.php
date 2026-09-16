<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',
                'textarea', 
                array('label' => 'banners.title',
                 'translation_domain' => 'WebmastersAfricaPressBundle', 
                  'label_attr' => array(
                            "class" => "label-required"
                        ),
                    )
                )
            ->add('description',
                    'textarea', 
                        array('label' => 'banners.description', 
                                'translation_domain' => 'WebmastersAfricaPressBundle',
                                'label_attr' => array(
                                "class" => "label-required"
                        ),
                    )
                )
            ->add(
                'file',
                'file',
                array(
                    'label' => 'banners.image',
                    'required' => false,
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                        'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'image',
                'hidden',
                array(
                    'label' => 'banners.image',
                    'required' => false,
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                )
            )
            ->add('submit','submit', array('label' => 'Create', 'translation_domain' => 'WebmastersAfricaPressBundle'))
        ;
    }


    public function getName()
    {
        return 'webmasters_contentbundle_bannertype';
    }
}

<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                'textarea',
                array(
                    'label' => 'banners.title',
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
                    'required' => true,
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
            ->add('submit', 'submit', array(
                'label' => 'Create', 'translation_domain' => 'WebmastersAfricaLicenseBundle', 'attr' => array(
                    'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            ));
    }


    public function getName()
    {
        return 'webmasters_contentbundle_bannertype';
    }
}

<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'page_title',
                'text',
                array(
                    'label' => 'pages.page_title',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                )
            )
            ->add(
                'page_breadcrumb_title',
                'text',
                array(
                    'label' => 'pages.page_breadcrumb_title',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                )
            )
            ->add('page_content', 'textarea', array('label' => 'pages.page_content', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add(
                'seo_keywords',
                'textarea',
                array(
                    'label' => 'pages.seo_keywords',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                )
            )
            ->add(
                'seo_description',
                'textarea',
                array(
                    'label' => 'pages.seo_description',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                )
            )
            ->add('page_layout', 'choice', array(
                'choices'   => array('1' => 'Web Page', '2' => 'Module'),
                'required'  => true,
            ))
            // ->add('parent',null, array('label' => 'pages.parent', 'translation_domain' => 'WebmastersAfricaPressBundle', 'required' => false))
            ->add('url', 'text', array('label' => 'pages.url', 'translation_domain' => 'WebmastersAfricaPressBundle', 'required' => false))
            ->add(
                'page_order',
                'number',
                array(
                    'label' => 'pages.page_order',
                    'translation_domain' => 'WebmastersAfricaPressBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                )
            )
            ->add('published', null, array('label' => 'pages.published', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('update', 'submit', array('label' => 'pages.update', 'translation_domain' => 'WebmastersAfricaPressBundle'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\PressBundle\Entity\Page'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_pressbundle_page';
    }
}

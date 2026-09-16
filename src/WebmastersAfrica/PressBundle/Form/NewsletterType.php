<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsletterType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject',
                'text',
                 array('label' => 'newsletters.subject',
                        'translation_domain' => 'WebmastersAfricaPressBundle',
                        'label_attr' => array(
                                    "class" => "label-required"),
                    )
             )
            ->add('content',
                'textarea',
                 array('label' => 'newsletters.content',
                        'translation_domain' => 'WebmastersAfricaPressBundle',
                        'label_attr' => array(
                                    "class" => "label-required"),
                    )
             )
            ->add('sent',
                    null,
                     array('label' => 'newsletters.sent', 
                            'translation_domain' => 'WebmastersAfricaPressBundle',
                            'label_attr' => array(
                                    "class" => "label-required"),
                        )
                 )
            ->add('update','submit', array('label' => 'newsletters.update', 'translation_domain' => 'WebmastersAfricaPressBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\PressBundle\Entity\Newsletter'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_pressbundle_newsletter';
    }
}

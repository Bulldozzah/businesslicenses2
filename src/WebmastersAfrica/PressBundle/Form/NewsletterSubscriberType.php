<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsletterSubscriberType extends AbstractType
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
                         array('label' => 'subscribers.name',
                                 'translation_domain' => 'WebmastersAfricaPressBundle',
                                    'label_attr' => array(
                                        "class" => "label-required"),
                                )
                     )
            ->add('email',
                    'email', 
                        array('label' => 'subscribers.email', 
                                'translation_domain' => 'WebmastersAfricaPressBundle',
                                'label_attr' => array(
                                "class" => "label-required"),
                            )
                    )
            ->add('organisation',
                    'text', 
                        array('label' => 'subscribers.organisation', 
                                'translation_domain' => 'WebmastersAfricaPressBundle',
                                'label_attr' => array(
                                "class" => "label-required"),
                            )
                    )
            ->add('confirmed',null, array('label' => 'subscribers.confirmed', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('update','submit', array('label' => 'subscribers.update', 'translation_domain' => 'WebmastersAfricaPressBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\PressBundle\Entity\NewsletterSubscriber'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_pressbundle_newslettersubscriber';
    }
}

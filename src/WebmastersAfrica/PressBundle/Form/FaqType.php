<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FaqType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question',
                     'text',
                         array('label' => 'faqs.question', 
                                'translation_domain' => 'WebmastersAfricaPressBundle', 
                                'label_attr' => array(
                                "class" => "label-required"),
                            )
                     )
            ->add('answer',
                    'textarea', 
                        array('label' => 'faqs.answer',
                             'translation_domain' => 'WebmastersAfricaPressBundle', 
                             'label_attr' => array(
                                "class" => "label-required"),
                         )
                    )
            ->add('orderLevel',
                     'number',
                         array('label' => 'Order Level',
                                 'required' => true, 
                                 'label_attr' => array(
                                "class" => "label-required"),
                            )
                     )
            ->add('published', null, array('label' => 'faqs.published', 'required'  => false, 'translation_domain' => 'WebmastersAfricaPressBundle'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\PressBundle\Entity\Faq'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_pressbundle_faq';
    }
}

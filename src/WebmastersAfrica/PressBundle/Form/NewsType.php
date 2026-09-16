<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NewsType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',
                    'text',
                         array('label' => 'news.title',
                             'translation_domain' => 'WebmastersAfricaPressBundle',
                            'label_attr' => array(
                            "class" => "label-required"),
                        )
                     )
            ->add('article',
                    'textarea',
                         array('label' => 'news.article', 
                                'translation_domain' => 'WebmastersAfricaPressBundle',
                                
                            )
                     )
            ->add('published',null, array('label' => 'news.published', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('update','submit', array('label' => 'news.update', 'translation_domain' => 'WebmastersAfricaPressBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\PressBundle\Entity\News'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_pressbundle_news';
    }
}

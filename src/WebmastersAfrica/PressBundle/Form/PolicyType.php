<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PolicyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', null, array( 'label'=>'Title',
                                    'label_attr' => array(
                                    "class" => "label-required"),
                                )
                            )
        ->add('content',null, array( 'label'=>'Content',
                                    'label_attr' => array(
                                    "class" => "label-required"),
                                )
                            )
        ->add('seo_keywords',null, array( 'label'=>'SEO Keywords',
                                    'label_attr' => array(
                                    "class" => "label-required"),
                                )
                            )
        ->add(
            'seo_description')
        ->add(
            'policyType',null, array( 'label'=>'Policy Type',
                                    'label_attr' => array(
                                    "class" => "label-required"),
                                )
                            )
        ->add(
            'site',
            'choice',
            array(
                'label' => 'Site', 'choices'  => array(
                    1 => 'Notice & Comment'
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
            'data_class' => 'WebmastersAfrica\PressBundle\Entity\Policy'
        ));
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'policy';
    }
}

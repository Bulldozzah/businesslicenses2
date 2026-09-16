<?php

namespace WebmastersAfrica\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupType extends AbstractType
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
                 array('label' => 'groups.group_name',
                         'translation_domain' => 'WebmastersAfricaUserBundle',
                        'label_attr' => array(
                                    "class" => "label-required"),
                    )
             )
            ->add('update','submit', array('label' => 'groups.update', 'translation_domain' => 'WebmastersAfricaUserBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\UserBundle\Entity\Group'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_userbundle_group';
    }
}

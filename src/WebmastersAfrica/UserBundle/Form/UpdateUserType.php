<?php

namespace WebmastersAfrica\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UpdateUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', 'text', array('label' => 'users.first_name', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('last_name', 'text', array('label' => 'users.last_name', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('email', 'email', array('label' => 'users.email', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('username', 'text', array('label' => 'users.username', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add(
                'password',
                'password',
                array(
                    'label' => 'users.update_password',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    "required" => false
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\UserBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_userbundle_user';
    }
}

<?php

namespace WebmastersAfrica\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'first_name',
                'text',
                array(
                    'label' => 'users.first_name',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'last_name',
                'text',
                array(
                    'label' => 'users.last_name',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'phone_number',
                'number',
                array(
                    'label' => 'users.phone_number',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label' => 'users.email',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'username',
                'text',
                array(
                    'label' => 'users.username',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'password',
                'password',
                array(
                    'label' => 'users.password',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'agencies',
                null,
                array(
                    'label' => 'users.agencies',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'groups',
                null,
                array(
                    'label' => 'users.groups',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'users.enabled',
                    'translation_domain' => 'WebmastersAfricaUserBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add('update', 'submit', array('label' => 'users.update', 'translation_domain' => 'WebmastersAfricaUserBundle'));
        // ->add('create', 'submit', array('label' => 'Create'));
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

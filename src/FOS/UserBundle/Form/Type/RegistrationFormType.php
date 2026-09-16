<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'plainPassword',
                'repeated',
                array(
                    'type' => 'password',
                    'options' => array(
                        'translation_domain' => 'FOSUserBundle'
                    ),
                    'first_options' => array(
                        'label' => 'form.password',
                        'label_attr' => array(
                            "class" => "label-required"
                        )
                    ),
                    'second_options' => array(
                        'label' => 'form.password_confirmation',
                        'label_attr' => array(
                            "class" => "label-required"
                        )
                    ),
                    'invalid_message' => 'fos_user.password.mismatch',
                )
            )
            ->add(
                'first_name',
                'text',
                array(
                    'label' => 'users.first_name',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaUserBundle'
                )
            )
            ->add(
                'last_name',
                'text',
                array(
                    'label' => 'users.last_name',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaUserBundle'
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label' => 'users.email',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaUserBundle'
                )
            )
            ->add(
                'username',
                'text',
                array(
                    'label' => 'users.username',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaUserBundle'
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'fos_user_registration';
    }
}

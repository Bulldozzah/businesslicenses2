<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BusinessStartupType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $status[0] = "Unpublished";
        $status[1] = "Published";
        $builder
            ->add(
                'name',
                'text',
                array(
                    'label' => "Title",
                    "attr" => array(
                        "class" => "form-control w3-input w3-border w3-round"
                    ),
                    'label_attr' => array(
                                "class" => "label-required"
                        ),
                )
            )
            ->add('description', null,
                array('label_attr' => array(
                                "class" => "label-required"
                        ),
                ) 
            )
            ->add('links',null,
                array('label_attr' => array(
                                "class" => "label-required"
                        ),
            )       )
            ->add(
                'isPublished',
                'choice',
                array(
                    'label' => 'licenses.status',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'choices' => $status
                )
            )
            ->add('procedure_category');
    }

    /**
     * Set Default Options
     * 
     * @param OptionsResolverInterface $resolver // s
     * 
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'WebmastersAfrica\PressBundle\Entity\BusinessStartup'
            )
        );
    }

    /**
     * Get Name
     * 
     * @return string
     */
    public function getName()
    {
        return 'startup';
    }
}

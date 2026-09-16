<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProcedureCategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $status[0] = "Unpublish";
        $status[1] = "Publish";
        $builder
            ->add(
                'title',
                'text',
                array(
                    "label" => "Category Title",
                    "attr" => array(
                        "class" => "form-control w3-input w3-border w3-round",
                        "style" => "width:100%;"
                    )
                )
            )
            ->add(
                'publish',
                'choice',
                array(
                    'label' => 'licenses.status',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'choices' => $status,
                    "attr" => array(
                        "class" => "form-control w3-select",
                        "style" => "width: 100%"
                    )
                )
            );
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
                'data_class' =>
                'WebmastersAfrica\PressBundle\Entity\ProcedureCategory'
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

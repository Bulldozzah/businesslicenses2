<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LicenseFieldType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fieldlabel',
                 'text', 
                 array(
                    'label' => 'custom_license_fields.field_label',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
             )
            ->add('fieldtype',
                 'choice', 
                 array(
                    'choices'   => array('1' => 'Text Field', '2' => 'Dropdown', '3' => 'Textarea'),
                    'required'  => true,'label' => 'custom_license_fields.type', 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add('fieldchoices', 'collection', array('type' => new LicenseFieldChoiceType(),
        'allow_add'    => true,'by_reference' => false,'allow_delete' => true,'label' => 'custom_license_fields.field_choices', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
            ->add('order', 'number', array('label' => 'custom_license_fields.order', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
            ->add('showed', null, array('label' => 'custom_license_fields.showed', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
            ->add('update', 'submit', array('label' => 'custom_license_fields.update', 'translation_domain' => 'WebmastersAfricaLicenseBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\LicenseField'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_licensefield';
    }
}

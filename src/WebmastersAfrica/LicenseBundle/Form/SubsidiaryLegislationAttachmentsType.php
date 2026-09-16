<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubsidiaryLegislationAttachmentsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('label' => 'licenses.download_name', 'translation_domain' => 'WebmastersAfricaLicenseBundle', 'required'    => false))
            ->add(
                'file',
                'file',
                array(
                    'label' => "Subsidiary Legislation",
                    'required' => false,
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => "WebmastersAfrica\LicenseBundle\Entity\SubsidiaryLegislationAttachments"
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'subsidiary_legislation';
    }
}

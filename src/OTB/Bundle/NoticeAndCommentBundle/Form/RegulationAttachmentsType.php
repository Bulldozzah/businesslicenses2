<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationAttachments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class RegulationAttachmentsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                array(
                    'label' => 'licenses.download_name',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    "data_class" => null,
                    'required'    => false,
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'file',
                'file',
                array(
                    'label' => "Supporting Materials",
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
                'data_class' => "OTB\Bundle\NoticeAndCommentBundle\Entity\RegulationAttachments"
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'regulation_attachment';
    }
}

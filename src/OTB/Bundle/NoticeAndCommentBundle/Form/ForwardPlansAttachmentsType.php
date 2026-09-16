<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlansAttachments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class ForwardPlansAttachmentsType extends AbstractType
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
              'required'    => true
            )
          )
          ->add(
            'file',
            'file',
            array(
              'label' => "Supporting Document",
              'required' => true,
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
              'data_class' => "OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlansAttachments"
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'forward_plans_attachment';
    }
}

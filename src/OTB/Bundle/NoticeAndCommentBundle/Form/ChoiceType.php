<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choiceText', 'text', array('label' => 'choice name', 'required' => true));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OTB\Bundle\NoticeAndCommentBundle\Entity\Choice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'otb_choice';
    }
}

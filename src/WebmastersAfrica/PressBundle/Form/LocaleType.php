<?php

namespace WebmastersAfrica\PressBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LocaleType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title','text', array('label' => 'languages.title', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('localeCode','text', array('label' => 'languages.locale_code', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('enabled',null, array('label' => 'languages.enabled', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('is_default',null, array('label' => 'languages.is_default', 'translation_domain' => 'WebmastersAfricaPressBundle'))
            ->add('update','submit', array('label' => 'languages.update', 'translation_domain' => 'WebmastersAfricaPressBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\PressBundle\Entity\Locale'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_pressbundle_locale';
    }
}

<?php

namespace WebmastersAfrica\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SettingType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteTitle','text', array('label' => 'settings.site_title', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('siteDescription','text', array('label' => 'settings.site_description', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('siteOwner','text', array('label' => 'settings.site_owner', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('siteAddress','text', array('label' => 'settings.site_address', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('footer','text', array('label' => 'settings.site_footer', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('siteEmailTitle','text', array('label' => 'settings.site_email_title', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('closingDays','number', array('label' => "Days to close a regulation:", 'translation_domain' => 'WebmastersAfricaUserBundle', 'required' => true))
            ->add('siteEmailAddress','email', array('label' => 'settings.site_email_address', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('siteKeywords','text', array('label' => 'settings.site_keywords', 'translation_domain' => 'WebmastersAfricaUserBundle'))
            ->add('update','submit', array('label' => 'settings.update', 'translation_domain' => 'WebmastersAfricaUserBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\UserBundle\Entity\Setting'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_userbundle_setting';
    }
}

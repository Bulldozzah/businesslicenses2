<?php

namespace FOS\MessageBundle\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Message form type for starting a new conversation
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder
            ->add('recipient', 'entity', array(
                'class' => 'WebmastersAfricaUserBundle:User',
                'property' => 'username',
                'label' => 'messages.recipient', 'translation_domain' => 'WebmastersAfricaMessageBundle',
            ))
            ->add('subject', 'text', array('label' => 'messages.subject', 'translation_domain' => 'WebmastersAfricaMessageBundle'))
            ->add('body', 'textarea' , array('label' => 'messages.body', 'translation_domain' => 'WebmastersAfricaMessageBundle'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'intention'  => 'message',
        ));
    }

    public function getName()
    {
        return 'fos_message_new_thread';
    }
}

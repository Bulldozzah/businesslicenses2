<?php
namespace FOS\MessageBundle\FormType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Message form type for starting a new conversation with multiple recipients
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class NewThreadMultipleMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('recipients', 'recipients_selector', array('label' => 'messages.recipient', 'translation_domain' => 'WebmastersAfricaMessageBundle'))
            ->add('subject', 'text', array('label' => 'messages.subject', 'translation_domain' => 'WebmastersAfricaMessageBundle'))
            ->add('body', 'textarea', array('label' => 'messages.body', 'translation_domain' => 'WebmastersAfricaMessageBundle'));
    }

    public function getName()
    {
        return 'fos_message_new_multiperson_thread';
    }
}

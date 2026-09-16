<?php

namespace WebmastersAfrica\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $statusarray['pending'] = "Pending";
        $statusarray['done'] = "Done";

        $builder
            ->add('taskDescription', 'textarea', array('label' => 'tasks.description', 'translation_domain' => 'WebmastersAfricaTaskBundle'))
            ->add('taskStartDate', 'date', array('format' => 'dd-MM-yyyy', 'widget' => 'single_text', 'attr' => array('class' => 'datepicker'),'label' => 'tasks.start_date', 'translation_domain' => 'WebmastersAfricaTaskBundle'))
            ->add('taskEndDate', 'date', array('format' => 'dd-MM-yyyy', 'widget' => 'single_text', 'attr' => array('class' => 'datepicker'),'label' => 'tasks.end_date', 'translation_domain' => 'WebmastersAfricaTaskBundle'))
            ->add('assignee', null, array('label' => 'tasks.assignee', 'translation_domain' => 'WebmastersAfricaTaskBundle'))
            ->add('assignee', null, array('label' => 'tasks.assignee', 'translation_domain' => 'WebmastersAfricaTaskBundle'))
            ->add('taskStatus','choice', array('label' => 'tasks.status', 'translation_domain' => 'WebmastersAfricaTaskBundle', 'choices' => $statusarray))
            ->add('license', null, array('label' => 'tasks.license', 'required' => true, 'translation_domain' => 'WebmastersAfricaTaskBundle'))
            ->add('update', 'submit', array('label' => 'tasks.update', 'translation_domain' => 'WebmastersAfricaTaskBundle'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\TaskBundle\Entity\Task'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_taskbundle_task';
    }
}

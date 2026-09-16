<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use WebmastersAfrica\UserBundle\Entity\UserRepository;
use WebmastersAfrica\LicenseBundle\Entity\WorkflowRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class WorkflowType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $stages = array('submission' => 'submission', 'unpublish' => 'unpublished' ,"corrections" => "corrections" ,'assess' => 'assessment', 'publish' => 'publish', 'archive' => 'archive');
        $builder
            ->add(
                'title',
                null,
                array(
                    'attr' => array(
                        'class' => 'w3-input w3-border form-control'
                    ),
                    'label' => 'My Title',
                    'label_attr' => array(
                            "class" => "label-required"),
                )
            )
            ->add(
                'published'
            )
            ->add('taskDescription',
                 'text',
                  array('label' => 'Task Description',
                        'label_attr' => array(
                            "class" => "label-required"),
                )
              )
            ->add('firstStage')
            ->add('sendEmails')
            ->add(
                'nextStage',
                null,
                array(
                    'label' => 'Next Step'
                )
            )
            ->add(
                'assignee',
                'entity',
                array(
                    'label' => 'Assign Task automatically To:',
                    'class' => 'WebmastersAfricaUserBundle:User',
                    'query_builder' => function (UserRepository $repository) {
                        return $repository->getAllAdminUsersAgency();
                    },
                    'empty_value' => '',
                    'required' => false
                )
            )
            ->add(
                'notificationsUser',
                'entity',
                array(
                    'label' => 'Select Users to Notify',
                    'class' => 'WebmastersAfricaUserBundle:User',
                    'query_builder' => function (UserRepository $repository) {
                        return $repository->getAllAdminUsersOnly();
                    },
                    'empty_value' => '',
                    'multiple' => true,
                    'required' => false
                )
            )
            ->add(
                'agency',
                null,
                array('attr' => array(
                    'class' => 'chzn-select', 'style' => 'width: 100%'
                ), 'label' => 'Select agency')
            )
            ->add(
                'accessGroups',
                null,
                array('attr' => array(
                    'class' => 'chzn-select', 'style' => 'width: 100%'
                ), 'label' => 'Access Groups')
            )->add(
                'type',
                'choice',
                array(
                    'label' => 'Stage Type', 'choices' => $stages, 'attr' => array('class' => 'form-control')
                )
            )->add("previousStage", null, array("label" => "select reject stage",
                    'required' => false
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
                'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\Workflow'
            )
        );
    }

   
    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_workflow';
    }
}

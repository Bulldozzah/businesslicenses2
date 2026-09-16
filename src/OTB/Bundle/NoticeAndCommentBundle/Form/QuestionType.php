<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OTB\Bundle\NoticeAndCommentBundle\Form\ChoiceType;
use Doctrine\ORM\EntityRepository;

class QuestionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($_SESSION['survey_id']) {
            $builder->add(
                'survey',
                'entity',
                array(
                    'label' => 'Survey',
                    'label_attr' => array(
                        'class' => 'label-required'
                    ),
                    'class' => 'NoticeCommentBundle:Survey',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('a');
                        // the function returns a QueryBuilder object
                        return $qb->where($qb->expr()->eq('a.id', $_SESSION['survey_id']));
                    },
                    'required' => true,
                )
            );
        } else {
            $builder->add(
                'survey',
                'entity',
                array(
                    'label' => 'Survey',
                    'label_attr' => array(
                        'class' => 'label-required'
                    ),
                    'class' => 'NoticeCommentBundle:Survey',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('u');
                        // the function returns a QueryBuilder object
                        return $qb;
                    },
                    'required' => true,
                )
            );
        }
        $builder
            ->add(
                'questionsType',
                'choice',
                array(
                    'label' => 'Question Type:',
                    'attr' => array(
                        'class' => 'form-control w3-select w3-border'
                    ),
                    'choices' => array(
                        'input' => 'Open Text',
                        'radio' => 'Select One',
                        'checkbox' => 'Select Many'
                    ),
                    'required' => true,
                )
            )
            ->add(
                'questionText',
                'text',
                array(
                    'label' => 'Question text:',
                    'attr' => array(
                        'class' => 'w3-input w3-border',
                        'data-required' => 'required'
                    ),
                    "required" => true
                )
            )
            ->add(
                'isRequired',
                'checkbox',
                array(
                    'label' => 'Answer Required',
                    'attr' => array(
                        'class' => 'form-control w3-select w3-border'
                    ),
                    'required' => false
                )
            )
            ->add(
                'questionOrder',
                'hidden',
                array(
                    'label' => 'Question Order',
                    'required'    => false,
                    'empty_data'  => 1
                )
            )->add(
                'choices',
                'collection',
                array(
                    'type' => new ChoiceType(),
                    'prototype'    => true,
                    'allow_add'    => true,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'label' => false
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OTB\Bundle\NoticeAndCommentBundle\Entity\Question'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'otb_question';
    }
}

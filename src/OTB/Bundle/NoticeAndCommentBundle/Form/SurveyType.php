<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use OTB\Bundle\NoticeAndCommentBundle\Form\QuestionType;

class SurveyType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'surveyName',
                'text',
                array(
                    'label' => 'Survey Title',
                    'attr' => array('class' => 'form-control w3-input w3-border')
                )
            )
            ->add(
            	'published',
            	'hidden',
            	array(
                'label' => 'Publish',
                'required'    => false,
                'empty_data'  => false
              )
            );
            if ($_SESSION['agency']) {
            	$builder->add(
                'regulation',
                'entity',
                array(
                    'label' => 'Select Consultation',
                    'class' => 'NoticeCommentBundle:Regulation',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('r');
                        $qb->where($qb->expr()->eq('r.published', true));
                        $qb->andWhere($qb->expr()->eq('r.consultationStage', 1));
                        $qb->andWhere($qb->expr()->eq('r.isPublic', 1));
                        $qb->andWhere($qb->expr()->in('r.agency', $_SESSION['agency_list']));
                        return $qb;
                    }
                )
            	);
            } else {
            	$builder->add(
                'regulation',
                'entity',
                array(
                    'label' => 'Select Consultation',
                    'class' => 'NoticeCommentBundle:Regulation',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('r');
                        $qb->where($qb->expr()->eq('r.published', true));
                        $qb->andWhere($qb->expr()->eq('r.consultationStage', 1));
                        $qb->andWhere($qb->expr()->eq('r.isPublic', 1));
                        return $qb;
                    }
                )
            	);
            }
            
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'OTB\Bundle\NoticeAndCommentBundle\Entity\Survey'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'otb_survey';
    }
}

<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use OTB\Bundle\NoticeAndCommentBundle\Form\ForwardPlansAttachmentsType;
use Doctrine\ORM\EntityRepository;

class ForwardPlansType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                null,
                array(
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add('description', null, array('label_attr' => array(
                "class" => "label-required"
            )))
            ->add('relatedLinks', 'textarea', array("label" => "Related Links"))
            ->add(
                'problemAddressed',
                null,
                array(
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'publicConsultation',
                'textarea',
                array(
                    "label" => "Public Consultation Opportunities",
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add('impact', 'textarea', array(
                "label" => "Possible impacts on citizens and businesses",
                'label_attr' => array(
                    "class" => "label-required"
                )
            ))
            ->add(
                'attachments',
                "collection",
                array(
                    'type' => new ForwardPlansAttachmentsType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => true,
                    'label' => "Supporting Document"
                )
            )
            ->add(
                'published',
                'checkbox',
                array(
                    'label' => 'Publish',
                    'attr' => array(
                        "class" => 'w3-check',
                    ),
                    'label_attr' => array(
                        "class" => "label-required"
                    )

                )
            )
            ->add('offlineOffice', 'text', array('label' => 'Department Point of Contact'));
        if ($_SESSION['forward_plan_main']) {
            $builder->add(
                'forwardPlanCategory',
                'entity',
                array(
                    'label' => 'Forward Plan',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'class' => 'NoticeCommentBundle:ForwardPlanMainCategory',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('u');
                        // the function returns a QueryBuilder object
                        return $qb
                            ->leftJoin('u.agency', 'a')
                            ->where($qb->expr()->in('a.id', $_SESSION['agencyid']))
                            ->andWhere($qb->expr()->eq('u.id', $_SESSION['forward_plan_main']))
                            ->orderBy('u.name', 'ASC');
                    },
                )
            );
        } else {
            $builder->add(
                'forwardPlanCategory',
                'entity',
                array(
                    'label' => 'Forward Plan',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'label' => 'Forward Plan',
                    'class' => 'NoticeCommentBundle:ForwardPlanMainCategory',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('u');
                        // the function returns a QueryBuilder object
                        return $qb
                            ->leftJoin('u.agency', 'a')
                            ->where($qb->expr()->in('a.id', $_SESSION['agencyid']))
                            ->orderBy('u.name', 'ASC');
                    },
                )
            );
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'forward_plans';
    }
}

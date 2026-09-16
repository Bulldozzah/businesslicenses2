<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ForwardPlanMainCategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                'text',
                array(
                    "label" => "Title",
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'period',
                null,
                array(
                    "label" => "Period From",
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'period2',
                null,
                array(
                    "label" => "Period To",
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add('description', 'textarea', array("label" => "Description", 'label_attr' => array(
                "class" => "label-required"
            )))
            ->add('published');
        if ($_SESSION['agencyid']) {
            $builder->add(
                'agency',
                'entity',
                array(
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'label' => 'Agency',
                    'class' => 'WebmastersAfricaLicenseBundle:BusinessAgency',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('u');
                        // the function returns a QueryBuilder object
                        return $qb
                            // find all agencies in my agency list
                            ->where($qb->expr()->in('u.id', $_SESSION['agencyid']))
                            ->andWhere($qb->expr()->eq('u.deleted', 0))
                            ->orderBy('u.name', 'ASC');
                    },
                )
            );
        } else {
            $builder->add(
                'agency',
                'entity',
                array(
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'label' => 'Agency',
                    'class' => 'WebmastersAfricaLicenseBundle:BusinessAgency',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('u');
                        // the function returns a QueryBuilder object
                        return $qb
                            // find all agencies in my list
                            ->where($qb->expr()->eq('u.deleted', 0))
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
        $resolver->setDefaults(array(
            'data_class' => 'OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlanMainCategory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'forwardplanmaincategory';
    }
}

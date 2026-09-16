<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BusinessIndustryType extends AbstractType
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
                    'label' => 'industries.name',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'label' => 'industries.description',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'businesstypes',
                null,
                array(
                    'attr' => array(
                        'class' => 'chzn-select', 'style' => 'width: 100%'
                    ), 'label' => 'industries.business_types',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            // ->add(
            //     'agencies',
            //     'entity',
            //     array(
            //         'label' => 'Industries',
            //         'class' => 'WebmastersAfricaLicenseBundle:BusinessAgency',
            //         'query_builder' => function (EntityRepository $repository) {
            //             $qb = $repository->createQueryBuilder('u');
            //             // the function returns a QueryBuilder object
            //             return $qb
            //                 // find all locations where 'deleted' is NOT '1'
            //                 ->where($qb->expr()->neq('u.deleted', '1'))
            //                 ->orderBy('u.name', 'ASC');
            //         },
            //         'label_attr' => array(
            //             "class" => "label-required"
            //         )
            //     )
            // )
            ->add('updates', 'submit', array(
                'label' => 'industries.update', 'translation_domain' => 'WebmastersAfricaLicenseBundle', 'attr' => array(
                    'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\BusinessIndustry'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_businessindustry';
    }
}

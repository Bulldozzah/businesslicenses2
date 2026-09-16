<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use WebmastersAfrica\UserBundle\Entity\UserRepository;

class BusinessAgencyType extends AbstractType
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
                'text',
                array(
                    'label' => 'agencies.name',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'acronym',
                'text',
                array(
                    'label' => 'Abbreviation',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'required' => false
                )
            )
            ->add(
                'fax',
                'text',
                array(
                    'label' => 'agencies.fax',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'required' => false
                )
            )
            ->add(
                'address',
                'textarea',
                array(
                    'label' => 'agencies.address',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add(
                'telephone',
                'text',
                array(
                    'label' => 'agencies.telephone',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label' => 'agencies.email',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add(
                'website',
                'url',
                array(
                    'label' => 'agencies.website',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add(
                'hours',
                'text',
                array(
                    'label' => 'Working Hours',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            ->add(
                'postal_address',
                'textarea',
                array(
                    'label' => 'agencies.postal_address',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'latitude',
                'text',
                array(
                    'label' => 'agencies.latitude',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'required' => false
                )
            )
            ->add(
                'longitude',
                'text',
                array(
                    'label' => 'agencies.longitude',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'required' => false
                )
            )
            ->add(
                'file',
                'file',
                array(
                    'label' => 'agencies.map_scan',
                    'required' => false,
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )->add(
                'map_scan',
                'hidden',
                array(
                    'label' => 'agencies.map_scan',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'attr' =>  array("readonly" => true)
                )
            )
            ->add(
                'business_no',
                'text',
                array(
                    'label' => 'agencies.business_no',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'required' => false
                )
            )
            ->add(
                'location',
                'entity',
                array(
                    'label' => 'agencies.location',
                    'class' => 'WebmastersAfricaLicenseBundle:BusinessLocation',
                    'query_builder' => function (EntityRepository $repository) {
                        $qb = $repository->createQueryBuilder('u');
                        // the function returns a QueryBuilder object
                        return $qb
                            // find all locations where 'deleted' is NOT '1'
                            ->where($qb->expr()->neq('u.deleted', '1'))
                            ->orderBy('u.name', 'ASC');
                    },
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required"
                    )
                )
            )
            ->add(
                'users',
                'entity',
                array(
                    'label' => 'agencies.users',
                    'class' => 'WebmastersAfricaUserBundle:User',
                    'query_builder' => function (UserRepository $repository) {
                        return $repository->getAllAdminUsersOnly();
                    },
                    'multiple' => true,
                    'by_reference' => false,
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'attr' => array(
                        'class' => 'chzn-select', 'style' => 'width: 100%'
                    ),
                )
            )
            ->add(
                'agencyoffices',
                'collection',
                array(
                    'type' => new BusinessAgencyOfficeType(),
                    'allow_add' => true,
                    'by_reference' => false,
                    'allow_delete' => true,
                    'label' => 'agencies.agency_offices',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            )
            // ->add(
            //     'industries',
            //     'entity',
            //     array(
            //         'label' => 'Industries',
            //         'class' => 'WebmastersAfricaLicenseBundle:BusinessIndustry',
            //         'query_builder' => function (EntityRepository $repository) {
            //             $qb = $repository->createQueryBuilder('u');
            //             // the function returns a QueryBuilder object
            //             return $qb
            //                 // find all locations where 'deleted' is NOT '1'
            //                 ->where($qb->expr()->neq('u.deleted', '1'))
            //                 ->orderBy('u.name', 'ASC');
            //         },
            //         // 'expanded'  => true,
            //         'multiple'  => true,
            //     )
            // )
            ->add(
                'update',
                'submit',
                array(
                    'label' => 'agencies.update',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\BusinessAgency'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_businessagency';
    }
}

<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BusinessAgencyOfficeType extends AbstractType
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
                    'label' => 'agencies.name',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required_2"
                    )
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
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required_2"
                    ),
                )
            )
            ->add(
                'telephone',
                'text',
                array(
                    'label' => 'agencies.telephone',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required_2"
                    ),
                )
            )
            ->add(
                'email',
                'email',
                array(
                    'label' => 'agencies.email',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required_2"
                    ),
                )
            )
            ->add(
                'website',
                'url',
                array(
                    'label' => 'agencies.website',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'label_attr' => array(
                        "class" => "label-required_2"
                    ),
                )
            )
            ->add(
                'hours',
                'text',
                array(
                    'label' => 'agencies.hours',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    'required' => false
                )
            )
            ->add(
                'postal_address',
                'textarea',
                array(
                    'label' => 'agencies.postal_address',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    "required" => false
                )
            )
            ->add(
                'latitude',
                'text',
                array(
                    'label' => 'agencies.latitude',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    "required" => false
                )
            )
            ->add(
                'longitude',
                'text',
                array(
                    'label' => 'agencies.longitude',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    "required" => false
                )
            )

            ->add(
                'file',
                'file',
                array(
                    'label' => 'agencies.map_scan',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    "required" => false
                )
            )
            ->add(
                'is_main_location',
                'hidden',
                array(
                    'required' => false,
                    'label' => 'agencies.is_main_location',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle',
                    "empty_data" => false,
                )
            )->add(
                'mapScan',
                'hidden',
                array(
                    'label' => 'agencies.map_scan',
                    'translation_domain' => 'WebmastersAfricaLicenseBundle'
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
            'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\BusinessAgencyOffice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'businessagencyoffice';
    }
}

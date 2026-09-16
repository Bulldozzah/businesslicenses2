<?php

namespace WebmastersAfrica\LicenseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class BusinessLicenseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        if ($_SESSION['agency']) {
            $builder
                ->add(
                    'name',
                    'text',
                    array(
                        'label' => 'Name of License',
                        'label_attr' => array(
                            "class" => "label-required"
                        ), 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'stage',
                    null,
                    array('label' => 'Current Stage', 'empty_data' => "1")
                )
                ->add(
                    'keywords',
                    'textarea',
                    array(
                        'label' => 'licenses.keywords', 'label_attr' => array(
                            "class" => "label-required"
                        ), 'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'purpose',
                    'textarea',
                    array(
                        'label' => 'licenses.purpose',
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
                        'label' => 'licenses.description',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle',
                        'label_attr' => array("class" => "label-required")
                    )
                )
                ->add(
                    'comments',
                    'textarea',
                    array(
                        'label' => 'licenses.comments',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'license_no',
                    'text',
                    array(
                        'label' => 'licenses.license_no',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'application_fee',
                    'text',
                    array(
                        'label' => 'licenses.application_fee',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle',
                        'label_attr' => array("class" => "label-required")
                    )
                )
                ->add(
                    'license_fee',
                    'textarea',
                    array(
                        'label' => 'licenses.license_fee',
                        'label_attr' => array(
                            "class" => "label-required"
                        ),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'max_processing_time',
                    'text',
                    array(
                        'label' => 'licenses.max_processing_time',
                        'label_attr' => array("class" => "label-required"),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'gazetted_on',
                    'date',
                    array(
                        'format' => 'dd-MM-yyyy',
                        'widget' => 'single_text',
                        'attr' => array('class' => 'datepicker'),
                        'label' => 'licenses.gazetted_on',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'related_websites',
                    'textarea',
                    array(
                        'label' => 'licenses.related_website',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'validity',
                    'text',
                    array(
                        'label' => 'licenses.validity',
                        'label_attr' => array("class" => "label-required"),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'enactment',
                    'date',
                    array(
                        'format' => 'dd-MM-yyyy',
                        'widget' => 'single_text',
                        'attr' => array(
                            'class' => 'datepicker'
                        ),
                        'label' => 'licenses.enactment',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'gazetting_ref',
                    'text',
                    array(
                        'label' => 'licenses.gazetting_ref',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'contact_office',
                    'textarea',
                    array(
                        'label' => 'licenses.contact_office',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle',
                        'label_attr' => array(
                            "class" => "label-required"
                        ),
                        "required" => true
                    )
                )
                ->add(
                    'resolution_criteria',
                    'textarea',
                    array(
                        'label' => 'licenses.resolution_criteria',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'status',
                    'choice',
                    array(
                        "label" => "Publish License?", "choices" => [2 => "saved as Draft"]
                    )
                )

                ->add(
                    'universal',
                    'checkbox',
                    array(
                        'required' => false,
                        'label' => 'licenses.universal',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'principle_legislation',
                    null,
                    array(
                        'label' => 'Principle Legislation',
                        'label_attr' => array(
                            "class" => "label-required"
                        )
                    )
                )
                ->add(
                    'subsidiary_legislation',
                    "collection",
                    array(
                        'type' => new SubsidiaryLegislationAttachmentsType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'required' => false,
                        'label' => "Subsidiary Legislation",

                    )
                )
                ->add(
                    'file',
                    'file',
                    array(
                        "label" => "Attach Principle Legislation",
                        "required" => false,
                    )
                )
                ->add(
                    'principle_legislation_attachment',
                    'hidden',
                    array(
                        'label' => 'Attach Principle Legislation',
                        "required" => false,
                        'attr' => array("readonly" => true)
                    )
                )
                ->add(
                    'location',
                    'entity',
                    array(
                        'label' => 'licenses.location',
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
                    'agency',
                    'entity',
                    array(
                        'label' => 'licenses.agency',
                        'class' => 'WebmastersAfricaLicenseBundle:BusinessAgency',
                        'query_builder' => function (EntityRepository $repository) {
                            $qb = $repository->createQueryBuilder('u');
                            // the function returns a QueryBuilder object
                            return $qb
                                // find all locations where 'deleted' is NOT '1'
                                ->where($qb->expr()->in('u.id', $_SESSION['agencyid']))
                                ->andWhere($qb->expr()->eq('u.deleted', 0))
                                ->orderBy('u.name', 'ASC');
                        },
                        'translation_domain' => 'WebmastersAfricaLicenseBundle',
                        'label_attr' => array(
                            "class" => "label-required"
                        )
                    )
                )
                ->add(
                    'activities',
                    null,
                    array(
                        'label' => 'licenses.activities',
                        'label_attr' => array("class" => "label-required"),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'requirements',
                    'textarea',
                    array(
                        'label' => 'licenses.license_requirements',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'downloads',
                    'collection',
                    array(
                        'type' => new LicenseDownloadType(),
                        'allow_add'    => true,
                        'by_reference' => false,
                        'allow_delete' => true,
                        'label' => 'licenses.license_downloads',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'subsidiary_legislation',
                    "collection",
                    array(
                        'type' => new SubsidiaryLegislationAttachmentsType(),
                        'prototype' => true,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'required' => false,
                        'label' => "Subsidiary Legislation",
                    )
                )
                ->add(
                    'save',
                    'submit',
                    array(
                        'label' => 'Submit',
                        'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )
                    )
                );
        } else {
            $builder
                ->add(
                    'name',
                    'text',
                    array(
                        'label' => 'Name of License',
                        'label_attr' => array("class" => "label-required"),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'stage',
                    null,
                    array('label' => 'Current Stage', 'empty_data' => 1)
                )
                ->add(
                    'keywords',
                    'textarea',
                    array(
                        'label' => 'licenses.keywords',
                        'label_attr' => array("class" => "label-required"),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'purpose',
                    'textarea',
                    array(
                        'label' => 'licenses.purpose',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle',
                        'label_attr' => array("class" => "label-required")
                    )
                )
                ->add(
                    'description',
                    'textarea',
                    array(
                        'label' => 'licenses.description',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle',
                        'label_attr' => array("class" => "label-required")
                    )
                )
                ->add(
                    'comments',
                    'textarea',
                    array(
                        'label' => 'licenses.comments',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'license_no',
                    'text',
                    array(
                        'label' => 'licenses.license_no',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'application_fee',
                    'textarea',
                    array(
                        'label' => 'licenses.application_fee',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'license_fee',
                    'textarea',
                    array(
                        'label' => 'licenses.license_fee',
                        'label_attr' => array("class" => "label-required"),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )->add(
                    'max_processing_time',
                    'text',
                    array(
                        'label' => 'licenses.max_processing_time',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'gazetted_on',
                    'date',
                    array(
                        'label' => 'licenses.gazetted_on',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'related_websites',
                    'textarea',
                    array(
                        'label' => 'licenses.related_website',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'validity',
                    'text',
                    array(
                        'label' => 'licenses.validity',
                        'label_attr' => array("class" => "label-required"),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'enactment',
                    'date',
                    array(
                        'label' => 'licenses.enactment',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'gazetting_ref',
                    'text',
                    array(
                        'label' => 'licenses.gazetting_ref',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'contact_office',
                    'textarea',
                    array(
                        'label' => 'licenses.contact_office',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle',
                        'label_attr' => array(
                            "class" => "label-required"
                        ),
                        "required" => true
                    )
                )
                ->add(
                    'resolution_criteria',
                    'textarea',
                    array(
                        'label' => 'licenses.resolution_criteria',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'status',
                    'choice',
                    array("label" => "Publish License?", "choices" => [2 => "saved Draft"])
                )
                ->add(
                    'universal',
                    'checkbox',
                    array('required' => false, 'label' =>
                    'licenses.universal', 'translation_domain' => 'WebmastersAfricaLicenseBundle')
                )
                ->add(
                    'principle_legislation',
                    null,
                    array(
                        'label' => 'Principle Legislation',
                        'label_attr' => array(
                            "class" => "label-required"
                        )
                    )
                )
                ->add(
                    'subsidiary_legislation',
                    'text',
                    array('label' => 'Subsidiary Legislation ')
                )
                ->add(
                    'file',
                    'file',
                    array(
                        "label" => "Attach Principle Legislation",
                        "required" => false,
                    )
                )
                ->add(
                    'principle_legislation_attachment',
                    'hidden',
                    array(
                        'label' => 'Attach Principle Legislation',
                        "required" => false,
                        'attr' => array("readonly" => true)
                    )
                )
                ->add(
                    'agency',
                    null,
                    array(
                        'label' => 'licenses.agency',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle',
                        'label_attr' => array(
                            "class" => "label-required"
                        )
                    )
                )
                ->add(
                    'location',
                    'entity',
                    array(
                        'label' => 'licenses.location',
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

                    )
                )
                ->add(
                    'agency',
                    null,
                    array(
                        'label' => 'licenses.agency',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'activities',
                    null,
                    array(
                        'label' => 'licenses.activities',
                        'label_attr' => array(
                            "class" => "label-required"
                        ),
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'requirements',
                    'textarea',
                    array(
                        'label' => 'licenses.license_requirements',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'downloads',
                    'collection',
                    array(
                        'type' => new LicenseDownloadType(),
                        'allow_add'    => true,
                        'by_reference' => false,
                        'allow_delete' => true,
                        'label' => 'licenses.license_downloads',
                        'translation_domain' => 'WebmastersAfricaLicenseBundle'
                    )
                )
                ->add(
                    'subsidiary_legislation',
                    "collection",
                    array(
                        'type' => new SubsidiaryLegislationAttachmentsType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'required' => false,
                        'label' => "Subsidiary Legislation",
                    )
                )
                ->add(
                    'save',
                    'submit',
                    array(
                        'label' => 'Save',
                        'attr' => array(
                            'class' => 'w3-right w3-center w3-button w3-blue w3-round-medium',
                            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                        )
                    )
                );
        }
    }

    /**
     * Set Default Options
     *
     * @param OptionsResolverInterface $resolver // Resolver
     *
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'WebmastersAfrica\LicenseBundle\Entity\BusinessLicense'
            )
        );
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return 'webmastersafrica_licensebundle_businesslicense';
    }
}

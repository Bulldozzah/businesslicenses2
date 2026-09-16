<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use OTB\Bundle\NoticeAndCommentBundle\Form\RegulationAttachmentsType;
use Doctrine\ORM\EntityRepository;

class RegulationType extends AbstractType
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
                    'label' => 'Consultation Title',
                    'label_attr' => array(
                            "class" => "label-required"
                    ),
                    'required' => true
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'Summary',
                    'label_attr' => array(
                            "class" => "label-required"
                    ),
                    'required' => true
                )
            )
            ->add(
                'expectedOutcome',
                null,
                array(
                    'label' => 'Other Affected Sectors & Expected Outcome'
                )
            )
            ->add('file', 'file', array("label" => "Main Document"))
            ->add('document', 'hidden', array("label" => "Main Document", "attr" => array("readonly"=>true)))
            ->add(
                'publishDate',
                'text',
                array(
                    'label' => 'Publish Date',
                    'required' => true
                )
            )
            ->add(
                'published',
                'checkbox',
                array(
                    'label' => 'Publish',
                    'attr' => array(
                        "class" => 'w3-check'
                    ),
                    'required' => true
                )
            )
            ->add(
                'isLoginRequired',
                'checkbox',
                array(
                    'label' => 'Is Login Required To make Comments',
                    'attr' => array(
                        "class" => 'w3-check'
                    ),
                    'required' => true
                )
            )
            ->add(
                'isPublic',
                'checkbox',
                array(
                    'label' => 'Public Consultation',
                    'attr' => array(
                        "class" => 'w3-check'
                    ),
                    'required' => true
                )
            )
            ->add(
                'closingDate',
                'text',
                array(
                    'label' => 'Consultation Closing Date',
                    'label_attr' => array(
                            "class" => "label-required"
                    ),
                    'required' => true
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
                    ),
                    'required' => true
                )
            )
            ->add(
                'industry',
                null,
                array(
                    'label' => 'Main Affected Sector',
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'required' => true
                )
            )
            ->add(
                'tags',
                null,
                array(
                    'label_attr' => array(
                        "class" => "label-required"
                    ),
                    'required' => true
                )
            )
            ->add(
                'keywords',
                null,
                array(
                    'label_attr' => array(
                            "class" => "label-required"
                    ),
                    'required' => true
                )
            )
            ->add(
                'specificInstructions',
                null,
                array('label_attr' => array(
                            "class" => "label-required"
                    ),
                'required' => true
                )
            )
            ->add(
                'supportingMaterials',
                null,
                array(
                    "label" => "Supporting Materials (online References/Links)",
                    'required' => true
                )
            )
            ->add(
                'offlineConsultations',
                null,
                array(
                    "label" => "Offline Consultations",
                    'required' => true
                )
            )
            ->add(
                'stage',
                'choice',
                array(
                    'label' => 'Consultation Status',
                    'choices'  => array(
                        1 => 'open',
                        2 => 'close'
                    ),
                    'label_attr' => array(
                            "class" => "label-required"
                    ),
                    'required' => true
                )
            )
            ->add(
                'supportingAttachments',
                "collection",
                array(
                    'type' => new RegulationAttachmentsType(),
                    'prototype' => true,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'required' => false,
                    'label' => "Supporting Materials"
                )
            )
            ->add(
                'isReviewComments',
                null,
                array(
                    'label' => "Review Comments Before Publishing?"
                )
            )
            ->add(
                'isAttachmentEnabled',
                null,
                array(
                    'label' => 'Enable Attachment For logged in user?',
                )
            )
            ->add(
                'regulationType',
                'choice',
                array(
                    'label' => 'Consultation Type',
                    'choices' => array(
                        1 => "Law",
                        2 => "By Law",
                        3 => "Instructions",
                        4 => "Decision",
                        5 => "Codes and Standards",
                        6 => "Forward Planning",
                        7 => "RIA",
                        8 => "Policy",
                        9 => "Other"
                    ),
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
            )
            ->add(
                'fileType',
                'text',
                array(
                    'label' => 'File Type',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
                )
            )
            ->add(
                'fileSize',
                'number',
                array(
                    'label' => 'File Size Limit',
                    'label_attr' => array(
                            "class" => "label-required"
                    )
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
                'data_class' => 'OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'noticeandcommentbundle_regulation';
    }
}

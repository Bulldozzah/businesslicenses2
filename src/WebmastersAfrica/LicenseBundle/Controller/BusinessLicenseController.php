<?php

namespace WebmastersAfrica\LicenseBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use WebmastersAfrica\LicenseBundle\Entity\BusinessLicense;
use WebmastersAfrica\LicenseBundle\Entity\LicenseFieldData;
use WebmastersAfrica\LicenseBundle\Form\BusinessLicenseType;
use Doctrine\Common\Collections\ArrayCollection;

use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;
use WebmastersAfrica\TaskBundle\Entity\Task;
use WebmastersAfrica\LicenseBundle\Entity\Workflow;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use WebmastersAfrica\LicenseBundle\Entity\ApplicationHistory;
use WebmastersAfrica\UserBundle\Entity\User;

/**
 * BusinessLicense controller.
 *
 */
class BusinessLicenseController extends Controller
{

    /**
     * Lists all BusinessLicense entities.
     *
     */
    public function indexAction(Request $request, $id = "")
    {
        if ($request->query->get('id')) {
            $id = $request->query->get('id');
        }
        $my_groups = false;
        $accessGranted = false;
        //If a user has logged in  and is part of agency, prepare an agency filter
        $em = $this->getDoctrine()->getManager();
        $my_agency = [];
        $stage_type = "";
        $user = $em->getRepository(
            'WebmastersAfricaUserBundle:User'
        )->find($this->get('security.context')->getToken()->getUser()->getId());
        $workflowRepository = $em->getRepository(
            Workflow::class
        );

        $agencies = $user->getAgencies();

        foreach ($agencies as $agency) {
            $my_agency[] = $agency->getId();
        }
        if ($my_agency) {
            // check user first stage
            // build user stage id
            $usersWorkflowStage = $workflowRepository->getStagesForThisUser($user->getId());
            if ($usersWorkflowStage) {
                foreach ($usersWorkflowStage as $key => $value) {
                    $my_stages[] = $value['id'];
                }
                if ($id == "") {
                    return $this->redirect($this->generateUrl('managelicenses_all'));
                } elseif ($id && in_array($id, $my_stages)) {
                    $current = $workflowRepository->find($id);
                    $title = $current->getTitle();
                    $stage_type = $current->getType();
                    if (!$title) {
                        throw $this->createNotFoundException("Workflow Stage Not Found");
                    }
                } else {
                    return $this->redirect($this->generateUrl('managelicenses'));
                }
                $accessGranted = $this->checkPermissionForThisWorkflowStage($workflowRepository->find($id));
            }
            $license_count = count(
                $em->getRepository(
                    BusinessLicense::class
                )->getBusinessLicensesInMyAgencies($my_agency)
            );
        }

        if ($my_agency) {
            $license = $em->getRepository(
                BusinessLicense::class
            )->getBusinessLicenseInMyAgencyAll($id, $my_agency);
            $_SESSION['my_agency'] = $my_agency;
        } else {
            $license = $em->getRepository(
                BusinessLicense::class
            )->findBy(['status' => 1]);
            $license_count = count($license);
        }
        $fixedfields = $em->getRepository(
            'WebmastersAfricaLicenseBundle:LicenseFixedField'
        )->findFixedFields(true);


        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }
        $workflow = $em->getRepository(
            Workflow::class
        );
        if ($my_agency) {
            // $my_workflow = $workflow->getLicensesUnderMyAgency($my_agency);
            $my_workflow = $workflow->getStagesInArray($my_stages);
        } else {
            $my_workflow = $workflow->findBy(['type' => 'publish']);
        }
        if ($my_groups) {
            $groups = $my_groups[0];
        } else {
            $groups = false;
        }

        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessLicense:index.html.twig',
            array(
                'showedfields' => $showedfields,
                'licenses' => $license,
                'workflow' => $my_workflow,
                'workflow_title' => $title,
                'workflow_id' => $id,
                'accessGranted' => $accessGranted,
                'stage_type' => $stage_type,
                'license_count' => $license_count
            )
        );
    }
    public function showUnpublishedLicensesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $my_agency = [];
        $user = $em->getRepository(
            'WebmastersAfricaUserBundle:User'
        )->find($this->get('security.context')->getToken()->getUser()->getId());

        $agencies = $user->getAgencies();

        $workflowRepository = $em->getRepository(
            Workflow::class
        );
        if ($agencies) {
            foreach ($agencies as $agency) {
                $my_agency[] = $agency->getId();
            }
        }
        if ($my_agency) {
            $license = $em->getRepository(
                BusinessLicense::class
            )->getUnpublishedBusinessLicensesInMyAgencies($my_agency);

            $license_count = $em->getRepository(
                BusinessLicense::class
            )->getBusinessLicensesInMyAgencies($my_agency);
            $usersWorkflowStage = $workflowRepository->getStagesForThisUser($user->getId());
            foreach ($usersWorkflowStage as $key => $value) {
                $my_stages[] = $value['id'];
            }
        } else {
            $license = $em->getRepository(BusinessLicense::class)->findBy(['status' => 4]);
        }
        if ($my_agency) {
            $my_workflow = $workflowRepository->getStagesInArray($my_stages);
        } else {
            $my_workflow = $workflowRepository->findBy(['type' => 'publish']);
        }

        $fixedfields = $em->getRepository(
            'WebmastersAfricaLicenseBundle:LicenseFixedField'
        )->findFixedFields(true);


        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }

        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessLicense:index_unpublished.html.twig',
            array(
                'showedfields' => $showedfields,
                'licenses' => $license,
                'workflow' => $my_workflow,
                'workflow_title' => "Unpublished Licenses",
                'accessGranted' => false,
                'stage_type' => "all",
                'workflow_id' => false,
                'license_count' => count($license_count)
            )
        );
    }

    public function showAllLicensesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $my_agency = [];
        $user = $em->getRepository(
            'WebmastersAfricaUserBundle:User'
        )->find($this->get('security.context')->getToken()->getUser()->getId());

        $agencies = $user->getAgencies();

        $workflowRepository = $em->getRepository(
            Workflow::class
        );
        if ($agencies) {
            foreach ($agencies as $agency) {
                $my_agency[] = $agency->getId();
            }
        }
        $_SESSION['my_agency'] = $my_agency;
        if ($my_agency) {
            $license = $em->getRepository(BusinessLicense::class)->getBusinessLicensesInMyAgencies($my_agency);
            $usersWorkflowStage = $workflowRepository->getStagesForThisUser($user->getId());
            foreach ($usersWorkflowStage as $key => $value) {
                $my_stages[] = $value['id'];
            }
        } else {
            $license = $em->getRepository(BusinessLicense::class)->findBy(['status' => 1]);
        }
        if ($my_agency) {
            // $my_workflow = $workflow->getLicensesUnderMyAgency($my_agency);
            $my_workflow = $workflowRepository->getStagesInArray($my_stages);
        } else {
            $my_workflow = $workflowRepository->findBy(['type' => 'publish']);
        }

        $fixedfields = $em->getRepository(
            'WebmastersAfricaLicenseBundle:LicenseFixedField'
        )->findFixedFields(true);


        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }

        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessLicense:indexAll.html.twig',
            array(
                'showedfields' => $showedfields,
                'licenses' => $license,
                'workflow' => $my_workflow,
                'workflow_title' => "My Licenses",
                'accessGranted' => false,
                'stage_type' => "all",
                'workflow_id' => true
            )
        );
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");

        foreach ($batch_items as $batch_item) {
            if ($batch_select == "publish") {
                $entity = $em->getRepository(
                    'WebmastersAfricaLicenseBundle:BusinessLicense'
                )->find($batch_item);
                $entity->setStatus(1);
                $workflow = $em->getRepository(Workflow::class)->findOneBy(['type' => 'publish']);
                $entity->setStage($workflow);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add('update', 'update');
            } elseif ($batch_select == "unpublish") {
                $entity = $em->getRepository(
                    'WebmastersAfricaLicenseBundle:BusinessLicense'
                )->find($batch_item);
                $entity->setStatus(4);
                // get the workflow with unpublished applications
                $workflow = $em->getRepository(Workflow::class)->findOneBy(['type' => 'unpublish']);
                $entity->setStage($workflow);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add('update', 'update');
            } elseif ($batch_select == "save_draft") {
                $entity = $em->getRepository(
                    'WebmastersAfricaLicenseBundle:BusinessLicense'
                )->find($batch_item);
                $entity->setStatus(2);
                $em->persist($entity);
                $this->get('session')->getFlashBag()->add('update', 'update');
            } elseif ($batch_select == "delete") {
                $entity = $em->getRepository(
                    'WebmastersAfricaLicenseBundle:BusinessLicense'
                )->find($batch_item);
                if ($entity) {
                    $stage = $entity->getStage()->getId();
                    $entity->setDeleted(true);
                    $em->flush();

                    $this->get('session')->getFlashBag()->add('delete', 'delete');
                }
            }
        }
        $em->flush();
        if ($entity) {
            return $this->redirect($this->generateUrl('managelicenses', ['id' => $entity->getStage()->getId()]));
        } else {
            return $this->redirect($this->generateUrl('managelicenses', ['id' => $stage]));
        }
    }

    /**
     * Creates a new BusinessLicense entity.
     *
     */
    public function createAction(Request $request)
    {
        $slug = $this->get('cocur_slugify');
        $published = false;
        $form_errors = false;
        $saved = true;

        $showedfields = [];
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }

        $query_2 = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                ORDER BY p.fieldlabel DESC'
        );
        $fixedfields_new = $query_2->getResult();

        foreach ($fixedfields_new as $field) {
            $buildFormFields[$field->getId()]  = ['required' => $field->getRequired(), 'showed' => $field->getShowed(), 'label' => $field->getFieldlabel()];
        }

        $entity = new BusinessLicense();
        $form   = $this->createCreateForm($entity, $buildFormFields);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $stage = $this->getLicenseFirstStage();
            if ($request->get('save_draft') != "save_draft") {
                $published = true;
                $entity->setStage($stage->getNextStage());
                $entity->setStatus(3);
                $this->get('session')->getFlashBag()->add(
                    'license_created',
                    "License Created Successfully and Submitted to " . $stage->getNextStage()->getTitle()
                );
            } else {
                $entity->setStage($stage);
                $entity->setStatus(2);
                $this->get('session')->getFlashBag()->add(
                    'license_created',
                    "License saved as a draft"
                );
                $saved = false;
                $published = false;
            }
            $entity->setSlug(
                $slug->slugify(
                    $request->get('webmastersafrica_licensebundle_businesslicense')['name']
                )
            );
            if ($request->files->get('webmastersafrica_licensebundle_businesslicense')['file']) {
                $fileName = $this->upload(
                    $request->files->get(
                        'webmastersafrica_licensebundle_businesslicense'
                    )['file']
                );

                $entity->upload($fileName);
            }
            // check if the stage is set correctly
            $entity->setCreatedBy($this->get('security.context')->getToken()->getUser());
            $em->persist($entity);
            $em->flush();
            $stage_is_set = $this->isApplicationAssessed($entity);
            if ($published && !$stage_is_set) {
                $entity->setStage($stage->getNextStage());
            } else {
                $entity->setStage($stage);
            }
            if (!$published) {
                $this->createHistoryForNewApplication($stage, $entity, true);
            } else {
                $this->createHistoryForNewApplication($stage, $entity);
            }

            if ($stage->getSendEmails() && $saved) {
                $this->sendNotificationsToUsersInThisStage($stage);
            }
            if ($published && $stage->getNextStage()->getAssignee() && $saved) {
                $this->createTask($stage, $entity);
            }
            $query = $em->createQuery(
                'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                ORDER BY p.order ASC'
            );
            $customfields = $query->getResult();

            //custom license fields
            $fielddatas = $request->request->get("custom");

            foreach ($customfields as $customfield) {
                $em = $this->getDoctrine()->getManager();
                $newfielddata = new LicenseFieldData();
                $newfielddata->setField($customfield);
                $newfielddata->setLicenseId($entity->getId());
                $newfielddata->setLicense($entity);
                $newfielddata->setFielddata($fielddatas[$customfield->getId()]);
                $em->persist($newfielddata);
                $em->flush();
            }
            if ($stage && $saved) {
                $accessGranted = $this->checkPermissionForThisWorkflowStage($stage->getNextStage());
                if ($accessGranted) {
                    return $this->redirect($this->generateUrl('managelicenses', ['id' => $stage->getNextStage()->getId()]));
                } else {
                    return $this->redirect($this->generateUrl('managelicenses', ['id' => $stage->getId()]));
                }
            } else {
                return $this->redirect($this->generateUrl('managelicenses', ['id' => $stage->getId()]));
            }
            return $this->redirect($this->generateUrl('managelicenses'));
        } else {
            $form_errors = true;
        }

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                ORDER BY p.order ASC'
        );
        $customfields = $query->getResult();

        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessLicense:new.html.twig',
            array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'customfields' => $customfields,
                'agency' => $_SESSION['agency'],
                'showedfields' => $showedfields,
                'form_errors' => $form_errors
            )
        );
    }
    public function createHistoryForNewApplication(Workflow $stage, BusinessLicense $license, $draft = null)
    {
        $em = $this->getDoctrine()->getManager();
        $history = new ApplicationHistory();
        $history->setUser($this->get('security.context')->getToken()->getUser());
        if ($draft) {
            $history->setCurrentStage($stage);
        } else {
            $history->setCurrentStage($stage->getNextStage());
        }
        $history->setPreviousStage($stage);
        $message = "License Details Submitted";
        $history->setActionType($message);
        $history->setUser($this->get('security.context')->getToken()->getUser());
        $history->setApplication($license);

        $em->persist($history);
        $em->flush();
    }

    public function upload(UploadedFile $file)
    {
        $fileName = $file->getClientOriginalName();

        $pos = strrpos($fileName, ".");
        $ext = substr($fileName, $pos);
        $fp = substr($fileName, 0, $pos);
        $dir = rtrim($this->container->getParameter('documents_directory'), '/\\') . DIRECTORY_SEPARATOR;

        while (file_exists($dir . $fileName)) {
            $fileName = $fp . "_" . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYX'), 0, 10) . $ext;
        }

        $file->move(
            $this->container->getParameter('documents_directory'),
            $fileName
        );
        return $fileName;
    }

    /**
     * Creates a form to create a BusinessLicense entity.
     *
     * @param BusinessLicense $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(BusinessLicense $entity, $buildFormFields = [])
    {
        $form = $this->createForm(
            new BusinessLicenseType(),
            $entity,
            array(
                'action' => $this->generateUrl('managelicenses_create'),
                'method' => 'POST',
            )
        );
        if ($buildFormFields) {
            if ($buildFormFields[3]['showed']) {
                if ($buildFormFields[3]['required']) {
                    $form->add(
                        'purpose',
                        'textarea',
                        array(
                            'label' => $buildFormFields[3]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'purpose',
                        'textarea',
                        array(
                            'label' => $buildFormFields[3]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'purpose',
                    'hidden',
                    array(
                        'label' => $buildFormFields[3]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildFormFields[4]['showed']) {
                if ($buildFormFields[4]['required']) {
                    $form->add(
                        'description',
                        'textarea',
                        array(
                            'label' => $buildFormFields[4]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'description',
                        'textarea',
                        array(
                            'label' => $buildFormFields[4]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'description',
                    'hidden',
                    array(
                        'label' => $buildFormFields[4]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildFormFields[6]['showed']) {
                if ($buildFormFields[6]['required']) {
                    $form->add(
                        'license_no',
                        'text',
                        array(
                            'label' => $buildFormFields[6]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'license_no',
                        'text',
                        array(
                            'label' => $buildFormFields[6]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'license_no',
                    'hidden',
                    array(
                        'label' => $buildFormFields[6]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[7]['showed']) {
                if ($buildFormFields[7]['required']) {
                    $form->add(
                        'application_fee',
                        'text',
                        array(
                            'label' => $buildFormFields[7]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'application_fee',
                        'text',
                        array(
                            'label' => $buildFormFields[7]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'application_fee',
                    'hidden',
                    array(
                        'label' => $buildFormFields[7]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[8]['showed']) {
                if ($buildFormFields[8]['required']) {
                    $form->add(
                        'license_fee',
                        'textarea',
                        array(
                            'label' => $buildFormFields[8]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'license_fee',
                        'textarea',
                        array(
                            'label' => $buildFormFields[8]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'license_fee',
                    'hidden',
                    array(
                        'label' => $buildFormFields[8]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[9]['showed']) {
                if ($buildFormFields[9]['required']) {
                    $form->add(
                        'max_processing_time',
                        'textarea',
                        array(
                            'label' => $buildFormFields[9]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'max_processing_time',
                        'textarea',
                        array(
                            'label' => $buildFormFields[9]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'max_processing_time',
                    'hidden',
                    array(
                        'label' => $buildFormFields[9]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[10]['showed']) {
                if ($buildFormFields[10]['required']) {
                    $form->add(
                        'gazetted_on',
                        'text',
                        array(
                            'label' => $buildFormFields[10]['label'],
                            'required' => false,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'gazetted_on',
                        'text',
                        array(
                            'label' => $buildFormFields[10]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'gazetted_on',
                    'hidden',
                    array(
                        'label' => $buildFormFields[10]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[11]['showed']) {
                if ($buildFormFields[11]['required']) {
                    $form->add(
                        'related_websites',
                        'textarea',
                        array(
                            'label' => $buildFormFields[11]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'related_websites',
                        'textarea',
                        array(
                            'label' => $buildFormFields[11]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'related_websites',
                    'hidden',
                    array(
                        'label' => $buildFormFields[11]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[12]['showed']) {
                if ($buildFormFields[12]['required']) {
                    $form->add(
                        'validity',
                        'text',
                        array(
                            'label' => $buildFormFields[12]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'validity',
                        'textarea',
                        array(
                            'label' => $buildFormFields[12]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'validity',
                    'hidden',
                    array(
                        'label' => $buildFormFields[12]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[13]['showed']) {
                if ($buildFormFields[13]['required']) {
                    $form->add(
                        'enactment',
                        'text',
                        array(
                            'label' => $buildFormFields[13]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'enactment',
                        'text',
                        array(
                            'label' => $buildFormFields[13]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'enactment',
                    'hidden',
                    array(
                        'label' => $buildFormFields[13]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[14]['showed']) {
                if ($buildFormFields[14]['required']) {
                    $form->add(
                        'gazetting_ref',
                        'textarea',
                        array(
                            'label' => $buildFormFields[14]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'gazetting_ref',
                        'textarea',
                        array(
                            'label' => $buildFormFields[14]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'gazetting_ref',
                    'hidden',
                    array(
                        'label' => $buildFormFields[14]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[15]['showed']) {
                if ($buildFormFields[15]['required']) {
                    $form->add(
                        'contact_office',
                        'textarea',
                        array(
                            'label' => $buildFormFields[15]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'contact_office',
                        'textarea',
                        array(
                            'label' => $buildFormFields[15]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'contact_office',
                    'hidden',
                    array(
                        'label' => $buildFormFields[15]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[16]['showed']) {
                if ($buildFormFields[16]['required']) {
                    $form->add(
                        'resolution_criteria',
                        'text',
                        array(
                            'label' => $buildFormFields[16]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'resolution_criteria',
                        'text',
                        array(
                            'label' => $buildFormFields[16]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'resolution_criteria',
                    'hidden',
                    array(
                        'label' => $buildFormFields[16]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[18]['showed']) {
                if ($buildFormFields[18]['required']) {
                    $form->add(
                        'universal',
                        'text',
                        array(
                            'label' => $buildFormFields[18]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'universal',
                        'text',
                        array(
                            'label' => $buildFormFields[18]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'universal',
                    'hidden',
                    array(
                        'label' => $buildFormFields[18]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[22]['showed']) {
                if ($buildFormFields[22]['required']) {
                    $form->add(
                        'requirements',
                        'textarea',
                        array(
                            'label' => $buildFormFields[22]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'requirements',
                        'textarea',
                        array(
                            'label' => $buildFormFields[22]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'requirements',
                    'hidden',
                    array(
                        'label' => $buildFormFields[22]['label'],
                        'required' => false
                    )
                );
            }
        }
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new BusinessLicense entity.
     *
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new BusinessLicense();
        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
            $_SESSION['agency'] = true;
            $user = $this->getUser();
            $agencies = $user->getAgencies();
            foreach ($agencies as $agency) {
                $agency_list[] = $agency->getId();
            }
            $_SESSION['agencyid'] = $agency_list;
        } else {
            $_SESSION['agency'] = false;
        }
        if (empty($agency_list)) {
            $this->get('session')->getFlashBag()->add(
                'access',
                "Access Denied! You haven't been assigned any agency."
            );
            return $this->redirect($this->generateUrl('managelicenses'));
        }
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                WHERE p.showed = :showed
                ORDER BY p.order ASC'
        )->setParameter('showed', true);

        $customfields = $query->getResult();

        $showedfields = [];
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();
        $buildFormFields = [];
        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }
        $query_2 = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                ORDER BY p.fieldlabel DESC'
        );
        $fixedfields_new = $query_2->getResult();

        foreach ($fixedfields_new as $field) {
            $buildFormFields[$field->getId()]  = ['required' => $field->getRequired(), 'showed' => $field->getShowed(), 'label' => $field->getFieldlabel()];
        }

        $form   = $this->createCreateForm($entity, $buildFormFields);
        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessLicense:new.html.twig',
            array(
                'entity' => $entity,
                'form'   => $form->createView(),
                'customfields' => $customfields,
                'agency' => $_SESSION['agency'],
                'showedfields' => $showedfields,
                'form_errors' => false
            )
        );
    }

    /**
     * How licenses in my stage
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $show_assessment_buttons = false;

        $_SESSION['agency'] = true;
        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $agency_list[] = $agency->getId();
        }
        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessLicense'
        )->getLicenseOnlyInMyAgencyList($id, $agency_list);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLicense entity.');
        }
        $entity = $entity[0];

        // show assessment buttons on permission
        $workflow = $em->getRepository(Workflow::class)->find($entity->getStage());
        $accessGranted = $this->checkPermissionForThisWorkflowStage($entity->getStage());

        if ($accessGranted && $entity->getStatus() == 3 || $accessGranted && $entity->getStatus() == 1 ||  $accessGranted && $entity->getStatus() == 4 || $accessGranted && $entity->getStatus() == 5) {
            $show_assessment_buttons  = true;
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                WHERE p.showed = :showed
                ORDER BY p.order ASC'
        )->setParameter("showed", true);

        $customfields = $query->getResult();

        $showedfields = [];

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }
        $deleteForm = $this->createDeleteForm($id);
        $workflow = $em->getRepository(Workflow::class)->find(2);
        $workflows = $em->getRepository(Workflow::class)->findAll();

        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessLicense:show_license_details.html.twig',
            array(
                'entity'      => $entity,
                'delete_form' => $deleteForm->createView(),
                'customfields' => $customfields,
                'showedfields' => $showedfields,
                'show_assessment_buttons' => $show_assessment_buttons,
                'workflows' => $workflows
            )
        );
    }

    /**
     * Displays a form to edit an existing BusinessLicense entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("LICENSE_AGENCY")')))) {
            $_SESSION['agency'] = true;
            $agencies = $this->getUser()->getAgencies();
            foreach ($agencies as $agency) {
                $agency_list[] = $agency->getId();
            }
            $_SESSION['agencyid'] = $agency_list;
        } else {
            $_SESSION['agency'] = false;
        }

        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessLicense'
        )->getLicenseOnlyInMyAgencyList($id, $agency_list);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLicense entity.');
        }
        $entity = $entity[0];
        if ($entity->getStage()->getType() === 'unpublish' || $entity->getStage()->getType() === 'assess') {
            $this->get('session')->getFlashBag()->add(
                'access',
                'Unable to edit the license'
            );

            return $this->redirect($this->generateUrl('managelicenses_show', ['id' => $entity->getId()]));
        }


        // check if user has permissions to edit from this stage
        $accessGranted = $this->checkPermissionsForThisLicense($entity);
        if (!$accessGranted) {
            $this->get('session')->getFlashBag()->add(
                'access',
                'Access Denied'
            );
            return $this->redirect($this->generateUrl('managelicenses_show', ['id' => $entity->getId()]));
        }
        // BBRRA Can Edit Any Any Stage of the Application

        if ($this->container->get('security.context')->isGranted(array(new Expression('hasRole("BRRA_AGENCY_EDIT")')))) {
            $bbraCanEdit = true;
        }
        if ($entity->getStatus() == 5 || $entity->getStatus() == 2) {
            $accessGranted = true;
        } else {
            if (!$bbraCanEdit) {
                $this->get('session')->getFlashBag()->add(
                    'access',
                    'Access Denied'
                );
                return $this->redirect($this->generateUrl('managelicenses_show', ['id' => $entity->getId()]));
            }
        }

        $query_2 = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                ORDER BY p.fieldlabel DESC'
        );
        $fixedfields_new = $query_2->getResult();

        foreach ($fixedfields_new as $field) {
            $buildFormFields[$field->getId()]  = ['required' => $field->getRequired(), 'showed' => $field->getShowed(), 'label' => $field->getFieldlabel()];
        }


        $editForm = $this->createEditForm($entity, $buildFormFields);
        $deleteForm = $this->createDeleteForm($id);

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                WHERE p.showed = :showed
                ORDER BY p.order ASC'
        )->setParameter("showed", true);
        $customfields = $query->getResult();

        $showedfields = [];

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }


        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessLicense:edit.html.twig',
            array(
                'entity'      => $entity,
                'customfields' => $customfields,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'agency' => $_SESSION['agency'],
                'showedfields' => $showedfields,
                'form_errors' => false
            )
        );
    }

    protected function checkPermissionsForThisLicense(BusinessLicense $license)
    {
        $accessGranted = false;
        $stage_permissions = $license->getStage()->getAccessGroups()->getValues();
        $logged_in_user_permissions = $this->getUser()->getGroups()->getValues();
        foreach ($logged_in_user_permissions as $key) {
            foreach ($stage_permissions as $value) {
                if ($key->getId() == $value->getId() && $key->getName() == $value->getName()) {
                    $accessGranted = true;
                }
            }
        }
        return $accessGranted;
    }

    protected function checkPermissionForThisWorkflowStage(Workflow $stage)
    {
        $em = $this->getDoctrine()->getManager();
        $accessGranted = false;

        $permissions = $em->getRepository(Workflow::class)->findOneBy(["id" => $stage->getId()]);
        $stage_permissions = $permissions->getAccessGroups()->getValues();
        $logged_in_user_permissions = $this->get('security.context')->getToken()->getUser()->getGroups()->getValues();

        foreach ($logged_in_user_permissions as $key) {
            foreach ($stage_permissions as $value) {
                if ($key->getId() == $value->getId() && $key->getName() == $value->getName()) {
                    $accessGranted = true;
                }
            }
        }
        return $accessGranted;
    }

    /**
     * Creates a form to edit a BusinessLicense entity.
     *
     * @param BusinessLicense $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(BusinessLicense $entity, $buildFormFields = [])
    {
        $form = $this->createForm(
            new BusinessLicenseType(),
            $entity,
            array(
                'action' => $this->generateUrl('managelicenses_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        if ($buildFormFields) {
            if ($buildFormFields[3]['showed']) {
                if ($buildFormFields[3]['required']) {
                    $form->add(
                        'purpose',
                        'textarea',
                        array(
                            'label' => $buildFormFields[3]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'purpose',
                        'textarea',
                        array(
                            'label' => $buildFormFields[3]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'purpose',
                    'hidden',
                    array(
                        'label' => $buildFormFields[3]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[4]['showed']) {
                if ($buildFormFields[4]['required']) {
                    $form->add(
                        'description',
                        'textarea',
                        array(
                            'label' => $buildFormFields[4]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'description',
                        'textarea',
                        array(
                            'label' => $buildFormFields[4]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'description',
                    'hidden',
                    array(
                        'label' => $buildFormFields[4]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildFormFields[6]['showed']) {
                if ($buildFormFields[6]['required']) {
                    $form->add(
                        'license_no',
                        'text',
                        array(
                            'label' => $buildFormFields[6]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'license_no',
                        'text',
                        array(
                            'label' => $buildFormFields[6]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'license_no',
                    'hidden',
                    array(
                        'label' => $buildFormFields[6]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildFormFields[7]['showed']) {
                if ($buildFormFields[7]['required']) {
                    $form->add(
                        'application_fee',
                        'text',
                        array(
                            'label' => $buildFormFields[7]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'application_fee',
                        'text',
                        array(
                            'label' => $buildFormFields[7]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'application_fee',
                    'hidden',
                    array(
                        'label' => $buildFormFields[7]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[8]['showed']) {
                if ($buildFormFields[8]['required']) {
                    $form->add(
                        'license_fee',
                        'textarea',
                        array(
                            'label' => $buildFormFields[8]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'license_fee',
                        'textarea',
                        array(
                            'label' => $buildFormFields[8]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'license_fee',
                    'hidden',
                    array(
                        'label' => $buildFormFields[8]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[9]['showed']) {
                if ($buildFormFields[9]['required']) {
                    $form->add(
                        'max_processing_time',
                        'textarea',
                        array(
                            'label' => $buildFormFields[9]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'max_processing_time',
                        'textarea',
                        array(
                            'label' => $buildFormFields[9]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'max_processing_time',
                    'hidden',
                    array(
                        'label' => $buildFormFields[9]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[10]['showed']) {
                if ($buildFormFields[10]['required']) {
                    $form->add(
                        'gazetted_on',
                        'text',
                        array(
                            'label' => $buildFormFields[10]['label'],
                            'required' => false,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'gazetted_on',
                        'text',
                        array(
                            'label' => $buildFormFields[10]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'gazetted_on',
                    'hidden',
                    array(
                        'label' => $buildFormFields[10]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[11]['showed']) {
                if ($buildFormFields[11]['required']) {
                    $form->add(
                        'related_websites',
                        'textarea',
                        array(
                            'label' => $buildFormFields[11]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'related_websites',
                        'textarea',
                        array(
                            'label' => $buildFormFields[11]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'related_websites',
                    'hidden',
                    array(
                        'label' => $buildFormFields[11]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[12]['showed']) {
                if ($buildFormFields[12]['required']) {
                    $form->add(
                        'validity',
                        'text',
                        array(
                            'label' => $buildFormFields[12]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'validity',
                        'textarea',
                        array(
                            'label' => $buildFormFields[12]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'validity',
                    'hidden',
                    array(
                        'label' => $buildFormFields[12]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[13]['showed']) {
                if ($buildFormFields[13]['required']) {
                    $form->add(
                        'enactment',
                        'text',
                        array(
                            'label' => $buildFormFields[13]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'enactment',
                        'text',
                        array(
                            'label' => $buildFormFields[13]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'enactment',
                    'hidden',
                    array(
                        'label' => $buildFormFields[13]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[14]['showed']) {
                if ($buildFormFields[14]['required']) {
                    $form->add(
                        'gazetting_ref',
                        'textarea',
                        array(
                            'label' => $buildFormFields[14]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'gazetting_ref',
                        'textarea',
                        array(
                            'label' => $buildFormFields[14]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'gazetting_ref',
                    'hidden',
                    array(
                        'label' => $buildFormFields[14]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[15]['showed']) {
                if ($buildFormFields[15]['required']) {
                    $form->add(
                        'contact_office',
                        'text',
                        array(
                            'label' => $buildFormFields[15]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'contact_office',
                        'text',
                        array(
                            'label' => $buildFormFields[15]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'contact_office',
                    'hidden',
                    array(
                        'label' => $buildFormFields[15]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[16]['showed']) {
                if ($buildFormFields[16]['required']) {
                    $form->add(
                        'resolution_criteria',
                        'text',
                        array(
                            'label' => $buildFormFields[16]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'resolution_criteria',
                        'text',
                        array(
                            'label' => $buildFormFields[16]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'resolution_criteria',
                    'hidden',
                    array(
                        'label' => $buildFormFields[16]['label'],
                        'required' => false
                    )
                );
            }
            if ($buildFormFields[18]['showed']) {
                if ($buildFormFields[18]['required']) {
                    $form->add(
                        'universal',
                        'text',
                        array(
                            'label' => $buildFormFields[18]['label'],
                            'required' => true,
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'universal',
                        'text',
                        array(
                            'label' => $buildFormFields[18]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'universal',
                    'hidden',
                    array(
                        'label' => $buildFormFields[18]['label'],
                        'required' => false
                    )
                );
            }

            if ($buildFormFields[22]['showed']) {
                if ($buildFormFields[22]['required']) {
                    $form->add(
                        'requirements',
                        'textarea',
                        array(
                            'label' => $buildFormFields[22]['label'],
                            'required' => true,
                            'attr' => ['data-required' => 'true'],
                            'label_attr' => array("class" => "label-required")
                        )
                    );
                } else {
                    $form->add(
                        'requirements',
                        'textarea',
                        array(
                            'label' => $buildFormFields[22]['label'],
                            'required' => false
                        )
                    );
                }
            } else {
                $form->add(
                    'requirements',
                    'hidden',
                    array(
                        'label' => $buildFormFields[22]['label'],
                        'required' => false
                    )
                );
            }
        }

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    public function getLicenseFirstStage()
    {
        $em = $this->getDoctrine()->getManager();
        $result = $em->getRepository(Workflow::class)->findOneBy(['firstStage' => 1]);

        return $result;
    }


    /**
     * Edits an existing BusinessLicense entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $slug = $this->get('cocur_slugify');
        $em = $this->getDoctrine()->getManager();
        $set_stage = false;
        $task = false;
        $form_errors = false;
        $new_license_stage = false;
        $activity = false;
        $entity = $em->getRepository(
            'WebmastersAfricaLicenseBundle:BusinessLicense'
        )->find($id);

        $currentStage = $entity->getStage();
        $currentStatus = $entity->getStatus();
        $edit_activity = "";

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLicense entity.');
        }
        $originalDownloads = new ArrayCollection();
        $originalLegislation = new ArrayCollection();


        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($entity->getDownloads() as $download) {
            $originalDownloads->add($download);
        }

        foreach ($entity->getSubsidiaryLegislation() as $legislation) {
            $originalLegislation->add($legislation);
        }

        $deleteForm = $this->createDeleteForm($id);
        $query_2 = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                ORDER BY p.fieldlabel DESC'
        );
        $fixedfields_new = $query_2->getResult();

        foreach ($fixedfields_new as $field) {
            $buildFormFields[$field->getId()]  = ['required' => $field->getRequired(), 'showed' => $field->getShowed(), 'label' => $field->getFieldlabel()];
        }


        $editForm = $this->createEditForm($entity, $buildFormFields);
        $editForm->handleRequest($request);
        $firstStage = $this->getLicenseFirstStage();
        if (!$firstStage) {
            $this->get('session')->getFlashBag()->add(
                'access',
                'This Workflow Is Not Yet Configure'
            );
            return $this->redirect($this->generateUrl('managelicenses'));
        }

        if ($editForm->isValid()) {
            // if ($entity->getStatus() == 2) {
            //     $entity->setStatus(2);
            //     $activity = "Save License as A Draft";
            // }
            if ($currentStatus == 2) {
                error_log("current status was saved as draft");
                // get the current stage
                // compare if the stage is equal to submission stage
                // update to next stage
                // user has published
                if ($entity->getStage()->getId() == $firstStage->getId()) {
                    $new_license_stage = $this->getLicenseFirstStage()->getNextStage();
                    $entity->setStage($new_license_stage);
                    $set_stage = true;
                    $entity->setStatus(3);
                    $activity = "License details updated";
                    error_log("Update to next stage 1");
                    error_log($new_license_stage->getTitle());
                } else {
                    error_log("This is not a submission stage at all");
                    $new_license_stage = $entity->getStage()->getNextStage();
                    $entity->setStage($new_license_stage);
                    $set_stage = true;
                    $entity->setStatus(3);
                    $activity = "License details updated";
                    error_log("Update to next stage 2");
                    error_log($new_license_stage->getTitle());
                }
            } elseif ($currentStatus == 5 || $currentStatus == 3) {
                error_log("Current status is under review or make corrections");
                if ($currentStatus == 5) {
                    error_log("Current status is make corrections");
                    $new_license_stage = $entity->getStage()->getNextStage();
                    if ($new_license_stage->getType() == 'publish') {
                        $new_license_stage = $currentStage;
                    }
                    $task_status = $this->getTaskDeclinedUpdate($entity);
                    $activity = 'License Details Updated';
                    $entity->setStatus(3);
                    $entity->setStage($new_license_stage);
                    error_log("New stage is ");
                    error_log($new_license_stage->getTitle());
                    // this
                } else {
                    error_log("Current Status is under review");
                    $new_license_stage = $entity->getStage()->getNextStage();
                    $entity->setStage($new_license_stage);
                    $set_stage = true;
                    $entity->setStatus(3);
                    $task_status = $this->getTaskDeclinedUpdate($entity);
                    $activity = "License Details Updated";
                    error_log("New stage is ");
                    error_log($new_license_stage->getTitle());
                }
            } elseif ($currentStatus == 1 || $currentStatus === 1) {
                $entity->setStage($entity->getStage());
                $entity->setStatus(1);
                $edit_activity = true;
                $activity = "License Details Updated Successfully";
                $new_license_stage = $entity->getStage();
            } else {
                error_log('current status is is saved as a draft or unknown');
                // move this license to the next stage
                $task = $this->isUserWorkingOnPendingTask($entity);

                $new_license_stage = $this->findTheNewStageToSetForThisLicense($entity);
                // update the task
                $entity->setStage($new_license_stage);
                $entity->setStatus(3);
                $set_stage = true;
                $activity = "License Saved as a draft updated";
            }
            foreach ($originalDownloads as $download) {
                if (false === $entity->getDownloads()->contains($download)) {
                    $entity->getDownloads()->removeElement($download);

                    $down = $em->getRepository(
                        'WebmastersAfricaLicenseBundle:LicenseDownload'
                    )->find($download->getId());
                    $em->remove($down);
                } else {
                    $updateDownload = $em->getRepository(
                        'WebmastersAfricaLicenseBundle:LicenseDownload'
                    )->find($download->getId());
                    $entity->getDownloads()->removeElement($download);
                    $em->remove($updateDownload);
                    $entity->addDownload($download);
                }
            }

            foreach ($originalLegislation as $sub_download) {
                if (false === $entity->getSubsidiaryLegislation()->contains($sub_download)) {
                    $entity->getSubsidiaryLegislation()->removeElement($sub_download);

                    $downs = $em->getRepository(
                        'WebmastersAfricaLicenseBundle:SubsidiaryLegislationAttachments'
                    )->find($sub_download->getId());
                    $em->remove($downs);
                } else {
                    $subsidiaryLegislationFind = $em->getRepository(
                        'WebmastersAfricaLicenseBundle:SubsidiaryLegislationAttachments'
                    )->find($sub_download->getId());
                    $entity->getSubsidiaryLegislation()->removeElement($sub_download);
                    $em->remove($subsidiaryLegislationFind);
                    $entity->addSubsidiaryLegislation($sub_download);
                }
            }

            if ($request->files->get('webmastersafrica_licensebundle_businesslicense')['file']) {
                $fileName = $this->upload(
                    $request->files->get(
                        'webmastersafrica_licensebundle_businesslicense'
                    )['file']
                );

                $entity->upload($fileName);
            }
            $em->persist($entity);
            $em->flush();


            //custom license fields
            $fielddatas = $request->request->get("custom");

            $query = $em->createQuery(
                'SELECT p
                    FROM WebmastersAfricaLicenseBundle:LicenseField p
                    ORDER BY p.order ASC'
            );
            $customfields = $query->getResult();

            foreach ($customfields as $customfield) {
                $query = $em->createQuery(
                    'SELECT p
                        FROM WebmastersAfricaLicenseBundle:LicenseFieldData p
                        WHERE p.field_id = :fieldid
                        AND p.license_id = :licenseid'
                )->setParameter("fieldid", $customfield->getId())->setParameter("licenseid", $entity->getId());
                $currentfielddatas = $query->getResult();
                if ($currentfielddatas) {
                    foreach ($currentfielddatas as $currentfielddata) {
                        $currentfielddata->setFielddata($fielddatas[$customfield->getId()]);
                    }
                } else {
                    $newfielddata = new LicenseFieldData();
                    $newfielddata->setField($customfield);
                    $newfielddata->setLicense($entity);
                    $newfielddata->setFielddata($fielddatas[$customfield->getId()]);
                    $em->persist($newfielddata);
                    $em->flush();
                }
            }

            if ($task) {
                $task_complete = $this->markMyTaskComplete($task);
            }


            // check for new stage with a new to create
            if ($set_stage) {
                $this->createTask($new_license_stage, $entity);
            }
            if (!$activity) {
                $activity = "Update License Details";
            }
            $this->setApplicationHistoryonUpdate($entity, $currentStage, $activity);

            if ($new_license_stage) {
                $this->get('session')->getFlashBag()->add(
                    'license_created',
                    "License Updated Successfully and Submitted to " . $new_license_stage->getTitle()
                );
                $accessGranted = $this->checkPermissionForThisWorkflowStage($new_license_stage);
                if ($accessGranted) {
                    return $this->redirect($this->generateUrl('managelicenses', ['id' => $new_license_stage->getId()]));
                } else {
                    return $this->redirect($this->generateUrl('managelicenses', ['id' => $currentStage->getId()]));
                }
            }

            return $this->redirect($this->generateUrl('managelicenses'));
        } else {
            $form_errors = true;
        }

        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseField p
                ORDER BY p.order ASC'
        );
        $customfields = $query->getResult();

        $showedfields = [];

        $query = $em->createQuery(
            'SELECT p
                FROM WebmastersAfricaLicenseBundle:LicenseFixedField p
                WHERE p.showed = :showed
                ORDER BY p.fieldlabel DESC'
        )->setParameter("showed", true);
        $fixedfields = $query->getResult();

        foreach ($fixedfields as $field) {
            $showedfields[] = $field->getId();
        }


        return $this->render(
            'WebmastersAfricaLicenseBundle:BusinessLicense:edit.html.twig',
            array(
                'entity'      => $entity,
                'customfields' => $customfields,
                'showedfields' => $showedfields,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'agency' => $_SESSION['agency'],
                'form_errors' => $form_errors
            )
        );
    }

    public function setApplicationHistoryonUpdate(BusinessLicense $license, Workflow $workflow, $activity)
    {
        // Application == License History
        $em = $this->getDoctrine()->getManager();
        $history = new ApplicationHistory();
        $history->setUser($this->get('security.context')->getToken()->getUser());
        $history->setPreviousStage($workflow);
        $history->setCurrentStage($workflow->getNextStage());
        $history->setApplication($license);
        $history->setActionType($activity);
        $em->persist($history);
        $em->flush();
        return;
    }

    public function isUserWorkingOnPendingTask(BusinessLicense $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->findOneBy(['stage' => $entity->getStage()->getId(), 'assignee' => $this->getUser()->getId(), 'license' => $entity->getId(), 'taskStatus' => 'pending']);
        return $task ? $task : false;
    }

    public function markMyTaskComplete(Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $task->setTaskStatus("complete");
        $em->persist($task);
        $em->flush();
        return $task;
    }
    /**
     * Deletes a BusinessLicense entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(BusinessLicense::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find BusinessLicense entity.');
        }

        $entity->setDeleted(1);
        $em->persist($entity);
        $em->persist($entity);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'delete',
            'delete'
        );
        //}

        return $this->redirect($this->generateUrl('managelicenses'));
    }

    public function findTheNewStageToSetForthisLicense(BusinessLicense $license)
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser()->getId();

        // get the stage user was working on.
        $task = $em->getRepository(Task::class)->findOneBy(['assignee' => $user, 'license' => $license->getId()]);
        $other_task = $em->getRepository(Task::class)->findBy(['assignee' => $user, 'license' => $license->getId()]);

        // task not found the this application is set to the first stage
        if (!$task) {
            return $this->getLicenseFirstStage();
        }
        $task = $em->getRepository(Task::class)->find($task);
        $task->setTaskStatus("complete");
        $em->persist($task);
        $em->flush();
        $license_next_stage = $license->getStage()->getNextStage();
        if (!$license_next_stage) {
            return false;
        }

        if (count($other_task) >= 1) {
            // mark them also complete cause its the same license.
            // probably the user will work on it or the system auto generate them
            $em = $this->getDoctrine()->getManager();
            foreach ($other_task as $key) {
                $other_task->setStatus("complete");
                $other_task->setUpdated(\Datetime("now"));
                $em->persist($other_task);
                $em->flush();
            }
        }
        return $license_next_stage;
    }

    /**
     * Creates a form to delete a BusinessLicense entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('managelicenses_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function createTask(Workflow $stage, BusinessLicense $license)
    {
        $em = $this->getDoctrine()->getManager();
        $task_info = $stage;
        // check if any user has been assigned task in this stage
        if (!$stage->getAssignee()) {
            return false;
        }
        $userToAssign = $em->getRepository(User::class)->findOneBy(["id" => $stage->getAssignee()->getId()]);
        $assigner_name = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->id());
        $taskEndDate = ($task_info->getNumberOfDays()) ? $task_info->getNumberOfDays() : \Datetime("now");

        $task_description = ($task_info->getTaskDescription()) ? $task_info->getTaskDescription() : "Hi , have entered this license details for your approval";
        $task = new Task();
        $task->setTaskDescription($task_description);
        $task->setTaskStartDate(
            date('Y-m-d H:i:s')
        );
        $task->setTaskEndDate(
            date(
                'Y-m-d H:i:s',
                strtotime(
                    date('Y-m-d H:i:s'),
                    $taskEndDate
                )
            )
        );
        $task->setTaskStatus("pending");
        $task->setLicense($license);
        $task->setAssignee($userToAssign);
        $task->setStage($license->getStage());

        $task->setAssigner(
            $assigner_name
        );

        $em->persist($task);
        $em->flush();
        // use events listeners to send emails

        if ($task_info->getSendEmails()) {
            $this->sendTaskEmail($task);
        }

        return true;
    }

    public function sendNotificationsToUsersInThisStage($stage)
    {
        $em = $this->getDoctrine()->getManager();
        $stageToSet = $stage->getNextStage();
        if ($stageToSet->getNotificationsUser()->count() < 1) {
            return false;
        }

        $settings = $settings = $em->getRepository(
            'WebmastersAfricaUserBundle:Setting'
        )->find(1);
        $message = \Swift_Message::newInstance()
            ->setSubject("New Task Created")
            ->setFrom(
                array(
                    $settings->getSiteEmailAddress() => $settings->getSiteEmailTitle()
                )
            )
            ->setTo($this->getUser()->getEmail())
            ->setBody(
                $entity->getTaskDescription() . " <a href='http://" . $_SERVER['HTTP_HOST'] . "'>View Application</a>
                    Yours,
                    eRegistry Team.",
                'text/html'
            );
        $this->get('mailer')->send($message);

        return true;
    }

    public function sendTaskEmail($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $entity->getNotificationsUser();
        $settings = $em->getRepository(
            'WebmastersAfricaUserBundle:Setting'
        )->find(1);
        foreach ($users as $key) {
            $message = \Swift_Message::newInstance()
                ->setSubject("New Task")
                ->setFrom(
                    array(
                        $settings->getSiteEmailAddress() => $settings->getSiteEmailTitle()
                    )
                )
                ->setTo()
                ->setBody(
                    "Hi, 
                    You received the following task on http://" . $_SERVER['HTTP_HOST'] . "
                        " . $entity->getTaskDescription() . "

                        Yours,
                        eRegistry Team.",
                    'text/html'
                );
            $this->get('mailer')->send($message);
        }


        return true;
    }

    protected function isApplicationAssessed(BusinessLicense $licenses)
    {
        // user can submit twice.
        // avoid application being published
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->findBy(['license' => $licenses->getId(), 'stage' => $licenses->getStage()]);
        $task_count = count($task);
        $workflow = $em->getRepository(Workflow::class)->findAll(['published' => 1]);
        $workflow_count = count($workflow);
        if ($task_count > 0 && $task_count <= count($workflow)) {
            return true;
        }
        return false;
    }

    protected function getTaskDeclined($license)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->findOneBy(['license' => $license, 'decline' => 1, 'taskStatus' => 'pending']);
        return $task ? $task : false;
    }
    protected function getTaskDeclinedUpdate($license)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository(Task::class)->findOneBy(['license' => $license, 'decline' => 1, 'taskStatus' => 'pending']);
        if ($task) {
            $myTask = $em->getRepository(Task::class)->find($task->getId());

            $myTask->setTaskStatus('complete');
            $em->persist($myTask);
            $em->flush();
        }
        return $task ? $task : false;
    }

    public function getDeclinedTaskForThisLicenseAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $license = $em->getRepository(BusinessLicense::class)->findOneBy(['id' => $id, 'status' => 5]);
        if (!$license) {
            throw $this->createNotFoundException('Business License Entity Not Found');
        }
        $task = $this->getTaskDeclined($license);
        if (!$task) {
            return $this->redirect($this->generateUrl('managelicenses_show', ['id' => $license->getId()]));
        }

        return $this->redirect($this->generateUrl('managetasks_show', ['id' => $task->getId()]));
    }

    public function moveLicenseToAnotherStage($id, $stage_id)
    {
        $em = $this->getDoctrine()->getManager();
        $license = $em->getRepository(BusinessLicense::class)->findOneBy(['id' => $id, 'stage' => $stage_id]);
        if (!$license) {
            throw $this->createNotFoundException('Business License Entity Not Found');
        }
        $workflow = $em->getRepository(Workflow::class)->find($stage_id);
        if ($workflow) {
            throw $this->createNotFoundException('Workflow Stage  Not Found');
        }
        $this->moveApplicationToStage($license, $stage_id);
        return $this->redirect($this->generateUrl('managelicenses_show', ['id' => $license->getId()]));
    }

    public function moveApplicationToStage(BusinessLicense $license, Workflow $stage)
    {
        $em = $this->getDoctrine()->getManager();
        $license->setStage($stage);
        $em->persist($license);
        $em->flush();
    }
}

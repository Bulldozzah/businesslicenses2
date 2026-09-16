<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlans;
use OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlansAttachments;
use OTB\Bundle\NoticeAndCommentBundle\Form\ForwardPlansType;
use WebmastersAfrica\LicenseBundle\Entity\BusinessAgency;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ForwardPlans controller.
 *
 * @Route("/forwardplans")
 */
class ForwardPlansController extends Controller
{

    /**
     * Lists all ForwardPlans entities.
     *
     * @Route("/", name="forwardplans")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $my_agency = [];
        $entities = [];
        $count = 0;
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        if ($my_agency) {
            $entities = $em->getRepository(ForwardPlans::class)->listAllForwardsPlansByAgency($my_agency);
        }


        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ForwardPlans entity.
     *
     * @Route("/", name="forward_plans_create")
     * @Method("POST")
     * @Template("NoticeCommentBundle:ForwardPlans:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $slug = $this->get('cocur_slugify');
        $em = $this->getDoctrine()->getManager();
        $entity = new ForwardPlans();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        $user = $this->getUser();
        $my_agency = [];
        $count = 0;
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $_SESSION['agencyid'] = $my_agency;

        if ($form->isValid()) {
            $entity->setSlug(
                $slug->slugify(
                    $request->request->get('forward_plans')['title']
                )
            );
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );
            return $this->redirect($this->generateUrl('forwardplanmaincategory_show', ['id' => $entity->getForwardPlanCategory()->getId()]));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ForwardPlans entity.
     *
     * @param ForwardPlans $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ForwardPlans $entity)
    {
        $form = $this->createForm(
            new ForwardPlansType(),
            $entity,
            array(
                'action' => $this->generateUrl('forward_plans_create'),
                'method' => 'POST',
            )
        );

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Create',
                'attr' => array(
                    "class" => "w3-btn w3-white w3-border w3-border-blue w3-large w3-right",
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            )
        );

        return $form;
    }

    /**
     * Displays a form to create a new ForwardPlans entity.
     *
     * @Route("/new", name="forwardplans_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $forward_plan_main = $request->query->get('id');
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $my_agency = [];
        $count = 0;
        $agencies = $user->getAgencies();
        // ofcourse there better ways to do this
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $_SESSION['agencyid'] = $my_agency;

        if ($forward_plan_main) {
            $_SESSION['forward_plan_main'] = $forward_plan_main;
        } else {
            $_SESSION['forward_plan_main'] = '';
        }

        $entity = new ForwardPlans();
        $form   = $this->createCreateForm($entity);
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ForwardPlans entity.
     *
     * @Route("/{id}", name="forwardplans_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(ForwardPlans::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ForwardPlans entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ForwardPlans entity.
     *
     * @Route("/{id}/edit", name="forward_plans_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(ForwardPlans::class)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ForwardPlans entity.');
        }


        $user = $this->getUser();
        $my_agency = [];
        $count = 0;
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $_SESSION['agencyid'] = $my_agency;


        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a ForwardPlans entity.
     *
     * @param ForwardPlans $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ForwardPlans $entity)
    {
        $form = $this->createForm(
            new ForwardPlansType(),
            $entity,
            array(
                'action' => $this->generateUrl('forward_plans_update', array('id' => $entity->getId())),
                'method' => 'PUT',
            )
        );

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Update',
                'attr' => array(
                    "class" => "w3-btn w3-border w3-border-blue w3-large w3-right",
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            )
        );

        return $form;
    }
    /**
     * Edits an existing ForwardPlans entity.
     *
     * @Route("/{id}", name="forwardplans_update")
     * @Method("PUT")
     * @Template("NoticeCommentBundle:ForwardPlans:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $slug = $this->get('slugify');

        $entity = $em->getRepository(ForwardPlans::class)->find($id);
        $forward_plans = new ForwardPlans();

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ForwardPlans entity.');
        }
        $supportingDocuments = new ArrayCollection();

        foreach ($entity->getAttachments() as $attachment) {
            $supportingDocuments->add($attachment);
        }


        $user = $this->getUser();
        $my_agency = [];
        $count = 0;
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $_SESSION['agencyid'] = $my_agency;


        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            foreach ($supportingDocuments as $documents) {
                if (false === $entity->getAttachments()->contains($documents)) {
                    $entity->getAttachments()->removeElement($documents);
                    $downs = $em->getRepository(ForwardPlansAttachments::class)->find($documents->getId());
                    $em->remove($downs);
                } else {
                    $forwardPlansAttachment = $em->getRepository(
                        ForwardPlansAttachments::class
                    )->find($documents->getId());
                    $em->remove($forwardPlansAttachment);
                    $entity->addAttachment($forwardPlansAttachment);
                }
            }
            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );
            return $this->redirect($this->generateUrl('forwardplanmaincategory_show', array('id' => $entity->getForwardPlanCategory()->getId())));
        }
        $em->persist($entity);
        $em->flush();
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ForwardPlans entity.
     *
     * @Route("/{id}", name="forwardplans_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository(ForwardPlans::class)->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ForwardPlans entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('forward_plans'));
    }

    /**
     * Creates a form to delete a ForwardPlans entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('forward_plans_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }


    /**
     * Lists all Forward Regulation/Regulation.
     *
     * @Route("/", name="notice_comments_planning")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Regulation:forward_planning.html.twig")
     *
     * @return array
     */
    public function filterForwardPlansAction(Request $request, $character)
    {
        $character = preg_replace("#[^a-z]#i", '', $character);
        $agencies = $this->agencyList();
        $em = $this->getDoctrine()->getManager();
        $forward_plan = $em->getRepository(
            BusinessAgency::class
        )->filterAgencyByTitle(
            $character
        );

        $paginate  = $this->get('knp_paginator');
        $agencies = $this->agencyList();
        $pagination = $paginate->paginate(
            $forward_plan,
            $request->query->getInt('page', 1),
            24
        );

        return array(
            'agency_list' => $pagination,
            'active_alphabet' => $character
        );
    }

    protected function agencyList()
    {
        $em = $this->getDoctrine()->getManager();
        $agencies = $em->getRepository(BusinessAgency::class)->getActiveAgencies();
        return $agencies;
    }

    /**
     * Finds and displays a agency forward Plans.
     *
     * @Method("GET")
     * @Template("NoticeCommentBundle:Regulation:forward_plans.html.twig")
     */
    public function showAgencyForwardPlansAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(BusinessAgency::class)
            ->getAgencyWithPublishedForwardPlanRegulations($slug)[0];

        if (!$entity) {
            $entity = $em->getRepository(BusinessAgency::class)->findOneBy(['slug' => $slug]);
            if (!$entity) {
                throw $this->createNotFoundException(
                    'Unable to find Forward Plan entity.'
                );
            }
        }

        return array(
            'agency' => $entity,
        );
    }

    /**
     * Lists all Forward Regulation/Regulation.
     *
     * @Route("/", name="notice_comments_planning")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Regulation:forward_planning.html.twig")
     *
     * @return array
     */
    public function forwardPlansAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $paginate  = $this->get('knp_paginator');
        $agencies = $em->getRepository(BusinessAgency::class)->getAgenciesWithForwardPlansOnly();
        $pagination = $paginate->paginate(
            $agencies,
            $request->query->getInt('page', 1),
            24
        );
        $character = "";

        return array(
            'agency_list' => $pagination,
            'active_alphabet' => $character,
        );
    }
    /**
     * Lists all Forward Regulation/Regulation.
     *
     * @Route("/", name="notice_comments_planning")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Regulation:forward_planning.html.twig")
     *
     * @return array
     */
    public function searchAgencyForwardPlansAction(Request $request)
    {
        $agency = $request->get('search_key_agency');
        $em = $this->getDoctrine()->getManager();
        $agency_filter = $em->getRepository(BusinessAgency::class)->filterAgencyByNameSearch($agency);
        $paginate  = $this->get('knp_paginator');
        $pagination = $paginate->paginate(
            $agency_filter,
            $request->query->getInt('page', 1),
            24
        );
        return array(
            'agency_list' => $pagination,
            'active_alphabet' => $character,
        );
    }
    /**
     * Lists all Forward Regulation/Regulation.
     *
     * @Route("/", name="forward_plans_advanced_search")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Regulation:forward_search_result.html.twig")
     *
     * @return array
     */
    public function searchForwardPlansAction(Request $request)
    {
        $agency = $request->request->get('agency');
        $paginate  = $this->get('knp_paginator');
        $em = $this->getDoctrine()->getManager();
        $agency_entity = $em->getRepository(BusinessAgency::class)
            ->find($agency);
        return array(
            'agency' => $agency_entity,
            'agencies' => $this->agencyList()
        );
    }



    /**
     * Show the details of a single forward plan
     *
     * @Route("/", name="forward_plans_frontend_show")
     * @Method("GET")
     * @Template("NoticeCommentBundle:ForwardPlans:forward_plan_details.html.twig")
     *
     * @return mixed
     */
    public function showFordPlanDetailsAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $forward_plan = $em->getRepository(
            ForwardPlans::class
        )->findOneBy(['slug' => $slug, 'deleted' => 0, 'published' => 1]);
        if (!$forward_plan) {
            throw $this->createNotFoundException('Plan Not Found');
        }

        return array(
            'forward_plan' => $forward_plan,
        );
    }

    public function batchAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $fp = $em->getRepository(ForwardPlans::class)->find($batch_item);
                $fp->setDeleted(1);
                $fp->setPublished(false);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        return $this->redirect(
            $this->generateUrl('forwardplanmaincategory_show', ['id' => $id])
        );
    }

    public function batch2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $fp = $em->getRepository(ForwardPlans::class)->find($batch_item);
                $fp->setDeleted(1);
                $fp->setPublished(false);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'delete',
                    'delete'
                );
            }
        }

        return $this->redirect($this->generateUrl('forward_plans'));
    }
}

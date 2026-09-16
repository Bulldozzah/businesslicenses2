<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use OTB\Bundle\NoticeAndCommentBundle\Entity\ForwardPlanMainCategory;
use OTB\Bundle\NoticeAndCommentBundle\Form\ForwardPlanMainCategoryType;

/**
 * ForwardPlanMainCategory controller.
 *
 * @Route("/forwardplanmaincategory")
 */
class ForwardPlanMainCategoryController extends Controller
{

    /**
     * Lists all ForwardPlanMainCategory entities.
     *
     * @Route("/", name="forwardplanmaincategory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
        $my_agency = [];
        $count = 0;

        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $entities = $em->getRepository('NoticeCommentBundle:ForwardPlanMainCategory')->listAllMainForwardPlansInMyAgency($my_agency);

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ForwardPlanMainCategory entity.
     *
     * @Route("/", name="forwardplanmaincategory_create")
     * @Method("POST")
     * @Template("NoticeCommentBundle:ForwardPlanMainCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
        $entity = new ForwardPlanMainCategory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $slug = $this->get('slugify');
        $my_agency = [];
        $count = 0;
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $_SESSION['agencyid'] = $my_agency;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setSlug(
                $slug->slugify(
                    $request->request->get('forwardplanmaincategory')['name']
                )
            );
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );
            return $this->redirect($this->generateUrl('forwardplanmaincategory'));
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ForwardPlanMainCategory entity.
     *
     * @param ForwardPlanMainCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ForwardPlanMainCategory $entity)
    {
        $form = $this->createForm(new ForwardPlanMainCategoryType(), $entity, array(
            'action' => $this->generateUrl('forwardplanmaincategory_create'),
            'method' => 'POST',
        ));

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
     * Displays a form to create a new ForwardPlanMainCategory entity.
     *
     * @Route("/new", name="forwardplanmaincategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
        $my_agency = [];
        $count = 0;
        $agencies = $user->getAgencies();
        foreach ($agencies as $agency) {
            $count++;
            $my_agency[] = $agency->getId();
        }
        $_SESSION['agencyid'] = $my_agency;
        $entity = new ForwardPlanMainCategory();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ForwardPlanMainCategory entity.
     *
     * @Route("/{id}", name="forwardplanmaincategory_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:ForwardPlanMainCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ForwardPlanMainCategory entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ForwardPlanMainCategory entity.
     *
     * @Route("/{id}/edit", name="forwardplanmaincategory_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:ForwardPlanMainCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ForwardPlanMainCategory entity.');
        }

        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
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
     * Creates a form to edit a ForwardPlanMainCategory entity.
     *
     * @param ForwardPlanMainCategory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ForwardPlanMainCategory $entity)
    {
        $form = $this->createForm(new ForwardPlanMainCategoryType(), $entity, array(
            'action' => $this->generateUrl('forwardplanmaincategory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Update',
                'attr' => array(
                    "class" => "w3-btn w3-white w3-border w3-border-blue w3-large w3-right",
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            )
        );

        return $form;
    }
    /**
     * Edits an existing ForwardPlanMainCategory entity.
     *
     * @Route("/{id}", name="forwardplanmaincategory_update")
     * @Method("PUT")
     * @Template("NoticeCommentBundle:ForwardPlanMainCategory:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:ForwardPlanMainCategory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ForwardPlanMainCategory entity.');
        }


        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($this->get('security.context')->getToken()->getUser()->getId());
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
        $slug = $this->get('slugify');
        if ($editForm->isValid()) {
            $entity->setSlug(
                $slug->slugify(
                    $request->request->get('forwardplanmaincategory')['name']
                )
            );
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'update',
                'update'
            );

            return $this->redirect($this->generateUrl('forwardplanmaincategory'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ForwardPlanMainCategory entity.
     *
     * @Route("/{id}", name="forwardplanmaincategory_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NoticeCommentBundle:ForwardPlanMainCategory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ForwardPlanMainCategory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('forwardplanmaincategory'));
    }

    /**
     * Creates a form to delete a ForwardPlanMainCategory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('forwardplanmaincategory_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }
}

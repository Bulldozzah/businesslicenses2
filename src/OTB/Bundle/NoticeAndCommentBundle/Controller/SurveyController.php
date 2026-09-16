<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Survey;
use OTB\Bundle\NoticeAndCommentBundle\Form\SurveyType;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Choice;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Question;
use OTB\Bundle\NoticeAndCommentBundle\Entity\SurveyResponse;
use OTB\Bundle\NoticeAndCommentBundle\Entity\SurveyAnswer;
use Doctrine\Common\Collections\ArrayCollection;
use OTB\Bundle\NoticeAndCommentBundle\Form\QuestionType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Survey controller.
 *
 * @Route("/survey")
 */
class SurveyController extends Controller
{

    /**
     * Lists all Survey entities.
     *
     * @Route("/", name="manage_surveys_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('NoticeCommentBundle:Survey')->findBy(['published' => true], ['id' => 'desc']);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all unpublished Survey entities.
     *
     * @Route("/", name="manage_surveys_index_deleted")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:indexDeleted.html.twig")
     */
    public function indexDeletedAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('NoticeCommentBundle:Survey')->findBy(['published' => false], ['id' => 'DESC']);

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Survey entity.
     *
     * @Route("/", name="manage_surveys_create")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Survey:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Survey();
        $user = $this->getUser();
        if ($user->getAgencies()) {
            $agencies = $user->getAgencies();
            foreach ($agencies as $agency) {
                $agency_list[] = $agency->getId();
            }
            $_SESSION['agency_list'] = $agency_list;
            $_SESSION['agency'] = true;
        } else {
            $_SESSION['agency'] = false;
        }
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );
            return $this->redirect($this->generateUrl('manage_surveys_deleted'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Survey entity.
     *
     * @Route("/", name="manage_surveys_questions_create")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Survey:new_question.html.twig")
     */
    public function createSurveyQuestionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Question();
        $survey = $em->getRepository(Survey::class)->find($request->get('otb_question')['survey']);
        if (!$survey) {
            throw $this->createNotFoundException('Unable to find Survey Entity');
        }
        $form = $this->createQuestionForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );
            return $this->redirect($this->generateUrl('manage_surveys_show', ['id' => $entity->getSurvey()->getId()]));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'survey' => $survey
        );
    }

    /**
     * Creates a form to create a Survey entity.
     *
     * @param Survey $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Survey $entity)
    {
        $form = $this->createForm(new SurveyType(), $entity, array(
            'action' => $this->generateUrl('manage_surveys_create'),
            'method' => 'POST',
        ));

        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Create',
                'attr' => array(
                    'class' => 'w3-button w3-blue w3-round-medium',
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            )
        );

        return $form;
    }

    private function createQuestionForm(Question $entity)
    {
        $form = $this->createForm(new QuestionType(), $entity, array(
            'action' => $this->generateUrl('manage_surveys_questions_create'),
            'method' => 'POST'
        ));
        $form->add(
            'submit',
            'submit',
            array(
                'label' => 'Create',
                'attr' => array(
                    'class' => 'w3-button w3-blue w3-round-medium',
                    'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
                )
            )
        );

        return $form;
    }

    /**
     * Displays a form to create a new Survey entity.
     *
     * @Route("/new", name="manage_surveys_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Survey();
        $form   = $this->createCreateForm($entity);
        $user = $this->getUser();
        if ($user->getAgencies()) {
            $agencies = $user->getAgencies();
            foreach ($agencies as $agency) {
                $agency_list[] = $agency->getId();
            }
            $_SESSION['agency_list'] = $agency_list;
            $_SESSION['agency'] = true;
        } else {
            $_SESSION['agency'] = false;
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new survey questions
     *
     * @Route("/new", name="manage_surveys_new_question")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:new_question.html.twig")
     */
    public function newSurveyQuestionAction(Request $request)
    {
        $survey = $request->query->get('id');
        if ($survey) {
            $_SESSION['survey_id'] = $survey;
        } else {
            $_SESSION['survey_id'] = 0;
        }
        $entity = new Question();
        $form   = $this->createQuestionForm($entity);
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->find($survey);
        if (!$survey) {
            throw $this->createNotFoundException('Unable to find Survey Entity');
        }
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'survey' => $survey
        );
    }

    /**
     * Finds and displays a Survey entity.
     *
     * @Route("/{id}", name="manage_surveys_show")s
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Survey')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Survey entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Survey entity.
     *
     * @Route("/{id}/edit", name="manage_surveys_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getAgencies()) {
            $agencies = $user->getAgencies();
            foreach ($agencies as $agency) {
                $agency_list[] = $agency->getId();
            }
            $_SESSION['agency_list'] = $agency_list;
            $_SESSION['agency'] = true;
        } else {
            $_SESSION['agency'] = false;
        }

        $entity = $em->getRepository('NoticeCommentBundle:Survey')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Survey entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Survey question entity.
     *
     * @Route("/{id}/edit", name="manage_surveys_questions_edit")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:edit_question.html.twig")
     */
    public function editSurveyQuestionAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Question')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Question entity.');
        }

        $editForm = $this->createSurveyQuestionEditForm($entity);
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Creates a form to edit a Survey entity.
     *
     * @param Survey $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Survey $entity)
    {
        $form = $this->createForm(new SurveyType(), $entity, array(
            'action' => $this->generateUrl('manage_surveys_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array(
            'class' => 'w3-button w3-blue w3-round-medium',
            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
        )));

        return $form;
    }

    /**
     * Creates a form to edit a Survey entity.
     *
     * @param Survey $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createSurveyQuestionEditForm(Question $entity)
    {
        $form = $this->createForm(new QuestionType(), $entity, array(
            'action' => $this->generateUrl('manage_surveys_questions_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array(
            'class' => 'w3-button w3-blue w3-round-medium',
            'style' => "padding: 10px 30px 30px 30px; margin-right:15px;"
        )));

        return $form;
    }
    /**
     * Edits an existing Survey entity.
     *
     * @Route("/{id}", name="manage_surveys_update")
     * @Method("PUT")
     * @Template("NoticeCommentBundle:Survey:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Survey')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Survey entity.');
        }
        $user = $this->getUser();
        if ($user->getAgencies()) {
            $agencies = $user->getAgencies();
            foreach ($agencies as $agency) {
                $agency_list[] = $agency->getId();
            }
            $_SESSION['agency_list'] = $agency_list;
            $_SESSION['agency'] = true;
        } else {
            $_SESSION['agency'] = false;
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('manage_surveys_index'));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Survey Question entity.
     *
     * @Route("/{id}", name="manage_surveys_question_update")
     * @Method("PUT")
     * @Template("NoticeCommentBundle:Survey:edit_question.html.twig")
     */
    public function updateSurveyQuestionAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Question')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find question entity.');
        }

        $surveyChoiceList = new ArrayCollection();
        if ($entity->getChoices()) {
            foreach ($entity->getChoices() as $choice) {
                $surveyChoiceList->add($choice);
            }
        }
        $editForm = $this->createSurveyQuestionEditForm($entity);
        $editForm->handleRequest($request);
        if ($editForm->isValid()) {
            foreach ($surveyChoiceList as $choiceList) {
                if (false === $entity->getChoices()->contains($choiceList)) {
                    $entity->getChoices()->removeElement($choiceList);
                    $down = $em->getRepository(Choice::class)->find($choiceList->getId());
                    $em->remove($down);
                }
            }
            $em->flush();
            return $this->redirect($this->generateUrl('manage_surveys_show', ['id' => $entity->getSurvey()->getId()]));
        }
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        );
    }

    /**
     * Deletes a Survey entity.
     *
     * @Route("/{id}", name="'manage_surveys_delete'")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NoticeCommentBundle:Survey')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Survey entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('manage_surveys_index'));
    }

    /**
     * Creates a form to delete a Survey entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('manage_surveys_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function questionBatchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        $deleted = false;

        foreach ($batch_items as $batch_item) {
            if ($batch_select == 'delete') {
                $entity = $em->getRepository(Question::class)->find($batch_item);
                $redirect_value = $entity->getSurvey()->getId();
                if ($entity) {
                    $em->remove($entity);
                    $em->flush();
                    $deleted = true;
                }
            }
        }

        if ($deleted) {
            $this->get('session')->getFlashBag()->add(
                'delete',
                'delete'
            );
            return $this->redirect($this->generateUrl('manage_surveys_show', ['id' => $redirect_value]));
        }
        return $this->redirect($this->generateUrl('manage_surveys_show', ['id' => $redirect_value]));
    }

    public function orderQuestionPositionAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $updated = false;
        $positions = $request->get('positions');
        foreach ($positions as $position) {
            $index = $position[0];
            $newPosition = $position[1];
            $question = $em->getRepository(Question::class)->find($index);
            if ($question) {
                $question->setQuestionOrder($newPosition);
                $em->persist($question);
                $updated = true;
            }
        }

        $em->flush();
        if ($updated) {
            return new Response(
                json_encode(
                    [
                        'success' => true,
                        'message' => 'updated successfully'
                    ]
                )
            );
        } else {
            return new Response(
                json_encode(
                    [
                        'success' => false,
                        'message' => 'Something went wrong'
                    ]
                )
            );
        }
    }

    /**
     * Show survey details on frontend.
     *
     * @Route("/", name="show_frontend_surveys")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:show_frontend.html.twig")
     */
    public function showFrontendSurveryAction(Request $request, $slug, $survey_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Regulation::class)->findOneBy(['slug' => $slug]);
        if (!$entity) {
            throw $this->createNotFoundException('Consulation not found');
        }
        $survey = $em->getRepository(Survey::class)->findOneBy(['id' => $survey_id, 'regulation' => $entity, 'published' => true]);
        if (!$survey) {
            throw $this->createNotFoundException('Survey Not found');
        }

        return array(
            'survey' => $survey,
            'entity' => $entity
        );
    }

    public function frontEndSurveyResponseAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $surveyAnswer = new SurveyAnswer();
        $surveyResponse = new SurveyResponse();
        $survey = $em->getRepository(Survey::class)->findOneBy(['id' => $request->get('survey_id')]);
        if (!$survey) {
            throw $this->createNotFoundException('Survey Not found');
        }

        $question_responses = $request->get('question_id');
        if (!is_array($question_responses)) {
            $this->get('session')->getFlashBag()->add(
                'survey_failure',
                'Something went wrong please try again'
            );
            return $this->redirect($this->generateUrl('frontend_survey_2', ['slug' => $survey->getRegulation()->getSlug(), 'survey_id' => $survey->getId()]));
        }
        $surveyResponse->setSurvey($survey);
        $surveyResponse->setTimeTaken(date('d-m-Y H:i:s'));
        $em->persist($surveyResponse);
        $em->flush();
        foreach ($question_responses as $response_key => $response_value) {
            $surveyAnswer->setSurveyResponse($surveyResponse);
            $question = $em->getRepository(Question::class)->find($response_key);
            if (is_array($response_value)) {
                for ($i = 0; $i < count($response_value); $i++) {
                    $surveyAnswer->setQuestion($question);
                    $surveyAnswer->setAnswerValue($response_value[$i]);
                    $em->persist($surveyAnswer);
                    $em->flush();
                    $surveyAnswer = new SurveyAnswer();
                    $surveyAnswer->setSurveyResponse($surveyResponse);
                }
            } else {
                $surveyAnswer->setQuestion($question);
                $surveyAnswer->setAnswerValue($response_value);
                $em->persist($surveyAnswer);
                $em->flush();
            }
            $surveyAnswer = new SurveyAnswer();
        }
        return $this->redirect($this->generateUrl('thank_you_survey_response', ['survey_id' => $survey->getId()]));
    }

    /**
     * Survey thank you note
     *
     * @Route("/", name="survey_thank_you_note")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:thank_you_note.html.twig")
     */
    public function frontEndSurverThankYouAction(Request $request, $survey_id)
    {
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->find($survey_id);
        if (!$survey) {
            throw $this->createNotFoundException('Survey Not Found');
        }
        return array(
            'survey' => $survey,
            'entity' => $survey->getRegulation()
        );
    }

    /**
     * Survey thank you note
     *
     * @Route("/", name="manage_surveys_responses")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:survey_responses.html.twig")
     */
    public function showListOfResponsesAction(Request $request, $survey_id)
    {
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->find($survey_id);
        if (!$survey) {
            throw $this->createNotFoundException('Survey not found');
        }

        return array(
            'survey' => $survey
        );
    }
    /**
     * Survey thank you note
     *
     * @Route("/", name="manage_surveys_responses")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:survey_responses_details.html.twig")
     */
    public function showListOfResponsesDetailsAction(Request $request, $survey_id)
    {
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->find($survey_id);
        if (!$survey) {
            throw $this->createNotFoundException('Survey not found');
        }

        return array(
            'survey' => $survey
        );
    }

    /**
     * Survey thank you note
     *
     * @Route("/", name="manage_surveys_show_response")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:survey_response_details.html.twig")
     */
    public function showSurveyResponseAction(Request $request, $survey_id, $response_id)
    {
        $em = $this->getDoctrine()->getManager();
        $response = $em->getRepository(SurveyResponse::class)->findOneBy(['survey' => $survey_id, 'id' => $response_id]);
        if (!$response) {
            throw $this->createNotFoundException('Survey response not found');
        }

        $survey = $em->getRepository(Survey::class)->findOneBy(['id' => $survey_id]);

        return array(
            'response' => $response,
        );
    }

    public function downloadExcelResponseResultAction(Request $request, $survey_id)
    {
        $em = $this->getDoctrine()->getManager();
        $responses = $em->getRepository(SurveyResponse::class)->findBy(['survey' => $survey_id]);
        if (!$responses) {
            throw $this->createNotFoundException('Survey response not found');
        }
        $survey = $em->getRepository(Survey::class)->findOneBy(['id' => $survey_id]);
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $count = 0;
        $count_value = 1;
        $phpExcelObject->getProperties()->setCreator("eRegistry")
            ->setLastModifiedBy("eRegistry")
            ->setTitle("{$survey->getSurveyName()}")
            ->setSubject("{$survey->getSurveyName()} Responses")
            ->setKeywords("survey response, eRegistry, {$survey->getSurveyName()} Responses")
            ->setCategory("Response Result");
        foreach ($survey->getQuestions() as $question) {
            $cell_value = $this->generateCellsLetter($count);
            $phpExcelObject->setActiveSheetIndex(0)->setCellValue("{$cell_value}1", $question->getQuestionText())->getColumnDimension($cell_value)
                ->setWidth(strlen($question->getQuestionText()));
            foreach ($question->getSurveyAnswers() as $response_value) {
                $count_value += 1;
                $phpExcelObject->setActiveSheetIndex(0)->setCellValue("{$cell_value}{$count_value}", $response_value->getAnswerValue());
            }
            $count += 1;
            $count_value = 1;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Simple');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
        // se crea el response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // y por último se añaden las cabeceras
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            "{$survey->getSurveyName()}.xls"
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    private function generateCellsLetter($count_value)
    {
        $count = 0;
        for ($i = 'A'; $i !== 'AC'; $i++) {
            if ($count == $count_value) {
                return $i;
            }
            $count += 1;
        }
    }

    public function publishAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->find($request->get('id'));
        if (!$survey) {
            return new Response(json_encode(['success' => false, 'message' => 'Survey Not Found']));
        }

        $survey->setPublished(true);
        $em->persist($survey);
        $em->flush();

        return new Response(json_encode(['success' => true, 'message' => 'Survey published successfully']));
    }


    public function unpublishAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->find($request->get('id'));
        if (!$survey) {
            return new Response(json_encode(['success' => false, 'message' => 'Survey Not Found']));
        }

        $survey->setPublished(false);
        $em->persist($survey);
        $em->flush();

        return new Response(json_encode(['success' => true, 'message' => 'Survey unpublished successfully']));
    }


    /**
     * Preview Survey Details.
     *
     * @Route("/", name="show_frontend_surveys")
     * @Method("GET")
     * @Template("NoticeCommentBundle:Survey:preview_survey.html.twig")
     */
    public function previewSurveyAction($survey_id)
    {
        $em = $this->getDoctrine()->getManager();
        $survey = $em->getRepository(Survey::class)->findOneBy(['id' => $survey_id]);
        if (!$survey) {
            throw $this->createNotFoundException('Survey Not found');
        }

        return array(
            'survey' => $survey,
            'entity' => $survey->getRegulation()
        );
    }

    public function batchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        $published = false;
        $unpublished = false;
        $count = 0;

        foreach ($batch_items as $batch_item) {
            $entity = $em->getRepository(Survey::class)->find($batch_item);
            if ($entity) {
                if ($batch_select == "unpublish") {
                    $entity->setPublished(false);
                    $unpublished = true;
                    $count = $count + 1;
                } else if ($batch_select == 'publish') {
                    $entity->setPublished(true);
                    $published = true;
                    $count = $count + 1;
                }
            }
        }

        $em->flush();

        if ($published) {
            $this->get('session')->getFlashBag()->add(
                'publish',
                $count . ' Survey(s) published Successfully'
            );
            return $this->redirect($this->generateUrl('manage_surveys_deleted'));
        } else {
            $this->get('session')->getFlashBag()->add(
                'publish',
                $count . ' Survey(s) unpublished Successfully'
            );
            return $this->redirect($this->generateUrl('manage_surveys_index'));
        }
    }
}

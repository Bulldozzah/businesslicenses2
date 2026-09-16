<?php

namespace OTB\Bundle\NoticeAndCommentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Comment;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Regulation;
use OTB\Bundle\NoticeAndCommentBundle\Entity\Like;
use OTB\Bundle\NoticeAndCommentBundle\Form\CommentType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use AppBundle\Pagination\PaginatedCollection;
use OTB\Bundle\NoticeAndCommentBundle\Entity\AbusiveTerms;
use WebmastersAfrica\UserBundle\Entity\User;

/**
 * Comment controller.
 *
 * @Route("/comments")
 */
class CommentController extends Controller
{

    /**
     * Lists all Comment entities.
     *
     * @Route("/", name="comments")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('NoticeCommentBundle:Comment')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Comment entity.
     *
     * @Route("/{regulation_id}", name="comments_create")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Comment:new.html.twig")
     */
    public function createAction(Request $request, $regulation_id)
    {
        $entity = new Comment();
        $em = $this->getDoctrine()->getManager();
        $comment = $request->request->get('comment');
        $position = $request->request->get('position');
        if (empty($comment) || empty($position)) {
            $request->getSession()->getFlashBag()->add(
                'regulation',
                "Regulation could be created at the moment"
            );
            return $this->redirect(
                $this->generateUrl(
                    'regulation_comments',
                    array('regulation_id' => $request->request->get('regulation'))
                )
            );
        }
        if ($request->files->has('document') == true && !empty($request->files->get('document'))) {
            $fileName = $this->upload($request->files->get('document'));
            $entity->setDocument($fileName);
        }
        $regulation = $em->getRepository(
            'NoticeCommentBundle:Regulation'
        )->find($regulation_id);

        $entity->setRegulation($regulation);
        $entity->setComment($comment);
        $entity->setPosition($position);
        $em->persist($entity);
        $em->flush();
        return $this->redirect(
            $this->generateUrl(
                'regulation_comments',
                array('id' => $regulation_id)
            )
        );



        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move(
            $this->container->getParameter('documents_directory'),
            $fileName
        );
        return $fileName;
    }

    public function downvoteCommentAction(Request $request, $comment_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $comment_id]);
        if (!$entity) {
            return $this->createNotFoundException("Comment Not Found");
            return new Response(json_encode(['success' => false, 'message' => 'Comment Not Found']));
        }
        if (count($entity->getUpvoteCount()) < 0) {
            $request->getSession()->getFlashBag()->add(
                'comment_upvote',
                "Comment downvoted successful"
            );
            return $this->redirect($this->generateUrl('manage_comment_list', ['id' => $entity->getRegulation()->getId()]));
        }
        $upvote_count = (int) $entity->getUpvoteCount();
        $new_upvote = $upvote_count - 1;

        $entity->setUpvoteCount($new_upvote);
        $em->persist($entity);
        $em->flush();
        $request->getSession()->getFlashBag()->add(
            'comment_upvote',
            "Comment downvoted successful"
        );
        return $this->redirect($this->generateUrl('manage_comment_list', ['id' => $entity->getRegulation()->getId()]));
        return new Response(json_encode(['success' => true, 'message' => 'Downvote Successful']));
    }

    public function upvoteCommentAction(Request $request, $comment_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $comment_id]);
        if (!$entity) {
            return $this->createNotFoundException("Comment Not Found");
            return new Response(json_encode(['success' => false, 'message' => 'Comment Not Found']));
        }
        if (count($entity->getUpvoteCount()) > 10000) {
            $request->getSession()->getFlashBag()->add(
                'comment_upvote',
                "Comment upvoted successful"
            );
            return $this->redirect($this->generateUrl('manage_comment_list', ['id' => $entity->getRegulation()->getId()]));
        }
        $upvote_count = (int) $entity->getUpvoteCount();
        $new_upvote = $upvote_count + 1;
        $entity->setUpvoteCount($new_upvote);
        $em->persist($entity);
        $em->flush();
        $request->getSession()->getFlashBag()->add(
            'comment_upvote',
            "Comment upvoted successful"
        );
        return $this->redirect($this->generateUrl('manage_comment_list', ['id' => $entity->getRegulation()->getId()]));

        return new Response(json_encode(['success' => true, 'message' => 'Upvote Successful']));
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/{comment_id}", name="reply_create")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Regulation:comments_list.html.twig")
     */
    public function replyAction(Request $request, $comment_id)
    {

        $em = $this->getDoctrine()->getManager();
        $reply = $request->request->get('reply');
        $regulation = $request->request->get('regulation');
        $comment_id = $request->request->get('comment_id');
        if (empty($reply) || empty($regulation)) {
            $request->getSession()->getFlashBag()->add(
                'regulation',
                "Regulation could be created at the moment"
            );
            return $this->redirect(
                $this->generateUrl(
                    'manage_comment_list',
                    array('id' => $request->request->get('regulation'))
                )
            );
        }
        $comment = $em->getRepository(
            'NoticeCommentBundle:Comment'
        )->find($comment_id);
        $regulation = $em->getRepository(
            'NoticeCommentBundle:Regulation'
        )->find($regulation);
        $entity = new Comment();
        $entity->setRegulation($regulation);
        $entity->setParent($comment);
        $entity->setParentRight($comment);
        $entity->setComment($reply);
        $entity->setPosition(0);
        $entity->setIsAdmin(1);
        $entity->setUser($this->getUser());
        $em->persist($entity);
        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'create',
            'create'
        );
        return $this->redirect(
            $this->generateUrl(
                'manage_comment_list',
                array('id' => $request->request->get('regulation'))
            )
        );
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/{comment_id}", name="reply_create")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Regulation:comments_list.html.twig")
     */
    public function regulationReplyModeratorAction(Request $request, $regulation_id)
    {

        $entity = new Comment();
        $em = $this->getDoctrine()->getManager();
        $reply = $request->request->get('reply');
        $regulation = $request->request->get('regulation');
        if ($regulation == $regulation_id) {
            if (empty($reply) || empty($regulation)) {
                $request->getSession()->getFlashBag()->add(
                    'regulation',
                    "Regulation could be created at the moment"
                );
                return $this->redirect(
                    $this->generateUrl(
                        'manage_comment_list',
                        array('id' => $request->request->get('regulation'))
                    )
                );
            }
            $regulation = $em->getRepository(
                'NoticeCommentBundle:Regulation'
            )->find($regulation_id);
            if (!$regulation) {
                $request->getSession()->getFlashBag()->add(
                    'regulation',
                    "Regulation is missing"
                );
                return $this->redirect(
                    $this->generateUrl(
                        'manage_comment_list',
                        array('id' => $request->request->get('regulation'))
                    )
                );
            }
            $em = $this->getDoctrine()->getManager();
            $entity->setRegulation($regulation);
            $entity->setComment($reply);
            $entity->setIsAdmin(1);
            $entity->setUser($this->getUser());

            $em->persist($entity);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'create',
                'create'
            );
            return $this->redirect(
                $this->generateUrl(
                    'manage_comment_list',
                    array('id' => $request->request->get('regulation'))
                )
            );
            $request->getSession()->getFlashBag()->add(
                'regulation',
                "Regulation is missing"
            );
            return $this->redirect(
                $this->generateUrl(
                    'manage_comment_list',
                    array('id' => $request->request->get('regulation'))
                )
            );
        }
    }

    public function publishCommentsAjaxAction(Request $request)
    {
        $comment_id = $request->get('comment_id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $comment_id]);
        if (!$entity) {
            return new Response(json_encode(['success' => false, 'message' => 'Comment Not Found']));
        }
        $children_comments = $em->getRepository(
            Comment::class
        )->FindBy(
            [ 'parentRight' => $comment_id ]
        );
        if ($children_comments) {
            foreach ($children_comments as $replyComment) {
                $replyComment->setHidden(0);
                $em->persist($replyComment);
            }
        }
        $entity->setDeleted(0);
        $entity->setHidden(0);
        $entity->setPublish(1);
        $entity->setPendingReview(0);
        $entity->setIsAbusive(0);
        $em->persist($entity);
        $em->flush();

        return new Response(json_encode(['success' => true, 'message' => 'Comment Publish Successfully']));
    }
    public function unpublishCommentsAjaxAction(Request $request)
    {
        $comment_id = $request->get('comment_id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $comment_id]);
        if (!$entity) {
            return new Response(json_encode(['success' => false, 'message' => 'Comment Not Found']));
        }
        $entity->setDeleted(0);
        $entity->setHidden(0);
        $entity->setPublish(0);
        $entity->setPendingReview(1);
        $em->persist($entity);
        $em->flush();

        return new Response(json_encode(['success' => true, 'message' => 'Comment Unpublished Successfully']));
    }
    public function publishReportedAbusiveCommentsAjaxAction(Request $request)
    {
        $comment_id = $request->get('comment_id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $comment_id]);
        if (!$entity) {
            return new Response(json_encode(['success' => false, 'message' => 'Comment Not Found']));
        }

        $children_comments = $em->getRepository(
            Comment::class
        )->FindBy(
            ['parentRight' => $comment_id]
        );
        if ($children_comments) {
            foreach ($children_comments as $replyComment) {
                $replyComment->setHidden(0);
                $em->persist($replyComment);
            }
        }
        $entity->setDeleted(0);
        $entity->setHidden(0);
        $entity->setPublish(1);
        $entity->setPendingReview(0);
        $entity->setIsAbusive(0);
        $em->persist($entity);
        $em->flush();

        return new Response(json_encode(['success' => true, 'message' => 'Comment Publish Successfully']));
    }
    public function unpublishReportedAbusiveCommentsAjaxAction(Request $request)
    {
        $comment_id = $request->get('comment_id');
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $comment_id]);
        if (!$entity) {
            return new Response(json_encode(['success' => false, 'message' => 'Comment Not Found']));
        }
        
        $children_comments = $em->getRepository(
            Comment::class
        )->FindBy(
            [ 'parentRight' => $comment_id ]
        );
        if ($children_comments) {
            foreach ($children_comments as $replyComment) {
                $replyComment->setHidden(1);
                $em->persist($replyComment);
            }
        }

        $entity->setDeleted(0);
        $entity->setHidden(1);
        $entity->setPublish(1);
        $entity->setPendingReview(0);
        $entity->setIsAbusive(0);
        $em->persist($entity);
        $em->flush();

        return new Response(json_encode(['success' => true, 'message' => 'Comment Unpublished Successfully']));
    }


    public function publishCommentsAction(Request $request, $id, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $id, 'regulation' => $regulation_id]);
        if (!$entity) {
            $request->getSession()->getFlashBag()->add(
                'comment_publish_failure',
                "Unable to publish the comment. Not Found!"
            );
            return $this->redirect($this->generateUrl('un_published_comment_regulation', ['id' => $regulation_id]));
        }

        $children_comments = $em->getRepository(
            Comment::class
        )->FindBy(
            ['parentRight' => $id]
        );
        if ($children_comments) {
            foreach ($children_comments as $replyComment) {
                $replyComment->setHidden(0);
                $em->persist($replyComment);
            }
        }

        $entity->setPublish(1);
        $entity->setHidden(0);
        $entity->setPendingReview(0);
        $entity->setIsAbusive(0);
        $em->persist($entity);
        $em->flush();
        $request->getSession()->getFlashBag()->add(
            'comment_publish_success',
            "Comment published Successfully"
        );
        return $this->redirect($this->generateUrl('un_published_comment_regulation', ['id' => $regulation_id]));
    }

    public function publishMainCommentsAction(Request $request, $id, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $id, 'regulation' => $regulation_id]);
        if (!$entity) {
            $request->getSession()->getFlashBag()->add(
                'comment_publish',
                "Unable to publish the comment. Not Found!"
            );
            return $this->redirect($this->generateUrl('manage_comment_list', ['id' => $regulation_id]));
        }

        $children_comments = $em->getRepository(
            Comment::class
        )->FindBy(
            ['parentRight' => $id]
        );
        if ($children_comments) {
            foreach ($children_comments as $replyComment) {
                $replyComment->setHidden(0);
                $em->persist($replyComment);
            }
        }

        $entity->setPublish(1);
        $entity->setHidden(0);
        $em->persist($entity);
        $em->flush();
        $request->getSession()->getFlashBag()->add(
            'comment_publish',
            "Comment published Successfully"
        );
        return $this->redirect($this->generateUrl('manage_comment_list', ['id' => $regulation_id]));
    }

    public function publishReplyAction(Request $request, $id, $comment_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $id, 'parent' => $comment_id]);
        if (!$entity) {
            $request->getSession()->getFlashBag()->add(
                'reply_publish',
                "Unable to publish the reply. Not Found!"
            );
            return $this->redirect($this->generateUrl('comments_show', ['id' => $comment_id]));
        }

        $children_comments = $em->getRepository(
            Comment::class
        )->FindBy(
            ['parent' => $id]
        );
        if ($children_comments) {
            foreach ($children_comments as $replyComment) {
                $replyComment->setHidden(0);
                $em->persist($replyComment);
            }
        }

        $entity->setPublish(1);
        $entity->setHidden(0);
        $em->persist($entity);
        $em->flush();
        $request->getSession()->getFlashBag()->add(
            'reply_publish',
            "Reply published Successfully"
        );
        return $this->redirect($this->generateUrl('comments_show', ['id' => $comment_id]));
    }

    public function unPublishReplyAction(Request $request, $id, $comment_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->findOneBy(['id' => $id, 'parent' => $comment_id]);
        if (!$entity) {
            $request->getSession()->getFlashBag()->add(
                'reply_publish',
                "Unable to un published the reply. Not Found!"
            );
            return $this->redirect($this->generateUrl('comments_show', ['id' => $comment_id]));
        }

        $children_comments = $em->getRepository(
            Comment::class
        )->FindBy(
            ['parent' => $id]
        );

        if ($children_comments) {
            foreach ($children_comments as $replyComment) {
                $replyComment->setHidden(1);
                $em->persist($replyComment);
            }
        }

        $entity->setPublish(0);
        $entity->setHidden(1);
        $em->persist($entity);
        $em->flush();

        $request->getSession()->getFlashBag()->add(
            'reply_publish',
            "Reply un published Successfully"
        );
        return $this->redirect($this->generateUrl('comments_show', ['id' => $comment_id]));
    }

    public function getRepliesAction(Request $request, $comment_id, $offset = 5)
    {
        $data = [];
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class);
        $replies = $entity->getReplies(
            $comment_id,
            $offset
        );

        foreach ($replies as $reply) {
            $data['replies'][] = array(
                'id' => $reply->getId(),
                'reply' => $reply->getComment(),
                'likes' => $reply->getLikes()
            );
        }
        $data['number_of_replies'] = count($entity->getRepliesCount($comment_id));

        return new Response(json_encode($data));
    }

    /**
     * Creates a form to create a Comment entity.
     *
     * @param Comment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Comment $entity)
    {
        $form = $this->createForm(
            new CommentType(),
            $entity,
            array(
                'action' => $this->generateUrl('comments_create'),
                'method' => 'POST',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Reply to an abusive comments
     *
     * @param Comment $entity The Entity
     *
     * @return mixed
     */
    public function abusiveCommentReplyAction(Request $request, Comment $comment_id)
    {
        if (!$comment_id) {
            return $this->createNotFoundException("Comment Not Found");
        }
        $reply_message = $request->request->get('reply_message');
        if (!$reply_message || empty($reply_message)) {
            $this->get('session')->getFlashBag()->add(
                'comment_unpublish',
                'Reply Unsuccessful'
            );
            return $this->redirect(
                $this->generateUrl(
                    "comments_reported_abusive_manage",
                    [
                        'regulation_id' => $comment_id->getRegulation()->getId()
                    ]
                )
            );
        } else {
            $domain = $_SERVER['HTTP_HOST'];
            $domain = str_replace("www.", "", $domain);
            $message = \Swift_Message::newInstance()
                ->setSubject('Reply From Zambia Notice & Comment Portal')
                ->setFrom($this->container->getParameter('mailer_user'))
                ->setTo($comment_id->getUser()->getEmail())
                ->setContentType("text/html")
                ->setBody(
                    $this->renderView(
                        "NoticeCommentBundle:Comment:abusive_comments_reply.html.twig",
                        array('reply_message' => $reply_message, "comment" => $comment_id)
                    )
                );
            $this->get('mailer')->send($message);
            $this->get('session')->getFlashBag()->add(
                'comment_publish',
                'Reply Successful'
            );
            return $this->redirect(
                $this->generateUrl(
                    "comments_reported_abusive_manage",
                    [
                        'regulation_id' => $comment_id->getRegulation()->getId()
                    ]
                )
            );
        }
    }

    /**
     * Displays a form to create a new Comment entity.
     *
     * @Route("/new", name="comments_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Comment();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    public function showCommentDetailsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = "";
        $entity = $em->getRepository('NoticeCommentBundle:Comment')->find($id);
        if ($entity->getUser()) {
            $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($entity->getId());
        }

        if (!$entity) {
            return $this->render(
                'NoticeCommentBundle:Regulation:show.html.twig',
                array(
                    'entity'      => $entity,
                    'user' => $user
                )
            );
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render(
            'NoticeCommentBundle:Regulation:show.html.twig',
            array(
                'entity'      => $entity,
                'delete_form' => $deleteForm->createView(),
                'user' => $user
            )
        );
    }

    /**
     * Displays a form to edit an existing Comment entity.
     *
     * @Route("/{id}/edit", name="comments_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
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
     * Creates a form to edit a Comment entity.
     *
     * @param Comment $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Comment $entity)
    {
        $form = $this->createForm(
            new CommentType(),
            $entity,
            array(
                'action' => $this->generateUrl(
                    'comments_update',
                    array('id' => $entity->getId())
                ),
                'method' => 'PUT',
            )
        );

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Comment entity.
     *
     * @Route("/{id}", name="comments_update")
     * @Method("PUT")
     * @Template("NoticeCommentBundle:Comment:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('NoticeCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect(
                $this->generateUrl(
                    'comments_edit',
                    array('id' => $id)
                )
            );
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Comment entity.
     *
     * @Route("/{id}", name="comments_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('NoticeCommentBundle:Comment')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException(
                    'Unable to find Comment entity.'
                );
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('comments'));
    }

    public function deleteReply1Action(Request $request, $id, $entity)
    {
        $parent_comment = $entity;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NoticeCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Comment Not Found'
            );
        }
        $em->remove($entity);
        $em->flush();
        $request->getSession()->getFlashBag()->add(
            'delete',
            "delete"
        );
        return $this->redirect($this->generateUrl('comments_show', ['id' => $parent_comment]));
    }

    public function deleteReply2Action(Request $request, $id, $entity)
    {
        $parent_comment = $entity;
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('NoticeCommentBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                'Comment Not Found'
            );
        }
        $em->remove($entity);
        $em->flush();
        $request->getSession()->getFlashBag()->add(
            'delete',
            "delete"
        );
        return $this->redirect($this->generateUrl('manage_comment_list', ['id' => $parent_comment]));
    }

    /**
     * Creates a form to delete a Comment entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comments_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }

    public function likeCommentAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(Comment::class)->find($id);
        $likes = (int) $entity->getLikes();
        $likes += 1;
        $entity->setLikes($likes);
        $em->persist($entity);
        $em->flush();

        return new Response(
            json_encode(['like_count' => $entity->getLikes()])
        );
    }

    /**
     * Generate a unique file name
     *
     * @return string
     */
    private function _generateUniqueFileName()
    {
        return md5(uniqid());
    }

    protected function serialize($data, $format = 'json')
    {
        return $this->get('jms_serializer')
            ->serialize($data, $format);
    }

    protected function createApiResponse($data, $statusCode = 200)
    {
        $json = $this->serialize($data);
        return new Response(
            $json,
            $statusCode,
            array(
                'Content-Type' => 'application/json'
            )
        );
    }

    public function likeCommentApiAction(Request $request, $id)
    {
        $user_id = false;
        $like = new Like();
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_id = $security_context->getToken()->getUser()->getId();
        }
        if (!$user_id) {
            return new Response(
                json_encode(['success' => false, "error" => "You need to login to like a comment"])
            );
        }
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);

        $has_likes = $em->getRepository(Like::class)->findBy(['comment' => $comment, 'regulation' => $comment->getRegulation(), 'user' => $user_id]);
        if ($has_likes) {
            return new Response(json_encode(['success' => false, "error" => "You have already liked this comment"]));
        } else {
            $like->setComment($comment);
            $like->setRegulation($comment->getRegulation());
            $like->setUser($user_id);
            $em->persist($like);
            $em->flush();
            return new Response(
                json_encode(
                    [
                        'success' => true,
                        "like_count" => $this->formatCommentData($comment),
                        'Content-Type' => 'application/json'
                    ]
                )
            );
        }
    }

    public function formatCommentData($comment)
    {
        if ($comment->getComment()) {
            $my_comment = $comment->getComment();
        } else {
            $my_comment = "/uploads/documents/" . $comment->getDocument();
        }
        $commentsArray = array(
            'id' => $comment->getId(),
            'parent' => (!empty($comment->getParent()) ? $comment->getParent()->getId() : null),
            'created' => date_format($comment->getCreateAt(), "Y/m/d H:i:s"),
            'modified' => date_format($comment->getUpdatedAt(), "Y/m/d H:i:s"),
            'content' => $my_comment,
            "creator" => $comment->getUser(),
            "pings" => [],
            'file_url' => (!empty($comment->getDocument()) ? "/uploads/documents/" . $comment->getDocument() : null),
            'file' => $comment->getDocument(),
            'fullname' => (!empty($comment->getUser()) ? $this->getUserDetails($comment->getUser()) : "GUEST USER"),
            "profile_picture_url" => "https://viima-app.s3.amazonaws.com/media/public/defaults/user-icon.png",
            "created_by_admin" => false,
            "created_by_current_user" => $this->isCreatedByCurrentUser(
                $comment->getUser()
            ),
            'upvote_count' => count($comment->getLikes()),
            'user_has_upvoted' => $this->userHasUpvoted($comment),
            "is_new" => false,
            "created_by_admin" => (bool) $comment->getIsAdmin(),
            "marked_abusive" => (bool) $comment->getIsAbusive(),
            "hidden" => (bool) $comment->getHidden()
        );

        return $commentsArray;
    }

    /**
     * Is comment created by current User
     *
     * @param mixed $user // userId
     *
     * @return boolean
     */
    public function isCreatedByCurrentUser($user)
    {
        if ($user) {
            $security_context = $this->container->get('security.context');
            if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user_id = $security_context->getToken()->getUser()->getId();
                return $user_id == (bool) $user;
            }
        }

        return false;
    }

    public function userHasUpvoted($comment)
    {
        $em = $this->getDoctrine()->getManager();
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_id = $security_context->getToken()->getUser()->getId();
            $has_likes = $em->getRepository(Like::class)->findBy(['comment' => $comment, 'regulation' => $comment->getRegulation(), 'user' => $user_id]);
            if ($has_likes) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get User details
     *
     * @param mixed $user //integer
     *
     * @return string
     */
    public function getUserDetails($user)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('WebmastersAfricaUserBundle:User')->find($user);
        if ($user->getAliasName()) {
            return $user->getAliasName();
        }
        return $user->getFullName();
    }

    public function getCanShare(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $result = array();
        $security_context = $this->container->get('security.context');
        if ($security_context->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user_id = $security_context->getToken()->getUser()->getId();
            if ($user_id) {
                $result = array("login" => true, "can_share" => $can_share);
            } else {
                $result = array("login" => false, "message" => "Login/Create Account to share");
            }
        }

        return new Response(json_encode($result));
    }

    public function markAsAbusiveAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository(Comment::class)->find($id);
        if ($comment) {
            $abusive = $comment->getIsAbusive();
            $comment->setIsAbusive($abusive + 1);
            $em->persist($comment);
            $em->flush();
            return new Response(
                json_encode(
                    $this->formatCommentData($comment)
                )
            );
        } else {
            return new Response(
                json_encode(
                    [
                        'success' => false,
                        'message' => 'comment not found'
                    ]
                )
            );
        }
    }

    public function verifyReCaptachaTokenAction(Request $request, $token)
    {
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $data = [
            "secret" => $this->container->getParameter('recaptacha_server_side'),
            "response" => $token,
            "remoteip" => $this->container->get('request')->getClientIp()
        ];

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => "POST",
                'content' => http_build_query($data)
            )
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        return new Response(
            $response
        );
    }

    // using template inheritance to be done in - right now time can't allow
    /**
     * List Comments Entity
     *
     * @Route("/{regulation_id}", name="comments_pending_review")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Regulation:comments_reported_abusive_list.html.twig")
     */
    public function getCommentsReportedAbusiveForRegulationAction(Request $request, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->findBy(['regulation' => $regulation_id, 'isAbusive' => 1]);
        $regulation = $em->getRepository(Regulation::class)->findOneBy(['id' => $regulation_id]);
        if (!$regulation) {
            throw $this->createNotFoundException('This Regulation Not Found');
        }
        return array(
            'comments' => $comments,
            'entity' => $regulation
        );
    }

    /**
     * List Comments Entity
     *
     * @Route("/{regulation_id}", name="comments_pending_review")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Regulation:comments_pending_review_list.html.twig")
     */
    public function getCommentsPendingReviewForRegulationAction(Request $request, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->findBy(['regulation' => $regulation_id, 'pendingReview' => 1]);
        $regulation = $em->getRepository(Regulation::class)->findOneBy(['id' => $regulation_id]);
        if (!$regulation) {
            throw $this->createNotFoundException('This Regulation Not Found');
        }
        return array(
            'comments' => $comments,
            'entity' => $regulation
        );
    }

    /**
     * List Comments Entity
     *
     * @Route("/{regulation_id}", name="comments_pending_review")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Regulation:comments_list_user_deleted.html.twig")
     */
    public function getUserDeletedCommentsAction(Request $request, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->findBy(['regulation' => $regulation_id, 'deleted' => 1]);
        $regulation = $em->getRepository(Regulation::class)->findOneBy(['id' => $regulation_id]);
        if (!$regulation) {
            throw $this->createNotFoundException('This Regulation Not Found');
        }
        return array(
            'comments' => $comments,
            'entity' => $regulation
        );
    }


    /**
     * List Comments Entity
     *
     * @Route("/{regulation_id}", name="comments_pending_review")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Regulation:comments_unpublished_list.html.twig")
     */
    public function unpublishedCommentListAction(Request $request, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->getUnpublishedComments($regulation_id);
        $regulation = $em->getRepository(Regulation::class)->findOneBy(['id' => $regulation_id]);
        if (!$regulation) {
            throw $this->createNotFoundException('This Regulation Not Found');
        }
        return array(
            'comments' => $comments,
            'entity' => $regulation
        );
    }

    public function getDeletedCommentsAction(Request $request, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->findBy(['regulation_id' => $regulation_id, 'deleted' => 1]);

        return array(
            'comments' => $comments
        );
    }

    /**
     * List Comments Entity
     *
     * @Route("/{regulation_id}", name="comments_abusive")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Comment:abusive_comments.html.twig")
     */
    public function getAbusiveCommentsAction()
    {
        $em  = $this->getDoctrine()->getManager();
        $comments = $em->getRepository(Comment::class)->getAbusiveComments();
        return array(
            'comments' => $comments
        );
    }

    public function unpublishCommentsAction(Request $request, $id, $regulation_id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository(
            Comment::class
        )->findOneBy(['id' => $id, 'regulation' => $regulation_id]);
        if (!$entity) {
            $request->getSession()->getFlashBag()->add('comment_publish', "Comment Unpublish Unsuccessful");
            return $this->redirect(
                $this->generateUrl('manage_comment_list', array('id' => $request->request->get('regulation')))
            );
        }
        $repliesExists = $em->getRepository(Comment::class)->findOneBy(['parent' => $entity->getId()]);
        $repliesExists_2 = $em->getRepository(Comment::class)->findOneBy(['parentRight' => $entity->getId()]);
        if ((is_null($entity->getParent())) || ($repliesExists || $repliesExists_2)) {
            $entity->setPublish(0);
            $entity->setHidden(1);
            $entity->setPendingReview(0);
            $em->persist($entity);
            $em->flush();
        } else {
            $entity->setPublish(0);
            $entity->setHidden(1);
            $entity->setPendingReview(0);
            $em->persist($entity);
            $em->flush();
        }
        $children_comments = $em->getRepository(
            Comment::class
        )->FindBy(
            [ 'parentRight' => $id ]
        );
        if ($children_comments) {
            foreach ($children_comments as $replyComment) {
                $replyComment->setHidden(1);
                $em->persist($replyComment);
            }
        }
        $this->get('session')->getFlashBag()->add('comment_publish', 'Comment un published');
        return $this->redirect(
            $this->generateUrl(
                'manage_comment_list',
                array('id' => $regulation_id)
            )
        );
    }

    /**
     * Admin mark abusive
     */
    public function markAbusiveAdminAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        if ($batch_select == 'publish') {
            $entity = new Comment();
            $em = $this->getDoctrine()->getManager();
            foreach ($batch_items as $key) {
                $comment = $em->getRepository(Comment::class)->find($key);
                if (!$comment) {
                    $this->get('session')->getFlashBag()->add('comment_publish', 'Comment Not found');
                } else {
                    $comment->setPublish(1);
                    $comment->setHidden(0);
                    $comment->setIsAbusive(0);
                    $em->persist($comment);
                    $em->flush();
                    $em->clear();
                }
                $this->get('session')->getFlashBag()->add('comment_publish', 'Comment published Successfully');
                return $this->redirect(
                    $this->generateUrl(
                        'comments_abusive_comments',
                        array('id' => $request->request->get('regulation'))
                    )
                );
            }
        } else if ($batch_select == 'unpublish') {
            foreach ($batch_items as $key) {
                $comment = $em->getRepository(Comment::class)->find($key);
                if (!$comment) {
                    // 
                 } else {
                    $repliesExists = $em->getRepository(Comment::class)->findOneBy(['parent' => $comment->getId()]);
                    $repliesExists_2 = $em->getRepository(Comment::class)->findOneBy(['parentRight' => $comment->getId()]);
                    if (is_null($comment->getParent()) || ($repliesExists || $repliesExists_2)) {
                        $comment->setPublish(1);
                        $comment->setHidden(1);
                        $comment->setIsAbusive(0);
                        $em->persist($comment);
                        $em->flush();
                        $em->clear();
                    } else {
                        $comment->setPublish(0);
                        $comment->setHidden(1);
                        $comment->setIsAbusive(0);
                        $em->persist($comment);
                        $em->flush();
                        $em->clear();
                    }
                }
                $this->get('session')->getFlashBag()->add('comment_publish', 'Comment un published Successfully');
                return $this->redirect(
                    $this->generateUrl(
                        'comments_abusive_comments',
                        array('id' => $request->request->get('regulation'))
                    )
                );
            }
            return $this->redirect(
                $this->generateUrl(
                    'comments_abusive_comments',
                    array('id' => $request->request->get('regulation'))
                )
            );
        } else {
            if ($request->request->get('regulation')) {
                $entity = new Comment();
                $em = $this->getDoctrine()->getManager();
                $reply = $request->request->get('reply');
                $regulation = $request->request->get('regulation');

                $comment_id = $request->request->get('comment_id');
                if (empty($reply) || empty($regulation)) {
                    return $this->redirect(
                        $this->generateUrl(
                            'manage_comment_list',
                            array('id' => $regulation)
                        )
                    );
                }

                $comment = $em->getRepository(
                    'NoticeCommentBundle:Comment'
                )->find($comment_id);
                $regulation = $em->getRepository(
                    'NoticeCommentBundle:Regulation'
                )->findOneBy(['id' => $regulation]);
                if ($regulation) {
                    $entity = new Comment();
                    $entity->setRegulation($regulation);
                    $entity->setParent($comment);
                    $entity->setParentRight($comment);
                    $entity->setComment($reply);
                    $entity->setPosition(0);
                    $entity->setIsAdmin(1);
                    $entity->setPublish(1);
                    $entity->setHidden(0);
                    $entity->setUser($this->getUser());
                    $em->persist($entity);
                    $em->flush();
                    $this->get('session')->getFlashBag()->add(
                        'create',
                        'create'
                    );
                    return $this->redirect(
                        $this->generateUrl(
                            'manage_comment_list',
                            array('id' => $request->request->get('regulation'))
                        )
                    );
                } else {
                    $request->getSession()->getFlashBag()->add(
                        'regulation',
                        "Comment Creation Unsuccessful"
                    );
                    return $this->redirect(
                        $this->generateUrl(
                            'manage_comment_list',
                            array('id' => $request->request->get('regulation'))
                        )
                    );
                }
            }
        }
    }

    public function createAbusiveTermsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $words = explode(",", $request->request->get('terms'));
        for ($i = 0; $i < count($words); $i++) {
            $new_word = trim($words[$i]);
            $entity = new AbusiveTerms();
            $entity->setTerm($new_word);
            $em->persist($entity);
            $em->flush();

            $em->clear();
            $entity = new AbusiveTerms();
        }

        $this->get('session')->getFlashBag()->add('create', 'Abusive Comments Created');
        return $this->redirect($this->generateUrl('manageabusiveterms_index'));
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/{regulation_id}", name="comments_create")
     * @Method("POST")
     * @Template("NoticeCommentBundle:Comment:abusive_terms_list.html.twig")
     */
    public function listOfAbusiveTermsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $terms = $em->getRepository(AbusiveTerms::class)->findAll();
        $my_list = [];

        return array(
            'terms' => $terms
        );
    }

    public function listOfAbusiveTermsDeleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $batch_items = $request->request->get("batch_items");
        $batch_select = $request->request->get("batch_select");
        $published = false;
        $unpublished = false;
        $count = 0;
        foreach ($batch_items as $batch_item) {
            if ($batch_select == "delete") {
                $entity = $em->getRepository(AbusiveTerms::class)->find($batch_item);
                $em->remove($entity);
                $count = $count + 1;
            }
        }

        $em->flush();
        $this->get('session')->getFlashBag()->add(
            'delete',
            $count . 'delete'
        );
        return $this->redirect($this->generateUrl('manageabusiveterms_index'));
    }

    public function checkForAbusiveCommentsAction()
    {
        $counter = 0;
        $em = $this->getDoctrine()->getManager();
        $terms = $em->getRepository(AbusiveTerms::class)->findOnyUniqueOnes();
        $values = [];
        foreach ($terms as $array) {
            foreach ($array as $key => $value) {
                $values[$key][] = $value;
            }
        }

        $comments = $em->getRepository(Comment::class)->getCommentsToCheckForAbusiveWords();
        $comments_id = [];
        for ($i = 0; $i < count($values['terms']); $i++) {
            if ($values['terms'][$i]) {
                $query = $em->createQuery(
                    'SELECT c
                        FROM NoticeCommentBundle:Comment c
                        WHERE c.comment like :terms AND c.isAbusive = 0
                        ORDER BY c.id DESC'
                )->setParameter('terms', "%" . strtolower($values['terms'][$i]) . "%");
                $comments = $query->getResult();
                if ($comments) {
                    foreach ($comments as $comment) {
                        $counter += 1;
                        $em = $this->getDoctrine()->getManager();
                        $comment_to_update = $em->getRepository(Comment::class)->find($comment->getId());
                        $comment_to_update->setIsAbusive(1);
                        $comment_to_update->setCheckAbusive(1);
                        $comment_to_update->setHidden(1);
                        $em->persist($comment_to_update);
                        $em->flush();
                        $em->clear();
                        $comment_to_update = "";
                    }
                }
            }
        }
        return new Response(json_encode(['success' => true, 'count' => $counter]));
    }
}

<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Post;
use AppBundle\Form\Type\SearchPostsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ApiPostController
 * @package AppBundle\Controller
 */
class PostController extends AbstractApiController
{
    /**
     * @Route("/posts", name="api_posts")
     * @param Request $request
     * @return JsonResponse
     */
    public function postsAction(Request $request)
    {
        $form = $this->createForm(SearchPostsType::class);;
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
        }
        //If no search parameters given, default values will be returned. In this case, null
        $data = $form->getData();

        $posts = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->search($data['from'], $data['to'], $data['author'])
        ;

        //Date getter will be used instead
        $this->normalizer->setIgnoredAttributes(['datetime']);
        return $this->prepareJsonResponse($posts, 'posts', '"count": '.count($posts));
    }

    /**
     * @Route("/posts/{id}", name="api_post")
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function postAction(Request $request, Post $post)
    {
        //Date getter will be used instead
        $this->normalizer->setIgnoredAttributes(['datetime']);
        return $this->prepareJsonResponse($post, 'post');
    }
}

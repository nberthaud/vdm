<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Post;
use AppBundle\Form\Type\SearchPostsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiPostController
 * @package AppBundle\Controller
 */
class PostController extends AbstractApiController
{
    //http://symfony.dev/app_dev.php/api/posts?from=2012-12-01T12:50:00&to=2012-12-01T12:50:00&author=toto
    /**
     * @Route("/posts", name="api_posts")
     * @param Request $request
     * @return JsonResponse
     */
    public function postsAction(Request $request)
    {
        $data = ['from' => null, 'to' => null, 'author' => null];
        $form = $this->createForm(SearchPostsType::class);;
        $form->submit($request->query->all());

        if ($form->isValid()) {
            $data = $form->getData();
        }

        extract($data);

        $posts = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Post')
            ->search($from, $to, $author)
        ;

        $this->normalizer->setIgnoredAttributes(['datetime']);
        return $this->prepareJsonResponse($posts, 'posts', 'count: '.count($posts));
    }

    /**
     * @Route("/posts/{id}", name="api_post")
     * @param Request $request
     * @param Post $post
     * @return JsonResponse
     */
    public function postAction(Request $request, Post $post)
    {
        $this->normalizer->setIgnoredAttributes(['datetime']);
        return $this->prepareJsonResponse($post, 'post');
    }
}

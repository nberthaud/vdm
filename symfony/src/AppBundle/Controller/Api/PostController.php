<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
//        $form = $this->createFormBuilder(null, ['csrf_protection' => false])
//            ->add('from', DateTimeType::class, ['required' =>  false, 'date_widget' => 'single_text'])
//            ->add('to', DateType::class, ['required' =>  false])
//            ->add('author', TextType::class, ['required' =>  false])
//            ->setMethod('GET')
//            ->getForm();
//
//        //$form->handleRequest($request);
//
//        $form->submit($request->query->all());
//        $form->isValid();
//        $data = $form->getData();
//        //$data = $form->get('author')->getData();
//var_dump($data);
        $posts = $this->getDoctrine()->getRepository('AppBundle:Post')->findAll();
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

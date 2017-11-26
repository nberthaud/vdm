<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Repository\PostRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * API Testing
 *
 * Class PostControllerTest
 * @package Tests\AppBundle\Controller
 */
class PostControllerTest extends WebTestCase
{
    /** @var  PostRepository */
    protected $postRepository;

    /** @var  EntityManager */
    protected $em;

    public function setUp()
    {
        $kernel = static::bootKernel();
        $container = $kernel->getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->postRepository = $this->em->getRepository('AppBundle:Post');

    }

    public function testPostByIdSuccess()
    {
        $post = new Post();
        $post->setDatetime($date = new \DateTime())
             ->setAuthor('UnitTestAuthor')
             ->setContent('Unit Test Content');

        $this->em->persist($post);
        $this->em->flush();

        $client = static::createClient();
        $client->request('GET', '/api/posts/'.$post->getId());

        $data = json_decode($client->getResponse()->getContent());

        //Test the code response
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //Test the json content
        $this->assertEquals('Unit Test Content', $data->post->content);
        $this->assertEquals('UnitTestAuthor', $data->post->author);
        $this->assertEquals($date->format('Y-m-d\TH:i:s\Z'), $data->post->date);
        $this->assertEquals($post->getId(), $data->post->id);

        $post = $this->postRepository->find($post->getId());
        $this->em->remove($post);
        $this->em->flush();

        $client->request('GET', '/api/posts/'.$post->getId());

        //Test non existant id results in 403
        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testSearchPostSuccess()
    {
        $post = new Post();
        $post->setDatetime($date = new \DateTime())
            ->setAuthor('UnitTestAuthor')
            ->setContent('Unit Test Content');

        $this->em->persist($post);
        $this->em->flush();

        $client = static::createClient();
        $client->request('GET', '/api/posts?author=UnitTestAuthor');

        //Test the code response
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        //Test the JSON content
        $data = json_decode($client->getResponse()->getContent());
        $this->assertEquals('Unit Test Content', end($data->posts)->content);

        $count = $data->count;

        $post = $this->postRepository->find($post->getId());
        $this->em->remove($post);
        $this->em->flush();

        $client->request('GET', '/api/posts?author=UnitTestAuthor');
        $data = json_decode($client->getResponse()->getContent());

        //Test the counter
        $this->assertEquals(--$count, $data->count);

        //Test bad search parameters results in bad request code
        $client->request('GET', '/api/posts?autor=UnitTestAuthor');
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }
}

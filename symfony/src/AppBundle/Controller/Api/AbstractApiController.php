<?php

namespace AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class AbstractApiController extends Controller
{
    /**
     * @var ObjectNormalizer
     */
    protected $normalizer;

    /**
     * AbstractApiController constructor.
     */
    public function __construct()
    {
        $this->normalizer = new ObjectNormalizer();
    }


    /**
     * @param $payload
     * @param $payloadContainer
     * @param null $extraData
     * @return JsonResponse
     */
    public function prepareJsonResponse($payload, $payloadContainer, $extraData = null)
    {
        $serializer = new Serializer([$this->normalizer], [new JsonEncoder()]);

        $data = '{"'.$payloadContainer.'": '.
                    $serializer->serialize($payload, 'json').
                    (!!$extraData ? ', '.$extraData : '').
                '}';

        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }
}
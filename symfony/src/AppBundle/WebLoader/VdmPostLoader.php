<?php

namespace AppBundle\WebLoader;

use AppBundle\Entity\Post;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class VdmPostLoader
{
    /**
     * @param $string
     * @return bool|mixed
     */
    public function extractAuthorFromString($string)
    {
        $pattern = '#^.*Par\s(.*)\s-\s.*$#m';
        $matches = [];
        preg_match($pattern, $string, $matches);

        return isset($matches[1]) && !!trim($matches[1]) ? $matches[1] : false;
    }

    /**
     * @param $string
     * @return bool|\DateTime
     */
    public function extractDatetimeFromString($string)
    {
        $pattern = '#^.*\/\s(.*)\s\/.*$#m';
        $matches = [];
        preg_match($pattern, $string, $matches);

        if (!isset($matches[1])) {
            return false;
        }

        $dateFormatter = new \IntlDateFormatter(
            "fr-FR",
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN,
            'EEEE dd MMMM y hh:mm'
        );

        $ts = $dateFormatter->parse($matches[1]);
        $date = new \DateTime();
        $date->setTimestamp($ts);
        $date->setTimezone(new \DateTimeZone("UTC"));

        return $date;
    }


    /**
     * @param int $quantity
     * @return array
     */
    public function getLastsPosts($quantity = 200)
    {
        $client = new \GuzzleHttp\Client();

        $posts = [];
        $pageId = 1;
        $nbPosts = 0;

        while ($nbPosts < $quantity) {
            $res = $client->request('GET', 'http://www.viedemerde.fr/?page=' . $pageId);
            $html = (string)$res->getBody(true);

            $crawler = new DomCrawler($html);
            foreach ($crawler->filter('#content article div.panel-body') as $DOMElement) {
                if ($post = $this->parsePostNode(new DomCrawler($DOMElement))) {
                    $posts[] = $post;
                    if (++$nbPosts == $quantity) {
                        break;
                    }
                }
            }
            $pageId++;
        }

        return $posts;
    }

    /**
     * @param DomCrawler $node
     * @return Post|bool
     */
    public function parsePostNode(DomCrawler $node)
    {
        $contentNode        = $node->filter('div.panel-content p a');
        $authorAndDateNode  = $node->filter('div.text-center');

        if (!!$contentNode->count() && !!$authorAndDateNode->count()) {
            $content    = trim($contentNode->text());
            $author     = $this->extractAuthorFromString($authorAndDateNode->text());
            $date       = $this->extractDatetimeFromString($authorAndDateNode->text());

            if (!$content || !$author || !$date) {
                return false;
            }

            $post = new Post();
            $post->setContent($content);
            $post->setAuthor($author);
            $post->setDatetime($date);

            return $post;
        }

        return false;
    }
}
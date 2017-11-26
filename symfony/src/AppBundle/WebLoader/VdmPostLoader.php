<?php

namespace AppBundle\WebLoader;

use AppBundle\Entity\Post;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

/**
 * Give the ability to get the last 200 posts from the viedemerde website.
 *
 * Class VdmPostLoader
 * @package AppBundle\WebLoader
 */
class VdmPostLoader
{
    /**
     * Extract the author from a viedemerde shaped author/date string.
     *
     * @param $string
     * @return bool|mixed
     */
    public function extractAuthorFromString($string)
    {
        //extract pattern
        $pattern = '#^.*Par\s(.*)\s-\s.*$#m';
        $matches = [];
        preg_match($pattern, $string, $matches);

        return isset($matches[1]) && !!trim($matches[1]) ? $matches[1] : false;
    }

    /**
     * Extract and convert a string to an UTC Datetime from a viedemerde shaped author/date string.
     *
     * @param $string
     * @return bool|\DateTime
     */
    public function extractDatetimeFromString($string)
    {
        //extract pattern
        $pattern = '#^.*\/\s(.*)\s\/.*$#m';
        $matches = [];
        preg_match($pattern, $string, $matches);

        if (!isset($matches[1])) {
            return false;
        }

        //Convert date into a usable format
        $dateFormatter = new \IntlDateFormatter(
            "fr-FR",
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN,
            'EEEE dd MMMM y hh:mm'
        );

        //We need to get the timestamp to instantiate the final datetime object and converting the date to UTC
        $timestamp = $dateFormatter->parse($matches[1]);
        $date = new \DateTime();
        $date->setTimestamp($timestamp);
        $date->setTimezone(new \DateTimeZone("UTC"));

        return $date;
    }


    /**
     * Call the viedemerde website page after page to reach the 200 posts download.
     * An array of posts is returned.
     *
     * @param int $quantity
     * @return array
     */
    public function getLastsPosts($quantity = 200)
    {
        $client = new \GuzzleHttp\Client();

        $posts = [];
        $pageId = 1;
        $nbPosts = 0;

        //For each page posts are extracted until we reach 200
        while ($nbPosts < $quantity) {
            //Url and css selectors should be parameters but are left hardcoded intentionnaly
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
     * Give the ability to extract information from a viedemerde post node
     * A brand new post is returned if success.
     *
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
<?php

namespace Tests\AppBundle\WebLoader;

use AppBundle\WebLoader\VdmPostLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DomCrawler\Crawler;

class VdmPostLoaderTest extends TestCase
{
    /** @var VdmPostLoader $vdmPostLoader */
    protected $vdmPostLoader;

    public function setUp()
    {
        $this->vdmPostLoader = new VdmPostLoader();
    }

    public function testNumberOfPostIsValid()
    {
        $posts = $this->vdmPostLoader->getLastsPosts();
        $this->assertEquals(200, count($posts));
    }

    public function testAuthorExtractionFailed()
    {
        $string = 'vsdvsdv';
        $author = $this->vdmPostLoader->extractAuthorFromString($string);
        $this->assertFalse($author);
    }

    public function testAuthorExtractionSuccess()
    {
        $string = 'Par Striff -  / mercredi 22 novembre 2017 20:00 /';
        $author = $this->vdmPostLoader->extractAuthorFromString($string);
        $this->assertSame($author, 'Striff');
    }

    public function testExtractedDateIsUTCSuccess()
    {
        $string = 'Par Striff -  / mercredi 22 novembre 2017 20:00 /';
        $date   = $this->vdmPostLoader->extractDatetimeFromString($string);
        $this->assertSame($date->getTimezone()->getName(), 'UTC');
    }

    public function testExtractedDateSuccess()
    {
        $string = 'Par Striff -  / mercredi 22 novembre 2017 20:00 /';
        $date   = $this->vdmPostLoader->extractDatetimeFromString($string);
        $this->assertSame($date->format('Y-m-d H:i:s'), '2017-11-22 19:00:00');
    }

    public function testDomPostNodeParsedSuccess()
    {
        $html       = '<div class="title clearfix topbar"> <div class="pull-left logo"> <span class="icon-logo_vdm"></span> News </div> <div class="pull-right"> <span id="twitter_252579" class="twitter sharrre btn btn-default btn-xs" data-text="#vdm Farfouillez dans vos placards, vous pourriez peut-être retrouver une ancienne couverture ayant appartenu..." data-url="http://bit.ly/2zr2W3t" data-count="true"></span> <span id="facebook_252579" class="facebook sharrre btn btn-default btn-xs" data-text="Farfouillez dans vos placards, vous pourriez peut-être retrouver une ancienne couverture ayant appartenu à votre grand-mère... qui peut valoir de l\'or !" data-url="http://bit.ly/2zr2W3t" data-count="true"></span> <a class="btn btn-default btn-xs bookmark" href="javascript:void(0);" data-route="/api/v2/article/252579/bookmark"> <i class="fa fa-star" aria-hidden="true"></i> </a> </div> </div> <div class="panel-content"> <div class="row"> <div class="col-md-4"> <figure class="thumbnail block"><a href="/article/il-pensait-que-sa-vie-etait-une-vdm-puis-il-est-passe-de-sdf-a-millionnaire-par-le-plus-heureux-des-_252579.html"> <img class="lazyload img-responsive module-5 fullwidth" data-src="/img/782dbd7a24b4aad5ec385817526793b0.jpeg/small" alt=""></a> </figure> </div> <div class="col-md-8"> <a href="/article/il-pensait-que-sa-vie-etait-une-vdm-puis-il-est-passe-de-sdf-a-millionnaire-par-le-plus-heureux-des-_252579.html"> <h2 class="block">Il pensait que sa vie était une VDM, puis il est passé de SDF à millionnaire… par le plus heureux des hasards ! </h2> </a> <p class="block"> <a href="/article/il-pensait-que-sa-vie-etait-une-vdm-puis-il-est-passe-de-sdf-a-millionnaire-par-le-plus-heureux-des-_252579.html"> Farfouillez dans vos placards, vous pourriez peut-être retrouver une ancienne couverture ayant appartenu à votre grand-mère... qui... </a> </p> </div> </div> </div> <div class="text-center" style="background-color: #eee; color: #999; font-weight: lighter; font-size: 12px; padding: 5px;"> Par Anonyme - <i class="fa fa-female" aria-hidden="true"></i> / mercredi 22 novembre 2017 21:30 / France - Paris</div>';
        $crawler    = new Crawler($html);
        $post       = $this->vdmPostLoader->parsePostNode($crawler);
        $this->assertInstanceOf('AppBundle\Entity\Post', $post);
    }
}

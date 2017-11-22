<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\Table(name="post")
 */
class Post
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5000)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="text")
     */
    private $author;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param mixed $datetime
     * @return Post
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
        return $this;
    }

    public function getDate()
    {
        return $this->getDatetime()->format('Y-m-dTH:i:s\Z');
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     * @return Post
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }
}
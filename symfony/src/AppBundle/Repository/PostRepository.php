<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class PostRepository
 * @package AppBundle\Repository
 */
class PostRepository extends EntityRepository
{
    /**
     * @return mixed
     */
    public function clearAllPosts()
    {
        return $this
            ->getEntityManager()
            ->createQuery('DELETE AppBundle:Post p')
            ->execute()
        ;
    }
}
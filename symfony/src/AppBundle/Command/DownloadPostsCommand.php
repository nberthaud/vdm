<?php
namespace AppBundle\Command;

use AppBundle\Manager\PostManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadPostsCommand extends Command
{
    /** @var PostManager */
    private $postManager;

    /** @var EntityManager */
    private $em;

    public function __construct(PostManager $postManager, EntityManager $em)
    {
        $this->postManager = $postManager;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:posts:refresh')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to download the last 200 viedemerde posts...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Posts Refresher',
            '===============',
            '',
        ]);

        $posts = $this->postManager->getLastsPosts();

        $this->em->getRepository('AppBundle:Post')
                 ->clearAllPosts();

        foreach ($posts as $post) {
            $this->em->persist($post);
        }

        $this->em->flush();
    }
}
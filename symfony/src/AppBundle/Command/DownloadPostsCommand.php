<?php
namespace AppBundle\Command;

use AppBundle\Manager\PostManager;
use AppBundle\WebLoader\VdmPostLoader;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadPostsCommand extends Command
{
    /** @var VdmPostLoader */
    private $vdmPostLoader;

    /** @var EntityManager */
    private $em;

    public function __construct(VdmPostLoader $vdmPostLoader, EntityManager $em)
    {
        $this->vdmPostLoader = $vdmPostLoader;
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
            '',
            '===============',
            'Posts Refresher',
            '===============',
            '',
        ]);

        $output->writeln([
            'Downloading posts from viedemerde',
            'Please wait...',
            '',
        ]);

        $posts = $this->vdmPostLoader->getLastsPosts();

        $output->writeln([
            'Now, cleaning the database for new batch of posts',
            '',
        ]);

        $this->em->getRepository('AppBundle:Post')
                 ->clearAllPosts();

        $output->writeln([
            'Inserting posts into database',
            '',
        ]);

        foreach ($posts as $post) {
            $this->em->persist($post);
        }

        $output->writeln([
            'Flushing...',
            '',
        ]);

        $this->em->flush();

        $output->writeln([
            'Process cleared',
            '',
        ]);
    }
}
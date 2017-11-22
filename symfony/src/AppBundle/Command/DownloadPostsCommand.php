<?php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadPostsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:posts:download')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to download the last 200 viedemerde posts...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Posts Downloader',
            '================',
            '',
        ]);
    }
}
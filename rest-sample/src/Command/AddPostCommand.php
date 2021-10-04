<?php

namespace App\Command;

use App\Entity\PostFactory;
use App\Repository\PostRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-post',
    description: 'Add a short description for your command',
)]
class AddPostCommand extends Command
{


    public function __construct(private PostRepository $posts)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('title', InputArgument::REQUIRED, 'Title of a post')
            ->addArgument('content', InputArgument::REQUIRED, 'Content of a post')
            //->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $title = $input->getArgument('title');

        if ($title) {
            $io->note(sprintf('Title: %s', $title));
        }

        $content = $input->getArgument('content');

        if ($content) {
            $io->note(sprintf('Content: %s', $content));
        }

        $entity = PostFactory::create($title, $content);
        $this ->posts->getEntityManager()->persist($entity);
        $this ->posts->getEntityManager()->flush();

//        if ($input->getOption('option1')) {
//            // ...
//        }


        $io->success('Post is saved: '.$entity);

        return Command::SUCCESS;
    }
}

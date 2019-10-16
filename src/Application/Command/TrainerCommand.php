<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class TrainerCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('console:task:run')
            ->setDescription('Run some task of trainer by ID.')
            ->addArgument('id', InputArgument::REQUIRED)
            ->addArgument('task', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument('id');
        $task = $input->getArgument('task');
        $output->writeln(
            sprintf('Номер ID тренера - %s и задача - %s', $id, $task ?? 'default')
        );
    }
}
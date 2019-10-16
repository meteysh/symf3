<?php

declare(strict_types=1);

namespace App\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Config\Definition\Exception\Exception;
use App\Controller\Lesson;
use App\Controller\Training;
use App\Controller\TaskTrainer;

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
        $taskDo = new Lesson();
        $id = $input->getArgument('id');
        $task = $input->getArgument('task');

        $output->writeln(
            $taskDo->showTask()
        );
    }
}

//Можно принимать аргументы - ID и имя задачи командой: php bin/console console:task:run 5 lesson
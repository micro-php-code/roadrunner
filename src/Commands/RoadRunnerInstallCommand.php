<?php

declare(strict_types=1);

namespace MicroPHP\RoadRunner\Commands;

use MicroPHP\Framework\Attribute\Attributes\CMD;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;

#[CMD]
class RoadRunnerInstallCommand extends Command
{
    /** @noinspection PhpPossiblePolymorphicInvocationInspection */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(['./vendor/bin/rr', 'get-binary'], timeout: 120);
        $process->mustRun(function ($type, $buffer) use ($output) {
            $output->writeln($buffer);
        });

        $helper = $this->getHelper('question');

        $question = new Question('Add .rr.yaml config file ? (y/n) ', 'y');

        $answer = $helper->ask($input, $output, $question);

        if ('y' == $answer) {
            copy(__DIR__ . '/../.rr.yaml', './.rr.yaml');
        }

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setName('roadrunner:install')
            ->setDescription('Install last version of roadrunner');
    }
}

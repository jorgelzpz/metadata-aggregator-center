<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('Metadata center', '0.9');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);

$console
    ->register('sets:list')
    ->setDescription('List configured metadata sets')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        $sets = $app['sets.repository']->findAll();

        foreach ($sets as $set) {
            $line = sprintf(
                "%s\t%s\t%s",
                $set->getName(),
                $set->getUrl(),
                $set->getFilter()
            );
            $output->writeln($line);
        }

    })
;

$console->add($app['command.sets.generate']);

return $console;

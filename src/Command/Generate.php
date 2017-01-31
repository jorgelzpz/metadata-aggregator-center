<?php

namespace RedIRIS\MetadataCenter\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use RedIRIS\MetadataCenter\PyFF\Configuration;
use RedIRIS\MetadataCenter\PyFF\Settings as PyFFSettings;
use RedIRIS\MetadataCenter\Repository\Sets;


class Generate extends Command
{
    protected $sets_repository;

    protected $pyff_settings;

    /**
     * @param Sets $sets_repository
     * @param PyFFSettings $pyff_settings
     */
    public function __construct(Sets $sets_repository, PyFFSettings $pyff_settings)
    {
        parent::__construct();
        $this->sets_repository = $sets_repository;
        $this->pyff_settings = $pyff_settings;
    }

    protected function configure()
    {
        $this->setName('sets:generate');
        $this->setDescription('Generate configured metadata sets');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sets = $this->sets_repository->findAll();
        $fs = new Filesystem();

        foreach ($sets as $set) {
            $config = new Configuration(
                $set->getUrl(),
                $set->getFilter()
            );

            $pyff_config_file = $this->pyff_settings->getConfigDir()
                . '/' . $set->getName(). '.fd';
            $pyff_result = $fs->tempnam($this->pyff_settings->getOutputDir(), 'pyff');

            $output->writeln(
                '<info>Generating set ' . $set->getName(). '</info>'
            );
            $config_contents = $config->generate($pyff_result);
            file_put_contents($pyff_config_file, $config_contents);

            $pyff_full_command = $this->pyff_settings->getCommand()
                . ' ' . $pyff_config_file;
            $process = new Process(
                $pyff_full_command,
                $this->pyff_settings->getCacheDir()
            );
            $process->setTimeout($this->pyff_settings->getTimeout());
            $process->run();

            $success = true;

            if (!$process->isSuccessful()) {
                $output->writeln(
                    '<error>Error running '. $pyff_full_command  .'</error>',
                    OutputInterface::VERBOSITY_QUIET
                );
                $output->writeln(
                    '<error>' . $process->getOutput() . "\n" .
                    $process->getErrorOutput() . '</error>',
                    OutputInterface::VERBOSITY_QUIET
                );

                $success = false;
            }

            // pyFF found 0 elements
            if ($process->isSuccessful() &&
                    strstr($process->getErrorOutput(), 'ERROR:root:empty select - stop')) {
                $output->writeln(
                    '<error>Warning! Empty set for ' . $set->getName() . '</error>',
                    OutputInterface::VERBOSITY_QUIET
                );

                $success = false;
            }

            if ($success === true) {
                // Dump XML contents to the actual metadata file
                $xml_file = sprintf('%s/%s.xml',
                    $this->pyff_settings->getOutputDir(),
                    $set->getName()
                );
                $fs->dumpFile($xml_file, file_get_contents($pyff_result));
            }

            $fs->remove($pyff_result);

            // pyFF output
            $entity_ids = $this->getEntityIdsFromOutput($process->getOutput());
        }
    }

    protected function getEntityIdsFromOutput($output)
    {
        $result = [];

        foreach (preg_split("/((\r?\n)|(\r\n?))/", $output) as $line) {
            if ($line[0] === '[' || empty($line)) {
                continue;
            }

            $result[] = $line;
        }

        return $result;
    }
}


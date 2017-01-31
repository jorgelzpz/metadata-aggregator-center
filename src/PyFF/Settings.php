<?php

namespace RedIRIS\MetadataCenter\PyFF;


class Settings
{
  protected $command;

  protected $timeout;

  protected $config_dir;

  protected $cache_dir;

  protected $output_dir;

  /**
     * @param string $command
     * @param int $timeout
     * @param string $config_dir
     * @param string $cache_dir
     * @param string $output_dir
   */
  public function __construct($command, $timeout, $config_dir, $cache_dir, $output_dir)
  {
        $this->command = $command;
        $this->timeout = $timeout;
        $this->config_dir = $config_dir;
        $this->cache_dir = $cache_dir;
        $this->output_dir = $output_dir;
  }

  public function getCommand()
  {
    return $this->command;
  }

  public function getTimeout()
  {
    return $this->timeout;
  }


  public function getConfigDir()
  {
    return $this->config_dir;
  }

  public function getCacheDir()
  {
    return $this->cache_dir;
  }

  public function getOutputDir()
  {
    return $this->output_dir;
  }

}

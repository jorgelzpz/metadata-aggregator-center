<?php

namespace RedIRIS\MetadataCenter\PyFF;

class Configuration
{
    /** @var string */
    private $url;

    /** @var string */
    private $certificate;

    /** @var string */
    private $filter;

    /** @var string */
    private $xslt;

    /** @var bool */
    private $dump_info;

    /**
     * Builds a new pyFF configuration
     *
     * @param string $url
     * @param string $filter
     * @param bool $dump_info
     */
    public function __construct($url, $filter, $dump_info = true)
    {
        $this->url = $url;
        $this->filter = $filter;
        $this->certificate = '';
        $this->xslt = '';
        $this->dump_info = true;
    }

    /*
     * Sets a certificate for validation
     *
     * @param string $certificate
     */
    public function setCertificate($certificate)
    {
        $this->certificate = $certificate;
    }

    /*
     * Sets an XSLT to apply
     *
     * @param string $xslt
     */
    public function setXslt($xslt)
    {
        $this->xslt = $xslt;
    }

    public function generate(
        $output,
        $name = null,
        $cache_duration = 'PT5H',
        $valid_until = 'P10D'
    )
    {
        if ($name === null) {
            $name = $this->url;
        }

        $result = <<<CFG
- load:
  - $this->url $this->certificate
- select: "$this->url!//$this->filter"

CFG;
        if (!empty($this->xslt)) {
            $result .= <<<CFG
- xslt:
    stylesheet: $this->xslt

CFG;
        }

        $result .= <<<CFG
- finalize:
    Name: $name
    cacheDuration: $cache_duration
- publish: $output

CFG;
        if ($this->dump_info === true) {
        $result .= <<<CFG
- info
CFG;
        }

        return $result;
    }
}

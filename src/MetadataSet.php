<?php

namespace RedIRIS\MetadataCenter;

class MetadataSet
{
    protected $id;

    protected $url;

    protected $filter;

    protected $name;

    /**
     * @param mixed $url
     * @param mixed $filter
     * @param mixed $name
     */
    public function __construct($url = '', $filter = '', $name = '')
    {
        $this->url = $url;
        $this->filter = $filter;
        $this->name = $name;
    }

    /**
     * Getter for id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter for id
     *
     * @param  int $id
     */
    public function setId( $id)
    {
        $this->id = $id;
    }

    /**
     * Getter for url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Setter for url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Getter for filter
     *
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Setter for filter
     *
     * @param string $filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    }

    /**
     * Getter for name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Setter for name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}

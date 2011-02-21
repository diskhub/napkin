<?php
namespace HypoConf\ConfigScopes;

use HypoConf;

//use \Tools\ArrayTools;
//use \Tools\LogCLI;

abstract class Parser
{
    protected $parsers = array();
    public function GetSubParsers()
    {
        return $this->parsers;
    }
    abstract public function __construct(array &$templates);
}
<?php

namespace Tools;

use \Symfony\Component\Yaml\Yaml;
use \Symfony\Component\Yaml\Dumper;

class FileOperation
{
    public static function getAllFilesByExtension($path='.', $extension = 'yml')
    {
        $Directory = new \RecursiveDirectoryIterator($path);
    	$Iterator = new \RecursiveIteratorIterator($Directory);
    	$Regex = new \RegexIterator($Iterator, '/^.+\.'.$extension.'$/i', \RecursiveRegexIterator::GET_MATCH);
    	$Files = array();
    
    	foreach ($Regex as $File)
    	{
    	        $Files[] = $File[0];
    	}
    	sort($Files, SORT_LOCALE_STRING);
    	return $Files;
    }
    
    public static function ToYAMLFile($array, $stdout = false, $file="tmp.yml")
    {
            $dumper = new Dumper();
            $yaml = $dumper->dump($array, 6);
            if ($stdout === false) file_put_contents($file, $yaml);
            else echo PHP_EOL.$yaml;
    }
    
    public static function pathinfo_utf($path) 
    { 
        if (strpos($path, '/') !== false) $basename = end(explode('/', $path)); 
        elseif (strpos($path, '\\') !== false) $basename = end(explode('\\', $path)); 
        else return false; 
        if (empty($basename)) return false; 
        
        $dirname = substr($path, 0, strlen($path) - strlen($basename) - 1); 
        
        if (strpos($basename, '.') !== false) 
        { 
            $extension = end(explode('.', $path)); 
            $filename = substr($basename, 0, strlen($basename) - strlen($extension) - 1); 
        } 
        else 
        { 
            $extension = ''; 
            $filename = $basename; 
        } 
        
        return array 
        ( 
            'dirname' => $dirname, 
            'basename' => $basename, 
            'extension' => $extension, 
            'filename' => $filename 
        );
    }
}
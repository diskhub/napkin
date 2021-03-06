<?php

namespace Tools;

//use \Tools\LogCLI;

class ArrayTools
{
    public static function dearraizeIfNotRequired($values)
    {
        if(is_array($values))
            return (count($values) === 1) ? $values[0] : $values;
        else return $values;
    }

    public static function isAssoc(array $array) //http://stackoverflow.com/questions/173400/php-arrays-a-good-way-to-check-if-an-array-is-associative-or-sequential
    {
        return (bool)count(array_filter(array_keys($array), 'is_string'));
    }

    public static function isIterativeScope($configData)
    {
        if(is_array($configData))
        {
            if(!self::isAssoc($configData))
            {
                if(is_array(current($configData))) return true;
            }
        }
        return false;
    }

    public static function translateToIterativeScope($name, $configData)
    {
        $output = array();
        if(is_array($configData))
        {
            //$name = current(array_keys($configData));
            foreach($configData as $data)
            {
                $output[][$name] = $data;
            }
        }
        else
        {
            $output[][$name] = $configData;
        }
        //return array($name => $output);
        return $output;
    }

    public static function translateFromIterativeScope(array $configData)
    {
        $output = array();
        foreach($configData as $entry)
        {
            foreach($entry as $data)
            {
                $output[] = $data;
            }
        }
        return $output;
    }

    public static function &accessArrayElementByPath(&$arr, $path = null, $checkEmpty = false, $emptyResponse = null) //$trimPath=0
    {
        // Check path
        if (!$path) user_error("Missing array path for array", E_USER_WARNING);
        
        // Vars
        $pathElements = explode('/', $path);
        $path =& $arr;
        
        // Go through path elements
        foreach ($pathElements as $e)
        {
            // Check set
            if (!isset($path[$e])) return $emptyResponse;
            
            // Check empty
            if ($checkEmpty and empty($path[$e])) return $emptyResponse;
            
            // Update path
            $path =& $path[$e];
        }
        
        // Everything checked out, return value
        return $path;
    }
    
    public static function &mergeArrayElementByPath(&$arr, $path = null, $value = null, $skipN = 0, $noOverride = false) //$trimPath=0
    {
        // Check path
        if (!$path) user_error("Missing array path for array", E_USER_WARNING);
        
        // Vars
        $pathElements = explode('/', $path);
        $path =& $arr;
        
        if($skipN > 0) $pathElements = array_splice($pathElements, 0, count($pathElements)-$skipN);
        
        // Go through path elements
        foreach ($pathElements as $e)
        {
            // Check set
            if (!isset($path[$e])) $path[$e] = array();
            
            // Update path
            $path =& $path[$e];
        }
        $path = (is_array($value))
                ? (($noOverride === false) ? self::MergeArrays($path, $value) : array_merge_recursive((array)$path, (array)$value)) 
                : $value;
        
        // Everything checked out, return value
        return $path;
    }
    
    public static function &replaceArrayElementByPath(&$arr, $path = null, $value = null, $skipN = 0)
    {
        // Check path
        if (!$path) user_error("Missing array path for array", E_USER_WARNING);
        
        // Vars
        $pathElements = explode('/', $path);
        $path =& $arr;
        
        if($skipN > 0) $pathElements = array_splice($pathElements, 0, count($pathElements)-$skipN);
        
        // Go through path elements
        foreach ($pathElements as $e)
        {
            // Check set
            if (!isset($path[$e])) $path[$e] = array();
            
            // Update path
            $path =& $path[$e];
        }
        $path = $value;
        
        // Everything checked out, return value
        return $path;
    }
    
    public static function &unsetArrayElementByPath(&$arr, $path = null, $skipN = 0)
    {
        // Check path
        if (!$path) user_error("Missing array path for array", E_USER_WARNING);
        
        // Vars
        $pathElementsAll = explode('/', $path);
        $path =& $arr;
                
        $pathElements = array_splice($pathElementsAll, 0, count($pathElementsAll)-1-$skipN);
        //var_dump($pathElements);
        // Go through path elements
        foreach ($pathElements as $e)
        {
            // Check set
            if (!isset($path[$e])) return $path; //$path[$e] = array();
            //var_dump($path);
            // Update path
            $path =& $path[$e];
        }
        
        //var_dump($path);
        //var_dump(end($pathElementsAll));
        unset($path[end($pathElementsAll)]);
        //var_dump($path);
        // Everything checked out, return value
        return $path;
    }
    
    public static function GetMultiDimentionalElements(&$ArrayInput, $withChildren = false, $onlyValues = true)
    {
        //if(is_array($ArrayInput) && !is_object($ArrayInput))
        //{
        $recursive = new \ParentIterator(new \RecursiveArrayiterator($ArrayInput));
        $iterator  = new \RecursiveIteratorIterator($recursive, \RecursiveIteratorIterator::SELF_FIRST);
        $elements = array();
        foreach ($iterator as $item)
        {
            // Build path from "parent" array keys
            for ($path = "", $i = 0; $i <= $iterator->getDepth(); $i++) {
                $path .= "/" . $iterator->getSubIterator($i)->key();
            }
            if($withChildren === true)
            {
                foreach($iterator->current() as $name => $value)
                {
                    if($onlyValues === true && is_array($value))
                    {
                        $continue = false;
                    }
                    else
                    {
                        $continue = true;
                    }
                    // TODO: for now, this works, but maybe determining by the array key isn't the brightest idea
                    if(is_string($name) && $continue === true)
                    {
                        $subpath = $path . "/" . $name;
                        $elements[] = ltrim($subpath, "/");
                    }
                    elseif(is_numeric($name) && $continue === true)
                    {
                        $subpath = $path . "/" . $value;
                        $elements[] = ltrim($subpath, "/");
                    }
                }
            }
            else
            {
                // Output depth and "path"
                //printf("%d %s\n", $iterator->getDepth() + 1, ltrim($path, "/"));
                $elements[] = ltrim($path, "/");
            }
        }
        return $elements;
        //}
    }
    
    /*
    public static function GetMultiDimentionalElementsWithChildren(&$ArrayInput)
    {
        //if(is_array($ArrayInput) && !is_object($ArrayInput))
        //{
        $recursive = new \ParentIterator(new \RecursiveArrayIterator($ArrayInput));
        $iterator  = new \RecursiveIteratorIterator($recursive, \RecursiveIteratorIterator::SELF_FIRST);
        $elements = array();
        foreach ($iterator as $item)
        {
            // Build path from "parent" array keys
            for ($path = "", $i = 0; $i <= $iterator->getDepth(); $i++) {
                
                $path .= "/" . $iterator->getSubIterator($i)->key();
            }
            foreach($iterator->current() as $name => $value)
            {
//                if(!is_array($value))
//                if(is_string($value))
                if(is_string($name))
                {
                    $subpath = $path . "/" . $name;
//                    $subpath = $path . "/" . $value;
                    $elements[] = ltrim($subpath, "/");
                }
            }
        }
        return $elements;
        //}
    }
    */

    /**
     * Merges any number of arrays of any dimensions, the later overwriting
     * previous keys, unless the key is numeric, in whitch case, duplicated
     * values will not be added.
     *
     * The arrays to be merged are passed as arguments to the function.
     *
     * @access public
     * @return array Resulting array, once all have been merged
     */



    /**
     * Safe merge of arrays.
     *
     * @static
     * @param $arr1
     * @param $arr2
     * @return array
     */
    public static function MergeArrays($arr1, $arr2) 
    {
        // Holds all the arrays passed
        //$params = & func_get_args ();
        if(!is_array($arr1))
        {
            $arr1 = (array)$arr1;
            if(!is_array($arr2)) 
            {
                $arr2 = array();
            }
            else return $arr2;
        }
        
        $params = array($arr1, $arr2);
       
        // First array is used as the base, everything else overwrites on it
        $return = array_shift ( $params );
       
        // Merge all arrays on the first array
        foreach ( $params as $array ) {
            /*
             * if we have a numeric array, let's reset it, $array has priority over $return
             * and we would be replacing the values anyway (so we don't want any leftovers)
             */
            if(!self::isAssoc($array) && count($return) > count($array))
                $return = array();

            foreach ( $array as $key => $value ) {
//                if (isset ( $return [$key] ) && is_numeric($key) && !is_array($value)) //
//                {
//                    LogCLI::MessageResult('Key: '.LogCLI::BLUE.$key.LogCLI::RESET, 2, LogCLI::INFO);
//                    LogCLI::MessageResult('Key: '.LogCLI::BLUE.$value.LogCLI::RESET, 2, LogCLI::INFO);
//                }
                // Numeric keyed values are added (unless already there)
                //if (is_numeric ( $key ) && (! in_array ( $value, $return ))) {
                /*
                    if (is_array ( $value ) && isset($return[$key])) {
                        $return [] = self::MergeArrays ( $return[$key], $value ); // double $$key ?
                    } else {
                        $return [] = $value;
                    }
                */ 
                // String keyed values are replaced
                //} else {
                    if (isset ( $return [$key] ) && is_array ( $value ) && is_array ( $return [$key] )) {
                        $return [$key] = self::MergeArrays ( $return[$key], $value ); // double $$key ?
                    } else {
                        $return [$key] = $value;
                    }
                //}
            }
        }
       
        return $return;
    }
    
    public static function max_key($array) {
        foreach ($array as $key => $val)
        {
            if ($val == max($array)) return $key;
        }
    }
    
    public static function TraverseTreeWithPath(array &$paths, $lookForPath = 'somepath/something')
    {
        LogCLI::Message('Traversing definition tree in search for the partial path: '.LogCLI::YELLOW.$lookForPath.LogCLI::RESET, 6);
        
        $matches = array();
        $matchAccuracy = array();
        $lookForPathParts = array_reverse(explode('/', $lookForPath));
        
//        foreach(self::GetMultiDimentionalElementsWithChildren($paths) as $num => $path)
        foreach(self::GetMultiDimentionalElements($paths, true) as $num => $path)
        {
            if (strpos($path, $lookForPath) !== false)
            {
                LogCLI::MessageResult('Match found at: '.LogCLI::BLUE.$path.LogCLI::RESET, 2, LogCLI::INFO);
                $thisPathParts = array_reverse(explode('/', $path));
                foreach($lookForPathParts as $i => &$pathPart)
                {
                    if($thisPathParts[$i] == $pathPart)
                    $matchAccuracy[$num] = $i+1;
                }
                $matches[$num] = $path;
            }
        }
        
        /*
        if(empty($matches)) LogCLI::MessageResult(LogCLI::YELLOW.'No matches found for partial path: '.LogCLI::BLUE.$lookForPath.LogCLI::RESET, 2, LogCLI::INFO);
        else
        {
            LogCLI::MessageResult('Best match found at: '.LogCLI::BLUE.$matches[self::max_key($matchAccuracy)].LogCLI::RESET, 2, LogCLI::INFO);
        }
        */
        
        LogCLI::Result(LogCLI::INFO);
        //if(!empty($matches)) 
        if(!empty($matchAccuracy))
        return array('all' => $matches, 'best' => $matches[self::max_key($matchAccuracy)]);
        else return false;
    }
    
    public static function TraverseTree(array &$paths, $lookFor = 'defaults')
    {
        LogCLI::Message('Traversing definition tree in search for: '.LogCLI::YELLOW.$lookFor.LogCLI::RESET, 6);
        
        $matches = array();
        foreach(self::GetMultiDimentionalElements($paths) as $path)
        {
            //LogCLI::MessageResult('Element: '.LogCLI::BLUE.$path.LogCLI::RESET, 6, LogCLI::INFO);
            $pathElements = explode('/', $path);
            //$lastElement = end($pathElements);
            //if(stripos($path, $lookFor) !== FALSE)
            if(end($pathElements) == $lookFor)
            {
                LogCLI::MessageResult('Match found at: '.LogCLI::BLUE.$path.LogCLI::RESET, 2, LogCLI::INFO);
                $matches[] = $path;
                /*
                this code is good, don't remove
                if(!is_object(self::accessArrayElementByPath($this->nginx, $fullpath)))
                {
                    $last = StringTools::ReturnLastBit($fullpath);
                    $fullpath = StringTools::DropLastBit($fullpath, 1);
                    $fullpath = StringTools::AddBit($fullpath, $this->foreignDefinitions[$last]);
                    LogCLI::MessageResult('Common config detected! Defined by: '.LogCLI::BLUE.$fullpath.LogCLI::RESET, 5, LogCLI::INFO);
                }
                */
            }
        }
        
        LogCLI::Result(LogCLI::INFO);
        
        if(!empty($matches)) return $matches;
        else return false;
    }
    
    /*
    public static function MergeArrays($Arr1, $Arr2)
    {
        //var_dump($Arr1);
        //debug_print_backtrace();
        //$Arr1 = array_merge_recursive($Arr1, $Arr2);
        
        foreach($Arr2 as $key => $Value)
        {
    
            if(array_key_exists($key, $Arr1) && is_array($Value))
              $Arr1[$key] = self::MergeArrays($Arr1[$key], $Arr2[$key]);
            
            else
              $Arr1[$key] = $Value;
        }
        
        return $Arr1;
    }
    */
    
    /**
     * Merges any number of arrays / parameters recursively, replacing
     * entries with string keys with values from latter arrays.
     * If the entry or the next value to be assigned is an array, then it
     * automagically treats both arguments as an array.
     * Numeric entries are appended, not replaced, but only if they are
     * unique
     *
     * calling: result = array_merge_recursive_distinct(a1, a2, ... aN)
    **/
    /*
    public static function MergeArrays() {
        //$numeric = 0;
      $arrays = func_get_args();
      $base = array_shift($arrays);
      if(!is_array($base)) $base = empty($base) ? array() : array($base);
      foreach($arrays as $append) {
        if(!is_array($append)) $append = array($append);
        foreach($append as $key => $value) {
          if(!array_key_exists($key, $base) and !is_numeric($key)) {
            $base[$key] = $append[$key];
            continue;
          }
          if(is_array($value) or is_array($base[$key])) {
            $base[$key] = self::MergeArrays($base[$key], $append[$key]);
          } else if(is_numeric($key)) {
              //$numeric++;
              //var_dump($numeric);
            if(!in_array($value, $base)) $base[] = $value;
          } else {
            $base[$key] = $value;
          }
        }
      }
      return $base;
    }
    */
}
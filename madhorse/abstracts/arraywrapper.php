<?php

namespace madHorse\abstracts;

abstract class arraywrapper
{
    protected $arData = NULL;
    protected $delim = ".";
    protected $path;
    
    private function &_ref(&$array,$key)
    {
        return $array[$key];
    }
    /** The following two functions (_array_get_path(), and isElement()) is taken from the http://thehighcastle.com/blog/38/php-dynamic-access-array-elements-arbitrary-nesting-depth and then further modified**/
    private function &_array_get_path( &$array, $path, $delim = NULL, $value = NULL, $unset = false ) 
    { 
        $num_args = func_num_args();
        $element = &$array;
        
        if ( ! is_array( $path ) && strlen( $delim = (string) $delim ) )
        {
            $path = explode( $delim, $path );
        }
        
        if ( ! is_array( $path ) ) 
        { 
            /** path syntex validation **/
            throw new \Exception("Invalid path format ".$this->path = $path);
        }
        
        while ( $path && ( $key = array_shift( $path ) ) ) 
        {
            if ( ! $path && $num_args >= 5 && $unset ) 
            {
                unset( $element[ $key ] );
                unset( $element );
                $element = NULL;
            }
            else 
            {
                if(!isset($element[ $key ]) && $num_args >= 4 && ! $unset)
                {
                    if(!is_array($element))
                        $element = array();
                    $element[ $key ] = array();
                }
                else if(!isset($element[ $key ]))
                    throw new \Exception("Path does not exits ".$this->path);
                
                $element =& $element[ "$key" ];
           }
        }
        
        
        if ( $num_args >= 4 && ! $unset ) 
            $element = $value;
        return $element;
    }
    
    function isElement($path)
    {
        $this->path = $path;
        
        if("." == $path)
            return $this->arData !== NULL;
        $has = false;

        $array = $this->arData;        
        if ( ! is_array( $path ) ) 
        {
            $path = explode( $this->delim, $path );
        }
        
        foreach ( $path as $key ) 
        {
            if(NULL === $array)
            {
                $has = false;
                break;
            }
            if ( $has = array_key_exists( $key, $array ) ) 
            {
                $array = $array[ $key ];
            }
        }
        // foreach
        return $has;
    }
    
    function getValue($path)
    {
        $this->path = $path;
        if("." == $path)
            return $this->arData;
        else return $this->_array_get_path($this->arData,$path,$this->delim);
    }
    
    function unsetElement($path)
    {
        $this->path = $path;
        
        if("." == $path)
            $this->arData = NULL;
        else $this->_array_get_path( $this->arData, $path, $this->delim, NULL, true );
    }
    
    function setValue($path,$value)
    {
        $this->path = $path;
        
        if("." == $path)
        {
            $this->arData = $value;
            return $value;   
        }
        else return $this->_array_get_path( $this->arData, $path, $this->delim, $value);
    }
}

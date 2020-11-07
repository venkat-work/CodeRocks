<?php

if (! function_exists('getHorseColor')) {
   
    /**
     * Calculate Horse Age,Sex and Color.
     */
    function getHorseAgeSexColor($dob,$sex,$color)
    {
        $age = date("Y") - date("Y",strtotime($dob));
        
        if(strtolower($sex) == "c" && $age > 4){
            $sex = "m";
        }
        if(strtolower($sex) == "f" && $age > 4){
            $sex = "m";
        }
       
        return $age."/".$sex."/".$color;
    }

    /**
     * Get current Horse sex.
     */
    function getHorseSex($dob,$sex)
    {
        $age = date("Y") - date("Y",strtotime($dob));
        
        if(strtolower($sex) == "c" && $age > 4){
            $sex = "m";
        }
        if(strtolower($sex) == "f" && $age > 4){
            $sex = "m";
        }
       
        return $sex;
    }

     /**
     * Calculate Horse Age,Sex and Color.
     */
    function getHorseAge($dob)
    {
        $age = date("Y") - date("Y",strtotime($dob));
        
        return $age;
    }
}
<?php

namespace App\Services;

class DepartmentService{
    public function generateCode($name){
        $name = trim($name);

        $words = explode(' ',$name);

        if(count($words) >= 2){
            return strtoupper(
                substr($words[0],0,1).
                substr($words[1], 0, 1)
            );
        }

        return strtoupper(substr($words[0], 0, 2));
    }
}
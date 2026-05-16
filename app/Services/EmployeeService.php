<?php

namespace App\Services;
use App\Models\User;

class EmployeeService{
    public function generateCode($department,$userId){
        $number = str_pad($userId , 3 , '0' , STR_PAD_LEFT);

        return $department->code . '-EMP-' . $number;
    }
}
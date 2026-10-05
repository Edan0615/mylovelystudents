<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
class StudentController extends Controller
{
    
    public function index(){
        //展示資料
        
        return view("students.index");  
    }
    
}

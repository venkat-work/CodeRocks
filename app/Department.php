<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Department extends Model
{
	use HasRoles;

    protected $guard_name = 'api'; // or whatever guard you want to use

    //
    public function __construct(){
    	$this->setTable("departments");
    }
}

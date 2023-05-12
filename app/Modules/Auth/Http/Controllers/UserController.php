<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware(['auth:api', 'role:admin']);
    }

    public function listUsers(){

        return User::all();
    }

    public function listRoles(){
        return Role::all();
    }

}

<?php

namespace App\Modules\Auth\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\User;
use Exception;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Validator;
use Illuminate\Support\Facades\DB;

class AuthenController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100|unique:users',
            'password' => [
                'required',
                'string',
                'min:6',
                // 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
                'confirmed'
            ]
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        DB::beginTransaction();
        try {
            $total_user = User::count();

            $user = User::create(array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            ));

            if( $total_user == 0) {
                $arr_roles = [
                    ["name" => "admin", "guard_name" => "api"],
                    ["name" => "mod", "guard_name" => "api"],
                    ["name" => "user", "guard_name" => "api"],
                    ["name" => "guide", "guard_name" => "api"]
                ];

                $arr_permissions = [
                    ["name" => "update_guest", "guard_name" => "api"],
                    ["name" => "delete_guest", "guard_name" => "api"],
                    ["name" => "create_guest", "guard_name" => "api"],
                    ["name" => "list_guest", "guard_name" => "api"],
                    ["name" => "detail_guest", "guard_name" => "api"]
                ];

                Role::insert($arr_roles);
                Permission::insert($arr_permissions);

                $role = Role::where("name", "admin")->first();
                $role->givePermissionTo(["update_guest", "delete_guest", "create_guest", "list_guest", "detail_guest"]);

                $role = Role::where("name", "mod")->first();
                $role->givePermissionTo(["update_guest", "create_guest", "list_guest", "detail_guest"]);

                $role = Role::where("name", "user")->first();
                $role->givePermissionTo(["list_guest", "detail_guest"]);

                $role = Role::where("name", "guide")->first();
                $role->givePermissionTo(["list_guest"]);

                $user->assignRole("admin");
            }else {
                $user->assignRole("guide");
            }

            DB::commit();

            return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
            ], 200  );
        } catch ( Exception $e) {
            DB::rollBack();
            return response()->json($e, 500);
        }

    }

    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile() {
        return response()->json(auth()->user());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function changePassWord(Request $request) {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:6',
            'new_password' => 'required|string|confirmed|min:6',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $userId = auth()->user()->id;

        $user = User::where('id', $userId)->update(
                    ['password' => bcrypt($request->new_password)]
                );

        return response()->json([
            'message' => 'User successfully changed password',
            'user' => $user,
        ], 201);
    }

    public function changeUserRole(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
            'role_id' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $role = Role::find($request->role_id);
        $user = User::find($request->id)->syncRoles([])->assignRole($role);

        return response()->json([
            'message' => 'User successfully changed role',
            'user' => $user,
        ], 201);
    }
}

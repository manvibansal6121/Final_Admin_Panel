<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\UserPermission;
use App\Models\Permission;
use App\Models\User;
use App\Models\Product;

class StaffController extends Controller
{
    //ADD STAFF
    public function addStaff(Request $request)
    {
      //Validating
      $validation = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email|unique:admins',
        'password' => 'required',
        'image' => 'required',
        'roles' => 'required|array|exists:permissions,id',
      ]);

      //If Validation Fails
      if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'error' => $validation->errors(),
        ], 400);
      }

      //Saving Data
      $admin = new Admin();
      $admin->name = $request->input('name');
      $admin->email = $request->input('email');
      $admin->image = $request->input('image');
      $admin->password = Hash::make($request->input('password'));
      $admin->save();

      $roles = $request->input('roles');

      //Saving UserPermissions Table Data
      foreach ($roles as $roleId) {
        $userPermission = new UserPermission();
        $userPermission->admin_id = $admin->id;
        $userPermission->permission_id = $roleId;
        $userPermission->save();
      }

      //Returning Response
      return response()->json([
        'success' => true,
        'message' => 'Staff added successfully.',
      ],200);
    }

    //LISTING STAFF  WITH PERMISSIONS
    public function list(Request $request)
    {
    $searchQuery = $request->query('search');
    $limit = $request->query('limit', 4);
    
    $admins = Admin::whereNull('profile');
    
    // Search Staff
    if ($searchQuery) {
        $admins->where(function ($query) use ($searchQuery) {
            $query->where('name', 'like', '%' . $searchQuery . '%')
                ->orWhere('email', 'like', '%' . $searchQuery . '%');
        });
    }
    
    $admins = $admins->paginate($limit);
    $adminData = [];

    foreach ($admins as $admin) {
        $permissions = UserPermission::where('admin_id', $admin->id)
            ->pluck('permission_id');
        $permissionNames = Permission::whereIn('id', $permissions)
            ->pluck('permission');

        $adminData[] = [
            'id' => $admin->id,
            'name' => $admin->name,
            'email' => $admin->email,
            'permissions' => $permissionNames,
        ];
    }

    // Returning Response
    return response()->json([
        'success' => true,
        'data' => $adminData,
    ], 200);
    }

   //VIEW PARTICULAR STAFF
   public function view($id)
   {
    //Finding id 
    $admin = Admin::whereNull('profile')
                  ->find($id);
     
    if(!$admin)
    {
        return response()->json([
            'sucess' => false,
            'message' => 'No Data Found',
        ],404);
    }          

    $permissions = UserPermission::where('admin_id', $admin->id)
                                     ->pluck('permission_id');
    $permissionNames = Permission::whereIn('id', $permissions)
                                    ->pluck('permission');

    //Returning Response
    return response()->json([
        'id' => $admin->id,
        'Name' => $admin->name,
        'Email' => $admin->email,
        'Phone no'=>$admin->phone_number,
        'Role' => $permissionNames,
    ],200);
   }

   //UPDATING STAFF
   public function updateStaff(Request $request, $id)
   {
    //Finding Id
    $admin = Admin::whereNull('profile')
                   ->find($id);

    if (!$admin) {
        return response()->json([
            'success' => false,
            'error' => 'Staff not found.',
        ], 404);
    }

    //Validating
    $validation = Validator::make($request->all(), [
        'email' => 'email|unique:admins',
        'roles' => '|array|exists:permissions,id',
    ]
    );
    //If Validation Fails
    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'error' => $validation->errors(),
        ], 400);
    }

    if ($request->has('name')) {
        $admin->name = $request->input('name');
    }
    if ($request->has('email')) {
        $admin->email = $request->input('email');
    }
    if ($request->has('image')) {
        $admin->image = $request->input('image');
    }
    if ($request->has('password')) {
        $admin->password = Hash::make($request->input('password'));
    }
    if ($request->has('phone_number')) {
        $admin->phone_number = $request->input('phone_number');
    }

    //Saving Data
    $admin->save();

    if ($request->has('roles')) {
        $roles = $request->input('roles');

        
        UserPermission::where('admin_id', $admin->id)->delete();
       
        foreach ($roles as $roleId) {
            $userPermission = new UserPermission();
            $userPermission->admin_id = $admin->id;
            $userPermission->permission_id = $roleId;
            $userPermission->save();
        }
    }

    // Return the response
    return response()->json([
        'success' => true,
        'message' => 'Staff updated successfully.',
    ], 200);
   }

   //BLOCK UNBLOACK STAFF
   public function blockStaff($id)
   {
    //Find Id
      $admin = Admin::whereNull('profile')
                    ->find($id);
    
      if(!$admin)
      {
        return response()->json([
            'sucess' => false,
            'message' => 'No Staff Found',
        ],404);
      }

      //Changing Status
      if($admin->block === 0)
      {
        $admin->block = 1;
        $message = 'Staff Blocked';
      }
      else
      {
        $admin->block = 0;
        $message = 'Staff Unblocked';
      }

      //Saving Changed Status
      $admin->save();

      //Returing Response
      return response()->json([
        'sucess' => true,
        'message' => $message,
      ],200);
   }

   //DELETE STAFF
   public function destroy($id)
   {
    //Find Id
    $admin = Admin::whereNull('profile')
                  ->find($id);
    //If Staff Not Found
    if(!$admin)
    {
        return response()->json([
            'success' => false,
            'message' => 'No Staff Found'
        ],404);
    }

    UserPermission::where('admin_id', $admin->id)->delete();
    //Delete Staff
    $admin->delete();
    //Returning Response
    return response()->json([
        'success' => true,
        'message' => 'Staff deleted successfully',
    ],200);
   }
}

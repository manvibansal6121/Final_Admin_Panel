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


class AdminAuthController extends Controller
{
    //LOGIN
    public function login(Request $request)
    {
    //Validating
    $validation = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    //If Validation fails
    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'error' => $validation->errors(),
        ], 400);
    } else {
        $email = $request->input('email');
        $password = $request->input('password');

        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $password])) {
            $admin = Auth::guard('admin')->user();
            $token = $admin->createToken('Admin Token')->plainTextToken;

            //If the login user is admin
            if ($admin->profile === 1) {
                $isAdmin = true;
                $response = [
                    'success' => true,
                    'isAdmin' => $isAdmin,
                    'data' => [
                        'id' => $admin->id,
                        'email' => $admin->email,
                        'token' => $token,
                    ],
                    'message' => 'You have successfully logged in as an admin',
                ];
            } else {
                $permissions = UserPermission::where('admin_id', $admin->id)
                    ->pluck('permission_id');
                $permissionNames = Permission::whereIn('id', $permissions)
                    ->pluck('permission');
                
                    $isAdmin = false;
                $response = [
                    'success' => true,
                    'isAdmin' => $isAdmin,
                    'data' => [
                        'id' => $admin->id,
                        'email' => $admin->email,
                        'token' => $token,
                        'permissions' => $permissionNames,
                    ],
                    'message' => 'You have successfully logged in as a staff member',
                ];
            }
            
            $admin->save();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        //Returning Response
        return response()->json([$response],200);
    }
    }

    //VIEW PROFILE
    public function viewProfile()
    {
         $admin = auth()->user();
      
        if(!$admin)
        {
            return response()->json([
                'success' => false,
                'message' => 'Not Found',
            ],404);
        }

        //Return Response
        return response()->json([
            'sucess' => true,
            'data' =>[
                'id' => $admin->id,
                'Name' => $admin->name,
                'Email' => $admin->email,
                'Image' => $admin->image,
            ]
            ],200);
    }

    //UPDATE PROFILE
    public function updateProfile(Request $request)
    {
        $admin = auth()->user();

        //If not authenticated
        if(!$admin)
        {
            return response()->json([
                'success' => false,
                'message' => 'No Profile found to update',
            ],404);
        }
        //Updating according to the user needs
        if($request->has('name'))
        {
            $admin->name = $request->input('name');
        }
        if($request->has('email'))
        {
            $admin->email = $request->input('email');
        }
        if($request->has('image'))
        {
            $admin->image = $request->input('image');
        }

        //Saving Data
        $admin->save();
        //Returning Response
        return response()->json([
            'sucess' => true,
            'data' =>[
                'name' => $admin->name,
                'email' => $admin->email,
                'image' => $admin->image,
            ],
            'message' => 'Profile updated successfully',
            ],200);
    }

    //UPDATE PASSWORD
    public function changePassword(Request $request)
    {
    $admin = auth()->user();

    if (!$admin) {
        return response()->json([
            'success' => false,
            'message' => 'No Data Found',
        ], 404);
    }

    //Validating
    $validation = Validator::make($request->all(), [
        'old_password' => 'required',
        'new_password' => 'required',
        'confirm_password' => 'required',
    ]);

    //If valodation fails
    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validation->errors()->first(),
        ], 400);
    }

    //Checking of Old Password with the request old password
    $oldPasswordMatches = Hash::check($request->input('old_password'), $admin->password);

    //If Old Password matches
    if ($oldPasswordMatches) {
        //If Confirm Password and New Password matches
        if ($request->input('new_password') === $request->input('confirm_password')) {
            $admin->password = Hash::make($request->input('new_password'));
            $admin->save();

            //Returning Response
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully.',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'New password and confirm password do not match.',
            ]);
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Incorrect old password.',
        ], 400);
    }
    }

    //LOGOUT PROFILE
    public function logout()
    {
        $user = auth()->user();
        //If not authenticated
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 404);
        }
        //Deleting Tokens
        $user->tokens()->delete();
        //Returning Response
        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

}






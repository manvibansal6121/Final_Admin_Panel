<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpVerifyMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
class UserController extends Controller
{
    //USER SIGNUP
    public function signup(Request $request)
    {
        //Validating
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        //If Validation Fails
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors(),
            ], 400);
        }
        //Saving Users Data and Generating OTP
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = bcrypt($request->input('password'));
        $otp = mt_rand(1000, 9999);
        $user->otp = $otp;
        $user->otp_created_at = Carbon::now();
        $user->save();
        //Sending Email
        Mail::to($user->email)->send(new OtpVerifyMail($user, $otp));
        //Returning Response
        return response()->json([
            'success' => true,
            'data' => [
                'Name' => $user->name,
                'Email' => $user->email,
            ],
            'message' => 'OTP has been sent. Please check your email.',
        ], 200);
    }

    //VERIFY EMAIL
    public function verifyEmail(Request $request)
    {
        //Validating
        $validation = validator::make($request->all(),[
            'email' => 'required|email',
            'otp' => 'required',
        ]);
        //If Validation Fails
        if($validation->fails())
        {
            return response()->json([
                'sucess' => false,
                'error' => $validation->error(),
            ],400);
        }
        $email = $request->input('email');
        $otp = $request->input('otp');
        $user = User::where('email', $email)
                    ->where('otp', $otp)
                    ->first();
        if(!$user)
        {
            return response()->json([
                'success' => false,
                'Message' => 'Invalid OTP',
            ],400);
        }
        //Setting OTP Expire time
        $otpExpire = Carbon::parse($user->otp_created_at)->addSeconds(30);
        if(Carbon::now()->gt($otpExpire))
        {
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save;
             return response()->json([
            'success' => false,
            'Message' => 'OTP has expired.Please request a new one',
            ],400);
        }
        //Saving Data
        $user->email_verified_at = Carbon::now();
        $user->save();
        //Returning Response
        return response()->json([
            'sucess' => true,
            'Message' => 'You have successfully signed up',
        ],200);
    }

    //RESEND OTP
    public function resendOTP(Request $request)
    {
        // VALIDATING EMAIL
        $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
        return response()->json([
            'Success' => 'false',
            'errors' => $validator->errors()
        ], 422);
        }
        //Generating OTP
        $email = $request->input('email');
        $otp = mt_rand(1000, 9999);
        //Saving Data
        $user = User::where('email', $email)->first();
        $user->otp = $otp;
        $user->otp_created_at = Carbon::now();
        $user->save();
        //Sending Mail
        Mail::to($email)->send(new OtpVerifyMail($user,$otp));
        //Returning Response
        return response()->json([
          'Success' => true,
          'message' => 'OTP has been sent successfully',
        ]);
    }

    //LISTING USERS
    public function listUser(Request $request)
    {
    $defaultLimit = 4;
    $limit = $request->query('limit', $defaultLimit);  
    
    if (!$request->has('limit')) {
        $limit = $defaultLimit;
    }

    $status = $request->query('status', 'all');
    $search = $request->query('search');
    $users = User::query();

    $users->select('id', 'name', 'email','phone_number');
    
    // Filter Users
    if ($status === 'active') {
        $users->where('active', true);
    } elseif ($status === 'deactive') {
        $users->where('active', false);
    } elseif ($status === 'block') {
        $users->where('block', true);
    } elseif ($status === 'unblock') {
        $users->where('block', false);
    }
    //Filter Users With Search
    if ($search) {
        $users->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone_number', 'like', "%$search%");
        });
    }
    
    //Pagination
    $data = $users->paginate($limit);
    
    // Returning Response
    return response()->json([
        'success' => true,
        'data' => $data,
    ], 200);
    }


    //EXPORT USER
    public function userExport(Request $request)
    {
    //Validating 
    $validator = Validator::make($request->all(), [
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);
    //If Validation Fails
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
        ], 422);
    }

    $status = $request->query('status', 'all');
    $search = $request->query('search');
    $startDate = $request->query('start_date');
    $endDate = $request->query('end_date');

    $users = User::query();

    $users->select('id', 'name', 'email', 'phone_number');

    // Filter Users
    if ($status === 'active') {
        $users->where('active', true);
    } elseif ($status === 'deactive') {
        $users->where('active', false);
    } elseif ($status === 'block') {
        $users->where('block', true);
    } elseif ($status === 'unblock') {
        $users->where('block', false);
    }

    //Filter Users With Search
    if ($search) {
        $users->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('phone_number', 'like', "%$search%");
        });
    }

    if ($startDate && $endDate) {
        $users->whereBetween('created_at', [$startDate, $endDate]);
    }

    $data = $users->get();

    // Returning Response
    return response()->json([
        'success' => true,
        'data' => $data,
    ], 200);
    }

    //ADD USER
    public function addUser(Request $request)
    {
        //Validating
        $validation = validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone_number' => 'required|min:10',
            'password' => 'required:min:6',
            'image' => 'required',
        ]);
        //If Validation Fails
        if($validation->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors(),
            ],400);
        }

        //Saving Users Data
        $user = new User();
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->phone_number = $request['phone_number'];
        $user->password= Hash::make($request['password']);
        $user->image=$request['image'];
        $user->save();

        //Returning Response
        return response()->json([
            'success' => true,
            'data' => [
                'Id'=>$user->id,
                'Name'=>$user->name,
                'Email'=>$user->email,
                'Phone_number'=>$user->phone_number,
                'Image'=>$user->image,
            ],
            'message'=>'User Added Successfully',
            ],200);
    }

    //VIEW PARTICULAR USER
    public function viewUser($id)
    {
        //Find User
        $user = User::find($id);

        if(!$user)
        {
            return response()->json([
                'success'=>false,
                'message'=>'User Not Found',
            ],404);
        }
 
        //Returning Response
        return response()->json([
            'success'=>true,
            'data'=>[
                'Id'=>$user->id,
                'Image'=>$user->image,
                'Name'=>$user->name,
                'Email'=>$user->email,
                'Phone no.'=>$user->phone_number,
            ],
        ],200);
    }

    //BLOCK OR UNBLOCK USER
    public function block($id)
    {
        //Finding User 
        $user = User::find($id);
        if(!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ],404);
        }
        //Updating User Based On Condition
        if($user->block===null)
        {
            $user->block = true;
            $message = 'User Blocked';
        }
        else{
            $user->block = null;
            $message = 'User Unblocked';
        }
        //Saving Updated Data
        $user->save();
        //Returning Response
        return response()->json([
            'success' => true,
            'message' => $message,
        ],200);
    }

    //ACTIVATE OR DEACTIVATE USER
    public function active($id){

        //Finding User
        $user = User::find($id);
        if(!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ],404);
        }
        //Updating User Based On Condition
        if($user->active===null)
        {
            $user->active = true;
            $message = 'User Deactivated';
        }
        else{
            $user->active = null;
            $message = 'User activated';
        }
        //Saving Updated Data
        $user->save();
        //Returning Response
        return response()->json([
            'success' => true,
            'message' => $message,
        ],200);
    }

    //DELETE USER
    public function destroy($id)
    {
        $user = User::find($id);

        if(!$user)
        {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ],404);
        }

        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User Deleted Successfully',
        ],200);
    }

    //LOGIN USER
    public function loginUser(Request $request)
    {
    //Validating
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ]);

    //If Validation fails
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
        ], 400);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials.',
        ], 401);
    }

    //Generating token
    $token = $user->createToken('authToken')->plainTextToken;

    //Returning Response
    return response()->json([
        'success' => true,
        'data' => [
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token,
        ],
        'message' => 'You have successgully logged in ',
    ],200);
    }

    //View User Profile
    public function viewUserProfile(Request $request)
    {
        $user = $request->user();

    //Returning Response
    return response()->json([
        'success' => true,
        'data' => [
            'name' => $user->name,
            'email' => $user->email,
        ],
    ], 200);
    }

    //LOGOUT USER
    public function logoutUser(Request $request)
    {
        $user = $request->user();
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
            'message' => 'User Logged out successfully',
        ]);
    }
}

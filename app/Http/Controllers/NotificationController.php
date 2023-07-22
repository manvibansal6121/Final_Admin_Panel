<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMail;
use App\Jobs\SendMailJob;

class NotificationController extends Controller
{
    //SENDING MAIL
    public function sendMail(Request $request)
    {
        //Validating
        $validation = Validator::make($request->all(), [
            'subject' => 'required',
            'description' => 'required',
            'type' => 'required|in:allusers,selectedusers',
        ]);

        //If Validation Fails
        if ($validation->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors(),
            ], 400);
        }

        $subject = $request->input('subject');
        $description = $request->input('description');
        $type = $request->input('type');

        //Filter Users Type
        if ($type === 'allusers') {
            $users = User::pluck('email')->all();
        } elseif ($type === 'selectedusers') {
            $selected = $request->input('selected_users', []);
            $users = User::whereIn('id', $selected)->pluck('email')->all();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type specified.',
            ], 400);
        }

        foreach ($users as $user) {
            SendMailJob::dispatch($user, $subject, $description);
        }

        //Returning Response
        return response()->json([
            'success' => true,
            'message' => 'Mail has been sent.',
        ], 200);
    }
}

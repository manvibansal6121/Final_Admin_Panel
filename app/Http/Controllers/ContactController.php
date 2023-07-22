<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ContactUs;

class ContactController extends Controller
{
    //LISTING CONTACT INFORMATION
    public function view(Request $request)
    {
        $search = $request->query('search');

        $query = ContactUs::query();

        //Search Contact
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orwhere('phone_number', 'like', '%' . $search . '%')
                  ->orwhere('Message', 'like', '%' . $search . '%')
                  ->orwhere('status', 'like', '%' . $search . '%');
            });
        }

        $contactus = $query->get();

        $responseData = [];
        
        $limit = $request->query('limit', 5);
        $contactus = $query->paginate($limit);

        foreach ($contactus as $contactus) {
            $responseData[] = [
                'Id' => $contactus->id,
                'Name' => $contactus->name,
                'Email' => $contactus->email,
                'Phone no' => $contactus->phone_number,
                'Message' => $contactus->Message,
                'Status' => $contactus->status,
            ];
        }

        //Returning Response
        return response()->json([
            'success' => true,
            'data' => $responseData,
        ], 200);
    }


    //EXPORT CONTACT
    public function contactExport(Request $request)
    {
    //Validate 
    $validator = Validator::make($request->all(), [
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    //If validation Fails
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors(),
        ], 422);
    }

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $search = $request->query('search');

    $query = ContactUs::query();

    // Search Contact
    if ($search) {
        $query->where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('phone_number', 'like', '%' . $search . '%')
                ->orWhere('Message', 'like', '%' . $search . '%')
                ->orWhere('status', 'like', '%' . $search . '%');
        });
    }

    $query->whereBetween('created_at', [$startDate, $endDate]);

    $contactus = $query->get();

    $responseData = [];
    foreach ($contactus as $item) {
        $responseData[] = [
            'Id' => $item->id,
            'Name' => $item->name,
            'Email' => $item->email,
            'Phone no' => $item->phone_number,
            'Message' => $item->Message,
            'Status' => $item->status,
        ];
    }

    // Returning Response
    return response()->json([
        'success' => true,
        'data' => $responseData,
    ], 200);
    }

    //CHANGE STATUS
    public function changeStatus($id)
    {
    //Find Id
    $contactus = ContactUs::find($id);

    if (!$contactus) {
        return response()->json([
            'success' => false,
            'message' => 'No Data Found',
        ], 404);
    }
 
    //Changing Status
    if ($contactus->status === 'open') {
        $contactus->status = 'resolved';
    } else {
        $contactus->status = 'open';
    }

    $contactus->save();
    //Returning Response
    return response()->json([
        'success' => true,
        'message' => $contactus->status,
    ]);
    }

    //DELETE CONTACT
    public function destroy($id)
    {
    //Find Id
    $contactus = ContactUs::find($id);
    if(!$contactus)
    {
        return response()->json([
            'sucess' => false,
            'message' => 'No Data Found',
        ],404);
    }

    //Deleting Contact
    $contactus->delete();
    //Returning Response
    return response()->json([
        'sucess' => true,
        'message' => 'Deleted successfully',
    ],200);
    }

}

<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
class DashboardController extends Controller
{
    //DASHBOARD
    public function dashboard(Request $request)
    {
    //Pagination
    $defaultLimit = 4;
    $limit = $request->query('limit', $defaultLimit);  
    
    if (!$request->has('limit')) {
        $limit = $defaultLimit;
    }
    
    $Countuser = User::count();
    $Countproduct = Product::count();
    $users = User::orderBy('created_at', 'desc')->paginate($limit);
    $userData = [];
    
    //Fetching Data
    foreach ($users as $user) {
        $userData[] = [
            'Id' => $user->id,
            'Name' => $user->name,
            'Email' => $user->email,
            'Phone no.' => $user->phone_number,
        ];
    }
    
    //Returning Response
    return response()->json([
        'success' => true,
        'data' => [
            'Users' => $Countuser,
            'Products' => $Countproduct,
            'data' => $userData,
        ],
    ], 200);
    }

}
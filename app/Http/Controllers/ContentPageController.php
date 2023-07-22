<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Validator;
 use Illuminate\Support\Str;
 use App\Models\Content;
 use Carbon\Carbon;

class ContentPageController extends Controller
{
    //ADD CONTACT PAGE
    public function addPage(Request $request)
    {
        //Validating 
        $validation = validator::make($request->all(),[
            'title' => 'required',
            'description'=>'required',
            'image'=>'required',
        ]);

        //If Validation Fails
        if($validation->fails())
        {
            return response()->json([
                'success' => false,
                'errors'=>$validation->errors(),
            ],400);
        }

        //Saving Data 
        $content =  new Content();
        $content->title = $request['title'];
        $content->description = $request['description'];
        $content->image = $request['image'];
        $content->name= Str::slug($request->input('title'), "_");
        $content->created_on = Carbon::now();
        $content->save();

        //Returning Response
        return response()->json([
            'success' => true,
            'data'=>[
                'id'=>$content['id'],
                'Page Title'=>$request['title'],
                'Name'=>$content['name'],
                'Description'=>$request['description'],
                'Image'=>$request['image'],
            ],
            'message' => 'Content Page Added Successfully',
        ],200);
    }

    //LISTING CONTACT PAGE
    public function view(Request $request)
    {
    $content = Content::query();
    $search = $request->query('search');

    // Searching Content Page
    if ($search) {
        $content->where(function ($query) use ($search) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('created_at', 'like', '%' . $search . '%');
        });
    }

    $limit = $request->query('limit', 4); 
    $contentData = $content->paginate($limit);

    $formattedData = $contentData->map(function ($item) {
        return [
            'id' => $item->id,
            'Page' => $item->title,
            'Description' => $item->description,
            'Created On' => $item->created_at->format('d/M/Y'),

        ];
    });

    // Returning Response
    return response()->json([
        'success' => true,
        'data' => $formattedData,
    ], 200);
    }

    //SHOW PARTICULAR CONTACT PAGE
    public function show($id)
    {
        //Finding Content Page
        $content = Content::find($id);
        if(!$content)
        {
            return response()->json([
                'success' => false,
                'message' => 'No Content Found',
            ],404);
        }

        //Returning Response
        return response()->json([
            'id' => $content->id,
            'Name' => $content->title,
            'Image' => $content->image,
            'Description' => $content->description,
        ],200);
    }

    //UPDATE CONTACT PAGE
    public function update(Request $request,$id)
    { 
        // Find Content Page
        $content = Content::find($id);
        if (!$content) {
        return response()->json([
        'success' => false,
        'message' => 'No Content Page Found',
        ], 404);
        }

        // Update Data
        if ($request->has('image')) {
        $content->image = $request->input('image');
        }

        if ($request->has('title')) {
        $content->title = $request->input('title');
        }

        if ($request->has('description')) {
        $content->description = $request->input('description');
        }
        //Saving Data
        $content->save();

        // Returning Response
        return response()->json([
           'success' => true,
            'data' => [
               'id' => $content->id,
               'Image' => $content->image,
               'Page Title' => $content->title,
               'Answer' => $content->description,
            ],
            'message' => 'Content Page Updated Successfully',
      ], 200);
}
}
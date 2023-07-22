<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Faq;

class FAQController extends Controller
{
    //ADD FAQ
    public function addFaq(Request $request)
    {
        //Validating
        $validation = validator::make($request->all(),[
            'question' => 'required',
            'answer' => 'required',
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
        $faq = new Faq();
        $faq->question = $request['question'];
        $faq->answer = $request['answer'];
        $faq->save();

        //Returning Response
        return response()->json([
            'sucess' => true,
            'data' => [
                'Id' => $faq->id,
                'Question' => $request['question'],
                'Answer' => $request['answer'],
            ],
            'message' => 'FAQ added successfully',
        ],200);
    }

    //LISTING FAQ
    public function view(Request $request)
    {
        $faq = Faq::query();
        $search = $request->query('search');

        //Searching FAQ
        if ($search) {
            $faq->where(function ($query) use ($search) {
                $query->where('question', 'like', '%' . $search . '%')
                      ->orWhere('answer', 'like', '%' . $search . '%');
            });
        }

         $limit = $request->query('limit', 5); 
         $faqData = $faq->paginate($limit);

        //Fetching Data
        $faqData = [];
        foreach ($faq->get() as $faq) {
            $faqData[] = [
                'id' => $faq->id,
                'Question' => $faq->question,
                'Answer' => $faq->answer,
            ];
        }

        //Returning Response
        return response()->json([
            'success' => true,
            'data' => $faqData,
        ], 200);
    }

    //UPDATE FAQ
    public function update(Request $request, $id)
    {
    // Find FAQ
    $faq = Faq::find($id);
    if (!$faq) {
        return response()->json([
            'success' => false,
            'message' => 'No Faq Found',
        ], 404);
    }
    // Update Data
    if ($request->has('question')) {
        $faq->question = $request->input('question');
    }
    if ($request->has('answer')) {
        $faq->answer = $request->input('answer');
    }

    //Saving Data
    $faq->save();

    // Returning Response
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $faq->id,
            'Question' => $faq->question,
            'Answer' => $faq->answer,
        ],
        'message' => 'Data Updated Successfully',
    ], 200);
    }

}

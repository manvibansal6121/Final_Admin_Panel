<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Category; 
use App\Models\Gallery;
class ProductController extends Controller
{
    //ADDING NEW PRODUCT
    public function addProduct(Request $request)
    {
        //Validating
        $validation = validator::make($request->all(),[
            'name' => 'required',
            'category_id' => 'required|integer',
            'description' => 'required',
            'coverimage' => 'required',
            'regularprice' => 'required|integer',
            'salesprice' => 'required|integer',
        ]);

        //If Validation Fails
        if($validation->fails())
        {
            return response()->json([
                'success' => false,
                'errors'=>$validation->errors(),
            ],400);
        }

        //Saving Product Information
        $product = new Product();
        $product->name = $request['name'];
        $product->category_id=$request['category_id'];
        $product->description=$request['description'];
        $product->coverimage=$request['coverimage'];
        $product->regularprice=$request['regularprice'];
        $product->salesprice=$request['salesprice'];
        $product->save();

        //Returning Response
        return response()->json([
            'success' => true,
            'data' => [
            'Id' => $product->id,
            'Product Name' => $product->name,
            'Category' => $product->category_id,
            'Description' => $product->description,
            'Images' => $product->coverimage,
            'Regular Price' => $product->regularprice,
            'Sale Price' => $product->salesprice,
            ],
            'message' => 'Product Added successfully',
        ],200);
    }

    //LISTING PRODUCTS
    public function view(Request $request)
    {
    $products = Product::query();
    $limit = $request->query('limit',10);  

    // Filter Products by Category ID
    if ($request->has('category_id')) {
        $products->whereIn('category_id', $request->input('category_id'));
    }

    // Filter Products by Price Range
    if ($request->has('range')) {
        $ranges = $request->input('range');

        $products->where(function ($query) use ($ranges) {
            foreach ($ranges as $range) {
                switch ($range) {
                    case 'low':
                        $query->orWhere('regularprice', '<=', 100);
                        break;
                    case 'medium':
                        $query->orWhereBetween('regularprice', [100, 500]);
                        break;
                    case 'high':
                        $query->orWhere('regularprice', '>', 500);
                        break;
                }
            }
        });
    }

    // Filter Products by Search
    if ($request->has('search')) {
        $search = $request->input('search');
        $products->where('name', 'like', '%' . $search . '%');
    }

    //Pagination
    $Products = $products->paginate($limit);

    $productData = [];
    foreach ($products->get() as $product) {
        $productData[] = [
            'Id' => $product->id,
            'Product Title' => $product->name,
            'Category' => $product->category['category'],
            'Price' => $product->regularprice,
            'Image' => $product->coverimage,
        ];
    }

    // Returning Response
    return response()->json([
        'success' => true,
        'data' => $productData,
    ], 200);
    }

    //PRODUCT EXPORT
    public function productExport(Request $request)
    {
    $products = Product::query();

    // Validate Start Date and End Date
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

    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Filter Products by Category ID
    if ($request->has('category_id')) {
        $products->whereIn('category_id', $request->input('category_id'));
    }

    // Filter Products by Price Range
    if ($request->has('range')) {
        $ranges = $request->input('range');

        $products->where(function ($query) use ($ranges) {
            foreach ($ranges as $range) {
                switch ($range) {
                    case 'low':
                        $query->orWhere('regularprice', '<=', 100);
                        break;
                    case 'medium':
                        $query->orWhereBetween('regularprice', [100, 500]);
                        break;
                    case 'high':
                        $query->orWhere('regularprice', '>', 500);
                        break;
                }
            }
        });
    }

    // Filter Products by Search
    if ($request->has('search')) {
        $search = $request->input('search');
        $products->where('name', 'like', '%' . $search . '%');
    }

    // Filter Products by Start and End Date
    $products->whereBetween('created_at', [$startDate, $endDate]);

    $productData = [];
    foreach ($products->get() as $product) {
        $productData[] = [
            'Id' => $product->id,
            'Product Title' => $product->name,
            'Category' => $product->category['category'],
            'Price' => $product->regularprice,
            'Image' => $product->coverimage,
        ];
    }

    // Returning Response
    return response()->json([
        'success' => true,
        'data' => $productData,
    ], 200);
    }

    //SHOW PARTICULAR PRODUCT
    public function show($id)
    {
    //Find Product
    $product = Product::find($id);

    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Product not found',
        ], 404);
    }

    // Calculate the discount
    $discount = ($product->regularprice - $product->salesprice) / $product->regularprice * 100;

    $galleryImages = Gallery::where('product_id', $product->id)->get(['id', 'image']);

    $formattedGalleryImages = [];
    foreach ($galleryImages as $image) {
        $formattedGalleryImages[] = [
            'id' => $image->id,
            'image' => $image->image,
        ];
    }

    //Returning Response
    return response()->json([
        'success' => true,
        'data' => [
            'Id' => $product->id,
            'Product Name' => $product->name,
            'Category' => $product->category['category'],
            'Description' => $product->description,
            'Regular Price' => $product->regularprice,
            'Sales Price' => $product->salesprice,
            'Discount' => $discount,
            'Cover Image' => $product->coverimage,
            'Images' => $formattedGalleryImages,
        ]
    ], 200);
    }

    //PRODUCT VISIBILITY
    public function visibility($id)
    {
        //Finding Product
        $product = Product::find($id);
        
        //Visibility of Product
        if($product->visibility === 1)
        {
            $product->visibility =  0;
            $message = 'Product visibility removed';
        }
        else
        {
            $product->visibility = 1;
            $message = 'Product visibility set';
        }

        //Saving Visibility Statuss
        $product->save();
        //Returning Response
        return response()->json([
            'success' => true,
            'message' => $message,
        ],200);
    }

     //UPDATE PRODUCT
     public function updateProduct(Request $request , $id)
    {
        //Finding Product Id
         $product = Product::find($id);
        //If No Id Found
        if(!$product)
        {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ],404);
        }

        //Validating
        $validation = validator::make($request->all(),[
            'category_id' => 'integer',
            'regularprice' => '|integer',
            'salesprice' => 'integer',
        ]);

        //If Validation Fails
        if($validation->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validation->errors(),
            ],400);
        }
         if($request->has('name'))
        {
            $product->name = $request->input('name');
        }
          if($request->has('category_id'))
        {
            $product->category_id = $request->input('category_id');
        }
          if($request->has('description'))
        {
            $product->description = $request->input('description');
        }
          if($request->has('coverimage'))
        {
            $product->coverimage = $request->input('coverimage');
        }
          if($request->has('regularprice'))
        {
            $product->regularprice = $request->input('regularprice');
        }
          if($request->has('salesprice'))
        {
            $product->salesprice = $request->input('salesprice');
        }

        //Saving Product Data
        $product->save();

        $galleryImages = Gallery::where('product_id', $product->id)->get(['id', 'image']);

        $formattedGalleryImages = [];
            foreach ($galleryImages as $image) {
               $formattedGalleryImages[] = [
               'id' => $image->id,
               'image' => $image->image,
            ];
     }

        //Returning Response
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'Product Name' => $product->name,
                'Category Name' => $product->category['category'],
                'Description' => $product->description,
                'Image' => $product->coverimage,
                'Regular Price' => $product->regularprice,
                'Sale Price' => $product->salesprice,
               'Images' => $formattedGalleryImages,
            ]
            ],200);
    }

    //UPLOAD IMAGE FOR A PARTICULAR PRODUCT
    public function UploadProductImage(Request $request, $id)
    {
    //Find Product
    $product = Product::find($id);
    //If No Id Found
    if (!$product) {
        return response()->json([
            'success' => false,
            'message' => 'Product not found',
        ], 404);
    }

    //Validating
    $validation = validator::make($request->all(), [
        'image' => 'required',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'success' => false,
            'error' => $validation->errors(),
        ], 400);
    }

    //Saving Data In Galleries Table
    $gallery = new Gallery();
    $gallery->product_id = $id; 
    $gallery->image = $request->input('image');
    $gallery->save();

    //Returning Response
    return response()->json([
        'success' => true,
        'data' => [
            'id' => $gallery->id,
            'product_id' => $gallery->product_id,
            'image' => $gallery->image,
        ],
        'message' => 'Image Uploaded Successfully',
    ], 200);
    }

}

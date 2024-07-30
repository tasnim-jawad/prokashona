<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
class CategoryController extends Controller
{
    public function all()
    {
        $paginate = (int) request()->paginate;
        $orderBy = request()->orderBy;
        $orderByType = request()->orderByType;

        $status = 1;
        if (request()->has('status')) {
            $status = request()->status;
        }

        $query = Category::where('status', $status)
            ->with([
                'parent' => function($q){
                    return $q->select('id','name','url','parent_id');
                },
            ])
            ->orderBy($orderBy, $orderByType);

        if (request()->has('search_key')) {
            $key = request()->search_key;
            $query->where(function ($q) use ($key) {
                return $q->where('id', $key)
                    ->orWhere('name', $key)
                    ->orWhere('url', $key)
                    ->orWhere('name', 'LIKE', '%' . $key . '%')
                    ->orWhere('url', 'LIKE', '%' . $key . '%')
                    ->orWhere('description', 'LIKE', '%' . $key . '%');
            });
        }

        $data = $query->paginate($paginate);
        return response()->json($data);
    }

    public function show($id)
    {
        $data = Category::where('id',$id)->first();
        if(!$data){
            return response()->json([
                'err_message' => 'not found',
                'errors' => ['role'=>['data not found']],
            ], 422);
        }
        return response()->json($data,200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => ['required'],
            'url' => ['required', 'unique:categories', 'min:3'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = Category::create($request->except('category_image'));
        $data->creator = Auth::user()->id;
        $data->save();
        $data->slug = $data->id . rand(1111, 9999).'-' . Str::slug($request->name);
        $data->save();

        if ($request->hasFile('category_image')) {
            $file = $request->file('category_image');
            $extension = $file->getClientOriginalExtension();
            $path = "uploads/category_image/".Str::slug($data->name,'-').$data->id.'.'.$extension;
            Image::make($file)->fit(100,100)->save(public_path($path));
            // $path = Storage::put('/uploads/category_image', $file);
            $data->category_image = $path;
            $data->save();
        }


        return response()->json($data, 200);
    }

    public function canvas_store()
    {
        $validator = Validator::make(request()->all(), [
            'full_name' => ['required'],
            'email' => ['required'],
            'subject' => ['required'],
            'message' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = new Category();
        $data->full_name = request()->full_name;
        $data->email = request()->email;
        $data->subject = request()->subject;
        $data->message = request()->message;
        $data->save();

        return response()->json($data, 200);
    }

    public function update()
    {
        $data = Category::find(request()->id);
        $validator = Validator::make(request()->all(), [
            'name' => ['required'],
            'url' => ['required'],
            // 'description' => ['required'],
            'parent_id' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data->name = request()->name;
        $data->url = request()->url;
        $data->description = request()->description;
        $data->update();

        if (request()->hasFile('category_image')) {
            $file = request()->file('category_image');
            $extension = $file->getClientOriginalExtension();
            $path = "uploads/category_image/".Str::slug($data->name,'-').$data->id.'.'.$extension;
            Image::make($file)->fit(100,100)->save(public_path($path));
            // $path = Storage::put('/uploads/category_image', $file);
            $data->category_image = $path;
            $data->save();
        }

        return response()->json($data, 200);
    }

    public function canvas_update()
    {
        $data = Category::find(request()->id);
        if(!$data){
            return response()->json([
                'err_message' => 'validation error',
                'errors' => ['name'=>['user_role not found by given id '.(request()->id?request()->id:'null')]],
            ], 422);
        }

        $validator = Validator::make(request()->all(), [
            'name' => ['required'],
            'url' => ['required'],
            'description' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data->full_name = request()->full_name;
        $data->email = request()->email;
        $data->subject = request()->subject;
        $data->message = request()->message;
        $data->save();

        return response()->json($data, 200);
    }

    public function soft_delete()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required','exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = Category::find(request()->id);
        $data->status = 0;
        $data->save();

        return response()->json([
                'result' => 'deactivated',
        ], 200);
    }

    public function destroy()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required','exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'category not exists',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = Category::find(request()->id);
        if(file_exists(public_path($data->category_image))){
            unlink(public_path($data->category_image));
        }
        if($data && $data->products()->count()){
            $data->products()->detach();
        }
        $data->delete();

        return response()->json('category deleted successfully');
    }

    public function restore()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required','exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = Category::find(request()->id);
        $data->status = 1;
        $data->save();

        return response()->json([
                'result' => 'activated',
        ], 200);
    }

    public function bulk_import()
    {
        $validator = Validator::make(request()->all(), [
            'data' => ['required','array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach (request()->data as $item) {
            $item['created_at'] = $item['created_at'] ? Carbon::parse($item['created_at']): Carbon::now()->toDateTimeString();
            $item['updated_at'] = $item['updated_at'] ? Carbon::parse($item['updated_at']): Carbon::now()->toDateTimeString();
            $item = (object) $item;
            $check = Category::where('id',$item->id)->first();
            if(!$check){
                try {
                    Category::create((array) $item);
                } catch (\Throwable $th) {
                    return response()->json([
                        'err_message' => 'validation error',
                        'errors' => $th->getMessage(),
                    ], 400);
                }
            }
        }

        return response()->json('success', 200);
    }

    public function all_json()
    {
        $category_json = Category::where('status',1)->select(['id','name','parent_id'])->get();
        return response()->json($category_json);
    }

    public function products($id)
    {
        $category = Category::where('status',1)->where('id',$id)->select(['id','name','parent_id'])->first();
        $products = $category->products()->paginate(12);
        return response()->json($products);
    }

    public function check_exists()
    {
        $check = Category::where('url',request()->url)->first();
        if($check && request()->has('category')){
            if($check->url == request()->category['url']){
                return 'not exists';
            }
        }
        if($check){
            return response()->json([
                'err_message' => 'validation error',
                'errors' => [
                    'url' => ['this url is exists']
                ],
            ], 422);
        }
    }

    public function add_to_top_cat()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required','exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'category not exists',
                'errors' => $validator->errors(),
            ], 422);
        }

        $id = request()->id;
        $category = Category::find($id);
        if($category->is_top_category){
            $category->is_top_category = 0;
            $category->save();
            return response()->json(0);
        }else{
            $category->is_top_category = 1;
            $category->save();
            return response()->json('success');
        }
    }

    public function add_to_public()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required','exists:categories,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'category not exists',
                'errors' => $validator->errors(),
            ], 422);
        }

        $id = request()->id;
        $category = Category::find($id);
        if($category->is_public){
            $category->is_public = 0;
            $category->save();
            return response()->json(0);
        }else{
            $category->is_public = 1;
            $category->save();
            return response()->json('success');
        }
    }
}

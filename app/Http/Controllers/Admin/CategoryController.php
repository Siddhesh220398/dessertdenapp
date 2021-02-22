<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pages.categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.pages.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'sequence' => 'required',
            'image' => 'required|image',
            'type'=>'required'
        ];
        $this->validateForm($request->all(), $rules);

        $category = new Category;
        $category->name = $request->name;
        $category->sequence = $request->sequence;
        $category->type = $request->type;
        $category->image = $request->file('image')->store('categories');
        $category->save();

        flash('Category added successfully.')->success();
        return redirect()->route('admin.categories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('admin.pages.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        if(!empty($request->action) && $request->action == 'change_status'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $category->active = ($request->value == 'y' ? 0 : 1);
            $category->save();
            $content['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
                'name' => 'required|string',
                'sequence' => 'required',
                'image' => 'nullable|image'
            ];
            $this->validateForm($request->all(), $rules);

            $category->name = $request->name;
            $category->sequence = $request->sequence;
            $category->type = $request->type;

            if (!empty($request->image)) {
                Storage::delete($category->image);
                $category->image = $request->file('image')->store('categories');
            }
            $category->save();

            flash('Category updated successfully.')->success();
            return redirect()->route('admin.categories.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if(!empty($request->action) && $request->action == 'delete_all'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            Storage::delete(Category::whereIn('id',explode(',',$request->ids))->pluck('image')->toArray());
            Category::destroy(explode(',',$request->ids));
            $content['status']=200;
            $content['message'] = "Categories deleted successfully.";
            return response()->json($content);
        }else{
            Storage::delete(Category::whereIn('id',explode(',',$id))->pluck('image')->toArray());
            Category::destroy($id);
            if(request()->ajax()){
                $content = array('status'=>200, 'message'=>"Category deleted successfully.");
                return response()->json($content);
            }else{
                flash('Category deleted successfully.')->success();
                return redirect()->route('admin.categories.index');
            }
        }
    }

    /**
     * Listing the all resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $categories = Category::where('id', '<>', 0)->orderBy('id','DESC');

        if($search != ''){
            $categories->where(function($query) use ($search){
                $query->where("name", "like", "%{$search}%");
            });
        }
        $count = $categories->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

        $categories = $categories->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($categories as $category) {
            $params = array(
                'url'=>route('admin.categories.update',$category->id),
                'checked'=> ($category->active == 0) ? "checked" : "",
                'getaction'=>'',
                'class'=>'',
                'id' => $category->id
            );
            $records['data'][] = [
                'checkbox'=>view('admin.shared.checkbox')->with('id',$category->id)->render(),
                'name' => $category->name,
                'type' => $category->type,
                'sequence' => $category->sequence,
                'image' => '<img src="' . Storage::url('app/public/'.$category->image) . '" alt="Image" class="img-thumbnail" />',
                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $category->id)->render(),
            ];
        }
        return $records;
    }
}

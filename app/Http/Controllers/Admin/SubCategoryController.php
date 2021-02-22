<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubCategoryController extends Controller
{

	public function index()
	{
		return view('admin.pages.subcategories.index');
	}


	public function create()
	{
		$categories=Category::where('active',1)->get();
		return view('admin.pages.subcategories.create',compact('categories'));
	}


	public function store(Request $request)
	{
		$rules = [
			'name' => 'required|string',
			'category_id' => 'required',
			// 'image' => 'required|image',
		];
		$this->validateForm($request->all(), $rules);
		$categories_type=Category::where('id',$request->category_id)->value('type');

		$subcategory = new SubCategoryModel;
		$subcategory->category_id = $request->category_id;
		$subcategory->name = $request->name;

		if($categories_type ==='cake'){
			$subcategory->subcat_type = 0;
		}else if($categories_type ==='bakery'){
			$subcategory->subcat_type = 1;
		}else {
			$subcategory->subcat_type = 2;
		}
		$subcategory->image = $request->file('image')->store('subcategories');
		$subcategory->save();

		flash('Sub Category added successfully.')->success();
		return redirect()->route('admin.subcategories.index');
	}


	public function show(SubCategoryModel $subcategory)
	{
		abort(404);
	}


	public function edit(SubCategoryModel $subcategory)
	{
		$categories=Category::all();
		return view('admin.pages.subcategories.edit', compact('subcategory','categories'));
	}


	public function update(Request $request, SubCategoryModel $subcategory)
	{
		if(!empty($request->action) && $request->action == 'change_status'){
			$content = ['status'=>204, 'message'=>"something went wrong"];
			$subcategory->active = ($request->value == 'y' ? 0 : 1);
			$subcategory->save();
			$content['status']=200;
			$content['message'] = "Status updated successfully.";
			return response()->json($content);
		}else{
			$rules = [
				'name' => 'required|string',
				'category_id' => 'required',
			// 'image' => 'required|image',
			];
			$this->validateForm($request->all(), $rules);
			$categories_type=Category::where('id',$request->category_id)->value('type');

			$subcategory->category_id = $request->category_id;
			$subcategory->name = $request->name;

			if($categories_type ==='cake'){
				$subcategory->subcat_type = 0;
			}else if($categories_type ==='bakery'){
				$subcategory->subcat_type = 1;
			}else {
				$subcategory->subcat_type = 2;
			}

			if (!empty($request->image)) {
				Storage::delete($subcategory->image);
				$subcategory->image = $request->file('image')->store('subcategories');
			}
			$subcategory->save();

			flash('Sub Category updated successfully.')->success();
			return redirect()->route('admin.subcategories.index');
		}
	}


	public function destroy(Request $request, $id)
	{
		if(!empty($request->action) && $request->action == 'delete_all'){
			$content = ['status'=>204, 'message'=>"something went wrong"];
			Storage::delete(SubCategoryModel::whereIn('id',explode(',',$request->ids))->pluck('image')->toArray());
			SubCategoryModel::destroy(explode(',',$request->ids));
			$content['status']=200;
			$content['message'] = "Sub Categories deleted successfully.";
			return response()->json($content);
		}else{
			Storage::delete(SubCategoryModel::whereIn('id',explode(',',$id))->pluck('image')->toArray());
			SubCategoryModel::destroy($id);
			if(request()->ajax()){
				$content = array('status'=>200, 'message'=>"Sub Category deleted successfully.");
				return response()->json($content);
			}else{
				flash('Sub Category deleted successfully.')->success();
				return redirect()->route('admin.subcategories.index');
			}
		}
	}


	public function listing(Request $request)
	{
		extract($this->DTFilters($request->all()));
		$subcategories = SubCategoryModel::where('id', '<>', 0)->orderBy('id','DESC');

		if($search != ''){
			$subcategories->where(function($query) use ($search){
				$query->where("name", "like", "%{$search}%");
			});
		}
		$count = $subcategories->count();

		$records["recordsTotal"] = $count;
		$records["recordsFiltered"] = $count;
		$records['data'] = array();

		$subcategories = $subcategories->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

		foreach ($subcategories as $subcategory) {
			$params = array(
				'url'=>route('admin.subcategories.update',$subcategory->id),
				'checked'=> ($subcategory->active == 0) ? "checked" : "",
				'getaction'=>'',
				'class'=>'',
				'id' => $subcategory->id
			);
			$records['data'][] = [
				'checkbox'=>view('admin.shared.checkbox')->with('id',$subcategory->id)->render(),
				'category'=>$subcategory->category->name,
				'name' => $subcategory->name,
				'image' => '<img src="' . Storage::url('app/public/'.$subcategory->image) . '" alt="Image" class="img-thumbnail" />',
				'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
				'action' => view('admin.shared.actions')->with('id', $subcategory->id)->render(),
			];
		}
		return $records;
	}

}

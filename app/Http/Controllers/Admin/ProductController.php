<?php

namespace App\Http\Controllers\Admin;

use App\Franchise;
use App\Http\Controllers\Controller;
use App\Models\Flavour;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PriceModel;
use App\Models\Product;
use App\Models\SubCategoryModel;
use App\Models\Weight;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
class ProductController extends Controller
{
    public function index()
    {

        return view('admin.pages.products.index');
    }
 public function report()
    {
        $products = Product::where('id', '<>', 0)->orderBy('id','DESC')->get();
        $users=User::all();
        $franchises=Franchise::all();
        return view('admin.pages.products.report',compact('products','franchises','users'));
    }

    public function importProductIndex()
    {

        return view('admin.pages.products.import');
    }
    public function importProduct(Request $request)
    {

        Excel::import(new ProductsImport,request()->file('importfile'));

        return back();
    }


    public function create()
    {
        $prices =PriceModel::where('active',1)->get();
        $flavours = Flavour::where('active',1)->get();
        $categories = SubCategoryModel::where('active',1)->get();
        return view('admin.pages.products.create',compact('flavours','categories','prices'));
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
        	'subcategory_id'=>'required',
            'name' => 'required',
            'description' => 'required',
            'code' => 'required',
            // 'image' => 'required|image',
            'weights' => 'nullable|array',
            'flavours' => 'nullable|array',
            'type'=>'nullable|array',
        ];
        $this->validateForm($request->all(), $rules);

        // dd($request->all());
        $product = new Product;
        $product->subcategory_id=$request->subcategory_id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->code = $request->code;
        $product->hsn_code = $request->hsn_code;
        $product->gst_price = $request->gst_price;
        $product->cgst = $request->cgst;
        $product->sgst = $request->sgst;

        $product->image = !empty($request->file('image'))? $request->file('image')->store('product') : '';
        if($request->price){
         $product->price=$request->price;
     }
     $product->save();

     if (!empty($request->weights)) {
        $weights = [];
        foreach ($request->weights as $key => $value) {
            $weights[] = new Weight(['weight' => $value]);
        }
        $product->weights()->delete();
        $product->weights()->saveMany($weights);
    }

    if (!empty($request->flavours)) {
        $input_flavours = [];
        foreach ($request->flavours as $flavour) {
            if (!empty($request->is_default) && $request->is_default == $flavour) {
                $input_flavours[$flavour] = ['is_default' => 1];
            } else {
                $input_flavours[$flavour] = ['is_default' => 0];
            }
        }
        $product->flavours()->sync($input_flavours);
    }

        // if (!empty($request->categories)) {
        //     $cake->categories()->sync($request->categories);
        // }

    if (!empty($request->types)) {
        $product->prices()->sync($request->types);
    }

    flash('Product added successfully.')->success();
    return redirect()->route('admin.products.index');
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Product $products)
    {
        abort(404);
    }

    public function edit(Product $product)
    {
        // dd($product);
        $prices =PriceModel::where('active',1)->get();
        $flavours = Flavour::where('active',1)->get();
        $categories = SubCategoryModel::where('active',1)->get();
        // $product_price=$product->prices->pluck('price_model_id')->toArray();
        $product_price=DB::table('price_model_product')->where('product_id',$product->id)->pluck('price_model_id')->toArray();
        // dd($product_price);
        $product_flavours = $product->flavours()->pluck('flavours.id')->toArray();
        $product_default_flavour = $product->flavours()->where('is_default', 1)->first()->id ?? 0;
        // dd($product_price);
        // $product_categories = $product->categories()->pluck('categories.id')->toArray();
        // dd($product_flavours);
        return view('admin.pages.products.edit', compact('product','flavours', 'product_flavours','categories', 'product_categories', 'product_default_flavour','prices','product_price'));
    }


    public function update(Request $request,Product $product)
    {
        // dd($request->all());
        if(!empty($request->action) && $request->action == 'change_status'){
            $content = ['status'=>204, 'message'=>"something went wrong"];
            $product->active = ($request->value == 'y' ? 0 : 1);
            $product->save();
            $content['status']=200;
            $content['message'] = "Status updated successfully.";
            return response()->json($content);
        }else{
            $rules = [
            	'subcategory_id' =>'required',
                'name' => 'required',
                'description' => 'required',
                'code' => 'required',
                'image' => 'nullable|image'
            ];
            $this->validateForm($request->all(), $rules);

            $products = Product::where('id',$request->product_id)->first();
            $products->subcategory_id = $request->subcategory_id;
            $products->name = $request->name;
            $products->description = $request->description;
            $products->code = $request->code;
            $product->hsn_code = $request->hsn_code;
            $product->gst_price = $request->gst_price;
            $product->cgst = $request->cgst;
            $product->sgst = $request->sgst;

            if (!empty($request->image)) {
                Storage::delete($products->image);

                $products->image = $request->file('image')->store('product');
            }

            if($request->price){
               $products->price=$request->price;
           }
           $products->save();

           if (!empty($request->weights)) {
            $weights = [];
            foreach ($request->weights as $key => $value) {
                $weights[] = new Weight(['weight' => $value]);
            }
            $products->weights()->delete();
            $products->weights()->saveMany($weights);
        }

        if (!empty($request->flavours)) {
            $input_flavours = [];
            foreach ($request->flavours as $flavour) {
                if (!empty($request->is_default) && $request->is_default == $flavour) {
                    $input_flavours[$flavour] = ['is_default' => 1];
                } else {
                    $input_flavours[$flavour] = ['is_default' => 0];
                }
            }
            $products->flavours()->sync($input_flavours);
        }

        if (!empty($request->categories)) {
            $products->categories()->sync($request->categories);
        }

        if (!empty($request->types)) {
// dd($request->types);
            $products->prices()->sync($request->types);
        }


        flash('product updated successfully.')->success();
        return redirect()->route('admin.products.index');
    }
}

public function destroy(Request $request, $id)
{
    if(!empty($request->action) && $request->action == 'delete_all'){
        $content = ['status'=>204, 'message'=>"something went wrong"];
        Storage::delete(Product::whereIn('id',explode(',',$request->ids))->pluck('image')->toArray());

        DB::table('flavour_product')->where('product_id',explode(',',$request->ids))->delete();

        Product::destroy(explode(',',$request->ids));
        $content['status']=200;
        $content['message'] = "Product deleted successfully.";
        return response()->json($content);
    }else{
        Storage::delete(Product::whereIn('id',explode(',',$id))->pluck('image')->toArray());
        DB::table('flavour_product')->where('product_id',$id)->delete();
        Product::destroy('id',$id);

        if(request()->ajax()){
            $content = array('status'=>200, 'message'=>"Product deleted successfully.");
            return response()->json($content);
        }else{
            flash('Product deleted successfully.')->success();
            return redirect()->route('admin.products.index');
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
     $products = Product::where('id', '<>', 0)->orderBy('id','DESC');
         // dd($products);
     if($search != ''){
        $products->where(function($query) use ($search){
            $query->where("name", "like", "%{$search}%");
            $query->where("code", "like", "%{$search}%");
        });
    }
    $count = $products->count();

    $records["recordsTotal"] = $count;
    $records["recordsFiltered"] = $count;
    $records['data'] = array();

    $products = $products->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

    foreach ($products as $product) {
        $params = array(
            'url'=>route('admin.products.update',$product->id),
            'checked'=> ($product->active == 0) ? "checked" : "",
            'getaction'=>'',
            'class'=>'',
            'id' => $product->id
        );
            // dd($params);;
       $image= !empty($product->image) ? Storage::url('app/public/'.$product->image  ):url('public/theme/images/logo.png') ;
        $records['data'][] = [
            'checkbox'=>view('admin.shared.checkbox')->with('id',$product->id)->render(),
            'name' => $product->name,
            'description' => $product->description,
            'total'=>OrderItem::where('product_id',$product->id)->count('id'),
            'code' => $product->code,
            'image' => '<img src="' . $image . '" alt="Image" class="img-thumbnail" />',
            'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),
            'action' => view('admin.shared.actions')->with('id', $product->id)->render(),
        ];
    }
    return $records;
}

    public function search(Request $request)
    {

    }
}

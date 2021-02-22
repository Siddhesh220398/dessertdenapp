<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;



use App\Models\PriceCategoryModel;

use App\Models\PriceModel;

use Illuminate\Http\Request;



class PriceCategoryController extends Controller

{

    public function index()

    {

        return view('admin.pages.cakeprices.index');

    }



    public function create()

    {

    	$types=PriceModel::all();

        return view('admin.pages.cakeprices.create',compact('types'));

    }



    

    public function store(Request $request)

    {

        $rules = [

            'cat_name' => 'required',

            'types'=>'required',

            'price'=>'required',

            

        ];

        $this->validateForm($request->all(), $rules);

        

        // dd($request->types);

        $price = new PriceCategoryModel();

        $price->price_id = $request->types;

        $price->cat_name = $request->cat_name;

        $price->price = $request->price;

       

        $price->save();



        flash('Prices added successfully.')->success();

        return redirect()->route('admin.cakeprices.index');

    }



   

    public function show(PriceModel $price)

    {

        abort(404);

    }



     

  public function edit(PriceCategoryModel $cakeprice)

    {

    	$types=PriceModel::all();

    	// dd($types);

        return view('admin.pages.cakeprices.edit', compact('cakeprice','types'));

    }

    

    public function update(Request $request, PriceCategoryModel $price,$id)

    {    	

        if(!empty($request->action) && $request->action == 'change_status'){

            $content = ['status'=>204, 'message'=>"something went wrong"];
            PriceCategoryModel::where('id',$id)->update(['active' => ($request->value == 'y' ? 0 : 1)]);

            // $price->active = ($request->value == 'y' ? 0 : 1);

            // $price->save();

            $price['status']=200;

            $content['message'] = "Status updated successfully.";

            return response()->json($content);

        }else{

            

            $rules = [

            'cat_name' => 'required',

            'types'=>'required',

            'price'=>'required',            

        	];

            $this->validateForm($request->all(), $rules);

            PriceCategoryModel::where('id',$request->pr_id)->update([
                'price_id'=> $request->types,
                'cat_name'=> $request->cat_name,
                'price' => $request->price,
            ]);
            // dd($request->pr_id);


            

            flash('Prices updated successfully.')->success();

            return redirect()->route('admin.cakeprices.index');

        }

    }



    

    public function destroy(Request $request, $id)

    {

        if(!empty($request->action) && $request->action == 'delete_all'){

            $content = ['status'=>204, 'message'=>"something went wrong"];

           

            PriceCategoryModel::destroy(explode(',',$request->ids));

            $content['status']=200;

            $content['message'] = "Prices deleted successfully.";

            return response()->json($content);

        }else{    

           

            PriceCategoryModel::destroy($id);

            if(request()->ajax()){

                $content = array('status'=>200, 'message'=>"Price deleted successfully.");

                return response()->json($content);

            }else{

                flash('Prices deleted successfully.')->success();

                return redirect()->route('admin.cakeprices.index');

            }

        }

    }



   

    public function listing(Request $request)

    {

        extract($this->DTFilters($request->all()));

        $prices = PriceCategoryModel::where('id', '<>', 0)->orderBy('id','DESC');

        

        // dd($prices);

        if($search != ''){

            $prices->where(function($query) use ($search){

                $query->where("id", "like", "%{$search}%");

            });

        }

        $count = $prices->count();



        $records["recordsTotal"] = $count;

        $records["recordsFiltered"] = $count;

        $records['data'] = array();



        $prices = $prices->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();



        

        foreach ($prices as $price) {

            $params = array(

                'url'=>route('admin.cakeprices.update',$price->id),

                'checked'=> ($price->active == 0) ? "checked" : "",

                'getaction'=>'',

                'class'=>'',

                'id' => $price->id

            );

            $records['data'][] = [

                'checkbox'=>view('admin.shared.checkbox')->with('id',$price->id)->render(),

                'cat_name' => $price->cat_name,

                'price' => $price->price,

               

                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),

                'action' => view('admin.shared.actions')->with('id', $price->id)->render(),

            ];

        }

        return $records; 

    }

}


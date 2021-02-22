<?php



namespace App\Http\Controllers\Admin;



use App\Models\City;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;



class CityController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index()

    {

        return view('admin.pages.cities.index');

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        return view('admin.pages.cities.create');

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

            'city_type' => 'required',



        ];

        $this->validateForm($request->all(), $rules);

        

        $city = new City;

        $city->name = $request->name;

        $city->city_type =$request->city_type;

        $city->save();



        flash('City added successfully.')->success();

        return redirect()->route('admin.cities.index');

    }



    /**

     * Display the specified resource.

     *

     * @param  \App\Models\City  $city

     * @return \Illuminate\Http\Response

     */

    public function show(City $city)

    {

        abort(404);

    }



    /**

     * Show the form for editing the specified resource.

     *

     * @param  \App\Models\City  $city

     * @return \Illuminate\Http\Response

     */

    public function edit(City $city)

    {

        return view('admin.pages.cities.edit', compact('city'));

    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \App\Models\City  $city

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, City $city)

    {

        if(!empty($request->action) && $request->action == 'change_status'){

            $content = ['status'=>204, 'message'=>"something went wrong"];

            $city->active = ($request->value == 'y' ? 0 : 1);

            $city->save();

            $content['status']=200;

            $content['message'] = "Status updated successfully.";

            return response()->json($content);

        }else{

            $rules = [

                'name' => 'required|string',

                'city_type' => 'required',

            ];

            $this->validateForm($request->all(), $rules);

            

            $city->name = $request->name;

            $city->city_type =$request->city_type;

            $city->save();

            

            flash('City updated successfully.')->success();

            return redirect()->route('admin.cities.index');

        }

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  \App\Models\City  $city

     * @return \Illuminate\Http\Response

     */

    public function destroy(Request $request, $id)

    {

        if(!empty($request->action) && $request->action == 'delete_all'){

            $content = ['status'=>204, 'message'=>"something went wrong"];

            City::destroy(explode(',',$request->ids));

            $content['status']=200;

            $content['message'] = "Cities deleted successfully.";

            return response()->json($content);

        }else{    

            City::destroy($id);

            if(request()->ajax()){

                $content = array('status'=>200, 'message'=>"City deleted successfully.");

                return response()->json($content);

            }else{

                flash('City deleted successfully.')->success();

                return redirect()->route('admin.cities.index');

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

        $cities = City::where('id', '<>', 0)->orderBy('id','DESC');

        

        if($search != ''){

            $cities->where(function($query) use ($search){

                $query->where("name", "like", "%{$search}%");

            });

        }

        $count = $cities->count();



        $records["recordsTotal"] = $count;

        $records["recordsFiltered"] = $count;

        $records['data'] = array();



        $cities = $cities->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();



        foreach ($cities as $city) {

            $params = array(

                'url'=>route('admin.cities.update',$city->id),

                'checked'=> ($city->active == 0) ? "checked" : "",

                'getaction'=>'',

                'class'=>'',

                'id' => $city->id

            );

            $records['data'][] = [

                'checkbox'=>view('admin.shared.checkbox')->with('id',$city->id)->render(),

                'name' => $city->name,

                'type'=>$city->city_type,

                'active' => view('admin.shared.switch')->with(['params'=> $params])->render(),

                'action' => view('admin.shared.actions')->with('id', $city->id)->render(),

            ];

        }

        return $records; 

    }

}


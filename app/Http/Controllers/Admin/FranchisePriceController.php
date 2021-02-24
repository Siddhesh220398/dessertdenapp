<?php

namespace App\Http\Controllers\Admin;

use App\Franchise;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FranchisePrice;
use App\Models\PriceType;
use Illuminate\Http\Request;

class FranchisePriceController extends Controller
{

    public function index()
    {
        return view('admin.pages.franchisesprice.index');
    }

    public function create()
    {
        $franchises = Franchise::where('active', 1)->get();
        $pricetypes = PriceType::all();
        $categories = Category::where('active', 1)->get();
        return view('admin.pages.franchisesprice.create', compact('franchises','pricetypes', 'categories'));
    }

    public function store(Request $request)
    {
//        dd($request->all());
        $rules = [
            'franchise_id' => 'required',

        ];
        $this->validateForm($request->all(), $rules);
        foreach ($request->percentage as $key => $val) {
            if ($val != 0) {
                $fp = FranchisePrice::where(['franchise_id' => $request->franchise_id, 'category_id' => $key])->first();
                if ($fp) {
                    $fp->update(['percentage' => $val]);
                } else {
                    FranchisePrice::create(['franchise_id' => $request->franchise_id, 'category_id' => $key, 'percentage' => $val]);
                }
            }
        }

        flash('franchise Price added successfully.')->success();
        return redirect()->route('admin.franchisesprice.index');
    }

    public function show(FranchisePrice $franchiseprice)
    {
        abort(404);
    }

    public function edit(Franchise $franchisesprice)
    {

        $franchises = Franchise::where('active', 1)->get();
        $pricetypes = PriceType::all();
        $categories = Category::where('active', 1)->get();
        return view('admin.pages.franchisesprice.edit', compact('franchisesprice', 'franchises', 'categories', 'pricetypes'));
    }

    public function update(Request $request, Franchise $franchisesprice)
    {
        $rules = [
            'franchise_id' => 'required',
        ];

        $this->validateForm($request->all(), $rules);
        foreach ($request->percentage as $key => $val) {
            if ($val != 0) {
                $fp = FranchisePrice::where(['franchise_id' => $request->franchise_id, 'category_id' => $key])->first();
                if ($fp) {
                    $fp->update(['percentage' => $val]);
                } else {
                    FranchisePrice::create(['franchise_id' => $request->franchise_id, 'category_id' => $key, 'percentage' => $val]);
                }
            }
        }
        flash('Franchise updated successfully.')->success();
        return redirect()->route('admin.franchisesprice.index');

    }

    public function destroy(Request $request, $id)
    {
        if (!empty($request->action) && $request->action == 'delete_all') {
            $content = ['status' => 204, 'message' => "something went wrong"];
            FranchisePrice::destroy(explode(',', $request->ids));
            $content['status'] = 200;
            $content['message'] = "Franchises deleted successfully.";
            return response()->json($content);
        } else {
            FranchisePrice::destroy($id);
            if (request()->ajax()) {
                $content = array('status' => 200, 'message' => "franchise deleted successfully.");
                return response()->json($content);
            } else {
                flash('franchise deleted successfully.')->success();
                return redirect()->route('admin.franchisesprice.index');
            }
        }
    }

    public function listing(Request $request)
    {
        extract($this->DTFilters($request->all()));
        $franchises = FranchisePrice::groupBy('franchise_id')->get('franchise_id');


//        if($search != ''){
//            // $id=Franchise::where("name", "like", "%{$search}%")->value('id');
//            $franchises->where(function($query) use ($search){
//                $query->where("id", "like", "%{$search}%");
//            });
//        }
        $count = $franchises->count();

        $records["recordsTotal"] = $count;
        $records["recordsFiltered"] = $count;
        $records['data'] = array();

//        $franchises = $franchises->offset($offset)->limit($limit)->orderBy($sort_column,$sort_order)->get();

        foreach ($franchises as $franchise) {
//            dd($franchise);
            $params = array(
                'url' => route('admin.franchisesprice.update', $franchise->franchise_id),
                'checked' => ($franchise->active == 0) ? "checked" : "",
                'getaction' => '',
                'class' => '',
                'id' => $franchise->franchise_id
            );
            $ar = array();
            $categories = FranchisePrice::where('franchise_id', $franchise->franchise_id)->get();
            foreach ($categories as $category) {

                $ar[] = $category->category->name . ' - ' . $category->percentage . '%  <br>';

            }

            $records['data'][] = [
                'checkbox' => view('admin.shared.checkbox')->with('id', $franchise->franchise_id)->render(),
                'franchise_id' => $franchise->franchise->name,
                'category_id' => $ar,
//    			'percentage' => $franchise->percentage,
                'active' => view('admin.shared.switch')->with(['params' => $params])->render(),
                'action' => view('admin.shared.actions')->with('id', $franchise->franchise_id)->render(),
            ];
        }
        return $records;
    }

    public function select(Request $request)
    {
        $percentage = PriceType::where('id', $request->pricetype_id)->value('percentage');
        $content['status'] = 200;
        $content['percentage'] = $percentage;
        return response()->json($content);

    }
}

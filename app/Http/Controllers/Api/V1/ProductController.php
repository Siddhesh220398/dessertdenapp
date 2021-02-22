<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SubCategoryModel;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

     private function productssFields($products)
    {
        $fields = [];

        foreach ($products as $product) {
            $prices = [];
            foreach ($product->prices as $price) {
                $values_price = $price->priceCat()->get(['id', 'cat_name', 'price']);
                $price_array = [];
                foreach ($values_price as $k => $p) {
                    $price_array[] = ['id' => $p['id'],
                        'cat_name' => $p['cat_name'], 'price' => (intval($p['price']))];
                }
                $prices[] = ['type' => $price->name, 'values' => $price_array];

            }

            $weight = $product->weights()->pluck('weight')->toArray();
            $weights = [];
            foreach ($weight as $key => $value) {
                array_push($weights, floatval($value));
            }
            $flavours_d = [];
            $flavour_default = $product->flavours()->where('is_default', 1)->get();
            foreach ($flavour_default as $key) {
                $flavours_d = [
                    'id' => $key->id,
                    'name' => $key->name,
                    'rate' => intval($key->rate)
                ];
            }
            $flavour_d = json_encode($flavours_d);
            $pricest = 0;
            foreach ($product->prices as $pricet) {
                $pricest = $price->priceCat()->where('price_id', '<>', 2)->value('price');
            }
            $pricest += $pricest;

            $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();


            $flavourprice = !empty($product->flavours()->where('is_default', 1)->value('rate')) ? $product->flavours()->where('is_default', 1)->value('rate') : 0;
            $fields[] = [
                'id' => $product->id,
                'name' => $product->name,
                'is_franchise' => $product->is_franchise,
                'is_like' =>  0,
                'category_id' => $subcategory->category_id,
                'subcategory_type' => $subcategory->subcat_type,
                'description' => $product->description,
                'instruction' => $product->instruction,
                'code' => $product->code,
                'weight' => !empty($weights) ? $weights : NULL,
                // 'weight'=>Weight::where('product_id',$product->id)->get('weight'),
                'price' => $product->price,
                'rate' => ((!empty($pricest) ? $pricest : 0) + $product->price + $flavourprice),
                'quantity' => $product->quantity,
                'types' => (!empty($prices)) ? $prices : [],
                'image' => !empty($product->image) ? Storage::url('app/public/' . $product->image) : NULL,
                'default' => !empty($flavour_d) ? json_decode($flavour_d) : NULL,
                'active' => $product->active,
                'today_special' => $product->today_special,

            ];
        }
        return $fields;

    }

    private function productsFields($products, $user_id)
    {
        $fields = [];

        foreach ($products as $product) {
            $prices = [];
            foreach ($product->prices as $price) {
                $values_price = $price->priceCat()->get(['id', 'cat_name', 'price']);
                $price_array = [];
                foreach ($values_price as $k => $p) {
                    $price_array[] = ['id' => $p['id'],
                        'cat_name' => $p['cat_name'], 'price' => (intval($p['price']))];
                }
                $prices[] = ['type' => $price->name, 'values' => $price_array];

            }

            $weight = $product->weights()->pluck('weight')->toArray();
            $weights = [];
            foreach ($weight as $key => $value) {
                array_push($weights, floatval($value));
            }
            $flavours_d = [];
            $flavour_default = $product->flavours()->where('is_default', 1)->get();
            foreach ($flavour_default as $key) {
                $flavours_d = [
                    'id' => $key->id,
                    'name' => $key->name,
                    'rate' => intval($key->rate)
                ];
            }
            $flavour_d = json_encode($flavours_d);
            $pricest = 0;
            foreach ($product->prices as $pricet) {
                $pricest = $price->priceCat()->where('price_id', '<>', 2)->value('price');
            }
            $pricest += $pricest;

            $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            $like = Wishlist::where(['product_id' => $product->id, 'user_id' => $user_id])->first();

            $flavourprice = !empty($product->flavours()->where('is_default', 1)->value('rate')) ? $product->flavours()->where('is_default', 1)->value('rate') : 0;
            $fields[] = [
                'id' => $product->id,
                'name' => $product->name,
                'is_franchise' => $product->is_franchise,
                'is_like' => (!empty($like)) ? 1 : 0,
                'category_id' => $subcategory->category_id,
                'subcategory_type' => $subcategory->subcat_type,
                'description' => $product->description,
                'instruction' => $product->instruction,
                'code' => $product->code,
                'weight' => !empty($weights) ? $weights : NULL,
                // 'weight'=>Weight::where('product_id',$product->id)->get('weight'),
                'price' => $product->price,
                'rate' => ((!empty($pricest) ? $pricest : 0) + $product->price + $flavourprice),
                'quantity' => $product->quantity,
                'types' => (!empty($prices)) ? $prices : [],
                'image' => !empty($product->image) ? Storage::url('app/public/' . $product->image) : NULL,
                'default' => !empty($flavour_d) ? json_decode($flavour_d) : NULL,
                'active' => $product->active,
                'today_special' => $product->today_special,

            ];
        }
        return $fields;

    }

    private function productsfFields($products, $franchise_id)
    {
        $fields = [];

        foreach ($products as $product) {
            $prices = [];
            foreach ($product->prices as $price) {
                $values_price = $price->priceCat()->get(['id', 'cat_name', 'price']);
                $price_array = [];
                foreach ($values_price as $k => $p) {
                    $price_array[] = ['id' => $p['id'],
                        'cat_name' => $p['cat_name'], 'price' => (intval($p['price']))];
                }
                $prices[] = ['type' => $price->name, 'values' => $price_array];

            }

            $weight = $product->weights()->pluck('weight')->toArray();
            $weights = [];
            foreach ($weight as $key => $value) {
                array_push($weights, floatval($value));
            }
            $flavours_d = [];
            $flavour_default = $product->flavours()->where('is_default', 1)->get();
            foreach ($flavour_default as $key) {
                $flavours_d = [
                    'id' => $key->id,
                    'name' => $key->name,
                    'rate' => intval($key->rate)
                ];
            }
            $flavour_d = json_encode($flavours_d);
            $pricest = 0;
            foreach ($product->prices as $pricet) {
                $pricest = $price->priceCat()->where('price_id', '<>', 2)->value('price');
            }
            $pricest += $pricest;

            $subcategory = SubCategoryModel::where('id', $product->subcategory_id)->first();
            $like = Wishlist::where(['product_id' => $product->id, 'franchise_id' => $franchise_id])->first();

            $flavourprice = !empty($product->flavours()->where('is_default', 1)->value('rate')) ? $product->flavours()->where('is_default', 1)->value('rate') : 0;
            $fields[] = [
                'id' => $product->id,
                'name' => $product->name,
                'is_franchise' => $product->is_franchise,
                'is_like' => (!empty($like)) ? 1 : 0,
                'category_id' => $subcategory->category_id,
                'subcategory_type' => $subcategory->subcat_type,
                'description' => $product->description,
                'instruction' => $product->instruction,
                'code' => $product->code,
                'weight' => !empty($weights) ? $weights : NULL,
                // 'weight'=>Weight::where('product_id',$product->id)->get('weight'),
                'price' => $product->price,
                'rate' => ((!empty($pricest) ? $pricest : 0) + $product->price + $flavourprice),
                'quantity' => $product->quantity,
                    'types' => (!empty($prices)) ? $prices : [],
                'image' => !empty($product->image) ? Storage::url('app/public/' . $product->image) : NULL,
                'default' => !empty($flavour_d) ? json_decode($flavour_d) : NULL,
                'active' => $product->active,
                'today_special' => $product->today_special,

            ];
        }
        return $fields;

    }

    private function productsDetailFields($products)
    {
        $prices = [];
        foreach ($products->prices as $price) {
            $values_price = $price->priceCat()->get(['id', 'cat_name', 'price']);
            $price_array = [];
            foreach ($values_price as $k => $p) {
                $price_array[] = ['id' => $p['id'],
                    'cat_name' => $p['cat_name'], 'price' => (intval($p['price']))];
            }
            $prices[] = ['type' => $price->name, 'values' => $price_array];
        }

        $weight = $products->weights()->pluck('weight')->toArray();
        $weights = [];
        foreach ($weight as $key => $value) {
            array_push($weights, floatval($value));
        }

        $flavours = $products->flavours()->get(['flavours.id', 'flavours.name', 'flavours.rate', 'is_default'])->toArray();
        $flavours_p = [];
        $flavours_d = [];
        foreach ($flavours as $key => $value) {
            $flavours_p[] = [
                'id' => $value['id'],
                'name' => $value['name'],
                'rate' => floatval($value['rate']),
                'is_default' => intval($value['is_default']),
            ];
        }

        $flavours_d[] = $flavours_p;
        $pricest = 0;
        foreach ($products->prices as $pricet) {
            $pricest = $pricet->priceCat()->where('price_id', '<>', 2)->value('price');
        }
        $pricest += $pricest;
        $flavourprice = !empty($products->flavours()->where('is_default', 1)->value('rate')) ? $products->flavours()->where('is_default', 1)->value('rate') : 0;
        $subcategory = SubCategoryModel::where('id', $products->subcategory_id)->first();
        return [
            'id' => $products->id,
            'name' => $products->name,
            'is_franchise' => $products->is_franchise,
            'subcategory' => $subcategory->name,
            'category_id' => $subcategory->category_id,
            'subcategory_type' => $subcategory->subcat_type,
            'code' => $products->code,
            'rate' => ((!empty($pricest) ? $pricest : 0) + $products->price + $flavourprice),
            'rates' => ($pricest + $products->price),
            'image' => !empty($products->image) ? Storage::url('app/public/' . $products->image) : NULL,
            'description' => $products->description,
            'instruction' => $products->instruction,
            'weights' => !empty($weights) ? $weights : NULL,
            'types' => (!empty($prices)) ? $prices : [],
            'quantity' => !empty($products->quantity) ? $products->quantity : NULL,
            'flavours' => !empty($products->flavours()->get()) ? $products->flavours()->get(['flavours.id', 'flavours.name', 'flavours.rate', 'is_default'])->toArray() : NULL,
            'active' => $products->active,
            'today_special' => $products->today_special,
        ];
    }

    public function products(Request $request)
    {

        if (!empty($request->subcategory_id)) {
            $products = Product::where(['subcategory_id' => $request->subcategory_id])->get();

            $user_id = $request->user_id;
            $franchise_id = $request->franchise_id;
            if (!empty($request->user_id)) {
                $this->status = 200;
                $this->response['data'] = $this->productsFields($products, $user_id);
                $this->response['message'] = trans('api.list', ['entity' => 'Product']);
            } elseif (!empty($request->franchise_id)) {
                $this->status = 200;
                $this->response['data'] = $this->productsfFields($products, $franchise_id);
                $this->response['message'] = trans('api.list', ['entity' => 'Product']);
            }else{
                 $this->status = 200;
                $this->response['data'] = $this->productssFields($products);
                $this->response['message'] = trans('api.list', ['entity' => 'Product']);
            }
        }

        if (!empty($request->search)) {

            $products = Product::where('name', 'like', '%' . $request->search . '%')->get();

            if (!empty($products)) {
                $user_id = $request->user_id;
                $franchise_id = $request->franchise_id;
                if (!empty($request->user_id)) {
                    $this->status = 200;
                    $this->response['data'] = $this->productsFields($products, $user_id);
                    $this->response['message'] = trans('api.list', ['entity' => 'Product']);
                } elseif (!empty($request->franchise_id)) {
                    $this->status = 200;
                    $this->response['data'] = $this->productsfFields($products, $franchise_id);
                    $this->response['message'] = trans('api.list', ['entity' => 'Product']);
                }else{
                       $this->status = 200;
                    $this->response['data'] = $this->productssFields($products);
                    $this->response['message'] = trans('api.list', ['entity' => 'Product']);
                }
            } else {
                $this->status = 412;
                $this->response['message'] = "Product Not Available";
            }
        }
        return $this->return_response();
    }

    public function productDetails(Request $request)
    {
        if (!empty($request->product_id)) {
            $products = Product::where(['id' => $request->product_id])->first();
            if (!empty($products)) {
                $this->status = 200;
                $this->response['data'] = $this->productsDetailFields($products);
                $this->response['message'] = trans('api.list', ['entity' => 'Product']);
            } else {
                $this->status = 412;
                $this->response['message'] = "Product Not Available";
            }
        } else {
            $this->response['message'] = "Product Id is required";
        }
        return $this->return_response();
    }

    public function todaySpecial(Request $request)
    {
        // $subcategories = SubCategoryModel::where('subcat_type', 0)->get('id');


        $products = Product::where('today_special', 1)->get();
        // dd($products);
        $this->status = 200;
        $this->response['data'] = $this->productssFields($products);
        $this->response['message'] = trans('api.list', ['entity' => 'Product']);

        return $this->return_response();
    }

    public function productsname(Request $request)
    {
        // dd('hel');

        $products = Product::all();
        $this->status = 200;
        $this->response['data'] = $this->productssFields($products);
        $this->response['message'] = trans('api.list', ['entity' => 'Product']);
        return $this->return_response();
    }

    public function updateProduct(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'active' => 'required'
        ];
        if ($this->ApiValidator($request->all(), $rules)) {

            if (!empty($request->active)) {
                if ($request->active === 'yes') {
                    $product = Product::where('id', $request->product_id)->update(['active' => 1]);
                    $this->status = 200;
                    $this->response['message'] = "Product Updated";
                } else if ($request->active === 'no') {
                    $product = Product::where('id', $request->product_id)->update(['active' => 0]);
                    $this->status = 200;
                    $this->response['message'] = "Product Updated";

                }
            }


        }
        return $this->return_response();
    }

    public function updateTodaysSpecialProduct(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'today_special' => 'required'
        ];
        if ($this->ApiValidator($request->all(), $rules)) {

            if (!empty($request->today_special)) {
                if ($request->today_special === 'yes') {
                    $product = Product::where('id', $request->product_id)->update(['today_special' => 1]);
                    $this->status = 200;
                    $this->response['message'] = "Product Updated";
                } else if ($request->today_special === 'no') {
                    $product = Product::where('id', $request->product_id)->update(['today_special' => 0]);
                    $this->status = 200;
                    $this->response['message'] = "Product Updated";

                }
            }


        }
        return $this->return_response();
    }

}

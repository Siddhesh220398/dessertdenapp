<?php

namespace App\Imports;

use App\Models\Flavour;
use App\Models\PriceModel;
use App\Models\Product;
use App\Models\Weight;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
// use Modules\Product\Entities\Product;

class ProductsImport implements ToCollection, WithHeadingRow, WithValidation
{
	use Importable;

	public function collection(Collection $rows)
	{
		$rules = [
			'*.name' => 'required',
			'*.description' => 'required',
			'*.weight' => 'required|array',
			'*.price' => 'required|numeric|min:0|max:99999999999999',
			'*.code' => 'required|numeric|min:0|max:99999999999999',

		];

		$messages = [
			'*.name.required' => 'Name field is required',
			'*.description.required' => 'Description field is required',
			'*.price.required' => 'Price field is required',
			'*.price.numeric' => 'Price field is numeric',
				'*.price.min' => 'Minimum value for price is 0.',
			'*.price.max' => 'Minimum value for price is 99999999999999.',
			'*.code.required' => 'code field is required',
			'*.code.numeric' => 'code field is numeric',
			'*.code.min' => 'Minimum value for code is 0.',
			'*.code.max' => 'Minimum value for code is 99999999999999.',

		];


		foreach ($rows as $row) {
			$subcategory_id = DB::table('sub_category_models')->where('name', 'LIKE', '%' . $row['subcategory'] . '%')->value('id');

			$weights=explode(",",$row['weight']);
			$flavours=explode(",",$row['flavour']);
			$types=explode(",",$row['type']);
     // dd($flavours);


			$product=Product::updateOrCreate(['name' => $row['name'] ],['name' => $row['name'],'description'=> $row['description'],'subcategory_id'=> $subcategory_id,'price'=> $row['price'],'code' => $row['code']]);



			Weight::where('product_id',$product->id)->delete();
			foreach ($weights as $weight) {
				$productweight = new Weight();
				$productweight->product_id =  $product->id;
				$productweight->weight =  $weight;
				$productweight->save();
			}
			// $product->weights()->saveMany($weights);
			if(!empty($row['flavour']))
			{
				// dd($flavours);
				DB::table('flavour_product')->where('product_id',$product->id)->delete();
				foreach ($flavours as $flavour) {
					$flavour_id = Flavour::where('name',$flavour)->value('id');
					$flavour_default=Flavour::where('name', $row['default'])->value('id');
					// dd($flavour_id);
					if($flavour_id == $flavour_default)
					{
						$default=1;
					}else{
						$default=0;
					}

					DB::table('flavour_product')->insert(['product_id'=>$product->id,'flavour_id'=>$flavour_id, 'is_default'=>$default]);

				}

			}

			if(!empty($row['type'])){
				DB::table('price_model_product')->where('product_id',$product->id)->delete();
				foreach ($types as $type ) {
					$type_id =PriceModel::where('name',$type)->value('id');

					DB::table('price_model_product')->insert(['product_id'=>$product->id,'price_model_id'=>$type_id]);
				}
			}
		}
}
		public function model(array $row)
		{

			$subcategory_id = DB::table('sub_category_models')->where('name', 'LIKE', '%' . $row['subcategory'] . '%')->value('id');
			$product_id=Product::latest()->value('id');
			$product_id  = (!empty($product_id ) ? $product_id +1 : 1);
			$weights=explode(",",$row['weight']);
// dd($product_id);
			foreach ($weights as $weight) {
				$productweight = new Weight();
				$productweight->product_id =  $product_id;
				$productweight->weight =  $weight;
				$productweight->save();

			}
			return new Product([
				'name'                  => $row['name'],
				'description'           => $row['description'],
				'subcategory_id'          => $subcategory_id,
				'price'                 => $row['price'],
				'code'                 => $row['code'],
			]);


		}


		public function rules(): array
		{
			return [
				'name' => 'required',
				'description' => 'required',
				'price' => 'required|numeric|min:0|max:99999999999999',

			];
		}
	}

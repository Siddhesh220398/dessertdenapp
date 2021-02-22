<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Weight;
use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
// use Modules\Product\Entities\Product;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
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

        Validator::make($rows->toArray(), $rules, $messages)->validate();

        foreach ($rows as $row) {

           $subcategory_id = DB::table('sub_category_models')->where('name', 'LIKE', '%' . $row['subcategory'] . '%')->value('id');
           $product_id=Product::latest()->value('id');
           $product_id  = (!empty($product_id ) ? $product_id +1 : 1);
           $weights=explode(",",$rows['weight']);

           foreach ($rows['weight'] as $weight) {
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

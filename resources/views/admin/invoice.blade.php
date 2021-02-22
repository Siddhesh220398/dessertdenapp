<!DOCTYPE>
<html>
<head>
    <style type="text/css">
        #customers td, #customers th {
            border: 1px solid #ddd;
            padding: 2px;
            font-weight: bold;

        }

        #customers {
            
            border-collapse: collapse;
            width: 100%;
        }

        .child {
            border: 2px solid black;

            font-size: 12px;
        }

    </style>
    <title>Invoice Of Dessertden</title>
</head>
<body>

<center>

    <div>
        <table cellspacing="10px" id="customers" class="child ">
            <tr>
                <td rowspan="2" colspan="5">
                    <b style="text-align: center; font-size: 15px;"><label>Customer Name:
                            {{!empty($order->user_id ) ? $order->user()->value('first_name'). " " . $order->user()->value('last_name') : App\Franchise::where('id',$order->franchises_id)->value('name')}}</label></b>
                </td>
                <td colspan="3"><label>Order NO: {{$order->order_no}}  </label><br/>
                    <label>Date: {{\Carbon\Carbon::now()->format('d/m/Y')}} </label>
                </td>
            </tr>
            <tr>

                <td colspan="3"><label>Order Type: &nbsp;{{$order->type}} </label><br/>
                    <label>Delivery Date: &nbsp;{{$order->delivery_date}} </label>
                </td>

            </tr>

            @if($order->type == 'Normal')
                <thead>
                <tr>
                    <th>Sr</th>
                    <th>Product Name </th>
                    <th>Instruction/Message on Cake</th>
                    <th>Type</th>
                    <th>Photo Type </th>
                    <th>Quantity </th>
                    <th>Product Photo </th>
                    <th>Photo </th>
                </tr>
                </thead>
<tbody>
                @php
                    $tmp=0;
                @endphp
                @foreach($order->items as $item)
                    @php

                        $product=App\Models\Product::where('id',$item->product_id)->first();
                        $subcategory=App\Models\SubCategoryModel::where('id',$product->subcategory_id)->first();
                        if($order->franchises_id !=''){
                        $percentage=App\Models\FranchisePrice::where(['franchise_id'=>$order->franchises_id,'category_id'=>$subcategory->category_id])->value('percentage');
                    }
                        $priceRateName=[];
                        if(!empty($item->type_rate)){
                            $type_ids=json_decode( $item->type_rate);
                            $ar=[];
                            foreach ($type_ids as $key => $value) {
                                $priceRate=App\Models\PriceCategoryModel::where('id',$value)->first();					// array_push($priceRateName, $priceRate);
                                    array_push($ar,$priceRate->cat_name);

                            }

                        }


                        if(!empty($item->is_photo)){
                        $photoRate=App\Models\PriceCategoryModel::where('id',$item->is_photo)->first();
                        }

                     $tmp ++;
                    @endphp

                    <tr>

                        <td><label>{{$tmp}}</label></td>
                        <td><label>{{App\Models\Product::where('id',$item->product_id)->value('name')}}</label><br/>
                        <label>Fn:{{!empty($item->flavour_id) ? $item->flavour->name  : ''}}</label>
                        </td>
                        <td>Instruction:{{$item->instruction}} <br/> MoC:{{$item->message_on_cake}}</td>
                        <td><label> {{!empty($ar)?implode(",",$ar):'' }}  </label></td>
                        <td>{{!empty($photoRate->cat_name)?$photoRate->cat_name:''}}</td>
                        <td><label>{{!empty($item->weight) ? $item->weight .' Kg' : $item->qty .' qty'}}</label>
                        </td>
                        <td>
                            <img src="{{ !empty(App\Models\Product::where('id',$item->product_id)->value('image'))?\Storage::url('app/public/'. App\Models\Product::where('id',$item->product_id)->value('image')):public_path('theme/images/logo.png')}}" style="width: 50px; height: 50px;">

                        </td>
                        <td>
                            @if(!empty($item->image))
                                <img
                                    src="{{ public_path('orders/'.$item->image)}}" style="width: 200px; height: 200px;">

                            @endif
                        </td>

                    </tr>
                @endforeach
           
            @endif

</tbody>
        </table>
    </div>
</center>
</body>
</html>

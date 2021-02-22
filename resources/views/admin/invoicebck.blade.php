<!DOCTYPE>
<html>
<head>
    <style type="text/css">
        #customers td, #customers th {
            border: 1px solid #ddd;
            padding: 8px;
            font-weight: bold;

        }

        #customers {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .child {
            border: 2px solid black;

            font-size: 12px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
    <title>Invoice Of Dessertden</title>
</head>
<body style="margin-top:40px; ">

<center>
    <div>
        <table style="width:100%;  font-weight: bold;">
            <tr>
                <td colspan="4">
                    <b style="font-size: 30px;  text-align: left;"><label>JEWIN FOODS</label></b>
                </td>

                <td rowspan="3" colspan="4">
                    <center><label>TUMMY TREAT</label></center>
                </td>

                <td rowspan="3" colspan="3" style="width: 120px;">
                    <center><img src="{{$logo}}" style="width: 100px; height:20%;"></center>
                </td>

            </tr>
            <tr>
                <td colspan="4">
                    <b style="font-size: 12px;">
                        <label>Avadh Road, Off. Kalavad Road, Nr. Royal Enclave,
                            Rajkot-360035</label></b>
                </td>
            </tr>
            <tr>
                <td>
                    <b style="font-size: 12px;"><label>Mobile : +91 8000380038 <br/> Email :
                            smile@dessertden.com</label></b>
                </td>
            </tr>
        </table>
    </div>
    <div>
        <table cellspacing="10px" id="customers" class="child ">
        @foreach($orders as $order)
                <tr>
                    <td style="text-align: left;" colspan="3">
                        <label>Debit Memo</label>
                    </td>
                    <td style="text-align: center;" colspan="3"><label>TAX INVOICE </label></td>
                    <td style="text-align: right;" colspan="3"><label>Original</label></td>
                </tr>
                <tr>
                    <td rowspan="2" colspan="6">
                        <b style="text-align: center; font-size: 15px;"><label>
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
                    <tr>

                        <td><label>Sr</label></td>
                        <td><label>Product Name </label></td>
                        <td><label>Flavour Name </label></td>
                        <td><label>Type </label></td>
                        <td><label>Photo Type </label></td>
                        <td><label>Quantity </label></td>
                        <td><label>Product Image </label></td>
                        <td><label>Photo </label></td>
                        <td><label>Amount </label></td>
                    </tr>



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
                            <td><label>{{App\Models\Product::where('id',$item->product_id)->value('name')}}</label></td>
                            <td><label>{{!empty($item->flavour_id) ? $item->flavour->name  : ''}}</label></td>
                            <td><label> {{!empty($ar)?implode(",",$ar):'' }}  </label></td>
                            <td>{{!empty($photoRate->cat_name)?$photoRate->cat_name:''}}</td>
                            <td><label>{{!empty($item->weight) ? $item->weight .' Kg' : $item->qty .' qty'}}</label>
                            </td>
                            <td><img
                                    src="{{ !empty(App\Models\Product::where('id',$item->product_id)->value('image'))?public_path(App\Models\Product::where('id',$item->product_id)->value('image')):''}}"
                                    style="width: 200px; height: 200px;">
                            </td>
                            <td><img
                                    src="{{!empty($item->image)? public_path('orders/'.$item->image):''}}"
                                    style="width: 200px; height: 200px;">
                            </td>
                            <td><label>{{ $item->amount }}  </label></td>
                        </tr>
                    @endforeach
                @elseif($order->type == 'Custom')
                    @php
                        $tmp=0;
                        $item =$order->customitem;
                        $tmp ++;
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
                    @endphp


                    <td><label>Product Name </label></td>
                    <td><label>Flavour </label></td>
                    <td><label>Type </label></td>
                    <td><label>Photo </label></td>
                    <td><label>Quantity </label></td>
                    <td><label>Cake Image </label></td>
                    <td><label>Idea Image </label></td>
                    <td><label>Photo Image </label></td>
                    <td><label>Amount </label></td>

                    <tr>

                        <td>
                            <label>{{App\Models\SubCategoryModel::where('id',$item->sub_category_id)->value('name')}}</label>
                        </td>
                        <td>{{$item->flavour->name}}</td>
                        <td><label> {{!empty($ar)?implode(",",$ar):'' }}  </label></td>
                        <td>{{!empty($photoRate->cat_name)?$photoRate->cat_name:''}}</td>
                        <td><label>{{$item->weight}} kg </label></td>
                        <td><img
                                src="{{!empty(App\Models\OrderImage::where(['order_id'=>$order->id,'type'=>'cake'])->value('image')) ? public_path(App\Models\OrderImage::where(['order_id'=>$order->id,'type'=>'cake'])->value('image')) : ''}}"
                                alt="Image" class="img-thumbnail" style="width: 100px; height: 100px;" /></td>
                        <td>
                            <img
                                src="{{!empty(App\Models\OrderImage::where(['order_id'=>$order->id,'type'=>'idea'])->value('image')) ? public_path(App\Models\OrderImage::where(['order_id'=>$order->id,'type'=>'idea'])->value('image') ): ''}}"
                                alt="Image" class="img-thumbnail" style="width: 100px; height: 100px;" /></td>
                        <td><img
                                src="{{!empty($item->image)? public_path('orders/'.$item->image):''}}"
                                style="width: 200px; height: 200px;" />
                        </td>
                        <td><label>{{ $item->amount }}  </label></td>
                    </tr>

                @endif
                <tr>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td><label>Total </label></td>
                    <td><label>{{$order->total_amount}}  </label></td>

                    <td><label>{{$order->total_amount}}  </label></td>
                </tr>


                <tr>
                    <td colspan="4">
                        <b style="text-align: center;"><label>GSTIN No. : 24AUHPK6483G1ZG
                            </label></b>
                    </td>
                    <td colspan="5"><label>Amount Paid: {{$order->total_amount}} </label>

                    </td>
                </tr>
                <tr>
                    <td colspan="5" >
                        <b style="text-align: left;"><label>Terms & Condition : For, JEWIN FOODS
                                <ol>
                                    <li> Goods Once Sold Will Not Be Accepted.</li>
                                    <li>Subject to RAJKOT Jurisdiction Only. E.&.O.E</li>
                                </ol>
                            </label></b>
                    </td>

                    <td colspan="4" ><label>For, JEWIN FOODS </label>
                        <br/><br/><br/><br/>
                        <label>(Authorised Signatory)</label>

                    </td>
                </tr>


            @endforeach

            </table>
    </div>
</center>
</body>
</html>

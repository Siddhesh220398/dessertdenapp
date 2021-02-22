<!DOCTYPE>
<html>
<head>
    <style type="text/css">
        #customers td, #customers th {
            border: 1px solid #ddd;
        }

        #customers1 td, #customers th {
            border: 1px solid #ddd;
            font-weight: bold;
        }


        #customers {
            border-collapse: collapse;
            width: 100%;
        }

        #customers1 {

            border-collapse: collapse;
            width: 100%;
        }

        .child {
            border: 2px solid black;
            font-size: 11px;
        }

        .child1 {
            border: 2px solid black;
            font-size: 12px;
        }

        /*body{*/
        /*    text-align: center;*/
        /*}*/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2cm;
        }

        @page {
            /*margin: 10px 25px;*/
            margin-top: 10px;

            margin-right: 25px;
            margin-left: 25px;
        }
    </style>
    <title>Invoice Of Dessertden</title>
</head>
<body>


<table cellspacing="3px" id="customers" class="child">
    <thead>
    <tr>
        <td colspan="4">
            <b style="font-size: 18px;  text-align: left;"><label>JEWIN FOODS</label></b>
        </td>

        <td rowspan="3" colspan="3">
            <center><label>TUMMY TREAT</label></center>
        </td>

        <td rowspan="3" colspan="4">

            <center><img src="{{$img}}" style="width: 100px; ">
            </center>
        </td>

    </tr>
    <tr>
        <td colspan="4">
            <b style="font-size: 11px;">
                <label>Avadh Road, Off. Kalavad Road, Nr. Royal Enclave,
                    Rajkot-360035</label></b>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <b style="font-size: 11px;"><label>Mobile : +91 8000380038 <br/> Email :
                    smile@dessertden.com</label></b>
        </td>
    </tr>

    <tr>
        <td style="text-align: left;" colspan="4">
            <label>Debit Memo</label>
        </td>

        <td style="text-align: center;" colspan="3"><label>TAX INVOICE </label></td>
        <td style="text-align: right;" colspan="4"><label>Original</label></td>
    </tr>
    <tr style="font-size: 11px;">
        <td rowspan="2" colspan="5">

            <label>Customer

                Name: {{!empty($invoices->user_id) ? App\User::where('id',$invoices->user_id)->value('first_name') .'  '.App\User::where('id',$invoices->user_id)->value('last_name'):App\Franchise::where('id',$invoices->franchise_id)->value('name')}}
                <br/> Address
                :{{!empty($invoices->user_id) ? '-':App\Franchise::where('id',$invoices->franchise_id)->value('address')}}
            </label>
            <br/><br/>
            <label><strong>GSTIN
                    No</strong>: {{!empty($invoices->franchise_id)?App\Franchise::where('id',$invoices->franchise_id)->value('gstn_no') :''}}
            </label>

        </td>
        <td colspan="6"><label>Invoice No: {{$invoices->invoice_no}}   </label><br/>
            <label>Date: {{$invoices->invoice_date}}</label>
        </td>
    </tr>
    <tr>

        <td colspan="6" style="font-size: 11px;"><label>Delivery Man:
                &nbsp; {{App\Admin::where('id',$invoices->deliveryboy_id)->value('name')}}</label><br/>
            <label>Bill Prepared By:
                &nbsp; {{App\Admin::where('id',$invoices->admin_id)->value('name')}}</label>
        </td>

    </tr>

    <tr style="text-align: center; font-size: 10px;">
        <th style="width: 20px;"><label>Sr</label></th>
        <th><label>Product Name </label></th>
        <th><label>Order No</label></th>
        <th><label>Quantity </label></th>
        <th><label>HSN/SAC </label></th>
        <th><label>Rate </label></th>
        <th><label>Net Rate </label></th>

        <th><label>GST %</label></th>
        <th><label>CGST </label></th>
        <th><label>SGST </label></th>
        <th><label>Net Amount</label></th>
    </tr>
    </thead>
    @php
        $invoice_items = \App\Models\MainInvoiceItem::where(['invoice_id'=>$invoices->id])->get();
$temp=1;
$amount=0;
$gstp=0;
    @endphp

    <tbody>
    @foreach($invoice_items as $item)
        @php
            if(!empty(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->first())){

                           $weight= !empty(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight'))?
                           \App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight') . "
                           Kg":\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('qty') .' Pcs';
                           $weights= !empty(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight'))?
                           \App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight'):\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('qty');
                           $amount+=\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('amount');
                           $amount1=\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('amount');
                           $netprice=$amount1/($weights);

                           }else{
                           $weight=\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight');
                           $weights=\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight');
                           $amount+=\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('amount');
                           $amount1=\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('amount');
                           $netprice=$amount1/$weight;


}
                    $p_id=\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->first('product_id');
                                                    $category=0;
                                                    if(!empty($p_id)){

                                                    $p_value=\App\Models\Product::where('id',$p_id->product_id)->first();
                                                    $category=\App\Models\SubCategoryModel::where('id',$p_value->subcategory_id)->value('category_id');
                                                    }
                                                    else{
                                                    $category=\App\Models\SubCategoryModel::where('id',\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('sub_category_id'))->value('category_id');
                                                    }
                                                    if(!empty($invoices->franchise_id)){
                                                    $price=\App\Models\FranchisePrice::where(['franchise_id'=>$invoices->franchise_id,'category_id'=>$category])->value('percentage');
                                                    }else{
                                                    $order_id1=\App\Models\OrderItem::where('id',$item->item_id)->value('order_id');
                                                    $price=\App\Models\Order::where(['id'=>$order_id1,'user_id'=>$invoices->user_id])->value('coupon_value');
                                                    }
                   if(!empty($p_value))
       $gstp+=(((\App\Models\OrderItem::where('id',$item->item_id)->value('amount'))-(((\App\Models\OrderItem::where('id',$item->item_id)->value('amount'))/(100+$p_value->gst_price))*100))/2);
       else
   $gstp+=(((\App\Models\CustomOrder::where('id',$item->item_id)->value('amount'))-(((\App\Models\CustomOrder::where('id',$item->item_id)->value('amount'))/(100+$p_value->gst_price))*100))/2);

        @endphp

        <tr style="font-size: 9px;">
            <td>{{$temp++}}</td>
            <td>
                {{!empty(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->first('product_id'))?
                App\Models\Product::where('id',(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('product_id')))->value('name')
                : \App\Models\SubCategoryModel::where('id',\App\Models\CustomOrder::where('id',$item->item_id)->value('sub_category_id'))->value('name')}}
                <br/>Fn:
                {{!empty(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->first('product_id'))?App\Models\Flavour::where('id',(\App\Models\OrderItem::where('id',$item->item_id)->value('flavour_id')))->value('name'):App\Models\Flavour::where('id',(\App\Models\CustomOrder::where('id',$item->item_id)->value('flavour_id')))->value('name')}}

            </td>
            <td>{{\App\Models\Order::where('id',$item->order_id)->value('order_no')}}</td>

            <td>
                @php
                    if(!empty(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->first())){

                    $weight= !empty(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight'))?
                    \App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight') . "
                    Kg":\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('qty') .' Pcs';
                    $weights= !empty(\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight'))?
                    \App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight'):\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('qty');
                    $amount+=\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('amount');
                    $amount1=\App\Models\OrderItem::where('order_id',$item->order_id)->where('id',$item->item_id)->value('amount');
                    $netprice=$amount1/($weights);

                    }else{
                    $weight=\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight');
                    $weights=\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('weight');
                    $amount+=\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('amount');
                    $amount1=\App\Models\CustomOrder::where('order_id',$item->order_id)->where('id',$item->item_id)->value('amount');
                    $netprice=$amount1/$weight;


}
                @endphp
                {{$weight}}
            </td>
            @php

                @endphp

            <td>{{!empty($p_value)? $p_value->hsn_code :19059020}}</td>
            <td>{{$netprice}}</td>

            <td>{{!empty($p_value)? round((($amount1/(100+$p_value->gst_price))*100),2) : round((($amount1/(100+18))*100),2)}}</td>
            <td>{{!empty($p_value)? $p_value->gst_price : 18}}</td>
            <td>{{!empty($p_value)? round((((\App\Models\OrderItem::where('id',$item->item_id)->value('amount'))-(((\App\Models\OrderItem::where('id',$item->item_id)->value('amount'))/(100+$p_value->gst_price))*100))/2),2) :  round((((\App\Models\CustomOrder::where('id',$item->item_id)->value('amount'))-(((\App\Models\CustomOrder::where('id',$item->item_id)->value('amount'))/(100+$p_value->gst_price))*100))/2),2)}}</td>
            <td>{{!empty($p_value)? round((((\App\Models\OrderItem::where('id',$item->item_id)->value('amount'))-(((\App\Models\OrderItem::where('id',$item->item_id)->value('amount'))/(100+$p_value->gst_price))*100))/2),2) :  round((((\App\Models\CustomOrder::where('id',$item->item_id)->value('amount'))-(((\App\Models\CustomOrder::where('id',$item->item_id)->value('amount'))/(100+$p_value->gst_price))*100))/2),2)}}</td>
            <td>{{!empty(\App\Models\OrderItem::where('id',$item->item_id)->first())?\App\Models\OrderItem::where('id',$item->item_id)->value('amount'):\App\Models\CustomOrder::where('id',$item->item_id)->value('amount')}}</td>
        </tr>

    @endforeach

    <tr style="font-size: 10px;">
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td>Total</td>
        <td>{{round($gstp,2)}}</td>
        <td>{{round($gstp,2)}}</td>
        <td>{{$amount}}</td>
    </tr>

    <tr style="font-size: 10px;">
        <td colspan="5">
            <b style="text-align: center;"><label>GSTIN No. : 24AUHPK6483G1ZG
                </label></b>
        </td>
        @php
            if($invoices->franchise_id){
            $balance=App\Franchise::where('id',$invoices->franchise_id)->value('balance');
            if($balance<1){
                $prev=(($balance*(-1))-$amount).' DB';
                $netamount=(($balance*(-1))) .' DB';
            }else{
                $prev=($balance+$amount).' Cr';
                $netamount=($balance) .' Cr';
            }
            }else{
                $prev='';
                $netamount=$amount .' DB';
            }
        @endphp
        <td colspan="6"><label>Previous
                Balance: {{$prev}}</label>
            <br/>
            Net Balance:{{$netamount}}
        </td>
    </tr>

    </tbody>
    <tfoot>
    <tr style="font-size: 10px;">
        <td colspan="5" rowspan="3">
            <b style="text-align: left;"><label>Terms & Condition : For, JEWIN FOODS
                    <ol>
                        <li> Goods Once Sold Will Not Be Accepted.</li>
                        <li>Subject to RAJKOT Jurisdiction Only. E.&.O.E</li>
                    </ol>
                </label></b>
        </td>
        <td colspan="2">
            <label>Amount Paid :- </label>
        </td>

        <td colspan="4" rowspan="3"><label>For, JEWIN FOODS </label>
            <br/><br/><br/><br/>
            <label>(Authorised Signatory)</label>

        </td>
    </tr>
    <tr style="font-size: 10px;">
        <td colspan="2">
            <label>Sigature :- </label>
        </td>

    </tr>
    <tr style="font-size: 10px;">
        <td colspan="2">
            <label>_____ Cash / ______ Cheque </label>
        </td>
    </tr>
    </tfoot>

</table>
</body>

</html>

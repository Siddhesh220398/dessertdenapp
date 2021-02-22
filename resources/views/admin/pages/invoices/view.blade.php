@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('invoices_details', $invoice) !!}
@endsection
@section('content')
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
            border-collapse: collapse;
            font-size: 12px;
        }
.child tr{
    border-bottom:2pt solid black;
}

    </style>


    <!-- BEGIN SAMPLE FORM PORTLET-->
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa {{ $icon }} font-green"></i>
                <span class="caption-subject font-green bold uppercase">Invoice View</span>
            </div>
        </div>
        <div class="portlet-body ">
            <form method="Post" action="{{route('admin.invoices.print')}}">
                @csrf
            <div id="printTable">
                <table style="width:100%;  font-weight: bold;">

                    <tr>
                        <td colspan="4">
                            <b style="font-size: 30px;  text-align: left;"><label>JEWIN FOODS</label></b>
                        </td>

                        <td rowspan="3" colspan="4">
                            <center><label>TUMMY TREAT</label></center>
                        </td>

                        <td rowspan="4" colspan="3" style="width: 120px;">
                            <center><img src="{{url('theme/images/logo.png')}}" style="width: 100px; height:20%;">
                            </center>
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

                <table cellspacing="10px" id="customers"
                       class="child">

                    <input type="hidden" value="{{$invoice}}" name="invoice_no" class="invoice_no">
                    <tr>
                        <td style="text-align: left;" colspan="4">
                            <label>Debit Memo</label>
                        </td>
                        <td style="text-align: center;" colspan="3"><label>TAX INVOICE </label></td>
                        <td style="text-align: right;" colspan="4"><label>Original</label></td>
                    </tr>
                    <tr>
                        <td rowspan="2" colspan="6">
                            <b style="text-align: center; font-size: 15px;">
                                <label>Customer
                                    Name: {{!empty($invoices->user_id) ? App\User::where('id',$invoices->user_id)->value('first_name') .'  '.App\User::where('id',$invoices->user_id)->value('last_name'):App\Franchise::where('id',$invoices->franchise_id)->value('name')}}
                                    <br/> {{!empty($invoices->user_id) ? '-':App\Franchise::where('id',$invoices->franchise_id)->value('address')}}
                                </label></b>
                            <br/><br/>
                            <label><strong>GSTIN No</strong>: {{!empty($invoices->franchise_id)?App\Franchise::where('id',$invoices->franchise_id)->value('gstn_no') :''}}</label>
                        </td>
                        <td colspan="5"><label>Invoice NO: {{$invoices->invoice_no}}   </label><br/>
                            <label>Date: {{$invoices->invoice_date}}</label>
                        </td>
                    </tr>
                    <tr>

                        <td colspan="5"><label>Delivery Man: &nbsp; {{App\Admin::where('id',$invoices->deliveryboy_id)->value('name')}}</label><br/>
                            <label>Bill Prepared By:
                                &nbsp; {{App\Admin::where('id',$invoices->admin_id)->value('name')}}</label>
                        </td>

                    </tr>


                    <tr>
                        <td><label>Sr</label></td>
                        <td><label>Product Name </label></td>
                        <td><label>Order No</label></td>
                        <td><label>Quantity </label></td>
                        <td><label>HSN/SAC </label></td>
                        <td><label>Net Rate </label></td>
                        <td><label>Discount </label></td>
                        <td><label>GST %</label></td>
                        <td><label>CGST % </label></td>
                        <td><label>SGST %</label></td>
                        <td><label>Net Amount</label></td>
                    </tr>
                    @php

                        $invoice_items = \App\Models\MainInvoiceItem::where(['invoice_id'=>$invoices->id])->get();


$temp=1;
$amount=0;
                    @endphp


                    @foreach($invoice_items as $item)
                     <tr>
                <td>{{$temp++}}</td>
                <td>{{!empty(\App\Models\OrderItem::where('id',$item->item_id)->first('product_id'))? App\Models\Product::where('id',(\App\Models\OrderItem::where('id',$item->item_id)->value('product_id')))->value('name') : App\Models\SubCategoryModel::where('id',$item->sub_category_id)->value('name')}}
                    <br/>Fn: {{!empty(\App\Models\OrderItem::where('id',$item->item_id)->first('product_id'))?App\Models\Flavour::where('id',(\App\Models\OrderItem::where('id',$item->item_id)->value('flavour_id')))->value('name'):App\Models\Flavour::where('id',(\App\Models\CustomOrder::where('id',$item->item_id)->value('flavour_id')))->value('name')}}

                </td>
                <td>{{\App\Models\Order::where('id',$item->order_id)->value('order_no')}}</td>
                <td>
                    @php
                        if(!empty(\App\Models\OrderItem::where('id',$item->item_id)->first())){

                            $weight= !empty(\App\Models\OrderItem::where('id',$item->item_id)->value('weight'))? \App\Models\OrderItem::where('id',$item->item_id)->value('weight') . " Kg":\App\Models\OrderItem::where('id',$item->item_id)->value('qty') .' Pcs';
                            $weights= !empty(\App\Models\OrderItem::where('id',$item->item_id)->value('weight'))? \App\Models\OrderItem::where('id',$item->item_id)->value('weight'):\App\Models\OrderItem::where('id',$item->item_id)->value('qty');
                        $amount+=\App\Models\OrderItem::where('id',$item->item_id)->value('amount');
                        $amount1=\App\Models\OrderItem::where('id',$item->item_id)->value('amount');

                        $netprice=$amount1/($weights);

                        }else{
                        dd('he');
                            $weight=\App\Models\CustomOrder::where('id',$item->item_id)->value('weight');
                            $weights=\App\Models\CustomOrder::where('id',$item->item_id)->value('weight');
                            $amount+=\App\Models\CustomOrder::where('id',$item->item_id)->value('amount');
                            $amount1=\App\Models\CustomOrder::where('id',$item->item_id)->value('amount');
                            $netprice=$amount1/intval($weights);
                        }
                    @endphp
                    {{$weight}}
                </td>
                @php
                $p_id=\App\Models\OrderItem::where('id',$item->item_id)->first('product_id');
                $category=0;
                if(!empty($p_id)){

                $p_value=\App\Models\Product::where('id',$p_id->product_id)->first();
                $category=\App\Models\SubCategoryModel::where('id',$p_value->subcategory_id)->value('category_id');
                }
                else{
                $category=\App\Models\SubCategoryModel::where('id',$item->sub_category_id)->value('category_id');
                }
                if(!empty($invoices->franchise_id)){
                    $price=\App\Models\FranchisePrice::where(['franchise_id'=>$invoices->franchise_id,'category_id'=>$category])->value('percentage');
                }else{
                    $order_id1=\App\Models\OrderItem::where('id',$item->item_id)->value('order_id');
                    $price=\App\Models\Order::where(['id'=>$order_id1,'user_id'=>$invoices->user_id])->value('coupon_value');
                }

                @endphp
                <td>{{!empty($p_value)? $p_value->hsn_code : $item->hsn}}</td>
                <td>{{$netprice}}</td>
                <td>{{!(empty($price))?$price:0}}</td>
                <td>{{!empty($p_value)? $p_value->gst_price : $item->gst_price}}</td>
                <td>{{!empty($p_value)? $p_value->gst_price/2 : $item->cgst}}</td>
                <td>{{!empty($p_value)? $p_value->gst_price/2 : $item->sgst}}</td>
                <td>{{!empty(\App\Models\OrderItem::where('id',$item->item_id)->first())?\App\Models\OrderItem::where('id',$item->item_id)->value('amount'):\App\Models\CustomOrder::where('id',$item->item_id)->value('amount')}}</td>
            </tr>
                    @endforeach
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Total</td>
                        <td>{{$amount}}</td>
                    </tr>

                    <tr>
                        <td colspan="3">
                            <b style="text-align: center;"><label>GSTIN No. : 24AUHPK6483G1ZG
                                </label></b>
                        </td>
                        <td colspan="2"><label>{{$invoices->cash_debit}}</label></td>
                        <td colspan="6"><label>Amount Paid: {{$amount}}</label>

                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <b style="text-align: left;"><label>Terms & Condition : For, JEWIN FOODS
                                    <ol>
                                        <li> Goods Once Sold Will Not Be Accepted.</li>
                                        <li>Subject to RAJKOT Jurisdiction Only. E.&.O.E</li>
                                    </ol>
                                </label></b>
                        </td>

                        <td colspan="5"><label>For, JEWIN FOODS </label>
                            <br/><br/><br/><br/>
                            <label>(Authorised Signatory)</label>

                        </td>
                    </tr>


                </table>

            </div>
            <br/>
            <br/>
            <div class="form-group">
                <button class=" btn btn-primary print" type="submit"> Print</button>
            </div>
            </form>

        </div>

        @endsection
        @push('scripts')
                <script type="text/javascript">
                // function printData() {
                //     var divToPrint = document.getElementById("printTable");
                //     newWin = window.open("");
                //     newWin.document.write(divToPrint.outerHTML);
                //     newWin.print();
                //     newWin.close();
                // }
                //
                // $('button').on('click', function () {
                //     printData();
                // })

                {{--$(document).on('click','.print',function () {--}}
                {{--    // alert($('.invoice_no').val());--}}
                {{--    $.ajax({--}}
                {{--        type: "Post",--}}
                {{--        url: "{{route('admin.invoices.print')}}",--}}
                {{--        data: {--}}
                {{--            '_token': $('input[name="_token"]').val(),--}}
                {{--            'invoice_no': $('.invoice_no').val(),--}}
                {{--        },--}}
                {{--        success: function (data) {--}}
                {{--            alert('success');--}}
                {{--        }--}}
                {{--    });--}}

                {{--});--}}
            </script>
    @endpush

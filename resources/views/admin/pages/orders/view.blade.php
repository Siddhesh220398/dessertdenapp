@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('orders_details', $order) !!}
@endsection

@section('content')
    <style>
        table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            border: 1px solid #ddd;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }
    </style>
    <div class="row ">
        <div class="col-md-12">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="{{ $icon }} font-green"></i>
                        <span class="caption-subject font-green sbold uppercase">Order Details</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">

                        <br/>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Order No : </label>
                                    <label class="control-label">{{ $order->order_no }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Shipping Method : </label>
                                    <label class="control-label">{{ $order->shipping_method }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Franchise Name : </label>
                                    <label
                                        class="control-label">{{!empty($order->franchises_id) ?\App\Franchise::where('id',$order->franchises_id)->value('name') :'-' }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Franchise address : </label>
                                    <label
                                        class="control-label">{{ !empty($order->franchises_id) ?$order->franchises->address :'' }}</label>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">City : </label>
                                    <label
                                        class="control-label">{{ !empty($order->city_id)?$order->city->name :''}}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Customer Name : </label>
                                    <label
                                        class="control-label">{{ !empty($order->user_id)? $order->user->first_name .'  '. $order->user->last_name  :$order->franchises->first_name .'  '. $order->franchises->last_name }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Time : </label>
                                    <label class="control-label">{{ $order->time->startingtime }}
                                        - {{ $order->time->endingtime }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Total amount : </label>
                                    <label class="control-label">{{ $order->total_amount }}</label>
                                </div>
                            </div>

                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Order type : </label>
                                    <label class="control-label">{{ $order->type }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Delivery Date : </label>
                                    <label class="control-label">{{ $order->delivery_date }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Status : </label>
                                    <label class="control-label">{{ $order->status }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Payment Method : </label>
                                    <label class="control-label">{{ $order->payment_method }}</label>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Razor Payment ID : </label>
                                    <label class="control-label">{{ $order->razorpay_payment_id }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Admin Status : </label>
                                    <label class="control-label">{{ $order->admin_status }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Address : </label>
                                    <label class="control-label">{{ $order->address }}</label>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="control-label bold">Zip code : </label>
                                    <label class="control-label">{{ $order->zip }}</label>
                                </div>
                            </div>
                        </div>
                        <br/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-container" style="overflow-x:scroll;">
                                    @if($order->type=="Normal")
                                    <table class="table table-striped table-bordered table-hover"
                                           id="hosts_table_DT">
                                        @csrf
                                        <tr>
                                            <th> Product Name</th>
                                            <th> Type</th>
                                            <th> Photo Type</th>
                                            <th> Quantity</th>
                                            <th>Message on Cake/ Instruction</th>

                                            <th> Voice Message</th>
                                            <th> Size</th>
                                            <th> Product Amount</th>
                                            <th> Flavour Amount</th>
                                            <th> Type Amount</th>
                                            <th> Photo Amount</th>
                                            <th> Total Amount</th>
                                            <th> Product Image</th>
                                            <th> Photo Image</th>
                                            <th> Customer Name/No.</th>
                                            <th> Completed Image</th>
                                        </tr>

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


                                                @endphp

                                                <tr>
                                                    <input type="hidden" value="{{$order->id}}" name="normal_order_id">
                                                    <td> {{$item->product->name}} <br/>
                                                        Fn:{{!empty($item->flavour_id)?$item->flavour->name:Null}} </td>

                                                    <td> {{!empty($ar)?implode(",",$ar):'-' }}  </td>
                                                    <td> {{!empty($photoRate->cat_name)?$photoRate->cat_name:'-'}} </td>
                                                    <td> {{!empty($item->weight)?($item->weight).' Kg'  : $item->qty .' Pcs'}}  </td>
                                                    <td> MOC: {{$item->message_on_cake}} <br> I:{{$item->instruction}} </td>
                                                    <td> {{$item->voice_msg}}</td>
                                                    <td> {{$item->size}} </td>
                                                    <td> {{$item->product->rate}} </td>
                                                    <td> {{!empty($item->flavour_id)?$item->flavour->rate:'-'}} </td>
                                                    <td> 0</td>
                                                    <td> {{!empty($photoRate->rate)?$photoRate->rate:'-'}}</td>
                                                    <td> {{$item->amount}}</td>
                                                    <td><img
                                                            src="{{ \Storage::url('app/public/'.$item->product->image)}}"
                                                            alt="Image" class="img-thumbnail"
                                                            style="width: 100px !important; height: 100px;"/></td>
                                                    <td><img
                                                            src="{{!empty($item->image)? url('public/orders/'.$item->image):'-'}}"
                                                            style="width: 100px; height: 100px;"></td>
                                                    <td>Name: {{$item->customer_name}} <br/> NO: {{$item->customer_no}} </td>

                                                    <td> -</td>
                                                </tr>
                                            @endforeach

                                    </table>
                                        @else
                                        <table class="table table-striped table-bordered table-hover">
                                                <input type="hidden" value="{{$order->id}}" name="custom_order_id">
                                                <tr>
                                                    <th> Subcategory Name</th>

                                                    <th> Type</th>
                                                    <th> Photo Type</th>
                                                    <th> Weight</th>
                                                    <th> Message on Cake /Instruction</th>

                                                    <th> Theme</th>
                                                    <th> Size</th>
                                                    <th> Flavour Amount</th>
                                                    <th> Type Amount</th>
                                                    <th> Photo Amount</th>
                                                    <th> Total Amount</th>
                                                    <th> Cake Image</th>
                                                    <th> Idea Image</th>
                                                    <th> Photo Image</th>
                                                    <th> Customer Name / No</th>

                                                    <th> Completed Image</th>
                                                </tr>
                                                @php
                                                    $tmp=0;
                                                    $items =$order->customitem;
                                                    $tmp ++;
                                                    $priceRateName=[];
                                                        if(!empty($items->type_rate)){
                                                            $type_ids=json_decode( $items->type_rate);
                                                            $ar=[];
                                                            foreach ($type_ids as $key => $value) {
                                                                $priceRate=App\Models\PriceCategoryModel::where('id',$value)->first();					// array_push($priceRateName, $priceRate);
                                                                    array_push($ar,$priceRate->cat_name);

                                                            }

                                                        }


                                                        if(!empty($items->is_photo)){
                                                        $photoRate=App\Models\PriceCategoryModel::where('id',$items->is_photo)->first();
                                                        }
                                                        $subcategoryname=App\Models\SubCategoryModel::where('id',$items->sub_category_id)->value('name');
                                                @endphp
                                                <tr>
                                                    <td> {{$subcategoryname}} <br/> Fn: {{!empty($items->flavour_id)?$items->flavour->name:Null}} </td>

                                                    <td> {{!empty($ar)?implode(",",$ar):'-' }} </td>
                                                    <td> {{!empty($photoRate->cat_name)?$photoRate->cat_name:'-'}} </td>
                                                    <td> {{!empty($items->weight)?($items->weight).' Kg'  : ''}}  </td>
                                                    <td> MOC: {{$items->message_on_cake}} <br/> I:{{$items->instruction}} </td>

                                                    <td> {{$items->theme}}</td>
                                                    <td> {{$items->size}} </td>
                                                    <td> {{!empty($items->flavour_id)?$items->flavour->rate:Null}} </td>

                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td> {{$items->amount}}</td>

                                                    <td><img
                                                            src="{{!empty(App\Models\OrderImage::where(['order_id'=>$order->id,'type'=>'cake'])->value('image')) ? url('public/'.App\Models\OrderImage::where(['order_id'=>$order->id,'type'=>'cake'])->value('image')) : url('theme/images/logo.png')}}"
                                                            alt="Image" class="img-thumbnail" /></td>
                                                    <td > <img
                                                            src="{{!empty(App\Models\OrderImage::where(['order_id'=>$order->id,'type'=>'idea'])->value('image')) ? url('public/'.App\Models\OrderImage::where(['order_id'=>$order->id,'type'=>'idea'])->value('image')) : url('theme/images/logo.png')}}"
                                                            alt="Image" class="img-thumbnail"  /></td>
                                                    <td></td>
                                                    <td> Name: {{$items->customer_name}}<br/> No: {{$items->customer_no}} </td>

                                                    <td> -</td>
                                                </tr>


                                        </table>
                                    @endif
                                </div>
                            </div>

                        </div>
                        <br/>


                        <div class="row form-group">
                            <div class="col-md-1">
                                <a href="{{route('admin.orders.index')}}" class="btn red">Back</a>
                            </div>
                            <div class="col-md-3">
                                <form action="{{route('admin.orders.print')}}" method="POST">
                                    @csrf
                                    <input type="hidden" value="{{ $order->id }}" name="order_id"/>
                                    <button type="submit" class="btn green">Print</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- End: SAMPLE FORM PORTLET -->
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">

    </script>
@endpush

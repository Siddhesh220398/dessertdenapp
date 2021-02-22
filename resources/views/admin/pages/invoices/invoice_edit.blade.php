<div class="col-md-12">
    <!-- BEGIN SAMPLE FORM PORTLET-->
    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa {{ $icon }} font-green"></i>
                <span class="caption-subject font-green bold uppercase">Orders</span>
            </div>
        </div>
        <div class="portlet-body ">
            <form class="invoicefrm" method="POST" action="{{ route('admin.invoices.update',$invoice_id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <table class="table table-striped table-bordered table-hover table-user" id="table_DT">
                    @php
                        $invoice_series=App\Models\MainInvoice::latest()->value('id');
                         $last_id = (!empty($invoice_series) ? $invoice_series + 1 : 1);
                    @endphp

                    <div class="form-group col-md-4 mb-5 {{ $errors->has('invoice_date') ? ' has-error' : '' }}">
                        <label for="invoice_no" class=" control-label">Invoice Date</label>

                        <div class="input-icon">
                            <i class="fa fa-map-marker"></i>
                            <input type="date" class="form-control" name="invoice_date" id="invoice_date"
                                   placeholder="Enter Invoice Date" maxlength="80" value="{{ old('invoice_date',$invdate) }}">
                            @if ($errors->has('invoice_date'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('invoice_date') }}</strong>
                                    </span>
                            @endif
                        </div>

                    </div>

                    <div class="form-group col-md-4">
                        <label for="deliveryboy_id" class=" control-label">{!! $mend_sign !!}Delivery Man</label>

                        <select class="form-control deliveryboy_id " name="deliveryboy_id">
                            <option value="">Select Delivery Boy</option>
                            @foreach($delivery_boys as $delivery_boy)
                                <option value="{{$delivery_boy->id}}" @if($delivery_boy->id == $deliveryboy_id) selected @endif>{{$delivery_boy->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('deliveryboy_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('deliveryboy_id') }}</strong>
                            </span>
                        @endif

                    </div>



                    @if(!empty($orders))

                        @foreach($orders as $order)

                            <tr>
                                <th><input type="checkbox" class="order_class order_{{$order->id}}" data-order_id="{{$order->id}}"  id="order_{{$order->id}}" >
                                    <input type="hidden" value="{{$order->user_id}}" name="user_id" />
                                    <input type="hidden" value="{{$order->franchises_id}}" name="franchise_id" />
                                </th>
                                <th colspan="4">Order No :{{$order->order_no}}</th>

                                <th  colspan="3">{{$order->type}} <input type="hidden" name="type" value="{{$order->type}}"/></th>
                                <th  colspan="2">Total Amount :{{$order->total_amount}} <input type="hidden" name="total_amount[{{$order->id}}]" value="{{$order->total_amount}}"/></th>
                            </tr>
                            <tr>

                                <th></th>
                                <th>Item Id</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>HSN/SAC</th>
                                <th>Discount</th>
                                <th>GST</th>
                                <th>CGST</th>
                                <th>SGST</th>
                                <th> Amount</th>
                            </tr>
                            @if($order->type=="Normal")
                                @php
                                    $items =App\Models\OrderItem::where(['order_id' => $order->id,'is_invoice'=>0])->get();
                                @endphp
                                @foreach($items as $item)
                                    <tr>
                                        <td><input type="checkbox" class="item_{{$order->id}}" id="item_{{$item->id}}" name="item_id[{{$order->id}}][]" value="{{$item->id}}">
                                        </td>
                                        <td>{{$item->id}}</td>
                                        <td>{{$item->product->name}}
                                            <br/>{{!empty($item->flavour_id)? 'Fn:'.$item->flavour->name:'-'}}</td>
                                        <td>{{!empty($item->qty)?$item->qty. ' Pcs':$item->weight . ' Kg'}}</td>
                                        <td><label>{{$item->product->hsn_code}}</label></td>
                                        <td><label></label></td>

                                        <td><label>{{$item->product->gst_price}}</label></td>
                                        <td><label>{{$item->product->gst_price/2}}</label></td>
                                        <td><label>{{$item->product->gst_price/2}}</label></td>

                                        <td>{{$item->amount}}</td>
                                    </tr>
                                @endforeach
                            @else
                                @php
                                    $customitem =App\Models\CustomOrder::where(['order_id' => $order->id,'is_invoice'=>0])->first();
                                @endphp

                                @if(!empty($customitem))
                                    <tr>
                                        <td><input type="checkbox" class="item_{{$order->id}}" id="item_{{$customitem->id}}" name="item_id[{{$order->id}}][]" value="{{$customitem->id}}">
                                        <td>{{$customitem->id}}</td>
                                        <td>{{App\Models\SubCategoryModel::where('id',$customitem->sub_category_id)->value('name')}}

                                            <br/>{{!empty($customitem->flavour_id)? 'Fn:'.$customitem->flavour->name:'-'}}
                                        </td>
                                        <td>{{$customitem->weight . ' Kg'}}</td>
                                        <td>19059020</td>
                                        @php
                                            if($customitem->sub_category_id){
                                            $category=App\Models\SubCategoryModel::where('id',$customitem->sub_category_id)->value('category_id');

                                            if($category){
                                            $discount=App\Models\FranchisePrice::where(['franchise_id'=>$order->franchises_id,'category_id'=>$category])->value('percentage');
                                            }
                                            }

                                        @endphp
                                        <td>@if($discount) {{$discount}} @else <input type="text" name="discount[{{$customitem->id}}]" class="form-control discount"> @endif</td>
                                        <td>18</td>
                                        <td>9</td>
                                        <td>9</td>

                                        <td>{{$order->total_amount}}</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                    @endif
                    <tr>
                        <td>
                            <div class="form-group">
                                <button type="submit" class="btn green submit"> Submit</button>
                            </div>
                        </td>
                    </tr>
                </table>

            </form>
        </div>
    </div>

</div>

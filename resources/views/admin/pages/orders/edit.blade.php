@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('edit_orders', $order) !!}
@endsection

@section('content')
    <div class="row ">
        <div class="col-md-12">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa {{ $icon }} font-green"></i>
                        <span class="caption-subject font-green bold uppercase">{{ $title }}</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <form id="frmFlavour" class="form-horizontal" role="form" method="POST"
                          action="{{ route('admin.orders.update', $order->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" class="form-control" name="order_id" id="order_id" maxlength="80"
                               value="{{  $order->id}}" >
                        <div class="form-group{{ $errors->has('order_no') ? ' has-error' : '' }}">
                            <label for="order_no" class="col-md-2 control-label">{!! $mend_sign !!}Order No</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-map-marker"></i>
                                    <input type="text" class="form-control" name="order_no" id="order_no" maxlength="80"
                                           value="{{ old('order_no', $order->order_no) }}" readonly>
                                    @if ($errors->has('order_no'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('order_no') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('delivery_date') ? ' has-error' : '' }}">
                            <label for="order_no" class="col-md-2 control-label">{!! $mend_sign !!}Delivery Date</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="date" class="form-control" name="delivery_date" id="delivery_date"
                                           value="{{ old('delivery_date', $order->delivery_date) }}">
                                    @if ($errors->has('delivery_date'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('delivery_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <br/>
                        <table class="order_tbl table table-striped table-bordered table-hover table-use">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Flavour</th>
                                <th>Weight/Qty</th>
                                <th>Type</th>
                                <th>Photo Type</th>
                            </tr>
                            </thead>

                            <tbody>
                            @if($order->type=="Normal")
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{App\Models\Product::where('id',$item->product_id)->value('name')}}</td>
                                    @if($order->p_type==0)
                                        <td><input type="text" value="{{App\Models\Flavour::where('id',$item->flavour_id)->value('name')}}" name="flavour_id"></td>
                                    @endif
                                    <td>@if($item->weight)
                                            <input type="text" value="{{$item->weight}}" name="weight[{{$item->id}}]"
                                                   style="width: 70px;"> Kg
                                        @else
                                            <input type="text" value="{{$item->qty}}" name="qty[{{$item->id}}]"
                                                   style="width: 70px;"> Pcs

                                        @endif
                                    </td>
                                    <td>@php
                                            $type_ids = json_decode($item->type_rate);

                                        @endphp

                                        <select class="form-control" name="type_rate[{{$item->id}}][]" multiple>
                                            <option value="">Select Type</option>
                                        @foreach ($types as $key )
                                                @if($type_ids)
                                                    <option value="{{$key->id}}"
                                                            @if(in_array($key->id, $type_ids)) selected @endif>{{$key->cat_name}}</option>
                                                @else
                                                    <option value="{{$key->id}}">{{$key->cat_name}}</option>

                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><select class="form-control" name="isphoto[{{$item->id}}]">
                                            <option value="">Select Photo Type</option>
                                        @foreach($photos as $photo)

                                                <option value="{{$photo->id}}" @if($photo->id==$item->is_photo) selected @endif>{{$photo->cat_name}}</option>
                                            @endforeach
                                        </select></td>

                                </tr>
                            @endforeach
                            @else
                                @php
                                    $item=$order->customitem;

                                @endphp
                                <tr>
                                    <td>{{App\Models\SubCategoryModel::where('id',$item->sub_category_id)->value('name')}}</td>
                                    @if($order->p_type==0)
                                        <td><input type="text" value="{{App\Models\Flavour::where('id',$item->flavour_id)->value('name')}}" name="flavour_id"></td>
                                    @endif
                                    <td>@if($item->weight)
                                            <input type="text" value="{{$item->weight}}" name="weight[{{$item->id}}]"
                                                   style="width: 70px;"> Kg

                                        @endif
                                    </td>
                                    <td>@php
                                            $type_ids = json_decode($item->type_rate);

                                        @endphp

                                        <select class="form-control" name="type_rate[{{$item->id}}][]" multiple>
                                            <option value="">Select Type</option>
                                            @foreach ($types as $key )
                                                @if($type_ids)
                                                    <option value="{{$key->id}}"
                                                            @if(in_array($key->id, $type_ids)) selected @endif>{{$key->cat_name}}</option>
                                                @else
                                                    <option value="{{$key->id}}">{{$key->cat_name}}</option>

                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td><select class="form-control" name="isphoto[{{$item->id}}]">
                                            <option value="">Select Photo Type</option>
                                            @foreach($photos as $photo)

                                                <option value="{{$photo->id}}" @if($photo->id==$item->is_photo) selected @endif>{{$photo->cat_name}}</option>
                                            @endforeach
                                        </select></td>

                                </tr>
                            @endif
                            </tbody>
                        </table>

                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-10">
                                <button type="submit" class="btn green">Submit</button>
                                <a href="{{route('admin.orders.index')}}" class="btn red">Cancel</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <!-- End: SAMPLE FORM PORTLET -->
        </div>
    </div>
@endsection

@push('scripts')
@endpush

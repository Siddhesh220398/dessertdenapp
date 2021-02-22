@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('edit_salereturns', $salereturn) !!}
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
                          action="{{ route('admin.salereturns.update', $salereturn->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" class="form-control" name="order_id" id="order_id" maxlength="80"
                               value="{{  $salereturn->id}}" >
                        <div class="form-group{{ $errors->has('order_no') ? ' has-error' : '' }}">
                            <label for="order_no" class="col-md-2 control-label">Order No</label>
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
                            <label for="order_no" class="col-md-2 control-label">Delivery Date</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="date" class="form-control" name="delivery_date" id="delivery_date"
                                           value="{{ old('delivery_date', $order->delivery_date) }}" disabled>
                                    @if ($errors->has('delivery_date'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('delivery_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('sale_date') ? ' has-error' : '' }}">
                            <label for="sale_date" class="col-md-2 control-label">Sale return Date</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="date" class="form-control" name="sale_date" id="sale_date"
                                           value="{{ old('delivery_date', $salereturn->date) }}" disabled>
                                    @if ($errors->has('sale_date'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('sale_date') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('order_item') ? ' has-error' : '' }}">
                            <label for="order_item" class="col-md-2 control-label">Item</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="text" class="form-control" name="order_item" id="order_item"
                                           value="{{ ($order_item)?$order_item->product->name.' | ' . $flavour : $custom_order->subcategory->name.'|'. $flavour  }}" disabled>
                                    @if ($errors->has('order_item'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('order_item') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('order_item') ? ' has-error' : '' }}">
                            <label for="customer" class="col-md-2 control-label">Franchise</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="text" class="form-control" name="customer" id="customer"
                                           value="{{$customer}}" disabled>
                                    @if ($errors->has('customer'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('customer') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('qty') ? ' has-error' : '' }}">
                            <label for="customer" class="col-md-2 control-label">Quantity</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="text" class="form-control" name="qty" id="qty"
                                           value="{{($salereturn->qty) ?$salereturn->qty.' Pcs' : $salereturn->weight . ' kg'}}" disabled>
                                    @if ($errors->has('qty'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('qty') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('reason_f') ? ' has-error' : '' }}">
                            <label for="customer" class="col-md-2 control-label">Franchise Reason</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="text" class="form-control" name="reason_f" id="reason_f"
                                           value="{{$salereturn->reason_f}}" disabled>
                                    @if ($errors->has('reason_f'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('reason_f') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('reason_a') ? ' has-error' : '' }}">

                            <label for="reason_a" class="col-md-2 control-label">Admin Reason</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="text" class="form-control" name="reason_f" id="reason_f"
                                           value="{{old('reason_a',$salereturn->reason_a)}}" >
                                    @if ($errors->has('reason_a'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('reason_a') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('percentage') ? ' has-error' : '' }}">

                            <label for="reason_a" class="col-md-2 control-label">Percentage</label>
                            <div class="col-md-6">
                                <div class="input-icon">
                                    <i class="fa fa-calendar-o"></i>
                                    <input type="text" class="form-control" name="percentage" id="percentage"
                                           value="{{old('percentage',$salereturn->percentage)}}" >
                                    @if ($errors->has('percentage'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('percentage') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <br/>


                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-10">
                                <button type="submit" class="btn green">Submit</button>
                                <a href="{{route('admin.salereturns.index')}}" class="btn red">Cancel</a>
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

@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('edit_invoices', $invoice) !!}
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
                <form id="frmCity" class="form-horizontal" role="form" method="POST" action="{{ route('admin.invoices.update', $invoice->id) }}" enctype="multipart/form-data">
                    @csrf
<input type="hidden" name="id" value="{{$invoice->id}}" class="invoice_id" />
                    <div class="form-group col-md-3 mb-5{{ $errors->has('invoice_no') ? ' has-error' : '' }}">
                        <label for="invoice_no" class=" control-label">Invoice No</label>

                        <div class="input-icon">
                            <i class="fa fa-map-marker"></i>

                            <input type="text" class="form-control" name="invoice_no" id="invoice_no"
                                   placeholder="Enter Invoice No" maxlength="80" required value="{{$invoice->invoice_no}}" readonly>
                            @if ($errors->has('invoice_no'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('invoice_no') }}</strong>
                                    </span>
                            @endif
                        </div>

                    </div>
                    <div class="form-group col-md-1"></div>

                    <div class="form-group col-md-3 mb-5 {{ $errors->has('invoice_date') ? ' has-error' : '' }}">
                        <label for="invoice_no" class=" control-label">Invoice Date</label>

                        <div class="input-icon">
                            <i class="fa fa-map-marker"></i>
                            <input type="date" class="form-control invoice_date" name="invoice_date" id="invoice_date"
                                   placeholder="Enter Invoice Date" maxlength="80" value="{{ old('invoice_date',$invoice->invoice_date) }}">
                            @if ($errors->has('invoice_date'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('invoice_date') }}</strong>
                                    </span>
                            @endif
                        </div>

                    </div>

                    <div class="form-group col-md-1"></div>

                    <div class="form-group col-md-3">
                        <label for="deliveryboy_id" class=" control-label">{!! $mend_sign !!}Delivery Man</label>

                        <select class="form-control deliveryboy_id " name="deliveryboy_id">
                            <option value="">Select Delivery Boy</option>
                            @foreach($delivery_boys as $delivery_boy)
                                <option value="{{$delivery_boy->id}}" @if($delivery_boy->id == $invoice->deliveryboy_id) selected @endif>{{$delivery_boy->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('deliveryboy_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('deliveryboy_id') }}</strong>
                            </span>
                        @endif

                    </div>
                    <div class="form-group col-md-1"></div>
                    <div class="form-group col-md-3">
                        <label for="cash_debit" class=" control-label">Cash/Debit</label>

                        <select class="form-control cash_debit " name="cash_debit" readonly>
                            <option value="">Select Option</option>
                            <option value="Cash" @if( $invoice->cash_debit =="Cash") selected @endif>Cash</option>
                            <option value="Debit" @if($invoice->cash_debit =="Debit") selected @endif>Debit</option>

                        </select>
                        @if ($errors->has('cash_debit'))
                            <span class="help-block">
                                <strong>{{ $errors->first('cash_debit') }}</strong>
                            </span>
                        @endif

                    </div>
                    <div class="form-group col-md-1"></div>
                    @if(!empty($invoice->franchise_id))
                    <div class="form-group col-md-3">
                        <label for="cash_debit" class=" control-label">Franchise</label>

                        <select class="form-control franchise_id " name="franchise_id" readonly>
                            <option value="">Select Franchise</option>
                            @foreach($franchises as $franchise)
                                <option value="{{$franchise->id}}" @if($franchise->id ==$invoice->franchise_id) selected @endif>{{$franchise->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('franchise_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('franchise_id') }}</strong>
                            </span>
                        @endif

                    </div>
                    @endif
                    <div class="form-group col-md-1"></div>
                    <div class="form-group col-md-3 {{ $errors->has('invdate') ? ' has-error' : '' }}">
                        <label for="invdate" class="control-label">Date</label>

                            <div class="input-icon">
                                <i class="fa fa-map-marker"></i>
                                <input type="date" class="form-control invdate" name="invdate"
                                       value="{{ old('invdate') }}">
                                @if ($errors->has('invdate'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('invdate') }}</strong>
                                </span>
                                @endif
                            </div>
                    </div>

                    <div class="form-group">
                        <div class=" col-md-10">
                            <button type="button" class="btn green get_orders">Get</button>

                        </div>
                    </div>

                </form>
            </div>
        </div>
        <!-- End: SAMPLE FORM PORTLET -->
    </div>
</div>
 <div id="order_div" class="row">

 </div>
@endsection

@push('scripts')
<script type="text/javascript">

    $(document).ready(function () {

        $(document).on('click','.get_orders',function () {
            $.ajax({
                type: "Post",
                url: "{{route('admin.invoices.getOrderedit')}}",
                data: {
                    '_token': $('input[name="_token"]').val(),
                    'user_id': $('.user_id').val(),
                    'invoice_id': $('.invoice_id').val(),
                    'franchise_id': $('.franchise_id').val(),
                    'invoice_date': $('.invoice_date').val(),
                    'invdate': $('.invdate').val(),
                    'deliveryboy_id': $('.deliveryboy_id').val(),
                    'invoice_no': $('#invoice_no').val(),
                },
                success: function (data) {
                    $('#order_div').html(data);
                }
            });

        });

        $(document).on('click','.order_class',function () {
            console.log($(this).data('order_id'))
            var order_id =$(this).data('order_id');
            if($(this).prop("checked") == true){
                console.log("Checkbox is checked.");
                $('.item_'+order_id).prop("checked",true);
            }
            else if($(this).prop("checked") == false){
                $('.item_'+order_id).prop("checked",false);
            }
        });
    });



</script>
@endpush

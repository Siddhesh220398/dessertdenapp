@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('add_invoices') !!}
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
                <div class="portlet-body ">
                    <form id="frmInvoice" class="form-horizontal " role="form" method="GET"
                           enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="user_id" class="col-md-6 control-label">Choose Customer Either Franchise to get Invoice</label>
                        </div>
                        <br/>

                        <div class="form-group">
                            <label for="user_id" class="col-md-2 control-label">{!! $mend_sign !!}Customers</label>
                            <div class="col-md-6">
                                <select class="form-control user_id" name="user_id">
                                    <option value="">Select Customer</option>
                                    @foreach($users as $user)
                                        <option value="{{$user->id}}">{{$user->first_name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('user_id'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('user_id') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="franchise_id" class="col-md-2 control-label">{!! $mend_sign !!}Franchises</label>
                            <div class="col-md-6">
                                <select class="form-control franchise_id " name="franchise_id">
                                    <option value="">Select Franchise</option>
                                    @foreach($franchises as $franchise)
                                        <option value="{{$franchise->id}}">{{$franchise->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('franchise_id'))
                                    <span class="help-block">
                                <strong>{{ $errors->first('franchise_id') }}</strong>
                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('invdate') ? ' has-error' : '' }}">
                            <label for="invdate" class="col-md-2 control-label">Date</label>
                            <div class="col-md-6">
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
                        </div>


                        <div class="form-group">
                            <div class="col-md-offset-2 col-md-10">
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
                    url: "{{route('admin.invoices.getorder')}}",
                    data: {
                        '_token': $('input[name="_token"]').val(),
                        'user_id': $('.user_id').val(),
                        'franchise_id': $('.franchise_id').val(),
                        'invdate': $('.invdate').val(),
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

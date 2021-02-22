@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('add_invoices') !!}
@endsection

@section('content')

    <div class="portlet-body ">
        <form>
            @csrf
            <div class="row">
                <div class="col-md-1"></div>
                <div class="col-md-2 form-group">
                    <label for="order_date" class="control-label">Invoice No</label>
                    <input type="date" class="form-control invoice_no" name="invoice_no"
                           value="{{ old('invoice_no') }}">
                </div>

                <div class="col-md-2 form-group">
                    <label for="order_date" class="control-label">Invoice Date</label>
                    <input type="date" class="form-control invoice_date" name="invoice_date "
                           value="{{ old('invoice_date') }}">
                </div>

                <div class="col-md-2  form-group">
                    <label for="invoice_no" class="control-label">Customer</label>
                    <select class="form-control franchise_id " name="franchise_id">
                        <option value="">Select Franchise</option>
                        @foreach($franchises as $franchise)
                            <option value="{{$franchise->id}}">{{$franchise->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2  form-group">
                    <label for="invoice_no" class="control-label">Orders</label>
                    <select class="form-control order_id " name="order_id">
                        <option value="">Select Orders</option>

                    </select>
                </div>


                <div class="col-md-2  form-group">
                    <label for="invoice_no" class="control-label">Delivery Boy</label>
                    <select class="form-control deliveryboy_id " name="deliveryboy_id">
                        <option value="">Select Delivery Boy</option>
                        @foreach($delivery_boys as $deliveryboy)
                            <option value="{{$deliveryboy->id}}">{{$deliveryboy->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1"></div>

            </div>
            <br/>
            <br/>
            <div class="row">
                <div class="col-md-12">
                    <table class=" order_tbl table table-striped table-bordered table-hover table-user" id="table_DT">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Order No</th>
                            <th>Quantity</th>
                            <th>Hsn/Sac</th>
                            <th>Net Rate</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <td>
                            <select class="item_id form-control" name="item_id[]">
                                <option>Select option</option>

                            </select>
                        </td>
                        <td><input type="text" class="form-control order_no" name="order_no[]" readonly></td>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">

        $(document).ready(function () {
            $(document).on('click', '.franchise_id', function () {
                $.ajax({
                    type: "GET",
                    url: "{{route('admin.invoices.getorders')}}",
                    data: {
                        '_token': $('input[name="_token"]').val(),
                        'franchise_id': $('.franchise_id').val()
                    },
                    success: function (data) {
                        console.log(data);
                      $('.order_id').html('');
                      $('.order_id').html(data);
                    }
                });
            });
            $(document).on('click', '.order_id', function () {
                $.ajax({
                    type: "GET",
                    url: "{{route('admin.invoices.getitems')}}",
                    data: {
                        '_token': $('input[name="_token"]').val(),
                        'order_id': $('.order_id').val()
                    },
                    success: function (data) {
                        console.log(data);
                      $('.item_id').html('');
                      $('.item_id').html(data);
                    }
                });
            });

            $(document).on('click', '.item_id', function () {
                var parent = $(this).parents("tr");
                $.ajax({
                    type: "GET",
                    url: "{{route('admin.invoices.orderdetails')}}",
                    data: {
                        '_token': $('input[name="_token"]').val(),
                        'item_id': parent.find('.item_id').val()
                    },
                    success: function (data) {
                        console.log(data);
                        $('.item_id').html('');
                        $('.item_id').html(data);
                    }
                });
            });


        });

    </script>
@endpush

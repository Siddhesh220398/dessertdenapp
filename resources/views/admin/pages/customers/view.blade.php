@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('customers_details', $customer) !!}
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

        tr:nth-child(even){background-color: #f2f2f2}
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
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab1" data-toggle="tab"> Info </a>
                        </li>
                        <li>
                            <a href="#tab2" data-toggle="tab">Order Detail</a>
                        </li>
                        <li>
                            <a href="#tab3" data-toggle="tab">Balance Detail</a>
                        </li>


                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab1">
                            <div class="row form-group">
                                <label for="name" class="col-md-2 control-label bold">Name : </label>
                                <div class="col-md-4">
                                    <label class="control-label">{{ $customer->first_name }} &nbsp; {{ $customer->last_name }}</label>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="name" class="col-md-2 control-label bold">Balance : </label>
                                <div class="col-md-4">
                                    <label class="control-label" @if($customer->balance <=0 )style="color: red" @else style="color: darkgreen" @endif>{{ $customer->balance }}</label>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="email" class="col-md-2 control-label bold">Email Address : </label>
                                <div class="col-md-4">
                                    <label class="control-label">{{ $customer->email }}</label>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="phone" class="col-md-2 control-label bold">Phone : </label>
                                <div class="col-md-4">
                                    <label class="control-label">{{ $customer->mobile_no }}</label>
                                </div>
                            </div>

                            <div class="row form-group">
                                <label for="profile" class="col-md-2 control-label bold">Profile : </label>
                                <div class="col-md-4">
                                    <img class="img-thumbnail" src="{{ public_path($customer->profile) }}" alt="Image">
                                </div>
                            </div>
                            <div class="row form-group">
                                <label for="profile" class="col-md-2 control-label bold">Total Orders : </label>
                                <div class="col-md-4">
                                     <label class="control-label">{{ App\Models\Order::where('user_id',$customer->id)->count() }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab2">
                            <table class="table table-striped table-bordered" id="orders_table_DT">
                                <thead>
                                <tr>
                                    <th>OrderNo</th>
                                    <th>Shipping Method</th>
                                    <th>Total Amount</th>
                                    <th>Type</th>
                                    <th>Payment Method</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td><a href="{{route('admin.orders.show',$order->id)}}">{{$order->order_no}}</a></td>
                                        <td>{{$order->shipping_method}}</td>
                                        <td>{{$order->total_amount}}</td>
                                        <td>{{$order->type}}</td>
                                        <td>{{$order->payment_method}}</td>
                                        <td>{{$order->delivery_date}}</td>
                                        <td>{{$order->status}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <br/>
                        <div class="tab-pane fade" id="tab3">
                            <table class="table table-striped table-bordered" id="chats_table_DT">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($balances as $balance)
                                    <tr>
                                        <td>{{\Carbon\Carbon::parse($balance->created_at)->format('d/m/Y')}}</td>
                                        <td>{{$balance->credit}}</td>
                                        <td>{{$balance->debit}}</td>
                                        <td @if($balance->totalbalance<=0) style="background-color: red; color:white;" @else  style="background-color: green; color:white;"  @endif>{{$balance->totalbalance}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row form-group">
                            <div class="col-md-10">
                                <a href="{{route('admin.customers.index')}}" class="btn red">Back</a>
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

@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('orders') !!}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <p>
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample"
                        aria-expanded="false" aria-controls="collapseExample">
                    Filter
                </button>
            </p>
            <div class="collapse" id="collapseExample">
                <div class="card card-body">
                    <form class="filterFrm " action="{{ Route('admin.orders.search')}}" method="POST">
                        @csrf
                    <div class="row">
                        <div class="col-md-2 form-group">
                            <label for="Customer_name" class="control-label">Customer name</label>
                            <select class="form-control" name="user_id">
                                <option value="">Select Customer</option>
                                @foreach($users as $user)
                                    <option value="{{$user->id}}">{{$user->first_name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="Customer_name" class="control-label">Franchise name</label>
                            <select class="form-control" name="franchises_id">
                                <option value="">Select franchises</option>
                                @foreach($franchises as $franchise)
                                    <option value="{{$franchise->id}}">{{$franchise->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="order_type" class="control-label">Order Type</label>
                            <select class="form-control" name="type">
                                <option value="">Select Type</option>
                                <option value="Normal">Normal</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="order_date" class="control-label">Delivery Date</label>
                            <input type="date" class="form-control delivery_date" name="delivery_date">
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="payment_type" class="control-label">Payment Method</label>
                            <select class="form-control" name="payment_method">
                                <option value="">Select Payment Method</option>
                                <option value="cod">COD</option>
                                <option value="online">Online</option>
                                <option value="balance">Balance</option>
                            </select>
                        </div>

                        <div class="col-md-2 form-group">
                            <label for="Status" class="control-label">Status</label>
                            <select class="form-control" name="status">
                                <option value="">Select Status</option>
                                <option value="place_order">Place Order</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="rejected">Rejected</option>
                                <option value="preparing">Preparing</option>
                                <option value="on_the_way">On the Way</option>
                                <option value="delivered">Delivered</option>
                                <option value="ready_for_delivery">Raedy for Delivery</option>
                                <option value="awaiting_payment">Awaiting Payment</option>
                                <option value="payment_done">Payment Done</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="Status" class="control-label">T/T/Y</label>
                            <select class="form-control" name="tty">
                                <option value="">Select option</option>
                                <option value="today">Today</option>
                                <option value="tomorrow">Tomorrow</option>
                                <option value="yesterday">Yesterday</option>
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label class="control-label">Product Type</label>
                            <select class="form-control" name="p_type">
                                <option value="">Select option</option>
                                <option value="Cake">Cake</option>
                                <option value="Bakery">Bakery</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success">Get</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="rorder_tbl table table-striped table-bordered table-hover table-use" id="table_DT">
                <thead>
                <tr>
                    <th>Order No</th>
                    <th>Item</th>
                    <th>Products</th>
                    <th>Customer Name</th>
                    <th>Shipping Method</th>
                    <th>Delivery Date</th>
                    <th>Type</th>
                    <th>Photo Cake</th>
                    <th>Payment Method</th>
                    <th>Status</th>

                </tr>
                </thead>
                <tbody>

                @foreach($orders as $order)
                    <tr>
                        <td><a href="{{route('admin.orders.show',$order->id)}}">{{$order->order_no}}</a></td>
                        <td> @if(App\Models\OrderItem::where('order_id',$order->id)->count('id')>0) {{ App\Models\OrderItem::where('order_id',$order->id)->count('id') }} @else {{ App\Models\CustomOrder::where('order_id',$order->id)->count('id') }} @endif</td>
                        <td>
                            @if($order->type=="Normal")
                                @php
                                $products=App\Models\OrderItem::where('order_id',$order->id)->get();
                                @endphp
                                <ul>
                                    @foreach($products as $product)
                                    <li>{{App\Models\Product::where('id',$product->product_id)->value('name')}}</li>
                                    @endforeach
                                </ul>
                            @else
                                @php
                                    $subcategory=App\Models\CustomOrder::where('order_id',$order->id)->value('sub_category_id');
                                @endphp
                                <ul>
                                    <li>{{App\Models\SubCategoryModel::where('id',$subcategory)->value('name')}}</li>

                                </ul>
                            @endif

                        </td>
                        <td>{{!empty($order->user_id) ?  App\User::where('id', $order->user_id)->value('first_name') . '  ' . App\User::where('id', $order->user_id)->value('last_name') : $order->franchises->name}}</td>
                        <td>{{$order->shipping_method}}</td>
                        <td>{{\Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y')}}</td>
                        <td>{{$order->type}}</td>

                       <td> @if($order->type=="Normal")
                        {{$photo_cake=App\Models\OrderItem::where('order_id',$order->id)->where('is_photo','<>','')->count('id')}}
                       @else
                          {{$photo_customcake=App\Models\CustomOrder::where('order_id',$order->id)->where('is_photo','<>','')->count('id')}}
                            @endif
                         </td>

                        <td>{{$order->payment_method}}</td>
                        <td>{{$order->status}}</td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('.order_tbl').DataTable({
                columnDefs: [
                    {orderable: false, targets: -1}
                ],
                "processing": true,
                "aaSorting": [[0, 'asc']],
                "scrollX": false
            });
        });

    </script>
@endpush

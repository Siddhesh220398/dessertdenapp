@extends('admin.layouts.app')

@section('breadcrumb')
    {!! Breadcrumbs::render('products') !!}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
<br/>
                <div class="card card-body">
                    <form class="filterFrm " action="{{ Route('admin.products.search')}}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-2 form-group"></div>
                            <div class="col-md-2 form-group">
                                <label for="order_date" class="control-label">From Date</label>
                                <input type="date" class="form-control delivery_date" name="fromdate">
                            </div>

                            <div class="col-md-2 form-group">
                                <label for="order_date" class="control-label">To Date</label>
                                <input type="date" class="form-control delivery_date" name="todate">
                            </div>

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

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Get</button>
                            </div>
                        </div>
                    </form>
                </div>

        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <table class="order_tbl table table-striped table-bordered table-hover table-user" id="table_DT">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Most Used Product</th>
                    <th>code</th>
                    <th>Image</th>

                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                <tr>
                    <td>{{$product->name}}</td>
                    <td>{{$product->description}}</td>
                    <td>{{App\Models\OrderItem::where('product_id',$product->id)->count('id')}}</td>
                    <td>{{$product->code}}</td>
                    <td><img src="{{!empty($product->image) ? Storage::url('app/public/'.$product->image  ):url('public/theme/images/logo.png')}}" alt="Image" class="img-thumbnail" style="height: 100px; width: 100px;"  /></td>
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

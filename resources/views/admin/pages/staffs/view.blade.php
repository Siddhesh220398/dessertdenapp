@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('staffs_details', $staff) !!}
@endsection

@section('content')
 <div class="row ">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="{{ $icon }} font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">Staff Details</span>
                </div>
            </div>
            <div class="portlet-body">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab1" data-toggle="tab"> INFO </a>
                    </li>
                     <li>
                        <a href="#tab2" data-toggle="tab"> Order Detail </a>
                    </li>
                    
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="tab1">
                        <div class="row form-group">
                            <label for="name" class="col-md-2 control-label bold">Name : </label>
                            <div class="col-md-4">
                                <label class="control-label">{{ $staff->name }}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="email" class="col-md-2 control-label bold">Email Address : </label>
                            <div class="col-md-4">
                                <label class="control-label">{{ $staff->email }}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="phone" class="col-md-2 control-label bold">Phone : </label>
                            <div class="col-md-4">
                                <label class="control-label">{{ $staff->mobile }}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="phone" class="col-md-2 control-label bold">Block Status : </label>
                            <div class="col-md-4">
                                @php
                                    if(!empty($staff->block)) {
                                        if($staff->block->block_type != 'permanent') {
                                            if (time() >= strtotime($staff->block->block_time)) {
                                                $activeStatus = true;
                                            } else {
                                                $period = 'blocked for ' . $staff->block->block_value . ' ' . $staff->block->block_value_type . ' (Unblock : ' . \Carbon\Carbon::parse($staff->block->block_time)->diffForHumans() . ')';
                                            }
                                        } else {
                                            $period = 'blocked for permanent';
                                        }
                                    } else {
                                        $activeStatus = true;
                                    }
                                @endphp
                                <label class="control-label">{{ !empty($activeStatus) ? 'Unblock' : $period}}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="profile" class="col-md-2 control-label bold">Profile : </label>
                            <div class="col-md-4">
                                <img class="img-thumbnail" src="{{ $staff->profile }}" alt="Image" style="width:50%;">
                            </div>
                        </div>
                    </div>
                      <div class="tab-pane fade" id="tab2">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="hosts_table_DT">
                            </table>
                        </div>
                    </div>
                   
                    <div class="row form-group">
                        <div class="col-md-10">
                            <a href="{{route('admin.staffs.index')}}" class="btn red">Back</a>
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
    $(function(){
        $('#hosts_table_DT').dataTable({
            "processing": true,
            "serverSide": true,
            "language": {
                "lengthMenu": "_MENU_ entries",
                "paginate": {
                    "previous": '<i class="fa fa-angle-left" ></i>',
                    "next": '<i class="fa fa-angle-right" ></i>'
                }
            },
            "columns": [
                { "title": "Order No" ,"data": "order_no"},
                { "title": "Delivery Date" ,"data": "delivery_date"},
                { "title": "Delivery Time" ,"data": "delivery_time"},
                { "title": "Type" ,"data": "type"},
                { "title": "total" ,"data": "total"},
                @if (in_array('view', $permissions) || in_array('edit', $permissions) || in_array('delete', $permissions))
                    { "title": "Action" ,"data": "action", searchble: false, sortable:false }
                @endif
            ],
            responsive: false,
            "order": [
                [0, 'asc']
            ],
            "lengthMenu": [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            "pageLength": 10,
            "ajax": {
                "url": "{{route('admin.assignorders.listing')}}", // ajax source
                "data": {
                    staff_id: function() {
                        return "{{ $staff->id }}";
                    }
                }
            },
            drawCallback: function( oSettings ) {
                $('.make-switch').bootstrapSwitch();
                $('.make-switch').bootstrapSwitch('onColor', 'success');
                $('.make-switch').bootstrapSwitch('offColor', 'danger');
            },
            "dom": "<'row' <'col-md-12'>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
        });

       
    });
</script>
@endpush
@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('user_details', $user) !!}
@endsection

@section('content')
 <div class="row ">
    <div class="col-md-12">
        <!-- BEGIN SAMPLE FORM PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="{{ $icon }} font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">User Details</span>
                </div>
            </div>
            <div class="portlet-body">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab1" data-toggle="tab"> INFO </a>
                    </li>
                    <li>
                        <a href="#tab2" data-toggle="tab"> Host Invitations </a>
                    </li>
                    <li>
                        <a href="#tab3" data-toggle="tab"> Guest Requests </a>
                    </li>
                    <li>
                        <a href="#tab4" data-toggle="tab"> Chat List </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active in" id="tab1">
                        <div class="row form-group">
                            <label for="name" class="col-md-2 control-label bold">Name : </label>
                            <div class="col-md-4">
                                <label class="control-label">{{ $user->name }}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="email" class="col-md-2 control-label bold">Email Address : </label>
                            <div class="col-md-4">
                                <label class="control-label">{{ $user->email }}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="phone" class="col-md-2 control-label bold">Phone : </label>
                            <div class="col-md-4">
                                <label class="control-label">{{ $user->phone }}</label>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label for="phone" class="col-md-2 control-label bold">Block Status : </label>
                            <div class="col-md-4">
                                @php
                                    if(!empty($user->block)) {
                                        if($user->block->block_type != 'permanent') {
                                            if (time() >= strtotime($user->block->block_time)) {
                                                $activeStatus = true;
                                            } else {
                                                $period = 'blocked for ' . $user->block->block_value . ' ' . $user->block->block_value_type . ' (Unblock : ' . \Carbon\Carbon::parse($user->block->block_time)->diffForHumans() . ')';
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
                                <img class="img-thumbnail" src="{{ $user->profile }}" alt="Image">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab2">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="hosts_table_DT">
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab3">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="guests_table_DT">
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab4">
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover" id="chats_table_DT">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Last Message</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chatList as $list)
                                    <tr>
                                        <td>{!! $list['name'] !!}</td>
                                        <td>{!! $list['message'] !!}</td>
                                        <td>{!! $list['action'] !!}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-10">
                            <a href="{{route('admin.users.index')}}" class="btn red">Back</a>
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
                { "title": "Date" ,"data": "date"},
                { "title": "User Name" ,"data": "user_name"},
                { "title": "From Airport Name" ,"data": "from_airport_name"},
                { "title": "To Airport Name" ,"data": "to_airport_name"},
                { "title": "Lounge Name" ,"data": "lounge_name"},
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
                "url": "{{route('admin.hosts.listing')}}", // ajax source
                "data": {
                    user_id: function() {
                        return "{{ $user->id }}";
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

        $('#guests_table_DT').dataTable({
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
                { "title": "Date" ,"data": "date"},
                { "title": "User Name" ,"data": "user_name"},
                { "title": "From Airport Name" ,"data": "from_airport_name"},
                { "title": "To Airport Name" ,"data": "to_airport_name"},
                { "title": "Lounge Name" ,"data": "lounge_name"},
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
                "url": "{{route('admin.guests.listing')}}", // ajax source
                "data": {
                    user_id: function() {
                        return "{{ $user->id }}";
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

        $('#chats_table_DT').dataTable({
            "language": {
                "lengthMenu": "_MENU_ entries",
                "paginate": {
                    "previous": '<i class="fa fa-angle-left" ></i>',
                    "next": '<i class="fa fa-angle-right" ></i>'
                }
            },
            responsive: false,
            "lengthMenu": [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            "pageLength": 10,
            "dom": "<'row' <'col-md-12'>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
        });
    });
</script>
@endpush
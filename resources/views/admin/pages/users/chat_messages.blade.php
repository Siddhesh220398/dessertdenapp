@extends('admin.layouts.app')

@section('breadcrumb')
{!! Breadcrumbs::render('user_chat_messages', $user_id, $receiver_id) !!}
@endsection

@push('page_css')
	<style type="text/css">
		.post {
		    transition: display .3s;
		    padding: 5px 0;
		    margin: 10px auto;
		    font-size: 13px;
		}
		.post.out .avatar {
		    float: right;
    		margin-left: 10px;
		}
		.post.in .avatar {
		    float: left;
    		margin-right: 10px;
		}
		.post .avatar {
			width: 45px;
			height: 45px;
    		border-radius: 50%!important;
		}
		.post.out .message {
			margin-right: 55px;
			text-align: right;
		}
		.post.in .message {
			margin-left: 55px;
			text-align: left;
		}
		.post .message {
			display: block;
		    padding: 5px;
		    position: relative;
		    color: #90a1af;
		    background: #36424c
		}
		.post.out .message .arrow {
			display: block;
			position: absolute;
			top: 9px;
			right: -6px;
			border-top: 6px solid transparent;
			border-bottom: 6px solid transparent;
			border-left-width: 6px;
			border-left-style: solid;
			border-left-color: #36424c;
		}
		.post.in .message .arrow {
			display: block;
			position: absolute;
			top: 9px;
			left: -6px;
			width: 0;
			height: 0;
			border-top: 6px solid transparent;
			border-bottom: 6px solid transparent;
			border-right-width: 6px;
			border-right-style: solid;
			border-right-color: #36424c;
		}
		.post.out .name, .post.out .datetime {
			text-align: right;
		}
		.post.in .name, .post.in .datetime {
			text-align: left;
		}
		.post .name, .post .datetime {
			font-size: 12px;
		    font-weight: 300;
		    color: #8496a7;
		}
		.post.out .message {
		    text-align: right;
		}
		.post.in .message {
		    text-align: left;
		}
		.post .message {
			color: #c3c3c3;
    		display: block;
		}
		.post .body {
			color: #c3c3c3;
			display: block;
		}
	</style>
@endpush

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-location-arrow font-green"></i>
                    <span class="caption-subject font-green sbold uppercase">Chat Messages</span>
                </div>
            </div>
            <div class="portlet-body">
            	@foreach($messages as $message)
	            	<div class="post {{ $message->sendUserId == $user_id ? 'out' : 'in' }}">
	                    <img class="avatar" alt="" src="{{ $message->senderUser->profile }}">
	                    <div class="message">
	                        <span class="arrow"></span>
	                        <a href="javascript:;" class="name">{{ $message->senderUser->name }}</a>
	                        <span class="datetime">{{ date('Y-m-d H:i:s', strtotime($message->created_at)) }}</span>
	                        <span class="body"> {{ $message->message }} </span>
	                    </div>
	                </div>
                @endforeach
                <div class="row form-group">
                    <div class="col-md-10">
                        <a href="{{route('admin.users.show', $user_id)}}" class="btn red">Back</a>
                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection
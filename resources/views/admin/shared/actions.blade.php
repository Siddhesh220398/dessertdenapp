@php
	$view = $view ?? true;
	$edit = $edit ?? true;
	$delete = $delete ?? true;
	$block = $block ?? false;
@endphp

@if (in_array('view', $permissions) || in_array('edit', $permissions) || in_array('delete', $permissions))
		@if (in_array('view', $permissions) && $view)
			<a href="{{ route($routeName.'.show', $id) }}" title="View" class="btn btn-success btn-md">View</a>
		@endif
		@if (in_array('edit', $permissions) && $edit)
			<a href="{{ route($routeName.'.edit', $id) }}" title="Edit" class="btn btn-warning btn-md">Edit</a>
		@endif
		@if (in_array('delete', $permissions) && $delete)
			<a title="Delete" href="{{ route($routeName.'.destroy', $id) }}" class="btn btn-danger btn-md act-delete">Delete</a>
		@endif
		@if ($block)
			<a title="Block User" data-toggle="modal" href="#block-modal" class="btn btn-warning btn-md" data-id="{{ $id }}">Block User</a>
		@endif
@endif





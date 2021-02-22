@if (in_array('edit', $permissions))
	<td>
		<input type="checkbox" {{ $params["checked"] }} data-id="{{ $params['id'] }}" data-url="{{ $params['url'] }}" class="make-switch {{ !empty($params['class']) ? $params['class'] : 'status-switch'}}" data-getaction="{{ (!empty($params['getaction']) ? $params['getaction'] : '') }}" data-on-text="<i class='fa fa-check'></i>" data-off-text="<i class='fa fa-times' ></i>" {{ !empty($params['disabled']) ? $params['disabled'] : '' }} >
	</td>
@endif

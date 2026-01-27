@php ($headerOption = [
  'title' => $module,
  'header_buttons' => [
    ($permission['create'] ? '<a class="'. \Config::get('view.buttons.primary') .'" href="'. route($routePrefix . '.create') .'" data-toggle="tooltip" data-original-title="Add New Game Location">'. \Config::get('settings.icon_add') .' <span>Add New Game</span></a>' : '')
  ],
  'filters' => isset($filters) ? $filters : [],
  'data'    => isset($data) ? $data : []
])
@extends('admin.layouts.layout', $headerOption)


@section('content')
<div class="table-responsive">
  <table class="{!! \Config::get('view.table.table_class') !!}">
    <thead class="{!! \Config::get('view.table.table_head_class') !!}">
      <tr>
       <th>Name {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'name', $orderBy) !!}</th>
       <th>Logo</th>
       <th>Is Active {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'is_active', $orderBy) !!}</th>
       @if($permission['edit'] || $permission['destroy'])
       <th width="15%" style="text-align: right;">Action</th>
       @endif
      </tr>
    </thead>
      <tbody>
      @if(count($data) != 0)
        @foreach ($data as $key => $val)
        <tr>
          <td>{{ $val->name }}</td>
          <td>
            @if($val->logo)
              @php($logoFileModel = \App\Models\File::find($val->logo))
              @if($logoFileModel)
                @php($logoFile = \App\Models\File::file($logoFileModel))
                <img src="{{ $logoFile['thumb'] ?? $logoFile['original'] }}" alt="Logo" style="max-width: 50px; max-height: 50px;">
              @else
                <span class="text-muted">No Logo</span>
              @endif
            @else
              <span class="text-muted">No Logo</span>
            @endif
          </td>
          <td>
            <div class="square-switchs">
              <input type="checkbox" 
                     name="is_active" 
                     value="1" 
                     id="is_active_{{ $val->id }}" 
                     class="toggle-is-active" 
                     data-id="{{ $val->id }}"
                     data-url="{{ route('game.locations.toggle-status', $val->id) }}"
                     {{ $val->is_active == 1 ? 'checked' : '' }} 
                     switch="dark"/>
              <label for="is_active_{{ $val->id }}" data-on-label="Active" data-off-label="Inactive"></label>
            </div>
          </td>
          @if($permission['edit'] || $permission['destroy'])
          <td class="text-right">
            <a href="{{ route('game.slots.index', $val->id) }}" class="{!! \Config::get('view.table.list_light_button') !!}" data-toggle="tooltip" title="Assign Slots" data-original-title="Assign Slots"><i class="bx bx-list-ul"></i></a>
            @if($permission['edit'])
            <a href="{{ route($routePrefix . '.edit',$val->id) }}" class="{!! \Config::get('view.table.list_light_button') !!}" data-toggle="tooltip" title="" data-original-title="Edit">{!! \Config::get('settings.icon_edit') !!}</a>
            @endif
            @if($permission['destroy'])
           <a class="{!! \Config::get('view.table.list_danger_button') !!}" data-toggle="tooltip" title="" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="event.preventDefault();
              document.getElementById('delete-form-{{$val->id}}').submit();" data-original-title="Delete">{!! \Config::get('settings.icon_delete') !!}</a>
            {!! Form::open([
              'method' => 'DELETE',
              'route' => [
                $routePrefix . '.destroy',
                $val->id
                ],
              'id' => 'delete-form-'.$val->id
            ]) !!}
            {!! Form::close() !!}
            @endif
          </td>
          @endif
        </tr>
         @endforeach
      @else
      <tr><td colspan="25"><div class="alert alert-danger">No Data</div></td></tr>
      @endif
    </tbody>
  </table>
</div>
@include('admin.components.pagination')

@push('page_script')
<script>
$(document).ready(function() {
    $('.toggle-is-active').on('change', function() {
        var checkbox = $(this);
        var id = checkbox.data('id');
        var url = checkbox.data('url');
        var isChecked = checkbox.is(':checked');
        var isActive = isChecked ? 1 : 0;
        
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                is_active: isActive
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Update checkbox state
                    checkbox.prop('checked', response.is_active == 1);
                } else {
                    // Revert checkbox state on error
                    checkbox.prop('checked', !isChecked);
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                // Revert checkbox state on error
                checkbox.prop('checked', !isChecked);
                alert('Error updating status. Please try again.');
            }
        });
    });
});
</script>
@endpush
@endsection

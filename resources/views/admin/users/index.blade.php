@php ($headerOption = [
'title' => $module,
'header_buttons' => [
  '<a class="'. \Config::get('view.buttons.secondary') .'" href="'. route('export.users', Request::input()) .'" data-toggle="tooltip" data-original-title="Export Records"><i class="bx bx-download"></i></a>',
  ($permission['create'] ? '<a class="'. \Config::get('view.buttons.primary') .'" href="'. route($routePrefix . '.create') .'" data-toggle="tooltip" data-original-title="Add New Record">'. \Config::get('settings.icon_add') .' <span>Add New</span></a>' : ''),
],
'filters' => isset($filters) ? $filters : [],
'data' => isset($data) ? $data : []
])
@extends('admin.layouts.layout', $headerOption)


@section('content')
<div class="table-responsive">
  <table class="{!! \Config::get('view.table.table_class') !!}">
    <thead class="{!! \Config::get('view.table.table_head_class') !!}">
      <tr>
        <th colspan="2">Name {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'first_name', $orderBy) !!}</th>
        <th>Contact {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'email', $orderBy) !!}</th>
        <th>Role {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'r__title', $orderBy) !!}</th>
        <th>Status</th>
        <th>Created At</th>
        @if($permission['edit'] || $permission['destroy'])
        <th width="15%" style="text-align: right;">Action</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @if(count($data) != 0)
        @foreach ($data as $key => $val)
        <tr>
          <td width="3%">{!! \App\Helpers\Helper::genLogo($val->avatar, $val, 'xs', 'rounded-circle') !!}</td>
          <td><a href="{{ route($routePrefix . '.show', $val->id) }}" class="show-modal-lg">{{ $val->full_name }}</a></td>
          <td><strong>Email:</strong> {{ $val->email }}</td>
          <td>{{ $val->role_title }}</td>
          <td><span class="badge badge-pill badge-soft-{{ $val->statuses[$val->status]['badge'] }} font-size-12">{!! $val->statuses[$val->status]['name'] !!}</span></td>
          <td>{{ \App\Helpers\Helper::showDate($val->created_at) }}</td>
          @if($permission['edit'] || $permission['destroy'])
          <td class="text-right">
            @if($permission['edit'])
            <a href="{{ route($routePrefix . '.edit', $val->id) }}" class="{!! \Config::get('view.table.list_light_button') !!}" data-toggle="tooltip" title="" data-original-title="Edit">{!! \Config::get('settings.icon_edit') !!}</a>
            @endif
            @if($permission['destroy'] && $val->id != auth()->user()->id)
              <a class="{!! \Config::get('view.table.list_danger_button') !!}" data-toggle="tooltip" title="" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="event.preventDefault();
                    document.getElementById('delete-form-{{$val->id}}').submit();" data-original-title="Delete">{!! \Config::get('settings.icon_delete') !!}</a>
              {!! Form::open([
                'method'  => 'DELETE',
                'route'   => [
                  $routePrefix . '.destroy',
                  $val->id
                ],
                'style' => 'display:inline',
                'id'    => 'delete-form-' . $val->id
              ]) !!}
              {!! Form::close() !!}
            @endif
          </td>
          @endif
        </tr>
        @endforeach
      @else
      <tr>
        <td colspan="25">
          <div class="alert alert-danger">No Data</div>
        </td>
      </tr>
      @endif
    </tbody>
  </table>
</div>

@include('admin.components.pagination')

@endsection
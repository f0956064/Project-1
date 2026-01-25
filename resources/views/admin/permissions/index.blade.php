@php ($headerOption = [
  'title' => $module,
  'header_buttons' => [
    ($permission['create'] ? '<a class="'. \Config::get('view.buttons.primary') .'" href="'. route($routePrefix . '.create') .'" data-toggle="tooltip" data-original-title="Add New Record">'. \Config::get('settings.icon_add') .' <span>Add New</span></a>' : '')
  ],
  'filters' => isset($filters) ? $filters : [],
  'data'    => isset($data) ? $data : []
])
@extends('admin.layouts.layout', $headerOption)


@section('content')
<form action="{{ route($routePrefix . '.assign.menu') }}" method="post" id="permission-form">
  @csrf
  {!! Form::hidden('permission_ids', null, ['id' => 'permission_ids']) !!}
  <div class="d-flex border border-secondary btn-light p-2">
    <div class="flex-grow-1 me-3">
        <div class="row" id="menu-dropdowns">
          <div class="col-lg-3">
            {!! Form::select('menu_id[]', $menus, null, ['class' => 'form-control parent-menu']) !!}
          </div>
        </div>
    </div>
    <div class="flex-shrink-0 align-self-center">
      <button type="submit" class="btn btn-danger mr-2 btn-delete-confirmation" name="action" value="delete" data-form-id="permission-form">Delete</button>
      <button type="submit" class="btn btn-dark" name="action" value="assign">Assign</button>
    </div>
  </div>
</form>
<div class="table-responsive">
  <table class="{!! \Config::get('view.table.table_class') !!}">
    <thead class="{!! \Config::get('view.table.table_head_class') !!}">
      <tr>
        <th>Permission types {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'permissions__p_type', $orderBy) !!}</th>
        <th>Menu {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'permissions__menu_id', $orderBy) !!}</th>
        <th>Module {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'permissions__class', $orderBy) !!}</th>
        <th>Function {!! \App\Helpers\Helper::sort($routePrefix . '.index', 'permissions__method', $orderBy) !!}</th>
        @if($permission['edit'] || $permission['destroy'])
        <th width="15%" style="text-align: right;">Action</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @foreach ($data as $value)
      <tr>
          <td>
            <div class="custom-control custom-checkbox">
              <input type="checkbox" name="permission_ids[]" value="{{ $value->id }}" id="permission-{{ $value->id }}" class="custom-control-input assign_permission_id"/>
              <label class="custom-control-label" for="permission-{{ $value->id }}">{{ ucwords(str_replace("_", " ", $value->p_type)) }}</label>
            </div>
          </td>
          <td>{{ $value->menu }}</td>
          <td>{{ $value->class }}</td>
          <td>{{ $value->method }}</td>
          @if($permission['edit'] || $permission['destroy'])
          <td class="text-right">
            @if($permission['edit'])
              <a href="{{ route($routePrefix . '.edit', $value->id, $srch_params) }}" class="{!! \Config::get('view.table.list_light_button') !!}" data-toggle="tooltip" title="" data-original-title="Edit">{!! \Config::get('settings.icon_edit') !!}</a>
            @endif
            @if($permission['destroy'])
              <a class="{!! \Config::get('view.table.list_danger_button') !!}" data-toggle="tooltip" title="" data-confirm="Are You Sure?|This action can not be undone. Do you want to continue?" data-confirm-yes="event.preventDefault();
                  document.getElementById('delete-form-{{$value->id}}').submit();" data-original-title="Delete">{!! \Config::get('settings.icon_delete') !!}</a>
              {!! Form::open([
                'method' => 'DELETE',
                'route' => [
                  $routePrefix . '.destroy',
                  $value->id
                  ],
                'id' => 'delete-form-' . $value->id
              ]) !!}
              {!! Form::close() !!}
            @endif
          </td>
          @endif
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@include('admin.components.pagination')
@endsection
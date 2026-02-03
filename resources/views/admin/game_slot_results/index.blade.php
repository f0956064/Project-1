@php ($headerOption = [
  'title' => $module,
  'header_buttons' => [
    '<a href="'.route($routePrefix.'.create').'" class="btn btn-primary waves-effect">Add New</a>'
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
        <th>#</th>
        <th>Game Location</th>
        <th>Game Slot</th>
        <th>Slot Time</th>
        <th>Game Mode</th>
        <th>Result Date</th>
        <th>Result Value</th>
        <th width="15%">Action</th>
      </tr>
    </thead>
    <tbody>
      @if(count($data) != 0)
        @foreach ($data as $key => $val)
          <tr>
            <td>{{ $i + $key + 1 }}</td>
            <td>{{ $val->location ? $val->location->name : $val->game_location_id }}</td>
            <td>{{ $val->slot ? $val->slot->name : $val->game_slot_id }}</td>
            <td>{{ $val->slot ? $val->slot->start_time . ' - ' . $val->slot->end_time : '-' }}</td>
            <td>{{ $val->mode ? $val->mode->name : $val->game_mode_id }}</td>
            <td>{{ $val->result_date }}</td>
            <td>{{ $val->result_value }}</td>
            <td>
                <a href="{{ route($routePrefix . '.edit', $val->id) }}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit"><i class="bx bx-edit"></i></a>
            </td>
          </tr>
        @endforeach
      @else
        <tr><td colspan="8"><div class="alert alert-danger">No Data</div></td></tr>
      @endif
    </tbody>
  </table>
</div>
@include('admin.components.pagination')
@endsection

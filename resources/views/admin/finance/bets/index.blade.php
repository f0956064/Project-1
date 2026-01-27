@php ($headerOption = [
  'title' => $module,
  'header_buttons' => [],
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
        <th>User</th>
        <th>Location</th>
        <th>Slot</th>
        <th>Mode</th>
        <th>Guess</th>
        <th class="text-right">Amount</th>
        <th>Date</th>
        <th>Placed At</th>
      </tr>
    </thead>
    <tbody>
      @if(count($data) != 0)
        @foreach ($data as $key => $val)
          <tr>
            <td>{{ $i + $key + 1 }}</td>
            <td>{{ $val->user ? ($val->user->full_name ?? $val->user->email) : $val->user_id }}</td>
            <td>{{ $val->location ? $val->location->name : $val->game_location_id }}</td>
            <td>{{ $val->slot ? $val->slot->name : $val->game_slot_id }}</td>
            <td>{{ $val->mode ? $val->mode->name : $val->game_mode_id }}</td>
            <td>{{ $val->guess }}</td>
            <td class="text-right">{{ number_format((float) $val->amount, 2) }}</td>
            <td>{{ $val->date }}</td>
            <td>{{ $val->created_at }}</td>
          </tr>
        @endforeach
      @else
        <tr><td colspan="25"><div class="alert alert-danger">No Data</div></td></tr>
      @endif
    </tbody>
  </table>
</div>
@include('admin.components.pagination')
@endsection


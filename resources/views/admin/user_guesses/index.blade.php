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
        <th>User Name</th>
        <th>Game Location</th>
        <th>Game Slot</th>
        <th>Slot Time</th>
        <th>Game Mode</th>
        <th>Date</th>
        <th>Guess</th>
        <th>Amount</th>
        <th>DateTime</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      @if(count($data) != 0)
        @foreach ($data as $key => $val)
          <tr>
            <td>{{ $i + $key + 1 }}</td>
            <td>{{ $val->user ? ($val->user->full_name ?? $val->user->name) : $val->user_id }}</td>
            <td>{{ $val->location ? $val->location->name : $val->game_location_id }}</td>
            <td>{{ $val->slot ? $val->slot->name : $val->game_slot_id }}</td>
            <td>{{ $val->slot ? $val->slot->start_time . ' - ' . $val->slot->end_time : $val->game_slot_id }}</td>
            <td>{{ $val->mode ? $val->mode->name : $val->game_mode_id }}</td>
            <td>{{ $val->date }}</td>
            <td>{{ $val->guess }}</td>
            <td>{{ $val->amount }}</td>
            <td>{{ $val->created_at }}</td>
            <td>
              <a href="{{ route('admin.user_guesses.delete', $val->id) }}" class="btn btn-danger btn-sm">Delete</a>
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

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
        <th>Game Location</th>
        <th>Slot</th>
        <th>Mode</th>
        <th>Date</th>
        <th>Winning Number</th>
        <th class="text-right">Bet Amount</th>
        <th class="text-right">Winning Amount</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
      @if($data->count())
        @foreach ($data as $key => $val)
          <tr>
            <td>{{ $i + $key + 1 }}</td>
            <td>{{ trim($val->user_name) ?: ($val->username ?? $val->user_id) }}</td>
            <td>{{ $val->location_name ?? $val->game_id }}</td>
            <td>{{ $val->slot_name ?? $val->slot_id }}</td>
            <td>{{ $val->mode_name ?? $val->game_mode_id }}</td>
            <td>{{ $val->date }}</td>
            <td>
              {{ $val->guess_number }}
            </td>
            <td class="text-right">{{ number_format((float) $val->bet_amount, 2) }}</td>
            <td class="text-right" style="color: #28a745; font-weight: 700;">
              +{{ number_format((float) $val->winning_amount, 2) }}
            </td>
            <td>{{ $val->created_at ? $val->created_at->format('M j, Y H:i') : '-' }}</td>
          </tr>
        @endforeach
      @else
        <tr><td colspan="10"><div class="alert alert-danger">No Data</div></td></tr>
      @endif
    </tbody>
  </table>
</div>
@include('admin.components.pagination')
@endsection

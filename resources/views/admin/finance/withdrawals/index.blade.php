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
        <th class="text-right">Amount</th>
        <th>Payment Mode</th>
        <th>Status</th>
        <th>Requested At</th>
        <th width="20%" style="text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
      @if(count($data) != 0)
        @foreach ($data as $key => $val)
          <tr>
            <td>{{ $i + $key + 1 }}</td>
            <td>{{ $val->user ? ($val->user->full_name ?? $val->user->email) : $val->user_id }}</td>
            <td class="text-right">{{ number_format((float) $val->amount, 2) }}</td>
            <td>{{ $val->payment_mode }}</td>
            <td>
              @if((int) $val->is_approved === 1)
                <span class="badge badge-soft-success">Approved</span>
              @elseif((int) $val->is_approved === 2)
                <span class="badge badge-soft-danger">Rejected</span>
              @else
                <span class="badge badge-soft-warning">Pending</span>
              @endif
            </td>
            <td>{{ $val->created_at }}</td>
            <td class="text-right">
              @if((int) $val->is_approved === 0)
                <form method="POST" action="{{ route('finance.withdrawals.approve', $val->id) }}" style="display:inline;">
                  @csrf
                  <button type="submit" class="{!! \Config::get('view.table.list_light_button') !!}" data-toggle="tooltip" title="Approve">
                    <i class="bx bx-check"></i>
                  </button>
                </form>
                <form method="POST" action="{{ route('finance.withdrawals.reject', $val->id) }}" style="display:inline;">
                  @csrf
                  <button type="submit" class="{!! \Config::get('view.table.list_danger_button') !!}" data-toggle="tooltip" title="Reject (Refund)">
                    <i class="bx bx-x"></i>
                  </button>
                </form>
              @endif
            </td>
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


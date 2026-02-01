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
        <th>Mobile No</th>
        <th>Status</th>
        <th class="text-right">Amount</th>
        <th>Payment Mode</th>
        <th>Requested At</th>
        <th width="20%" style="text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
      @if(count($data) != 0)
        @foreach ($data as $key => $val)
          <tr>
            <td>{{ $i + $key + 1 }}</td>
            <td>{{ $val->user ? $val->user->full_name : $val->user_id }}</td>
            <td>{{ $val->mobile_no }}</td>
            <td>
              @if((int) $val->is_approved === 1)
                <span class="badge badge-soft-success">Approved</span>
              @elseif((int) $val->is_approved === 2)
                <span class="badge badge-soft-danger">Rejected</span>
              @else
                <span class="badge badge-soft-warning">Pending</span>
              @endif
            </td>
            <td class="text-right">{{ number_format((float) $val->amount, 2) }}</td>
            <td>{{ $val->payment_mode }}</td>
            <td>{{ $val->created_at }}</td>
            <td class="text-right">
              @if((int) $val->is_approved === 0)
                <form method="POST" action="{{ route('user-deposits.approve', $val->id) }}" style="display:inline;">
                  @csrf
                  <button type="submit" class="btn btn-success btn-sm" data-toggle="tooltip" title="Approve">
                    <i class="bx bx-check"></i> Approve
                  </button>
                </form>
                <form method="POST" action="{{ route('user-deposits.reject', $val->id) }}" style="display:inline;">
                  @csrf
                  <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" title="Reject">
                    <i class="bx bx-x"></i> Reject
                  </button>
                </form>
              @endif
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

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
        <th width="10%" style="text-align: right;">Action</th>
      </tr>
    </thead>
    <tbody>
      @if(count($data) != 0)
        @foreach ($data as $key => $val)
          <tr>
            <td>{{ $i + $key + 1 }}</td>
            <td>{{ $val->user ? ($val->user->full_name ?? $val->user->email) : $val->user_id }}</td>
            <td class="text-right">{{ number_format((float) $val->amount, 2) }}</td>
            <td class="text-right">
              <a href="{{ route('finance.wallets.edit', $val->id) }}" class="{!! \Config::get('view.table.list_light_button') !!}" data-toggle="tooltip" title="Edit">
                {!! \Config::get('settings.icon_edit') !!}
              </a>
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


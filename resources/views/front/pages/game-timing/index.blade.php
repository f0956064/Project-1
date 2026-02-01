@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Game Timing</h3>
  </div>

  @if(!empty($slotsByLocation))
    @foreach($slotsByLocation as $item)
      <div class="front-card" style="padding: 20px; margin-bottom: 20px;">
        <h4 style="margin-top: 0; color: var(--theme-primary);">{{ $item['location']->name }}</h4>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" style="margin: 0;">
            <thead>
              <tr style="background: var(--theme-primary); color: #fff;">
                <th>Slot Name</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Result Time</th>
                <th>Off Days</th>
              </tr>
            </thead>
            <tbody>
              @foreach($item['slots'] as $slot)
                <tr>
                  <td>{{ $slot->name }}</td>
                  <td>{{ $slot->start_time ?? '-' }}</td>
                  <td>{{ $slot->end_time ?? '-' }}</td>
                  <td>{{ $slot->result_time ?? '-' }}</td>
                  <td>{{ $slot->off_days ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  @else
    <div class="front-card" style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No game timing available.</div>
  @endif
@endsection

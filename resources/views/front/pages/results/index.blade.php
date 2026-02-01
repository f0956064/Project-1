@extends('front.layouts.app')

@section('content')
  <div class="page-header">
    <h3 style="margin-top: 0; color: var(--theme-primary);">Game Results</h3>
  </div>

  <div class="front-card" style="padding: 16px; margin-bottom: 20px;">
    <strong>Wallet Balance:</strong> <span style="color: var(--theme-primary); font-size: 1.2em;">{{ number_format((float) ($wallet->amount ?? 0), 2) }}</span>
  </div>

  @if(!empty($resultsByLocation))
    @foreach($resultsByLocation as $locData)
      <div class="front-card" style="padding: 20px; margin-bottom: 20px;">
        <h4 style="margin-top: 0; color: var(--theme-primary);">{{ $locData['location']->name }}</h4>
        @foreach($locData['slotResults'] as $sr)
          <h5 style="margin-top: 16px; margin-bottom: 8px;">{{ $sr['slot']->name }}</h5>
          @if($sr['results']->count())
            <div class="table-responsive">
              <table class="table table-bordered table-striped" style="margin: 0 0 16px 0;">
                <thead>
                  <tr style="background: var(--theme-primary); color: #fff;">
                    <th>Result Date</th>
                    <th>Result</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($sr['results'] as $r)
                    <tr>
                      <td>{{ $r->result_date ?? ($r->created_at ? $r->created_at->format('Y-m-d') : '-') }}</td>
                      <td>{{ $r->result_value ?? $r->result ?? '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-muted" style="margin-bottom: 16px;">No results yet.</p>
          @endif
        @endforeach
      </div>
    @endforeach
  @else
    <div class="front-card" style="padding: 24px; text-align: center; color: var(--theme-text-muted);">No results available.</div>
  @endif
@endsection

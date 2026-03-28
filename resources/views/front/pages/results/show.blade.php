@extends('front.layouts.app')

@section('content')
<style>
    .results-page {
        background: #facc15; /* Yellow background like screenshot */
        min-height: 100vh;
        padding-top: 20px;
    }
    .results-container {
        padding-bottom: 80px;
    }
    .results-title {
        text-align: center;
        color: #4b2185;
        font-weight: 800;
        margin-bottom: 25px;
        font-size: 2.4rem;
    }
    .tabs-wrapper {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    .tab-btn {
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        border: none;
        transition: all 0.3s;
        min-width: 140px;
        text-align: center;
    }
    .tab-btn.active {
        background: #7c3aed;
        color: #fff;
    }
    .tab-btn:not(.active) {
        background: #fff;
        color: #7c3aed;
    }
    .date-section {
        background: #fff;
        border-radius: 4px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 10px 0;
    }
    .date-label {
        text-align: right;
        padding: 0 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
        font-size: 1.5rem;
    }
    .results-table {
        width: 100%;
        border-collapse: collapse;
    }
    .results-table td {
        border: 1px solid #7c3aed;
        text-align: center;
        padding: 8px;
        font-weight: 800;
        font-size: 1.6rem;
        width: {{ 100 / max(1, count($slots)) }}%;
    }
    .results-table-container {
        padding: 0 15px;
        overflow-x: auto;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>

<div class="results-page">
    <div class="container results-container">
        <div class="header-back" style="margin-bottom: 10px;">
           <a href="{{ route('front.results') }}" class="btn-action btn-sm" style="display: inline-flex; width: auto; padding: 6px 12px;">&larr; Back to Locations</a>
        </div>

        <h3 class="results-title">{{ $location->name }} Results</h3>

        <div class="tabs-wrapper">
            <button class="tab-btn active" onclick="switchTab('single', this)">Single/Patti</button>
            <button class="tab-btn" onclick="switchTab('jodi', this)">Jodi</button>
        </div>

        <!-- Single Tab Content -->
        <div id="single" class="tab-content active">
            @forelse($groupedResults as $date => $slotsData)
                <div class="date-section">
                    <p class="date-label">{{ \Carbon\Carbon::parse($date)->format('d F, Y') }}</p>
                    <div class="results-table-container">
                        <table class="results-table">
                            <tr>
                                @foreach($slots as $slot)
                                    @php
                                        $val = '-';
                                        if (isset($slotsData[$slot->id])) {
                                            $val = $slotsData[$slot->id][\App\Models\GameMode::TYPE_SINGLE] ?? '-';
                                        }
                                    @endphp
                                    <td>{{ $val }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($slots as $slot)
                                    @php
                                        $val = '-';
                                        if (isset($slotsData[$slot->id])) {
                                            $val = $slotsData[$slot->id][\App\Models\GameMode::TYPE_PATTI] ?? '-';
                                        }
                                    @endphp
                                    <td>{{ $val }}</td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                </div>
            @empty
                <div class="front-card" style="padding: 24px; text-align: center; background: #fff;">
                    <p class="text-muted">No results found for the last 30 days.</p>
                </div>
            @endforelse
        </div>

        <!-- Jodi Tab Content -->
        <div id="jodi" class="tab-content">
            @forelse($groupedResults as $date => $slotsData)
                <div class="date-section">
                    <p class="date-label">{{ \Carbon\Carbon::parse($date)->format('d F, Y') }}</p>
                    <div class="results-table-container">
                        <table class="results-table">
                            <tr>
                                @foreach($slots as $slot)
                                    @php
                                        $val = '-';
                                        if (isset($slotsData[$slot->id])) {
                                            $val = $slotsData[$slot->id][\App\Models\GameMode::TYPE_JODI] ?? '-';
                                        }
                                    @endphp
                                    <td>{{ $val }}</td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                </div>
            @empty
                <div class="front-card" style="padding: 24px; text-align: center; background: #fff;">
                    <p class="text-muted">No results found for the last 30 days.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('page_script')
<script>
    function switchTab(tabId, btn) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        // Remove active class from buttons
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        // Show selected content
        document.getElementById(tabId).classList.add('active');
        // Add active class to button
        btn.classList.add('active');
    }
</script>
@endpush
@endsection

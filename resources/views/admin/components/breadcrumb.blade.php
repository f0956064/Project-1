@if(isset($breadcrumb))
<ol class="breadcrumb m-0 mt-2 pt-3">
  @php ($breadcrumbLimit = count($breadcrumb))
  @php ($currentBreadcrumb = 1)
  @foreach($breadcrumb as $key => $val)
  <li class="breadcrumb-item">
    @if($currentBreadcrumb != $breadcrumbLimit)
    <a href="{{ $key }}">{{ $val }}</a>
    @else
    {{ $val }}
    @endif
    @php ($currentBreadcrumb++)
  </li>
  @endforeach
</ol>
@endif
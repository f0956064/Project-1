@php ($headerOption = [
  'title' => $module,
  'header_buttons' => [],
  'filters' => isset($filters) ? $filters : [],
  'noCardView' => true
])
@extends('admin.layouts.layout', $headerOption)


@section('content')
<form action="{{ route($routePrefix . '.assign', ['id' => $id]) }}" method="post">
  @csrf
  <div class="row mt-3">
    @include('admin.'. $routePrefix . '.manage-role-accordion')
  </div>
  <div class="row mt-3">
    <div class="col-lg-12">
      <button class="btn btn-danger btn-lg" type="submit">Assign Permission</button>
      <a href="{{ route('roles.index') }}" class="btn btn-dark btn-lg">Back</a>
    </div>
  </div>
</form>
@endsection
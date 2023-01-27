@extends(backpack_view('blank'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.list') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
  <div class="container-fluid">
    <h2>
      <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
      <small id="datatable_info_stack">{!! $crud->getSubheading() ?? '' !!}</small>
    </h2>
  </div>
@endsection

@section('content')
  <center>
    <div id="tools_panel" class="form-inline">

        <div class="form-group">

            <label for="inputSections" class="m-2">Раздел</label>
            <select id="inputSections" class="form-control m-2">
                <option selected>Раздел...</option>
                <option>...</option>
            </select>

            <label for="inputThemes" class="m-2">Тема</label>
            <select id="inputThemes" class="form-control m-2" style="max-width:400px">
                <option selected>Название темы</option>
                <option>...</option>
            </select>        

            <button id="btn_saveParagraphs"  class="btn btn-warning m-2" onclick="saveParagraphs()" disabled>
              Сохранить изменения.
            </button>
        </div> 
    </div>

    <div class="crudTable-container">
      <table  id="crudTable"
              class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2" cellspacing="0"
              style="max-width:800px">
        <!-- <thead>
          <tr>
            <th>Сорт</th>
            <th>Как на странице</th>
            <th>В редакторе</th>
          </tr>
        </thead> -->
        <tbody>
        </tbody>
      </table>
    </div>
</center>
@endsection

@section('after_styles')
  {{-- DATA TABLES --}}
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

  
  {{-- CRUD LIST CONTENT - crud_list_styles stack --}}
  @stack('crud_list_styles')
@endsection

@section('after_scripts')



@endsection
<script src="https://code.jquery.com/jquery-3.6.1.js" integrity="sha256-3zlB5s2uwoUzrXK3BT7AX3FyvojsraNFxCc2vC/7pNI=" crossorigin="anonymous"></script>


<script src="/admin_assets/ckeditor/ckeditor.js"></script>

<script src="/admin_assets/js/paragraphs-edit.js"></script>

<style>

</style>
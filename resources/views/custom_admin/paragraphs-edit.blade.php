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

            <label for="inputThmees" class="m-2">Тема</label>
            <select id="inputThmees" class="form-control m-2">
                <option selected>Название темы</option>
                <option>...</option>
            </select>        
      
        </div>
        
    </div>





      <table  id="crudTable"
              class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2" cellspacing="0"
              style="max-width:800px">
        <thead>
          <tr>
            <th>Сортировка</th>
            <th>Тема</th>
            <th>Доступ</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Тема</td>
            <td>Доступ</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Тема</td>
            <td>Доступ</td>
          </tr>
          <tr>
            <td>3</td>
            <td>Тема</td>
            <td>Доступ</td>
          </tr>
        </tbody>
      </table>
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

<script>
    const baseURL = document.location.protocol + "//" + document.location.host + "/api/admin/";
    
    let current_user = localStorage.getItem('current_user');
    let current_section = localStorage.getItem('current_section');

    dataBoot();

    function dataBoot() {
      //Загружаем данные и формируем таблицу
      $.ajax({
          url: baseURL+"get_data_for_user_extended",
          data: {
              current_user: current_user,
              current_section: current_section,
              token: "sdfsdfsdfsdfsdf"
          },       
          method: 'post',           
          success: function(data){ 
            if(data.status === "success"){
            
            }
          }
      });


</script>

<style>

</style>
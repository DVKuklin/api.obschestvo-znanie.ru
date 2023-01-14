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

            <label for="inputUsers" class="m-2">Пользователь</label>
            <select id="inputUsers" class="form-control m-2">
                <option selected>Пользователь...</option>
                <option>...</option>
            </select>

            <label for="inputSections" class="m-2">Раздел</label>
            <select id="inputSections" class="form-control m-2">
                <option selected>Название большого раздела</option>
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

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
          "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


    let current_user = localStorage.getItem('current_user');
    let current_section = localStorage.getItem('current_section');

    dataBoot();

    function dataBoot() {

      
      //Загружаем данные и формируем таблицу
      $.ajax({
          url: baseURL+"get_data_for_user_extended",
          headers: {'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')},
          data: {
              current_user: current_user,
              current_section: current_section
          },       
          method: 'post',           
          success: function(data){ 
              if(data.status === "success"){
                localStorage.setItem('current_user',data.current_user);
                localStorage.setItem('current_section',data.current_section);

                  //Заполняем select пользователи
                  let s = '';
                  let userActive = '';
                  data.users.forEach ((item,index,arr) => {

                      if (item.id == data.current_user) {
                          userActive = "selected";
                      } else {
                          userActive == "";
                      }
                      s += `<option ${userActive} value="${item.id}">${item.name}</option>`;

                      userActive = "";
                  });

                  inputUsers.innerHTML = s;
                  inputUsers.onchange = setCurrentUser;

                  //Заполняем select разделы
                  s = '';
                  let sectionActive = '';
                  data.sections.forEach ((item,index,arr) => {
                      if (item.id == data.current_section) {
                          sectionActive = "selected";
                      } else {
                          sectionActive = "";
                      }
                      s += `<option ${sectionActive} value="${item.id}">${item.name}</option>`;
                      sectionActive = "";
                  });

                  inputSections.innerHTML = s;
                  inputSections.onchange = setCurrentSection;

                  //Заполняем таблицу
                  s ='';
                  for (let i=0;i<data.themes.length;i++){

                      let permition = '';
                      if (data.themes[i].permition=='true') {
                          permition = 'checked';
                      }

                      s += `<tr>  
                              <td>${data.themes[i].sort}</td>
                              <td>${data.themes[i].theme}</td>
                              <td class="text-center">
                                <div class="custom-control custom-switch">
                                  <input type="checkbox" 
                                          ${permition} 
                                          class="custom-control-input" 
                                          id="customSwitch${i}"
                                          onclick = "setPermition(this,${data.themes[i].theme_id})">
                                  <label class="custom-control-label" for="customSwitch${i}"></label>
                                </div>
                              </td>
                            </tr>` 
                  }

                  let tbody = crudTable.querySelector('tbody');
                  tbody.innerHTML = s;
              } 
          },
          error: function (jqXHR, exception) {
            if (jqXHR.status === 0) {
              alert('Not connect. Verify Network.');
            } else if (jqXHR.status == 404) {
              alert('Requested page not found (404).');
            } else if (jqXHR.status == 500) {
              alert('Internal Server Error (500).');
            } else if (exception === 'parsererror') {
              alert('Requested JSON parse failed.');
            } else if (exception === 'timeout') {
              alert('Time out error.');
            } else if (exception === 'abort') {
              alert('Ajax request aborted.');
            } else {
              alert('Uncaught Error. ' + jqXHR.responseText);
            }
          }
      });
    }


    function setCurrentUser() {
      let user_id = inputUsers.options[inputUsers.options.selectedIndex].value;
      localStorage.setItem('current_user',user_id);
      location.reload();
    }

    function setCurrentSection() {
      let section_id = inputSections.options[inputSections.options.selectedIndex].value;
      localStorage.setItem('current_section',section_id);
      location.reload();
    }

    function setPermition(el,theme_id) {

      $.ajax({
        url: baseURL+"set_permition",
        data: {
            current_user: localStorage.getItem('current_user'),
            theme_id: theme_id,
            permition: el.checked,
            token: "sdfsdfsdfsdfsdf"
        },       
        method: 'post',           
        success: function(data){ 
            location.reload();
        }
      });
    }
</script>

<style>

</style>
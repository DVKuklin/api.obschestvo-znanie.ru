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
        </div> 
    </div>

    <div class="crudTable-container">
      <table  id="crudTable"
              class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2" cellspacing="0"
              style="max-width:800px">
        <thead>
          <tr>
            <th>Сорт</th>
            <th>Как на странице</th>
            <th>В редакторе</th>
          </tr>
        </thead>
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
<script src="https://cdn.ckeditor.com/ckeditor5/35.4.0/classic/ckeditor.js"></script>

<script src="/admin_assets/js/paragraphs-edit.js">
</script>

<script>
function getCookie(name) {
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

const baseURL = document.location.protocol + "//" + document.location.host + "/api/admin/";
    
let current_section = localStorage.getItem('paragraphs-edit-current_section');
let current_theme = localStorage.getItem('paragraphs-edit-current_theme');

dataBoot();

function dataBoot() {
  //Загружаем данные и формируем таблицу
    $.ajax({
        url: baseURL+"get_data_for_paragraphs_edit",
        headers: {'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')},
        data: {
            current_section: current_section,
            current_theme: current_theme,
        },       
        method: 'post',           
        success: function(data){ 

            //Заполняем select разделы
            let s = '';
            let sectionActive = '';
            data.sections.forEach ((item,index,arr) => {
                if (item.id == data.current_section) {
                    sectionActive = "selected";
                } else {
                    sectionActive = "";
                }
                s += `<option ${sectionActive} value="${item.id}">${item.name}</option>`;
                sectionActive = "";
            })
            inputSections.innerHTML = s;
            inputSections.onchange = setCurrentSection;

            //Заполняем select разделы
            s = '';
            themeActive = '';
            data.themes.forEach ((item,index,arr) => {
                if (item.id == data.current_theme) {
                    themeActive = "selected";
                } else {
                    themeActive = "";
                }
                s += `<option ${themeActive} value="${item.id}">${item.sort}. ${item.name}</option>`;
                themeActive = "";
            })
            inputThemes.innerHTML = s;
            inputThemes.onchange = setCurrentTheme;

            //Заполняем таблицу параграфов
            s = '';
            for (let i=0;i<data.paragraphs.length;i++){
                s += `<tr>  
                        <td>${data.paragraphs[i].sort}</td>
                        <td>${data.paragraphs[i].content}</td>
                        <td><div id="editor${i}">${data.paragraphs[i].content}</div></td>
                        <td class="td-with-buttons">
                          <div class="button-container">
                            <button  class="btn btn-primary" onclick="addParagraph(${data.paragraphs[i].sort},'above')"><nobr>Добавить сверху</nobr></button>
                            <div>
                              <button  class="btn btn-danger my-2" onclick="deleteParagraph(${data.paragraphs[i].id},${data.paragraphs[i].sort})">Удалить</button>
                              <button  class="btn btn-warning my-2">Применить</button>
                            </div>
                            <button  class="btn btn-primary" onclick="addParagraph(${data.paragraphs[i].sort},'below')"><nobr>Добавить снизу</nobr></button>
                          </div>
                        </td>
                      </tr>` 
            }

          let tbody = crudTable.querySelector('tbody');
          tbody.innerHTML = s;
          
          //Подключаем editors
          for (let i=0;i<data.paragraphs.length;i++){
            ClassicEditor
                .create( document.querySelector( `#editor${i}` ) )
                .catch( error => {
                    console.error( error );
                } );
          }
        },
      error: function (jqXHR, exception) {
        console.log('Ошибка интернета.')
      }
    });
}

function setCurrentSection() {
    let section_id = inputSections.options[inputSections.options.selectedIndex].value;
    localStorage.setItem('paragraphs-edit-current_section',section_id);
    location.reload();
}

function setCurrentTheme() {
    let theme_id = inputThemes.options[inputThemes.options.selectedIndex].value;
    localStorage.setItem('paragraphs-edit-current_theme',theme_id);
    location.reload();
}

function addParagraph(sort,position) {
  $.ajax({
        url: baseURL+"add_paragraph",
        headers: {'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')},
        data: {
            theme: current_theme,
            // theme: 453,
            sort: sort,
            position: position,
        },       
        method: 'post',           
        success: function(data){ 
          if (data.status == "success") {
            location.reload();
          }
          console.log(data);
        },
      error: function (jqXHR, exception) {
        console.log('Ошибка интернета.')
      }
  });
}

function deleteParagraph(id,sort) {
  let confirmation = confirm("Вы действительно хотите удалить параграф "+sort);
  if (!confirmation) return;
  $.ajax({
        url: baseURL+"delete_paragraph",
        headers: {'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')},
        data: {
            paragraph_id: id,
        },       
        method: 'post',           
        success: function(data){ 
          if (data.status == "success") {
            location.reload();
          } else {
            console.log(data);
          }
        },
      error: function (jqXHR, exception) {
        console.log('Ошибка интернета.')
      }
  });
}
</script>

<style>

</style>
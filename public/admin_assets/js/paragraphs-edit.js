let btnDelete = 
`<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
  <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
</svg>`;
let btnAdd = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
</svg>`;
function getCookie(name) {
  let matches = document.cookie.match(new RegExp(
    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
  ));
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

const baseURL = document.location.protocol + "//" + document.location.host + "/api/admin/";

let current_section = localStorage.getItem('paragraphs-edit-current_section');
let current_theme = localStorage.getItem('paragraphs-edit-current_theme');

let editors = [];//Здесь будут эдиторы
let isDataChanged = false;//Если данные были изменены, то будет true

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
            localStorage.setItem('paragraphs-edit-current_theme',data.current_theme);
            current_theme = data.current_theme;
            
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
                        <td>${data.paragraphs[i].sort}</td>`+
                        // <td class = "how-on-page">${data.paragraphs[i].content}</td>
                        `<td class = "in-editor"><div id="editor${i}">${data.paragraphs[i].content}</div></td>
                        <td class="td-with-buttons">
                          <div class="button-container">
                            <button  class="btn btn-primary my-1" 
                                     onclick="addParagraph(${data.paragraphs[i].sort},'above')" title="Добавить сверху">
                              ${btnAdd}
                            </button>
                            <button  class="btn btn-danger my-1" 
                                       onclick="deleteParagraph(${data.paragraphs[i].id},${data.paragraphs[i].sort})" title="Удалить">
                                ${btnDelete}
                            </button>`;
                if (i==data.paragraphs.length-1) {
                  s+=`      <button  class="btn btn-primary my-1" 
                                     onclick="addParagraph(${data.paragraphs[i].sort},'below')" title="Добавить снизу">
                              ${btnAdd}
                            </button>`;
                } else {
                  s+='       <div></div>';
                }

                  s+=`     </div>
                        </td>
                      </tr>` 
            }

          let tbody = crudTable.querySelector('tbody');
          tbody.innerHTML = s;
          
          // ❗️⚠️❌🔸🔹🔶🔷➖➕☑️✔️✅✖️📌⭐️🌟⚡️🔅🔆❌⭕️➡️🔺🔻🚩▶️⬇️● ○🔥💥😄

          let styles = [
            {
              name: 'Параграф с левой рамкой',
              element: 'p',
              classes: ['paragraph-with-left-border']
            }
          ];
          for (let i=1;i<33;i++) {
            styles.push({
              name: 'Маркер '+i,
              element: 'p',
              classes: [ 'li_emoji_'+i ]
            })
          }



          //Подключаем editors
          for (let i=0;i<data.paragraphs.length;i++){
            ClassicEditor
              .create( document.querySelector( `#editor${i}` ), {
                style: {
                  definitions: styles
                }
              } )
              .then( editor => {
                // console.log( editor );

                let ob = {
                  editor: editor,
                  paragraph_id: data.paragraphs[i].id
                }
                editors[i] = ob;

                editor.model.document.on( 'change:data', () => {
                  isDataChanged = true;
                  btn_saveParagraphs.removeAttribute('disabled');
                } );
              } )
              .catch( error => {
                  console.error( error );
              } );
          }
        },
      error: function (jqXHR, exception) {
        console.log('Ошибка интернета');

      }
    });
}

function saveParagraphs() {
  if (!confirm("Поддтвердите сохранение изменений.")) return;

  let paragraphs = [];

  for (let i=0;i<editors.length;i++) {
    let ob = {
      content: editors[i].editor.getData(),
      id: editors[i].paragraph_id
    }
    paragraphs[i] = ob;
  }

  $.ajax({
        url: baseURL+"save_paragraphs",
        headers: {'X-XSRF-TOKEN': getCookie('XSRF-TOKEN')},
        data: {
            paragraphs: paragraphs
        },       
        method: 'post',           
        success: function(data){ 
          if (data.status == "success") {
            btn_saveParagraphs.setAttribute('disabled','disabled');
            isDataChanged = false;
            alert('Все изменения успешно сохранены.');
          } else {
            alert('Что то пошло не так, изменения не сохранены.');
          }
          // console.log(data);
        },
      error: function (jqXHR, exception) {
        console.log('Ошибка интернета.')
      }
  });
}

function setCurrentSection() {
    if (isDataChanged) {
      let message = "У Вас есть несохраненные изменения. При смене раздела они будут потеряны. Если все равно хотите продолжить, нажмите ОК.";
      if (!confirm(message)) return;
    } 
    let section_id = inputSections.options[inputSections.options.selectedIndex].value;
    localStorage.setItem('paragraphs-edit-current_section',section_id);
    location.reload();
}

function setCurrentTheme() {
    if (isDataChanged) {
      let message = "У Вас есть несохраненные изменения. При смене темы они будут потеряны. Если все равно хотите продолжить, нажмите ОК.";
      if (!confirm(message)) return;
    } 
    let theme_id = inputThemes.options[inputThemes.options.selectedIndex].value;
    localStorage.setItem('paragraphs-edit-current_theme',theme_id);
    location.reload();
}

function addParagraph(sort,position) {
  if (sort==null || editors.length == 1) {
    sort = 1;
  } 

  let b = true;//Можно или нельзя добавлять параграф
  if (isDataChanged) {
    let message = "У Вас есть несохраненные изменения. При добавлении параграфа они будут потеряны. Если все равно хотите продолжить, нажмите ОК.";
    if (!confirm(message)) b=false;
  } 
  
  if (b) {
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
}

function deleteParagraph(id,sort) {
  if (isDataChanged) {
    let message = "У Вас есть несохраненные изменения. При удалении параграфа они будут потеряны. Если все равно хотите продолжить, нажмите ОК.";
    if (!confirm(message)) return;
  } 

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
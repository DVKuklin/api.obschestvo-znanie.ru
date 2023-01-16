const baseURL = document.location.protocol + "//" + document.location.host + "/api/admin/";

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
      "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}


let current_user = localStorage.getItem('current_user');
let current_section = localStorage.getItem('current_section');
let themes_on_page = [];

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
            for (let i=0;i<data.themes.length;i++) {
              themes_on_page[i] = data.themes[i].theme_id;
            }

              //Заполняем select пользователи
              let s = '';
              let userActive = '';
              data.users.forEach ((item,index,arr) => {

                  if (item.id == data.current_user) {
                      userActive = "selected";
                  } else {
                      userActive == "";
                  }
                  s += `<option ${userActive} value="${item.id}">${item.name} - ${item.email}</option>`;

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
              let permitions = '';

              for (let i=0;i<data.themes.length;i++) {
                if (data.themes[i].permition == 'true') {
                  permitions = 'checked';
                } else {
                  permitions = '';
                  break;
                }
              }

              s = ` <tr>
                      <td colspan="2"></td>
                      <td class="text-center">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" 
                                      ${permitions}
                                      class="custom-control-input"
                                      id="customSwitchAllTop"
                                      onclick="setPermitions(this)">
                          <label class="custom-control-label" for="customSwitchAllTop"></label>
                        </div>
                      </td>
                    </tr>`;

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

              s+= ` <tr>
                      <td colspan="2"></td>
                      <td class="text-center">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" 
                                      ${permitions}
                                      class="custom-control-input"
                                      id="customSwitchAllBottom"
                                      onclick="setPermitions(this)">
                          <label class="custom-control-label" for="customSwitchAllBottom"></label>
                        </div>
                      </td>
                    </tr>`;
              let tbody = crudTable.querySelector('tbody');
              tbody.innerHTML = s;
          } 
      },
      error: function (jqXHR, exception) {
        console.log('Ошибка интернета.')
      }
  });
}

function setPermitions(el) {
  $.ajax({
    url: baseURL+"set_permitions",
    data: {
        current_user: localStorage.getItem('current_user'),
        themes: themes_on_page,
        permition: el.checked,
    },       
    method: 'post',           
    success: function(data){ 
        console.log(data);
        location.reload();
    },
    error: function (jqXHR, exception) {
      console.log('Ошибка интернета.')
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
    },       
    method: 'post',           
    success: function(data){ 
        location.reload();
    }
  });
}

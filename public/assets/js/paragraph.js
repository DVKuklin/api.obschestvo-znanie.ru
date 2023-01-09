window.addEventListener('load',function() {
    let observer = new MutationObserver(afterObserve);

    observer.observe(crudTable, {
        childList: true, // наблюдать за непосредственными детьми
        subtree: true, // и более глубокими потомками
      });
});

function afterObserve() {
    // alert('Закгрузка таблицы завершена');
    let els = crudTable.querySelectorAll('.dtr-control span');
    let strs = [];
    for (let i=0;i<els.length;i++) {

        strs[i] = els[i].innerText;
    }

    this.disconnect();
    changeData(els,strs);
//     this.observe(crudTable, {
//         childList: true, // наблюдать за непосредственными детьми
//         subtree: true, // и более глубокими потомками
//     });
}

function changeData(els,strs) {
    for (let i=0;i<els.length;i++) {
        els[i].innerHTML = strs[i];
    }

    let tds = document.querySelectorAll('table.dataTable.nowrap td');
    for (let i=0;i<tds.length;i++) {
        tds[i].style.whiteSpace = 'normal';
    }
}

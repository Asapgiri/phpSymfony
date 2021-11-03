pubs = document.getElementById('left-pubs');
requestAjax('/api/get3pubs', pubs);

forums = document.getElementById('left-forums');
requestAjax('/api/get3forums', forums);



function requestAjax(path, element) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText)
            var arr = JSON.parse(this.responseText);
            arr.forEach((x) => {
                a = document.createElement('a')
                a.classList.add('list-group-item list-group-item-action p-2')
                a.href = x.href
                a.innerText = x.text
                element.appendChild(a)
            })
        }
    }
    xmlHttp.open("GET", path, true);
    xmlHttp.send();
}
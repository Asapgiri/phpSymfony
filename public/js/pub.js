function watchPubInfo(id) {
    var pub = document.getElementById('pubmessage-'+id)
    if (pub.classList.contains('already-in-load')) return
    pub.classList.add('already-in-load')

    pub.innerText = "Betöltés..."

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            pub.innerHTML =
`<img class="col-md-3" src="${resp.image}"/>
<div class="col-md-9">
    ${resp.text}
</div>`;
        }
    }
    xmlHttp.open("GET", '/api/pub/'+id, true);
    xmlHttp.send();
}
function watchMessage(id) {
    var ad = document.getElementById('admessage-'+id)
    if (ad.classList.contains('already-in-load')) return
    ad.classList.add('already-in-load')

    var seen = document.getElementById('collapse-'+id+'-seen')
    seen.innerText = "seen"
    try {
        seen.classList.remove('badge-danger')
        seen.classList.add('badge-success')
    }
    catch (e) {}

    ad.innerText = "Betöltés..."

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);
            billing = response.billing
            //console.log(billing)
            ad.innerHTML = `<table>
<tr><td><b><u>Hirdetés azonosító</u></b>:</td><td>${response.azon}</td></tr>
<tr><td><b><u>Város</u></b>:</td><td>${billing.city}</td></tr>
<tr><td class="table-width-5"><b><u>Utca/Házszám</u></b>:</td><td>${billing.address}</td></tr>
<tr><td><b><u>Adószám</u></b>:</td><td>${billing.taxnum ? billing.taxnum : '-'}</td></tr>
<tr class="table-message"><td><b><u>Üzenet</u></b>:</td><td>${response.message}</td></tr>
</table>`;
            colli = document.getElementById('colli-'+id)
            if (colli.classList.contains('bg-warning')) colli.classList.remove('bg-warning')
            if (response.adsCnt) document.getElementById('adsCnt').innerText = response.adsCnt
            else {
                adscnt = document.getElementById('adsCnt')
                if (adscnt) adscnt.remove()
            }

        }
    }
    xmlHttp.open("GET", '/api/hirdetes/'+id, true);
    xmlHttp.send();
}

function removeParentalClicking(className) {
    var elements = document.getElementsByClassName(className)
    for (let i = 0; i < elements.length; i++) {
        elements[i].stopPropagation();
    }
}
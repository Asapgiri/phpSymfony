function watchUserInfo(id) {
    var user = document.getElementById('usermessage-'+id)
    if (user.classList.contains('already-in-load')) return
    user.classList.add('already-in-load')

    user.innerText = "Betöltés..."

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            user.innerHTML =
                `<div class="col-md-4">
                <img class="img-hover" src="${resp.image}" />
            </div>
            <div class="col-md-8">
                <h2 class="mb-2">
                    ${(resp.lname != null || resp.fname != null) ? resp.lname + ' ' + resp.fname : ''}
                </h2>
                <table>
                        <tr class="mb-2">
                            <td class="min-width-2">Email:</td>
                            <td class="text-muted">${resp.email}${(!resp.email_visible) ? ' <small>(nem látható)</small>' : ''}</td>
                        </tr>
                        <tr class="mb-2">
                            <td class="min-width-2">Telefon:</td>
                            <td class="text-muted">${resp.tel}${(resp.tel) ? ' <small>(nem látható)</small>' : ''}</td>
                        </tr>
                        <tr class="border-bottom border-gray">
                            <td colspan="2">Leírás:</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                ${(resp.description) ? resp.description.replaceAll('\n', '<br>') : ''}
                            </td>
                        </tr>
                </table>
            </div>`;
        }
    }
    xmlHttp.open("GET", '/api/user/'+id, true);
    xmlHttp.send();
}
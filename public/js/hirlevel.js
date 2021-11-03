function hirdetesFeliratkoz(email) {
    if (!email) {
        if (!document.getElementById('hirdet-email').value) return;
        email = document.getElementById('hirdet-email').value
    }
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            flashbag = document.getElementById('flashbag')
            children = document.createElement('div')
            children.id = makeid(5)
            children.className = 'alert alert-warning alert-dismissible fade show p-2'
            children.setAttribute('role', 'alert')

            children.innerHTML = `${this.responseText}<button type="button" class="close p-2" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>`
            flashbag.appendChild(children)
            if (document.getElementById('ffbutton')) {
                document.getElementById('ffbutton').setAttribute('onclick', 'unsubscribe();')
                document.getElementById('ffbutton').innerText = "Leiratkozás hírlevélről"
            }
            setTimeout(function (id) {
                $('#'+id).alert('close');
            }, 5000, children.id)
        }
    }
    xmlHttp.open("POST", '/api/feliratkozas', true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send('email=' + email);

    $('#hirlevel .collapse').collapse('hide')
}

function dontShowAgain() {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", '/api/hidesub', true);
    xmlHttp.send();

    $('#hirlevel .collapse').collapse('hide')
}

function makeid(length) {
    var result           = [];
    var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    var charactersLength = characters.length;
    for ( var i = 0; i < length; i++ ) {
        result.push(characters.charAt(Math.floor(Math.random() *
            charactersLength)));
    }
    return result.join('');
}
function delMsg(id) {
    document.getElementById('msg-'+id).remove();
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Some response if error...
        }
    }
    xmlHttp.open("GET", '/api/msg/del/'+id, true);
    xmlHttp.send();
}

function refreshMessages(fid) {
    //try {
    messages = document.getElementsByClassName('message')
    if (messages[0] != undefined)
        lastId = messages[messages.length-1].id.split('-')[1]
    else lastId = 0;

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText);
            if (resp.lastId > lastId) getNewMessages(fid, resp.lastId - lastId)

            // Will be modified...
            //if (messages.length > resp.cnt) deleteDeletedMessages(messages, resp.msgIds)

        }
    }
    xmlHttp.open("GET", '/api/msg/rfs/'+fid, true);
    xmlHttp.send();
    //}
    //catch (e) { console.log('message load error...') }
}

function getNewMessages(fid, db) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var resp = JSON.parse(this.responseText)
            container = document.getElementById('msg-container')
            for (let i = 0; i < resp.msgs.length; i++) {
                msg = resp.msgs[i]
                child = document.createElement('div')
                child.classList.add('my-2')
                child.classList.add('p-2')
                child.classList.add('bg-white')
                child.classList.add('rounded')
                child.classList.add('box-shadow')
                child.classList.add('message')
                child.id = 'msg-'+msg.id
                child.innerHTML = `
                <p class="media-body pb-1 mb-0 lh-125 border-bottom border-gray">
                    <a href="/user/${msg.author}">@${msg.author}</a>:${msg.editable ? '<a class="float-right small" href="#" onClick="delMsg(' + msg.id + ')">Törlés</a>' : ''}
                </p>
                <p class="pt-1 pb-2 m-0">${msg.text.replaceAll('\n', '<br>')}</p>
                    <small class="d-block text-right">${msg.date}</small>`
                container.appendChild(child)
            }
        }
    }
    xmlHttp.open("GET", '/api/msg/get/'+fid+'/'+db, true);
    xmlHttp.send();
}

function deleteDeletedMessages(messages, msgIds) {
    Array.from(messages).forEach(msg => (!msgIds.includes(msg.id.split('-')[1]) ? msg.remove() : ''))
}

function msgSend(fid) {
    textarea = document.getElementById('form_msg_text')
    msgToSend = 'msg='+textarea.value
    textarea.value = ''

    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var msg = JSON.parse(this.responseText)
            container = document.getElementById('msg-container')
                child = document.createElement('div')
                child.classList.add('my-2')
                child.classList.add('p-2')
                child.classList.add('bg-white')
                child.classList.add('rounded')
                child.classList.add('box-shadow')
                child.classList.add('message')
                child.id = 'msg-'+msg.id
                child.innerHTML = `
                <p class="media-body pb-1 mb-0 lh-125 border-bottom border-gray">
                    <a href="/user/${msg.author}">@${msg.author}</a>:<a class="float-right small" href="#" onclick="delMsg(${msg.id})">Törlés</a>
                </p>
                <p class="pt-1 pb-2 m-0">${msg.text.replaceAll('\n', '<br>')}</p>
                    <small class="d-block text-right">${msg.date}</small>`
                container.appendChild(child)
        }
    }
    xmlHttp.open("POST", '/api/msg/add/'+fid, true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.send(msgToSend);
}
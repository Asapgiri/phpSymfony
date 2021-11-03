function getUploadedFiles() {
    files = document.getElementsByClassName('dz-filename');
    zipped = document.getElementById('form_ad_zip');
    zipped.value = "";

    for (let i = 0; i < files.length; i++) {
        zipped.value += files[i].innerText+';';
    }
}

function onlyNumbers(x){
    ki="";
    if (x >='0' && x <='9'){
        ki = x;
    }
    return ki;
}

function adszCheck(evt, adsz){
    // console.log(evt)
    if (evt.key != "Backspace") {
        ki = "";
        for (i = 0; i < adsz.length; i++) {
            if (i != 8 && i != 10) {
                ki += onlyNumbers(adsz.substr(i, 1));
            } else {
                ki += "-";
            }
        }
        if (ki.length == 8 || ki.length == 10) {
            ki += "-";
        }
        document.getElementById("form_tax_number").value = ki;
    }
}

function postCodeCheck(evt, postCode){
    // console.log(evt)
    if (evt.key != "Backspace") {
        ki = "";
        for (i = 0; i < postCode.length; i++) {
            ki += onlyNumbers(postCode.substr(i, 1));
        }
        document.getElementById("form_postcode").value = ki;
        if (postCode.length == 4) citySearch(postCode)

    }
}

function citySearch(postCode) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var response = JSON.parse(this.responseText);

            let i = 0;
            while (i < response.length && response[i].IRSZ != postCode) i++

            if (i < response.length) {
                document.getElementById('form_city').value = response[i].City
                document.getElementById('form_city_help').innerText = response[i].Ker
            }
        }
    }
    xmlHttp.open("GET", '/docs/irsz.json', true);
    xmlHttp.send();
}

function addTaxFields() {
    szamlazas = document.getElementById('tax-selector');
    gomb = document.getElementById('tax-button').getElementsByTagName('button')[0];
    gomb.innerText = "Számlázási cím választása";
    gomb.setAttribute('onclick', 'removeTaxFields();');
    document.getElementById('form_need_billdata').value = "true"

    szamlazas.outerHTML = `<div class="tax-selectors form-group row"><label class="col-form-label col-sm-3" for="form_tax_number">Adószám</label><div class="col-sm-9"><input type="text" id="form_tax_number" name="form[tax_number]" minlength="13" maxlength="13" style="width: 250px;" onkeyup="adszCheck(event, this.value)" placeholder="________-_-__" class="form-control"></div>
    </div><div class="tax-selectors form-group row"><label class="col-form-label col-sm-3 required" for="form_postcode">Irányítószám</label><div class="col-sm-9"><input type="text" id="form_postcode" name="form[postcode]" required="required" minlength="4" maxlength="4" style="width: 100px;" onkeyup="postCodeCheck(event, this.value)" placeholder="____" class="form-control"></div>
    </div><div class="tax-selectors form-group row"><label class="col-form-label col-sm-3 required" for="form_city">Város / Helység</label><div class="col-sm-9"><input type="text" id="form_city" name="form[city]" required="required" style="max-width: 500px;" class="form-control"><small id="form_city_help" class="form-text text-muted">---</small></div>
    </div><div class="tax-selectors form-group row"><label class="col-form-label col-sm-3 required" for="form_address">Utca, házszám</label><div class="col-sm-9"><input type="text" id="form_address" name="form[address]" required="required" class="form-control"></div>
    </div>`
}

function removeTaxFields() {
    gomb = document.getElementById('tax-button').getElementsByTagName('button')[0];
    gomb.innerText = "Új számlázási cím hozzáadása";
    gomb.setAttribute('onclick', 'addTaxFields();');
    document.getElementById('form_need_billdata').value = ""

    taxfields = document.getElementsByClassName('tax-selectors');

    for (let i = 0; taxfields.length - 1; i++) {
        taxfields[0].remove();
    }
    taxfields[0].outerHTML = selector
}
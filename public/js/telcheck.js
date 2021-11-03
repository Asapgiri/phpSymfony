function telCheck(evt, telnum) {
    if (evt.key != "Backspace") {
        ki = "";
        if (telnum.substr(0, 2) == "06") {
            telnum = telnum.replace('06', '+36');
        }
        if (telnum.length == 12 || telnum.length == 15) {
            //console.log('Was 12...')
            telnum = telnum.replaceAll(' ', '');
            if (telnum.substr(3, 1) == '1') {
                telnum = telnum.substr(0, 3) + ' ' + telnum.substr(3, 1) + ' ' + telnum.substr(4, 3) + ' ' + telnum.substr(7)
            }
            else {
                telnum = telnum.substr(0, 3) + ' ' + telnum.substr(3, 2) + ' ' + telnum.substr(5, 3) + ' ' + telnum.substr(8)
            }
        }
        else {
            for (i = 0; i < telnum.length; i++) {
                /*if (telnum.substr(0, 2) == "06") {
                    if (i != 0 && i < 2) {
                        ki += onlyNumbers(telnum.substr(i, 1));
                    } else if (i == 0) {
                        ki = telnum.substr(0, 1);
                    } else if (i > 2 && i < 14) {
                        if (telnum.substr(4, 1) == 1) {
                            if (i != 4 && i != 8) {
                                if (i > 11) {
                                    document.getElementById("form_telephone").value = ki;
                                    return;
                                }
                                ki += onlyNumbers(telnum.substr(i, 1));
                            } else {
                                ki += " " + onlyNumbers(telnum.substr(i, 1));
                            }
                        } else {
                            if (i != 5 && i != 9) {
                                ki += onlyNumbers(telnum.substr(i, 1));
                            } else {
                                ki += " " + onlyNumbers(telnum.substr(i, 1));
                            }
                        }
                        if (i == 13) ki += " "
                    } else if (i < 14) {
                        ki += " " + onlyNumbers(telnum.substr(i, 1));
                    }
                }
                else {*/

                if (i != 0 && i < 3) {
                    ki += onlyNumbers(telnum.substr(i, 1));
                } else if (i == 0) {
                    if (telnum.substr(0, 1) != "+"/* && telnum.substr(0, 1) != "0"*/) telnum = "+" + telnum
                    ki = telnum.substr(0, 1);
                } else if (i > 3) {
                    if (telnum.substr(4, 1) == 1) {
                        if (i != 5 && i != 9) {
                            if (i > 12) {
                                document.getElementById("form_telephone").value = ki;
                                return;
                            }
                            ki += onlyNumbers(telnum.substr(i, 1));
                        } else {
                            ki += " " + onlyNumbers(telnum.substr(i, 1));
                        }
                    } else {
                        if (i != 6 && i != 10) {
                            ki += onlyNumbers(telnum.substr(i, 1));
                        } else {
                            ki += " " + onlyNumbers(telnum.substr(i, 1));
                        }
                    }
                } else {
                    ki += " " + onlyNumbers(telnum.substr(i, 1));
                }
                //}
            }
        }

        if (ki.length > 15) ki = ki.substr(0, 15);
        document.getElementById("form_telephone").value = ki;
    }
}

function onlyNumbers(x){
    ki="";
    if (x >='0' && x <='9'){
        ki = x;
    }
    return ki;
}
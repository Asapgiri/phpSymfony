function searchFunction() {
    // Declare variables
    var input, filter, table, li, divs, i, j, txtValue, found;
    input = document.getElementById("searchbar");
    filter = input.value.toUpperCase();
    table = document.getElementById("table");
    li = table.getElementsByClassName("searchable");

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        row = li[i].getElementsByClassName("row")[0];
        divs = row.getElementsByTagName("div");
        found = false;
        for (j = 0; j < divs.length; j++) {
            if (divs[j]) {
                txtValue = divs[j].textContent || divs[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true
                }
            }
        }
        if (found) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

function searchWithoutRow() {
    // Declare variables
    var input, filter, table, li, divs, i, j, txtValue, found;
    input = document.getElementById("searchbar");
    filter = input.value.toUpperCase();
    table = document.getElementById("table");
    li = table.getElementsByClassName("searchable");

    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        txtValue = li[i].innerHTML || li[i].innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
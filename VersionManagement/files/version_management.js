/**
 * Created by schwarz on 16.06.2016.
 */

var newVersionCounter = 0;

function add_version_row() {
    var table_id = "version_view";
    var table = document.getElementById(table_id);
    var rows = table.getElementsByTagName("tr").length;
    var tr = table.insertRow(rows);
    var td1 = document.createElement("td");
    var td2 = document.createElement("td");
    var td3 = document.createElement("td");
    var td4 = document.createElement("td");
    var td5 = document.createElement("td");
    var td6 = document.createElement("td");
    /** version */
    td1.innerHTML = '<input type="text" id="proj-version-new-version" name="version_name[]" style="width:100%;" maxlength="64" value="" />';
    /** released */
    /* todo add input */
    td2.innerHTML = '<input type="checkbox" name="newVersionReleased' + newVersionCounter + '">';
    /** obsolete */
    /* todo add input */
    td3.innerHTML = '<input type="checkbox" name="newVersionObsolete' + newVersionCounter + '">';
    /** date */
    td4.innerHTML = '<input type="text" id="proj-version-date-order" name="version_date_order[]" size="15" value="" />';
    /** description */
    td5.innerHTML = '<input type="text" id="proj-version-description" name="version_description[]" value="" />';
    /** document type */
    td6.innerHTML = '';

    tr.className += "new_row";
    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(td5);
    tr.appendChild(td6);

    newVersionCounter++;
}

function del_version_row(initial_row_count) {
    var table_id = "version_view";
    var table = document.getElementById(table_id);
    var rows = table.getElementsByTagName("tr").length;

    if (rows > ( initial_row_count + 1 )) {
        document.getElementById(table_id).deleteRow(--rows);
    }
}
/**
 * Created by schwarz on 16.06.2016.
 */
function add_version_row ()
{
    var table_id = "version_view";
    var table = document.getElementById ( table_id );
    var rows = table.getElementsByTagName ( "tr" ).length;
    var tr = table.insertRow ( rows );
    var td1 = document.createElement ( "td" );
    var td2 = document.createElement ( "td" );
    var td3 = document.createElement ( "td" );
    var td4 = document.createElement ( "td" );
    var td5 = document.createElement ( "td" );
    /** version */
    td1.innerHTML = '<input type="text" id="proj-version-new-version" name="version_name[]" style="width:100%;" maxlength="64" value="" />';
    /** released */
    td2.innerHTML = '';
    /** obsolete */
    td3.innerHTML = '';
    /** date */
    td4.innerHTML = '<input type="text" id="proj-version-date-order" name="version_date_order[]" size="15" value="" />';
    /** description */
    td5.innerHTML = '<input type="text" id="proj-version-description" name="version_description[]" value="" />';

    tr.className += "new_row";
    tr.appendChild ( td1 );
    tr.appendChild ( td2 );
    tr.appendChild ( td3 );
    tr.appendChild ( td4 );
    tr.appendChild ( td5 );
}

function del_version_row ( initial_row_count )
{
    var table_id = "version_view";
    var table = document.getElementById ( table_id );
    var rows = table.getElementsByTagName ( "tr" ).length;

    if ( rows > ( initial_row_count + 1 ) )
    {
        document.getElementById ( table_id ).deleteRow ( --rows );
    }
}
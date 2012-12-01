<?php
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
//phpinfo();
var_dump( $_POST, $_FILES );
?>

<form enctype="multipart/form-data" action="/parser/test.php" method="post" id="form">
    <table class="form">
        <tr>
            <td><input type="file" name="excel_file" id="excel_file" /></td>
        </tr>
        <tr>
            <td>
                <select name="excel_file" id="excel_type">
                    <option value="1">Тип 1</option>
                    <option value="2">Тип 2</option>
                    <option value="3">Тип 3</option>
                </select>
                <input type="submit" value="go">
            </td>
        </tr>
    </table>
</form>
<?php

if ($_POST['save']) {
    $sql = "INSERT INTO daybook SET
        `date`                      = ".dbesc($_POST['date']).",
        `opening_coordinator`       = ".dbesc($_POST['opening_coordinator']).",
        `opening_comments`          = ".dbesc($_POST['opening_comments']).",
        `opening_5000`              = ".dbesc($_POST['opening_5000']).",
        `opening_2000`              = ".dbesc($_POST['opening_2000']).",
        `opening_1000`              = ".dbesc($_POST['opening_1000']).",
        `opening_0500`              = ".dbesc($_POST['opening_0500']).",
        `opening_0200`              = ".dbesc($_POST['opening_0200']).",
        `opening_0100`              = ".dbesc($_POST['opening_0100']).",
        `opening_0050`              = ".dbesc($_POST['opening_0050']).",
        `opening_0020`              = ".dbesc($_POST['opening_0020']).",
        `opening_0010`              = ".dbesc($_POST['opening_0010']).",
        `opening_0005`              = ".dbesc($_POST['opening_bagsandrolls_0005']).",
        `opening_bagsandrolls_0200` = ".dbesc($_POST['opening_bagsandrolls_0200']).",
        `opening_bagsandrolls_0100` = ".dbesc($_POST['opening_bagsandrolls_0100']).",
        `opening_bagsandrolls_0050` = ".dbesc($_POST['opening_bagsandrolls_0050']).",
        `opening_bagsandrolls_0020` = ".dbesc($_POST['opening_bagsandrolls_0020']).",
        `opening_bagsandrolls_0010` = ".dbesc($_POST['opening_bagsandrolls_0010']).",
        `opening_bagsandrolls_0005` = ".dbesc($_POST['opening_bagsandrolls_0005']);
    $res = mysql_query($sql);
    website_log("Coordinator ".$_POST['opening_coordinator']." entered the opening float into the daybook.");
    if (mysql_error()) $Page['error_message'] .= "<p>".mysql_error().".  This probably means that
    someone's already done the daybook this morning.</p>";
    else $Page['body'] .= "<p>The float has been entered into the daybook.  Thankyou.</p>";



} else {
    
    // Build select list for coordies.
    $res1 = mysql_query('SELECT morning_shift FROM `co-ordinators_roster` WHERE date=CURDATE()');
    if (mysql_num_rows($res1)>0) {
        $curr_coordie = mysql_fetch_assoc($res1);
        $curr_coordie = $curr_coordie['morning_shift'];
    } else {
        $curr_coordie = NULL;
    }
    $res = mysql_query("SELECT * FROM people WHERE coordinator=1 ORDER BY first_name");
    $coordies_options = "<option value=''></option>";
    while ($coordie = mysql_fetch_assoc($res)) {
        if ($coordie['id'] == $curr_coordie) $sel = 'selected '; else $sel = '';
        $coordies_options .= "<option value='".$coordie['id']."' $sel>
            ".$coordie['first_name']." ".$coordie['surname']."</option>";
    }
    
    
    $sql = "SELECT (float_5000 + float_2000 + closing_1000 + closing_0500 + closing_0200 +
        closing_0100 + closing_0050 + closing_0020 + closing_0010 + closing_0005 + closing_bagsandrolls_0200 +
        closing_bagsandrolls_0100 + closing_bagsandrolls_0050 + closing_bagsandrolls_0020 +
        closing_bagsandrolls_0010 + closing_bagsandrolls_0005) AS total FROM daybook ORDER BY date DESC LIMIT 1";
    $res = mysql_query($sql);
    $r = mysql_fetch_assoc($res);
    if ($res) $yesterday_total = $r['total']; else $yesterday_total = 0.00;
    
    $Page['script'] .= "function calc(numerator, denominator, destination) {
        document.getElementById(destination).value = (numerator * denominator).toFixed(2);
        sumAll();
        document.getElementById('diff').value = document.getElementById('total').value - $yesterday_total;
        document.getElementById('total').value = '$' + document.getElementById('total').value;
    }
    function sumAll() {
        document.getElementById('total').value = (
            (document.getElementById('5000_total').value * 1) +
            (document.getElementById('2000_total').value * 1) +
            (document.getElementById('1000_total').value * 1) +
            (document.getElementById('0500_total').value * 1) +
            (document.getElementById('0200_total').value * 1) +
            (document.getElementById('0100_total').value * 1) +
            (document.getElementById('0050_total').value * 1) +
            (document.getElementById('0020_total').value * 1) +
            (document.getElementById('0010_total').value * 1) +
            (document.getElementById('0005_total').value * 1) +
            (document.getElementById('opening_bagsandrolls_0200').value * 1) +
            (document.getElementById('opening_bagsandrolls_0100').value * 1) +
            (document.getElementById('opening_bagsandrolls_0050').value * 1) +
            (document.getElementById('opening_bagsandrolls_0020').value * 1) +
            (document.getElementById('opening_bagsandrolls_0010').value * 1) +
            (document.getElementById('opening_bagsandrolls_0005').value * 1)
        ).toFixed(2);
    }
    function init() {}";
    
    $Page['style'] .= "input, select, textarea {width:100%; text-align:right; border:1px solid gray}
    td, th {text-align:right}
    ";

    $Page['body'] .= "<form action='".$Page['id']."' method='post'>
    <div style='float:right; text-align:center; margin:3em; width:40%'>
    
    <p style='font-size:3em; color:orange; margin-bottom:3px'>Total float in:
    <input style='color:inherit; font-size:inherit; border:2px solid gray; width:auto'
    type='text' id='total' disabled='yes' size='6' name='total' /></p>

    <p style='margin-top:0'>Difference from yesterday ($$yesterday_total):
    <input type='text' style='width:auto' disabled='yes' size='5' id='diff' /></p>

    <p>Any comments?<br /><textarea name='opening_comments' style='text-align:left'></textarea></p>
    
    <p ><input type='submit' name='save' value='Save &raquo;' style='width:auto; color:green; font-size:2em' /></p>
    
    </div>
    
    <table style='margin:auto'>
        <tr>
            <th>Date (YYYY-MM-DD):</th>
            <td colspan='3'><input type='text' name='date' value='".date('Y-m-d')."' /></td>
        </tr>
        <tr>
            <th>Coordinator:</th>
            <td colspan='3'><select style='text-align:left' name='opening_coordinator'>$coordies_options</select></td>
        </tr>
        <tr>
            <th>Notes:</th>
            <td><input type='text' name='opening_5000' size='4' onChange=\"calc(this.value, 50, '5000_total')\" /></td>
            <td>x $50.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='5000_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='opening_2000' size='4' onChange=\"calc(this.value, 20, '2000_total')\" /></td>
            <td>x $20.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='2000_total'/></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='opening_1000' size='4' onChange=\"calc(this.value, 10, '1000_total')\" /></td>
            <td>x $10.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='1000_total'/></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='opening_0500' size='4' onChange=\"calc(this.value, 5, '0500_total')\" /></td>
            <td>x $5.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='0500_total' /></td>
        </tr>
        <tr>
            <th>Coins:</th>
            <td><input type='text' name='opening_0200' size='4' onChange=\"calc(this.value, 2, '0200_total')\" /></td>
            <td>x $2.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='0200_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='opening_0100' size='4' onChange=\"calc(this.value, 1, '0100_total')\" /></td>
            <td>x $1.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='0100_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='opening_0050' size='4' onChange=\"calc(this.value, 0.5, '0050_total')\" /></td>
            <td>x $0.50 =</td>
            <td><input type='text' disabled='yes' size='5' id='0050_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='opening_0020' size='4' onChange=\"calc(this.value, 0.2, '0020_total')\" /></td>
            <td>x $0.20 =</td>
            <td><input type='text' disabled='yes' size='5' id='0020_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='opening_0010' size='4' onChange=\"calc(this.value, 0.1, '0010_total')\" /></td>
            <td>x $0.10 =</td>
            <td><input type='text' disabled='yes' size='5' id='0010_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='opening_0005' size='4' onChange=\"calc(this.value, 0.05, '0005_total')\" /></td>
            <td>x $0.05 =</td>
            <td><input type='text' disabled='yes' size='5' id='0005_total' /></td>
        </tr>
        <tr>
            <th>Bags &amp; rolls:</th>
            <td></td>
            <td>$2.00 =</td>
            <td><input type='text' name='opening_bagsandrolls_0200' size='4' id='opening_bagsandrolls_0200'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$1.00 =</td>
            <td><input type='text' name='opening_bagsandrolls_0100' size='4' id='opening_bagsandrolls_0100'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$0.50 =</td>
            <td><input type='text' name='opening_bagsandrolls_0050' size='4' id='opening_bagsandrolls_0050'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$0.20 =</td>
            <td><input type='text' name='opening_bagsandrolls_0020' size='4' id='opening_bagsandrolls_0020'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$0.10 =</td>
            <td><input type='text' name='opening_bagsandrolls_0010' size='4' id='opening_bagsandrolls_0010'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$0.05 =</td>
            <td><input type='text' name='opening_bagsandrolls_0005' size='4' id='opening_bagsandrolls_0005'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
    </table>
    </form>";
    
}

?>
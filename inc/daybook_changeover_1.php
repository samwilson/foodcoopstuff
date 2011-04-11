<?php

if ($_POST['save']) {
    $sql = "UPDATE daybook SET
        `changeover1_coordinator_A`     = ".dbesc($_POST['changeover1_coordinator_A']).",
        `changeover1_coordinator_B`     = ".dbesc($_POST['changeover1_coordinator_B']).",
        `changeover1_comments`          = ".dbesc($_POST['changeover1_comments']).",
        `changeover1_cheques`           = ".dbesc($_POST['changeover1_cheques']).",
        `changeover1_5000`              = ".dbesc($_POST['changeover1_5000']).",
        `changeover1_2000`              = ".dbesc($_POST['changeover1_2000']).",
        `changeover1_1000`              = ".dbesc($_POST['changeover1_1000']).",
        `changeover1_0500`              = ".dbesc($_POST['changeover1_0500']).",
        `changeover1_0200`              = ".dbesc($_POST['changeover1_0200']).",
        `changeover1_0100`              = ".dbesc($_POST['changeover1_0100']).",
        `changeover1_0050`              = ".dbesc($_POST['changeover1_0050']).",
        `changeover1_0020`              = ".dbesc($_POST['changeover1_0020']).",
        `changeover1_0010`              = ".dbesc($_POST['changeover1_0010']).",
        `changeover1_0005`              = ".dbesc($_POST['changeover1_bagsandrolls_0005']).",
        `changeover1_bagsandrolls_0200` = ".dbesc($_POST['changeover1_bagsandrolls_0200']).",
        `changeover1_bagsandrolls_0100` = ".dbesc($_POST['changeover1_bagsandrolls_0100']).",
        `changeover1_bagsandrolls_0050` = ".dbesc($_POST['changeover1_bagsandrolls_0050']).",
        `changeover1_bagsandrolls_0020` = ".dbesc($_POST['changeover1_bagsandrolls_0020']).",
        `changeover1_bagsandrolls_0010` = ".dbesc($_POST['changeover1_bagsandrolls_0010']).",
        `changeover1_bagsandrolls_0005` = ".dbesc($_POST['changeover1_bagsandrolls_0005'])."
        WHERE `date`                    = ".dbesc($_POST['date']);
    $res = mysql_query($sql);
    website_log("Coordinators ".$_POST['changeover1_coordinator_A']." and
        ".$_POST['changeover1_coordinator_B']." midday changeover.");
    if (mysql_error()) $Page['error_message'] .= "<p>".mysql_error()."</p>";
    else $Page['body'] .= "<p>The midday changeover has been entered into the daybook.  Thankyou.</p>";



} else {
    
    // Build select list for coordies.
    $res1 = mysql_query('SELECT morning_shift, afternoon_shift FROM `co-ordinators_roster` WHERE date=CURDATE()');
    if (mysql_num_rows($res1)>0) {
        $curr_coordie = mysql_fetch_assoc($res1);
        $curr_coordie_A = $curr_coordie['morning_shift'];
        $curr_coordie_B = $curr_coordie['afternoon_shift'];
    } else {
        $curr_coordie_A = NULL;
        $curr_coordie_B = NULL;
    }
    
    $res = mysql_query("SELECT * FROM people WHERE coordinator=1 ORDER BY first_name");
    $coordie_A_select = "<select style='text-align:left' name='changeover1_coordinator_A'>
        <option value=''></option>";
    while ($coordie = mysql_fetch_assoc($res)) {
        if ($coordie['id'] == $curr_coordie_A) $sel = 'selected '; else $sel = '';
        $coordie_A_select .= "<option value='".$coordie['id']."' $sel>
            ".$coordie['first_name']." ".$coordie['surname']."</option>";
    }
    $res = mysql_query("SELECT * FROM people WHERE coordinator=1 ORDER BY first_name");
    $coordie_B_select = "<select style='text-align:left' name='changeover1_coordinator_B'>
        <option value=''></option>";
    while ($coordie = mysql_fetch_assoc($res)) {
        if ($coordie['id'] == $curr_coordie_B) $sel = 'selected '; else $sel = '';
        $coordie_B_select .= "<option value='".$coordie['id']."' $sel>
            ".$coordie['first_name']." ".$coordie['surname']."</option>";
    }
    
        
    $Page['script'] .= "function calc(numerator, denominator, destination) {
        document.getElementById(destination).value = (numerator * denominator).toFixed(2);
        sumAll();
    }
    function sumAll() {
        document.getElementById('total').value = (
            (document.getElementById('changeover1_cheques').value * 1) +
            (document.getElementById('10000_total').value * 1) +
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
            (document.getElementById('changeover1_bagsandrolls_0200').value * 1) +
            (document.getElementById('changeover1_bagsandrolls_0100').value * 1) +
            (document.getElementById('changeover1_bagsandrolls_0050').value * 1) +
            (document.getElementById('changeover1_bagsandrolls_0020').value * 1) +
            (document.getElementById('changeover1_bagsandrolls_0010').value * 1) +
            (document.getElementById('changeover1_bagsandrolls_0005').value * 1)
        ).toFixed(2);
        document.getElementById('diff').value = document.getElementById('total').value - document.getElementById('changeover1_xreading_ID').value;
        document.getElementById('diff').value = (document.getElementById('diff').value * 1).toFixed(2);
    }
    function init() {}";
    
    $Page['style'] .= "input, select, textarea {width:100%; text-align:right; border:1px solid gray}
    td, th {text-align:right}
    ";

    $Page['body'] .= "<form action='".$Page['id']."' method='post'>
    <div style='float:right; text-align:center; margin:3em; width:40%'>
    <table>
        <tr>
            <th>X Reading from tillroll:</th>
            <td><input type='text' name='changeover1_xreading_ID' id='changeover1_xreading_ID'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr>
            <th>Total cash and cheques in till:</th>
            <td><input type='text' id='total' disabled='yes' /></td>
        </tr>
        <tr>
            <th>Difference:</th>
            <td><input type='text' id='diff' disabled='yes' /></td>
        </tr>
    </table>
    <p>Any comments?<br /><textarea name='changeover1_comments' style='text-align:left'></textarea></p>
    
    <p ><input type='submit' name='save' value='Save &raquo;' style='width:auto; color:green; font-size:2em' /></p>
    
    </div>
    
    <table style='margin:auto'>
        <tr>
            <th>Date (YYYY-MM-DD):</th>
            <td colspan='3'><input type='text' name='date' value='".date('Y-m-d')."' /></td>
        </tr>
        <tr>
            <th>Coordinator A:</th>
            <td colspan='3'>$coordie_A_select</td>
        </tr>
        <tr>
            <th>Coordinator B:</th>
            <td colspan='3'>$coordie_B_select</td>
        </tr>
        <tr>
            <th>Cheques:</th>
            <td></td>
            <td>Total:</td>
            <td><input type='text' name='changeover1_cheques' id='changeover1_cheques' size='4'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();'/></td>
        </tr>
        <tr>
            <th>Notes:</th>
            <td><input type='text' name='changeover1_10000' size='4' onChange=\"calc(this.value, 100, '10000_total')\" /></td>
            <td>x $100.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='10000_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_5000' size='4' onChange=\"calc(this.value, 50, '5000_total')\" /></td>
            <td>x $50.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='5000_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_2000' size='4' onChange=\"calc(this.value, 20, '2000_total')\" /></td>
            <td>x $20.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='2000_total'/></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_1000' size='4' onChange=\"calc(this.value, 10, '1000_total')\" /></td>
            <td>x $10.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='1000_total'/></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_0500' size='4' onChange=\"calc(this.value, 5, '0500_total')\" /></td>
            <td>x $5.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='0500_total' /></td>
        </tr>
        <tr>
            <th>Coins:</th>
            <td><input type='text' name='changeover1_0200' size='4' onChange=\"calc(this.value, 2, '0200_total')\" /></td>
            <td>x $2.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='0200_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_0100' size='4' onChange=\"calc(this.value, 1, '0100_total')\" /></td>
            <td>x $1.00 =</td>
            <td><input type='text' disabled='yes' size='5' id='0100_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_0050' size='4' onChange=\"calc(this.value, 0.5, '0050_total')\" /></td>
            <td>x $0.50 =</td>
            <td><input type='text' disabled='yes' size='5' id='0050_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_0020' size='4' onChange=\"calc(this.value, 0.2, '0020_total')\" /></td>
            <td>x $0.20 =</td>
            <td><input type='text' disabled='yes' size='5' id='0020_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_0010' size='4' onChange=\"calc(this.value, 0.1, '0010_total')\" /></td>
            <td>x $0.10 =</td>
            <td><input type='text' disabled='yes' size='5' id='0010_total' /></td>
        </tr>
        <tr>
            <th></th>
            <td><input type='text' name='changeover1_0005' size='4' onChange=\"calc(this.value, 0.05, '0005_total')\" /></td>
            <td>x $0.05 =</td>
            <td><input type='text' disabled='yes' size='5' id='0005_total' /></td>
        </tr>
        <tr>
            <th>Bags &amp; rolls:</th>
            <td></td>
            <td>$2.00 =</td>
            <td><input type='text' name='changeover1_bagsandrolls_0200' size='4' id='changeover1_bagsandrolls_0200'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$1.00 =</td>
            <td><input type='text' name='changeover1_bagsandrolls_0100' size='4' id='changeover1_bagsandrolls_0100'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$0.50 =</td>
            <td><input type='text' name='changeover1_bagsandrolls_0050' size='4' id='changeover1_bagsandrolls_0050'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$0.20 =</td>
            <td><input type='text' name='changeover1_bagsandrolls_0020' size='4' id='changeover1_bagsandrolls_0020'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$0.10 =</td>
            <td><input type='text' name='changeover1_bagsandrolls_0010' size='4' id='changeover1_bagsandrolls_0010'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
        <tr><td colspan='2'></td><td>$0.05 =</td>
            <td><input type='text' name='changeover1_bagsandrolls_0005' size='4' id='changeover1_bagsandrolls_0005'
                 onChange='this.value = (this.value * 1).toFixed(2); sumAll();' /></td>
        </tr>
    </table>
    </form>";
    
}

?>
<?php

IF ($_POST['save']) {
	$sql = "";
}

$Page['body'] .= '<form action="" method="post">
<table border="1">
<tr>
    <th rowspan="2">Date</th>
    <th rowspan="2">Description</th>
    <th rowspan="2">Count</th>
    <th rowspan="2">Unit</th>
    <th colspan="2">Price Per Unit</th>
    <th colspan="2">Loss</th>
    <th rowspan="2">Actions</th>
</tr>
<tr>
    <th>Old</th><th>New Price</th><th>Before Markup</th><th>After Markup</th>
</tr>
<tr>
    <td><input type="text" name="date" value="'.date('Y-m-d').'" /></td>
    <td><input type="text" name="description" /></td>
    <td><input type="text" name="count" /></td>
    <td><select name="unit"><option value="kg">kg</option><option value="each">each</option></select></td>
    <td><input type="text" name="old_price" /></td>
    <td><input type="text" name="new_price" /></td>
    <td><input type="text" name="loss_before_markup" disabled="yes" /></td>
    <td><input type="text" name="loss_after_markup" disabled="yes" /></td>
	<td><input type="submit" name="save" value="Save &raquo;" /></td>
</tr>';

$sql = "SELECT `date`,description,count,unit,old_price,new_price,((old_price-new_price)*unit) as loss_after_markup
		FROM stockloss WHERE `date`<=DATE_SUB(`date`, INTERVAL 1 MONTH) ORDER BY `date` DESC";
$rows = mysql_fetch_assoc(mysql_query($sql));

foreach ($rows as $row) {
	$Page['body'] .= "<tr>
		<td>".$row['date']."</td>
		<td>".$row['description']."</td>
		<td rowspan='2'>".$row['count']."".$row['unit']."</td>
		<td>".$row['old_price']."</td>
		<td>".$row['new_price']."</td>
		<td></td>
		<td>".$row['loss_after_markup']."</td>
	</tr>";
}

$Page['body'] .= '</table>
</form>';

?>
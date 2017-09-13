<?php
	require "DifferenceEngine.php";

	require 'pscws4/pscws4.class.php';

	$pscws = new PSCWS4('utf8');
	$pscws->set_dict('pscws4/etc/dict.utf8.xdb');
	$pscws->set_rule('pscws4/etc/rules.utf8.ini');

	$file_path = "order1.php";
	if( file_exists($file_path) ){
		$file_arr = file($file_path);
	}

	$lines1 = $file_arr;

	$file_path = "order2.php";
	if( file_exists($file_path) ){
		$file_arr = file($file_path);
	}

	$lines2 = $file_arr;

	$diff = new Diff($lines1, $lines2);

	foreach( $diff->edits as $line_num => $line ){
		
		
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Computed Diff</title>
</head>
<style type="text/css">
	.diff-table {
	    clear: right;
	    background: #fff;
	    width: 100%;
	    border-collapse: collapse;
    	border-spacing: 0;
	}
	.diff-table td{
		vertical-align: top;
	}
	.diff-line-number{
		color: #666;
	    padding: 0 4px;
	    text-align: right;
	}
	.diff-chunk{
		min-height: 20px;
		line-height: 20px;
		padding: 0;
		margin: 0;
		font-family: Inconsolata,Consolas,Monaco;
	}
	.diff-line-with-removes{
		background: #fcd8d9;;
	}
	.diff-chunk-removed{
		background: #f88;
	}
	.diff-line-with-inserts{
		background: #e0fcd0;
	}
	.diff-chunk-inserted{
		background: #16ff00;
	}
	.diff-line-empty{
		background: #ccc;
	}
</style>
<body>
<?php
// var_dump($origin);die();
?>
	<div>
		<table class="diff-table" cellspacing="0" cellpadding="0">
			<?php
			foreach ($origin as $key => $value){
			?>
			<tr>
				<td class="diff-line-number"><?=$key+1?>.</td>
				<td><?=$origin[$key]?></td>
				<td class="diff-line-number"><?=$key+1?>.</td>
				<td><?=$change[$key]?></td>
			</tr>
			<?php
			}
			?>
			<!-- <tr>
				<td></td>
				<td><textarea>sdsd</textarea></td>
				<td></td>
				<td><textarea>sdsd</textarea></td>
			</tr> -->
		</table>
	</div>
</body>
</html>
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

// echo "<pre>";
// 	var_dump($diff);die();

	$origin = array();
	$change = array();

	foreach( $diff->edits as $line_num => $line ){
		if( $line->type == 'copy' ) {
			foreach ($line->orig as $key => $value) {
				$origin[] = '<p class="diff-chunk"><span class="diff-chunk diff-chunk-equal">'.$value.'</span></p>';
			}
			foreach ($line->closing as $key => $value) {
				$change[] = '<p class="diff-chunk"><span class="diff-chunk diff-chunk-equal">'.$value.'</span></p>';
			}
		}else if( $line->type == 'add' ){
			if( $line->orig ){
				foreach ($line->orig as $key => $value) {
					$origin[] = '<p class="diff-chunk diff-line-with-removes"><span class="diff-chunk diff-chunk-removed">'.$value.'</span></p>';
					$change[] = '<p class="diff-chunk diff-line-with-removes"></p>';
				}
			}
			if( $line->closing ){
				foreach ($line->closing as $key => $value) {
					$change[] = '<p class="diff-chunk diff-line-with-inserts"><span class="diff-chunk diff-chunk-inserted">'.$value.'</span></p>';
					$origin[] = '<p class="diff-chunk diff-line-with-removes"></p>';
				}
			}
		}else if( $line->type == 'change' ){
			foreach ($line->orig as $key => $value) {
				if( $value == "\n" ){
					$origin[] = '<p class="diff-chunk diff-line-empty"></p>';
					if( count($line->orig) > count($line->closing) ){
						$change[] = '<p class="diff-chunk diff-line-with-removes"></p>';
					}
				}else{
					$origin[] = '<p class="diff-chunk diff-line-with-removes"><span class="diff-chunk diff-chunk-removed">'.$value.'</span></p>';
				}
			}
			foreach ($line->closing as $key => $value) {
				if( $value == "\n" ){
					$change[] = '<p class="diff-chunk diff-line-empty"></p>';
					if( count($line->orig) < count($line->closing) ){
						$origin[] = '<p class="diff-chunk diff-line-with-removes"></p>';
					}
				}else{
					$change[] = '<p class="diff-chunk diff-line-with-inserts"><span class="diff-chunk diff-chunk-inserted">'.$value.'</span></p>';
				}
			}
		}else if( $line->type == 'delete' ){
			if( $line->orig ){
				foreach ($line->orig as $key => $value) {
					$origin[] = '<p class="diff-chunk diff-line-with-removes"><span class="diff-chunk diff-chunk-removed">'.$value.'</span></p>';
					$change[] = '<p class="diff-chunk diff-line-with-removes"></p>';
				}
			}
			if( $line->closing ){
				foreach ($line->closing as $key => $value) {
					$change[] = '<p class="diff-chunk diff-line-with-inserts"><span class="diff-chunk diff-chunk-inserted">'.$value.'</span></p>';
					$origin[] = '<p class="diff-chunk diff-line-with-removes"></p>';
				}
			}
		}else{
			var_dump($line);
		}
	}
	// foreach ($origin as $key => $value) {
	// 	echo $value;
	// }
	// die();
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
		white-space: pre-wrap;
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
			$line_total = count($origin) > count($change) ? count($origin) : count($change);
			$origin_num = 1;
			$change_num = 1;
			for( $key = 0; $key < $line_total; $key++ ){
			?>
			<tr>
				<td class="diff-line-number"><?php
				if( $origin[$key] !== '<p class="diff-chunk diff-line-with-removes"></p>' ){
					echo $origin_num.'.';
				}else{
					$origin_num--;
				}
				?>
				</td>
				<td><?=$origin[$key]?></td>
				<td class="diff-line-number"><?=$key+1?>.</td>
				<td><?=$change[$key]?></td>
			</tr>
			<?php
				$origin_num++;
				$change_num++;
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
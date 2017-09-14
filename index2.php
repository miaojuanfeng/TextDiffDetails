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

	$line_diff = new Diff($lines1, $lines2);

// echo "<pre>";
// 	var_dump($diff);die();

	$origin = array();
	$change = array();

	foreach( $line_diff->edits as $line_num => $line ){
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
			$word_diff = new WordLevelDiff($line->orig, $line->closing);
			$origin_word_line = '<p class="diff-chunk diff-line-with-removes">';
			$change_word_line = '<p class="diff-chunk diff-line-with-inserts">';
			foreach ($word_diff->edits as $word_num => $word) {
				if( $word->type == 'copy' ) {
					$origin_word_line .= '<span class="diff-chunk diff-chunk-equal">';
					foreach ($word->orig as $key => $value) {
						$origin_word_line .= $value;
					}
					$origin_word_line .= '</span>';
					$change_word_line .= '<span class="diff-chunk diff-chunk-equal">';
					foreach ($word->closing as $key => $value) {
						$change_word_line .= $value;
					}
					$change_word_line .= '</span>';
				}else if( $word->type == 'change' ){
					$origin_letter_line = implode('', $word->orig);
					$change_letter_line = implode('', $word->closing);

					$orig_letter = array();
					$closing_letter = array();

					// var_dump($word);
					// var_dump($origin_letter_line);
					// var_dump($change_letter_line);

					foreach ($word->orig as $key => $value) {
						if( isset($word->orig[$key]) ){
							$pscws->send_text($word->orig[$key]);
							while ($some = $pscws->get_result()){
							   foreach ($some as $letter){
							      $orig_letter[] = $letter['word'];
							   }
							}
						}
					}

					foreach ($word->closing as $key => $value) {
						if( isset($word->closing[$key]) ){
							$pscws->send_text($word->closing[$key]);
							while ($some = $pscws->get_result()){
							   foreach ($some as $letter){
							      $closing_letter[] = $letter['word'];
							   }
							}
						}
					}

					// var_dump($orig_letter);
					// var_dump($closing_letter);

					$letter_diff = new WordLevelDiff($orig_letter, $closing_letter);
					foreach ($letter_diff->edits as $letter_num => $letter) {
						// var_dump($letter);
						if( $letter->type == 'copy' ) {
							// $origin_word_line .= '<span class="diff-chunk diff-chunk-equal">';
							// foreach ($letter->orig as $key => $value) {
							// 	// 分词后会产生\n换行符，因为在同一行，无需换行，去掉此字符
							// 	if( $value !== "\n" ){
							// 		$origin_word_line .= $value;
							// 	}
							// }
							// $origin_word_line .= '</span>';
							// $change_word_line .= '<span class="diff-chunk diff-chunk-equal">';
							// foreach ($letter->closing as $key => $value) {
							// 	// 分词后会产生\n换行符，因为在同一行，无需换行，去掉此字符
							// 	if( $value !== "\n" ){
							// 		$change_word_line .= $value;
							// 	}
							// }
							// $change_word_line .= '</span>';
						}else if( $letter->type == 'change' ){
							//$origin_word_line .= '<span class="diff-chunk diff-chunk-removed">';
							foreach ($letter->orig as $key => $value) {
								// 分词后会产生\n换行符，因为在同一行，无需换行，去掉此字符
								if( $value !== "\n" ){
									// 会产生替换多次的问题
									$origin_letter_line = str_replace($value, '<span class="diff-chunk diff-chunk-changed">'.$value.'</span>', $origin_letter_line);
								}
							}
							//$origin_word_line .= '</span>';
							//$change_word_line .= '<span class="diff-chunk diff-chunk-removed">';
							foreach ($letter->closing as $key => $value) {
								// 分词后会产生\n换行符，因为在同一行，无需换行，去掉此字符
								if( $value !== "\n" ){
									$change_letter_line = str_replace($value, '<span class="diff-chunk diff-chunk-inserted">'.$value.'</span>', $change_letter_line);
								}
							}
							//$change_word_line .= '</span>';
						}else if( $letter->type == 'delete' ){
							if( $letter->orig ){
								//$origin_word_line .= '<span class="diff-chunk diff-chunk-removed">';
								foreach ($letter->orig as $key => $value) {
									// 分词后会产生\n换行符，因为在同一行，无需换行，去掉此字符
									if( $value !== "\n" ){
										// 会产生替换多次的问题
										$origin_letter_line = str_replace($value, '<span class="diff-chunk diff-chunk-removed">'.$value.'</span>', $origin_letter_line);
									}
								}
								//$origin_word_line .= '</span>';
							}
							if( $letter->closing ){
								//$change_word_line .= '<span class="diff-chunk diff-chunk-removed">';
								foreach ($letter->closing as $key => $value) {
									// 分词后会产生\n换行符，因为在同一行，无需换行，去掉此字符
									if( $value !== "\n" ){
										$change_letter_line = str_replace($value, '<span class="diff-chunk diff-chunk-inserted">'.$value.'</span>', $change_letter_line);
									}
								}
								//$change_word_line .= '</span>';
							}
						}else{
							echo "undefined letter: ";
							var_dump($letter);
						}
					}
					$origin_word_line .= $origin_letter_line;
					$change_word_line .= $change_letter_line;
				}
			}
			$origin_word_line .= '</p>';
			$change_word_line .= '</p>';
			$origin[] = $origin_word_line;
			$change[] = $change_word_line;
			// foreach ($origin as $key => $value) {
			// 	echo $value;
			// };
			// die();



			// foreach ($line->orig as $key => $value) {
			// 	$orig_letter = array();
			// 	$closing_letter = array();

			// 	if( isset($line->orig[$key]) ){
			// 		$pscws->send_text($line->orig[$key]);
			// 		while ($some = $pscws->get_result()){
			// 		   foreach ($some as $letter){
			// 		      $orig_letter[] = $letter['word'];
			// 		   }
			// 		}
			// 	}

			// 	if( isset($line->closing[$key]) ){
			// 		$pscws->send_text($line->closing[$key]);
			// 		while ($some = $pscws->get_result()){
			// 		   foreach ($some as $letter){
			// 		      $closing_letter[] = $letter['word'];
			// 		   }
			// 		}
			// 	}

			// 	die();

			// 	if( $value == "\n" ){
			// 		$origin[] = '<p class="diff-chunk diff-line-empty"></p>';
			// 		if( count($line->orig) > count($line->closing) ){
			// 			$change[] = '<p class="diff-chunk diff-line-with-removes"></p>';
			// 		}
			// 	}else{
			// 		foreach ($orig_letter as $k => $v) {
			// 			if( (isset($closing_letter[$k]) && $orig_letter[$k] !== $closing_letter[$k]) ||
			// 				!isset($closing_letter[$k])
			// 			){
			// 				// $count = substr_count($value, $orig_letter[$k]);
			// 				// $j = 0;
			// 				// for($i = 0; $i < $count; $i++){
			// 				//      $j = strpos($value, $orig_letter[$k], $j);
			// 				//      $value = substr_replace($value, '<span class="diff-chunk diff-chunk-removed">'.$orig_letter[$k].'</span>', $j, strlen($orig_letter[$k]));
			// 				//      $j += 51;
			// 				//      $j = $j+1;
			// 				// }
			// 				// var_dump($orig_letter[$k]);
			// 				// var_dump($closing_letter[$k]);
			// 				// echo "<br>";
			// 				$orig_letter[$k] = '<span class="diff-chunk diff-chunk-removed">'.$orig_letter[$k].'</span>';
			// 			}
			// 		}
			// 		// echo "<br/>";
			// 		// var_dump($value);die();
			// 		$origin[] = '<p class="diff-chunk diff-line-with-removes">'.implode('', $orig_letter).'</p>';
			// 		// $origin[] = '<p class="diff-chunk diff-line-with-removes">'.$value.'</p>';
			// 	}
			// }
			// foreach ($line->closing as $key => $value) {
			// 	$orig_letter = array();
			// 	$closing_letter = array();

			// 	if( isset($line->orig[$key]) ){
			// 		$pscws->send_text($line->orig[$key]);
			// 		while ($some = $pscws->get_result()){
			// 		   foreach ($some as $letter){
			// 		      $orig_letter[] = $letter['word'];
			// 		   }
			// 		}
			// 	}

			// 	if( isset($line->closing[$key]) ){
			// 		$pscws->send_text($line->closing[$key]);
			// 		while ($some = $pscws->get_result()){
			// 		   foreach ($some as $letter){
			// 		      $closing_letter[] = $letter['word'];
			// 		   }
			// 		}
			// 	}

			// 	if( $value == "\n" ){
			// 		$change[] = '<p class="diff-chunk diff-line-empty"></p>';
			// 		if( count($line->orig) < count($line->closing) ){
			// 			$origin[] = '<p class="diff-chunk diff-line-with-removes"></p>';
			// 		}
			// 	}else{
			// 		foreach ($closing_letter as $k => $v) {
			// 			if( (isset($orig_letter[$k]) && $closing_letter[$k] !== $orig_letter[$k]) ||
			// 				!isset($orig_letter[$k])
			// 			){
			// 				$closing_letter[$k] = '<span class="diff-chunk diff-chunk-inserted">'.$closing_letter[$k].'</span>';
			// 			}
			// 		}
			// 		$change[] = '<p class="diff-chunk diff-line-with-inserts">'.implode('', $closing_letter).'</p>';
			// 	}
			// }
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
			echo "undefined line: ";
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
	.diff-line-with-inserts{
		background: #e0fcd0;
	}
	.diff-chunk-inserted{
		background: #16ff00;
	}
	.diff-line-with-changes{
		background: #e0fcd0;
	}
	.diff-chunk-changed{
		background: #16ccff;
	}
	.diff-line-with-removes{
		background: #fcd8d9;;
	}
	.diff-chunk-removed{
		background: #f88;
		text-decoration: line-through;
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
				<td class="diff-line-number">
				<?php
				if( $change[$key] !== '<p class="diff-chunk diff-line-with-removes"></p>' ){
					echo $change_num.'.';
				}else{
					$change_num--;
				}
				?>
				</td>
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
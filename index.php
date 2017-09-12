<?php
	require "DifferenceEngine.php";

	require 'pscws4/pscws4.class.php';

	$pscws = new PSCWS4('utf8');
	$pscws->set_dict('pscws4/etc/dict.utf8.xdb');
	$pscws->set_rule('pscws4/etc/rules.utf8.ini');

	// $lines1 = array("yes, i am", "hello country, \nI am a teacher, \n32 years old.", "me too.", "how are you");
	// $lines2 = array("hello country, \nI am a teachers, \n32 years old.", "me too.", "how about you");

	$lines1 = array("我是XXX，我是一个老师，今年32岁", "我很好", "今天是一个好日子", "难为你呢", "生活大爆炸我也没看过，非常好看", "不能因为", "好好笑", "非常好笑");
	$lines2 = array("我是XXX，我不是一个学生，今年32岁", "我不是", "我很好", "今天是一个好日子", "难为我呢", "生活大爆炸好看吗，非常难看", "不能因为", "好好笑", "好好笑");

	$diff = new Diff($lines1, $lines2);
	// var_dump($diff->edits);
	$origin = array();
	$change = array();
	// var_dump($diff->edits);
	foreach( $diff->edits as $line_num => $line ){
		// var_dump($line);
		// $line_height = (count($line->orig)>count($line->closing))?count($line->orig):count($line->closing);
		if( !$line->orig ){
			$line->orig = array();
		}
		if( !$line->closing ){
			$line->closing = array();
		}
		$word_diff = new WordLevelDiff($line->orig, $line->closing);
		// var_dump($word_diff);
		$origin[$line_num] = '';
		$change[$line_num] = '';
		foreach ($word_diff->edits as $word_offset => $word) {
			if( $word->type == 'copy' ) {
				// echo "<pre>";
				// var_dump($word);
				foreach ($word->orig as $key => $value) {
					if( $value == "\n" ){
						$word->orig[$key] = '</p><p class="diff-chunk diff-chunk-equal">';
					}else{
						$origin[$line_num] .= '<p class="diff-chunk diff-chunk-equal">'.$value.'</p>';
					}
				}
				foreach ($word->closing as $key => $value) {
					if( $value == "\n" ){
						$word->closing[$key] = '</p><p class="diff-chunk diff-chunk-equal">';
					}else{
						$change[$line_num] .= '<p class="diff-chunk diff-chunk-equal">'.$value.'</p>';
					}
				}
				// $origin[$line_num] = '<p class="diff-chunk diff-chunk-equal">'.implode('', $word->orig).'</p>';
				// $change[$line_num] = '<p class="diff-chunk diff-chunk-equal">'.implode('', $word->closing).'</p>';
			}else if( $word->type == 'change' ){
				$key_len = count($word->orig) > count($word->closing) ? count($word->orig) : count($word->closing);
				for( $key = 0; $key < $key_len; $key++ ){
					$orig_letter = array();
					$closing_letter = array();

					if( isset($word->orig[$key]) ){
						$pscws->send_text($word->orig[$key]);
						while ($some = $pscws->get_result()){
						   foreach ($some as $letter){
						      $orig_letter[] = $letter['word'];
						   }
						}
					}

					if( isset($word->closing[$key]) ){
						$pscws->send_text($word->closing[$key]);
						while ($some = $pscws->get_result()){
						   foreach ($some as $letter){
						      $closing_letter[] = $letter['word'];
						   }
						}
					}

					if( isset($word->orig[$key]) ){ 
						if( $word->orig[$key] == "\n" ){
							$word->orig[$key] = '</p><p class="diff-chunk diff-chunk-removed">';
							$change[$line_num] .= '<p class="diff-chunk diff-line-empty"></p>';
						}else{
							foreach ($orig_letter as $k => $v) {
								if( (isset($closing_letter[$k]) && $orig_letter[$k] !== $closing_letter[$k]) ||
									!isset($closing_letter[$k])
								){
									$orig_letter[$k] = '<span class="diff-chunk diff-chunk-removed">'.$orig_letter[$k].'</span>';
								}
							}
							$origin[$line_num] .= '<p class="diff-chunk diff-line-with-removes">'.implode('', $orig_letter).'</p>';
						}
					}
					if( isset($word->closing[$key]) ){
						if( $word->closing[$key] == "\n" ){
							$word->closing[$key] = '</p><p class="diff-chunk diff-line-with-inserts">';
							$origin[$line_num] .= '<p class="diff-chunk diff-line-empty"></p>';
						}else{
							foreach ($closing_letter as $k => $v) {
								if( (isset($orig_letter[$k]) && $closing_letter[$k] !== $orig_letter[$k]) ||
									!isset($orig_letter[$k])
								){
									$closing_letter[$k] = '<span class="diff-chunk diff-chunk-inserted">'.$closing_letter[$k].'</span>';
								}
							}
							$change[$line_num] .= '<p class="diff-chunk diff-line-with-inserts">'.implode('', $closing_letter).'</p>';
						}
					}
				}
			}else if( $word->type == 'delete' ){
				$origin[$line_num] = '<p class="diff-chunk diff-chunk-removed">'.implode('', $word->orig).'</p>';
				$change[$line_num] = '<p class="diff-chunk diff-chunk-inserted">'.implode('', $word->closing).'</p>';
			}
		}
		// 	if( $word->type == 'change' ){
		// 		for($i=0;$i<$line_height;$i++){
		// 			if( isset($line->orig[$i]) ){
		// 				$origin[$line_num][$i] = $line->orig[$i];
		// 			}else{
		// 				$origin[$line_num][$i] = [];
		// 			}
		// 			if( isset($line->closing[$i]) ){
		// 				$change[$line_num][$i] = $line->closing[$i];
		// 			}else{
		// 				$change[$line_num][$i] = [];
		// 			}
		// 		}
		// 	}else if( $word->type == 'delete' ){
		// 		$orig_arr = array();
		// 		if( $word->orig ){
		// 			foreach ($word->orig as $key => $value) {
		// 				if( $value !== "\n" ){
		// 					$orig_arr[] = '<p>'.$value.'</p>';
		// 				}
		// 			}
		// 		}else{
		// 			foreach ($word->closing as $key => $value) {
		// 				if( $value !== "\n" ){
		// 					$orig_arr[] = "<p></p>";
		// 				}
		// 			}
		// 		}
		// 		$origin[$line_num][] = implode('', $orig_arr);
		// 		//
		// 		$change_arr = array();
		// 		if( $word->closing ){
		// 			foreach ($word->closing as $key => $value) {
		// 				if( $value !== "\n" ){
		// 					$change_arr[] = '<p>'.$value.'</p>';
		// 				}
		// 			}
		// 		}else{
		// 			foreach ($word->orig as $key => $value) {
		// 				if( $value !== "\n" ){
		// 					$change_arr[] = "<p></p>";
		// 				}
		// 			}
		// 		}
		// 		$change[$line_num][] = '<p>'.implode('', $change_arr).'</p>';
		// 	}else if( $word->type == 'copy' ) {
		// 		$orig_arr = array();
		// 		if( $word->orig ){
		// 			foreach ($word->orig as $key => $value) {
		// 				$orig_arr[] = $value;
		// 			}
		// 		}
		// 		$origin[$line_num][] = implode('', $orig_arr);
		// 		//
		// 		$change_arr = array();
		// 		if( $word->closing ){
		// 			foreach ($word->closing as $key => $value) {
		// 				$change_arr[] = $value;
		// 			}
		// 		}
		// 		$change[$line_num][] = implode('', $change_arr);
		// 		// var_dump($word);
		// 	}else{
		// 		var_dump($word);
		// 	}
		// }
	}
	// foreach ($origin as $key => $value) {
	// 	$origin[$key] = implode('', $value);
	// }
	// foreach ($change as $key => $value) {
	// 	$change[$key] = implode('', $value);
	// }
	// var_dump($origin);
	// var_dump($change);
	// echo "<pre>";
	// echo implode('', $origin);
	// var_dump($change);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Computed Diff</title>
</head>
<style type="text/css">
	.diff-chunk{
		height: 20px;
		line-height: 20px;
		padding: 0;
		margin: 0;
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
		background: #9f9;
	}
	.diff-line-empty{
		background: #ccc;
	}
</style>
<body>
	<div>
		<div style="width:49%; float:left; border:1px solid #eee;min-height:200px;">
			<?=implode('', $origin)?>
		</div>
		<div style="width:49%; float:right; border:1px solid #eee;min-height:200px;">
			<?=implode('', $change)?>
		</div>
		<div style="clear:both;float:none;"></div>
	</div>
</body>
</html>
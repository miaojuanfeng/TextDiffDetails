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
	$origin = '';
	$change = '';

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
		// $origin = '';
		// $change = '';
		foreach ($word_diff->edits as $word_offset => $word) {
			if( $word->type == 'copy' ) {
				// echo "<pre>";
				// var_dump($word);
				foreach ($word->orig as $key => $value) {
					if( $value == "\n" ){
						// $word->orig[$key] = '</span><span class="diff-chunk diff-chunk-equal">';
					}else{
						$origin .= '<p class="diff-chunk"><span class="diff-chunk diff-chunk-equal">'.$value.'</span></p>'."\n".'';
					}
				}
				foreach ($word->closing as $key => $value) {
					if( $value == "\n" ){
						// $word->closing[$key] = '</span><span class="diff-chunk diff-chunk-equal">';
					}else{
						$change .= '<p class="diff-chunk"><span class="diff-chunk diff-chunk-equal">'.$value.'</span></p>'."\n".'';
					}
				}
				// $origin = '<p class="diff-chunk diff-chunk-equal">'.implode('', $word->orig).'</p>';
				// $change = '<p class="diff-chunk diff-chunk-equal">'.implode('', $word->closing).'</p>';
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
							$word->orig[$key] = '</p>'."\n".'<p class="diff-chunk diff-chunk-removed">';
							$change .= '<p class="diff-chunk diff-line-empty"></p>'."\n".'';
						}else{
							foreach ($orig_letter as $k => $v) {
								if( (isset($closing_letter[$k]) && $orig_letter[$k] !== $closing_letter[$k]) ||
									!isset($closing_letter[$k])
								){
									$orig_letter[$k] = '<span class="diff-chunk diff-chunk-removed">'.$orig_letter[$k].'</span>';
								}
							}
							$origin .= '<p class="diff-chunk diff-line-with-removes">'.implode('', $orig_letter).'</p>'."\n";
						}
					}
					if( isset($word->closing[$key]) ){
						if( $word->closing[$key] == "\n" ){
							$word->closing[$key] = '</p>'."\n".'<p class="diff-chunk diff-line-with-inserts">';
							$origin .= '<p class="diff-chunk diff-line-empty"></p>'."\n".'';
						}else{
							foreach ($closing_letter as $k => $v) {
								if( (isset($orig_letter[$k]) && $closing_letter[$k] !== $orig_letter[$k]) ||
									!isset($orig_letter[$k])
								){
									$closing_letter[$k] = '<span class="diff-chunk diff-chunk-inserted">'.$closing_letter[$k].'</span>';
								}
							}
							$change .= '<p class="diff-chunk diff-line-with-inserts">'.implode('', $closing_letter).'</p>'."\n";
						}
					}
				}
			}else if( $word->type == 'delete' ){
				$origin .= '<p class="diff-chunk"><span class="diff-chunk diff-chunk-removed">'.implode('', $word->orig).'</span></p>'."\n".'';
				$change .= '<p class="diff-chunk"><span class="diff-chunk diff-chunk-inserted">'.implode('', $word->closing).'</span></p>'."\n".'';
			}
		}
	}
	// foreach ($origin as $key => $value) {
	// 	$origin[$key] = implode('', $value);
	// }
	// foreach ($change as $key => $value) {
	// 	$change[$key] = implode('', $value);
	// }
	$origin = explode("\n", $origin);
	array_pop($origin);
	$change = explode("\n", $change);
	array_pop($change);
	// echo implode('', $origin);
	// var_dump($change);
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
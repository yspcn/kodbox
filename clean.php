<?php
/**
 * KodBox 去除统计、更新提示与版权校验代码
 * 已测试支持版本：1.31
 */

function handle_vendor_js($filepath){
	$file = file_get_contents($filepath);
	if(!$file)return;

	$start = strpos($file, '(setTimeout,function(){');
	if($start){
		$c = 0;
		$start--;
		for($i=$start;$i>=0;$i--){
			$n = substr($file,$i,1);
			if($n == ')') $c++;
			elseif($n == '(') $c--;
			elseif($c == 0 && ($n == ',' || $n == ';')){
				$start = $i;
				break;
			}
		}
		$end = strpos($file, ',3e3))', $start);
		$content = substr($file, $start, $end - $start + 6);

		$file = str_replace($content,'',$file);
	}

	if(file_put_contents($filepath, $file)){
		echo '文件：'.$filepath.' <font color=green>处理成功</font><br/>';
	}else{
		echo '文件：'.$filepath.' <font color=red>处理失败，可能无写入权限</font><br/>';
	}
}

function handle_app_js($filepath){
	$file = file_get_contents($filepath);
	if(!$file)return;

	$file = str_replace('window.isSecureContext','!0',$file);

	if(!strpos($file,'"checkVersion":function(){return;')){
		$file = str_replace('"checkVersion":function(){','"checkVersion":function(){return;',$file);
	}

	if(!strpos($file,'"checkLang":function(){return;')){
		$file = str_replace('"checkLang":function(){','"checkLang":function(){return;',$file);
	}

	if(file_put_contents($filepath, $file)){
		echo '文件：'.$filepath.' <font color=green>处理成功</font><br/>';
	}else{
		echo '文件：'.$filepath.' <font color=red>处理失败，可能无写入权限</font><br/>';
	}
}

if(!file_exists('./static/app/dist'))exit('当前目录不存在KodBox程序文件');
handle_vendor_js('./static/app/dist/vendor.js');
handle_app_js('./static/app/dist/main.js');
handle_app_js('./static/app/dist/api.js');

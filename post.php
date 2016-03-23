<?php
	if (!isset($_GET['entry'])) 
	{
		echo '请求参数错误';
		exit;
	}

	$path=substr($_GET['entry'],0, 6);			//日志存储目录
	$entry=substr($_GET['entry'],7,9);			//日志文件名称

	$file_name='contents/'.$path.'/'.$entry.'.txt';//拼接出完整的日志路径
	if (file_exists($file_name)) 				//打开文件前需要判断文件是否存在
	{
		$fp=@fopen($file_name, 'r');			//以只读方式打开文件
		if ($fp) 
		{
			flock($fp, LOCK_SH);				//文件加锁
			$result=fread($fp, 1024);			//读出文件的内容，并以字符串形式赋给变量$result
		}
	flock($fp, LOCK_UN);						//解锁文件	
	fclose($fp);
	}

	//将字符串$result的内容按“|”分割后存入数组$content_array
	$content_array=explode('|',$result);

	//以下代码将日志内容输出至浏览器
	echo '<h1>我的个人BLOG</h1>';
	echo '<b>日志标题：</b>'.$content_array[0];
	echo '<br/><b>发布时间：</b>'.date('Y-m-d H:i:s',$content_array[1]);
	echo '<hr>';
	echo $content_array[2];
?>	

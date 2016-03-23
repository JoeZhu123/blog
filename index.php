<?php
	$login=false;
	session_start();

	if (!empty($_SESSION['user']) && $_SESSION['user']=='Joe') //判断用户是否登录
		$login=true;

		$file_array=array();
		$folder_array=array();

		$dir='contents';
		$dh=opendir($dir);

	if($dh) 
	{
		$filename=readdir($dh);
		while ($filename) 											//循环处理按年月归档的日志文章
		{
			if ($filename!='.' && $filename!='..') 
			{
				$folder_name=$filename;
				array_push($folder_array, $folder_name);
			}
			$filename=readdir($dh);	
		}
	}
	rsort($folder_array);

	$post_data=array();
	foreach ($folder_array as $folder) 
	{
		$dh=opendir($dir.'/'.$folder);								//处理每个目录下的日志文件
		while (($filename=readdir($dh))!==FALSE) 
		{
			if (is_file($dir.'/'.$folder.'/'.$filename)) 
			{
				$file=$filename;
				$file_name=$dir.'/'.$folder.'/'.$file;

				if (file_exists($file_name)) 
				{
					$fp=@fopen($file_name, 'r');
					if ($fp) 
					{
						flock($fp, LOCK_SH);
						$result=fread($fp, filesize($file_name));
					}
					flock($fp, LOCK_UN);
					fclose($fp);
				}
				$temp_data=array();
				$content_array=explode('|', $result);

				$temp_data['SUBJECT']=$content_array[0];			//文章标题
				$temp_data['DATE']=date('Y-m-d H:i:s',$content_array[1]);//发表时间
				$temp_data['CONTENT']=$content_array[2];					//文章内容
				$file=substr($file, 0,9);									//日志文章所在文件名
				$temp_data['FILENAME']=$folder.'-'.$file;
				array_push($post_data, $temp_data);
			}
		}
	}
?>	

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>基于文本的简易BLOG</title>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<div id="containor">
	<div id="header">
		<h1>我的个人BLOG</h1>
	</div>
	<div id="title">
		----I have a dream...
	</div>
	<div id="left">
		<?php
			foreach ($post_data as $post) 
			{
		?>
		<div id="blog_entry">
			<div id="blog_title"><?echo $post['SUBJECT'];?></div>
			<div id="blog_body">
				<div id="blog_date"><?echo $post['DATE'];?></div>
				<?echo $post['CONTENT'];?>
				<div>
					<?php
						if ($login) 
						{
							echo '<a href="edit.php?entry='.$post['FILENAME'].'">编辑</a>&nbsp;<a href="delete.php?entry='.$post['FILENAME'].'">删除</a>';//输出日志文章的编辑和删除链接
						}
					?>
				</div>
			</div><!-- blog_body-->
		</div><!-- blog_entry-->
		<?php
			}
		?>
	</div>
	<div id="right">
		<div id="sidebar">
			<div id="menu_title"> 关于我</div>
			<div id="menu_body"> 
				我是一个PHP爱好者
				<br/><br/>
				<?php
					if ($login) 
					{
						echo '<a href="logout.php">退出</a>';
					}
					else
					{
						echo '<a href="login.php">登录</a>';
					}
				?>
			</div>
		</div>
		<br/>
		<div id="sidebar">
			<div id="menu_title"> 日志归档</div>
				<? foreach ($folder_array as $ym) 		//输出日志按年月的归档
					{
						$entry=$ym;
						$ym=substr($ym, 0,4).'-'.substr($ym, 4,2);
						echo '<div id="menu_body"><a href="archives.php?ym='.$entry.'">'.$ym.'</a></div>';
					}
				?>
		</div>
	</div>
	<div id="footer">
		CopyRight 2016
	</div>
</div>
</body>
</html>
<?php 
	close($dh);
?>
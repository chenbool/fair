<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style>
		ul{
			list-style:none;
		}
		li{
			margin:5px;
		}
	</style>
</head>
<body>
	<h1><?= __CONTROLLER__.'/'.__ACTION__ ?></h1>
	<span>我的名字 <?= $name; ?></span> <br>
	<span>我的性别 <?= $sex; ?></span>

	<ul>
		<li> id ---  name </li>
		<?php foreach ($list as $key => $value): ?>
		<li><?= $value['id'] ?> ---  <?= $value['name'] ?></li>
		<?php endforeach; ?>
	</ul>
	<?= $page ?>

</body>
</html>
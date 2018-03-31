<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>upload</title>
</head>
<body>
	<form action="<?= __SELF__ ?>" method="post"  enctype="multipart/form-data">
		<input type="file" name="picname[]"/> <br>
		<input type="file" name="picname[]"/> <br>
		<input type="text" name="id" value="1">
		<input type="submit" value="提交">
	</form>
</body>
</html>
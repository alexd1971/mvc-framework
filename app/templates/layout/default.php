<?php
use core\Framework;
$app = Framework::application();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<meta name="keywords" content="" />
<meta name="description" content="" />
<meta name= "viewport" content= "width=1024" >
<?php echo $app->customHeaders(); ?>
</head>

<body>
<?php $app->content;?>
</body>
</html>
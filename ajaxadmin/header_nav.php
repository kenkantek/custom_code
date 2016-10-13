<?php
	$userInfo = getUserInfo();
?>
<div class="header">
    <div class="logo"><a href="#"><img src="images/logo.gif" alt="" title="" border="0" /></a></div>

    <div class="right_header">Welcome <?php echo $userInfo['username']; ?>, <a target="_blank" href="<?php echo 'http://'.$_SERVER['HTTP_HOST']; ?>">Visit site</a> | <a href="logout.php" class="logout">Logout</a></div>
    <div class="jclock"></div>
</div>
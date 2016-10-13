<?php

	$currentPage = str_replace(ADMIN_FOLDER, '', $_SERVER['REQUEST_URI']);
	$currentPage = str_replace('/', '', $currentPage);

	if($currentPage == 'main.php')
	{
		echo '
		<div class="menu">
			<ul>
			<li><a href="main.php" class="current" >Admin Home</a></li>
			<li><a href="manage_cars.php">Manage Cars</a></li>
			<li><a href="manage_users.php">Manage Users</a></li>
			</ul>
		</div>';
	}
	elseif($currentPage == 'manage_categories.php')
	{
		echo '
		<div class="menu">
			<ul>
			<li><a href="main.php">Admin Home</a></li>
			<li><a href="manage_categories.php" class="current" >Manage Categories</a></li>
			<li><a href="manage_cars.php">Manage Cars</a></li>
			<li><a href="manage_users.php">Manage Users</a></li>
			</ul>
		</div>';
	}
	elseif($currentPage == 'manage_cars.php')
	{
		echo '
		<div class="menu">
			<ul>
			<li><a href="main.php">Admin Home</a></li>
			<li><a href="manage_cars.php" class="current">Manage Cars</a></li>
			<li><a href="manage_users.php">Manage Users</a></li>
			</ul>
		</div>';
	}
	elseif($currentPage == 'manage_users.php')
	{
		echo '
		<div class="menu">
			<ul>
			<li><a href="main.php">Admin Home</a></li>
			<li><a href="manage_cars.php" >Manage Cars</a></li>
			<li><a href="manage_users.php" class="current">Manage Users</a></li>
			</ul>
		</div>';
	}

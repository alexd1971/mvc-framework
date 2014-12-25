<?php
use \core\MVCF;
$app = MVCF::app();
$imgDir = '/' . $app->baseDir . '/assets/images';
?>
<div class="wrapper">
<header class="header">
<nav class="navbar navbar-default" role="navigation">
<div class="container-fluid">
<div class="navbar-header">

<ul class="nav navbar-nav">
<li class="dropdown">
<a href="#" class="navbar-brand dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Матрица <span class="caret"></span></a>
<ul class="dropdown-menu" role="menu">
<li class="active"><a href="/">Матрица</a></li>
<li><a href="steikholder.html">Список стейкхолдеров</a></li>
<li><a href="projects.html">Cписок проектов</a></li>
<li class="divider"></li>
<li><a href="rogozin.html">Рогозин Д.О.</a></li>
</ul>
</li>
</ul>
</div>

<ul class="nav navbar-nav navbar-right">
<li><a href="#">Список проектов <i class="fa fa-angle-right"></i></a></li>
</ul>
</div>
</nav>
</header><!-- .header-->

<main class="content">
<div class="row">
<div class="col-xs-7">
<div class="row">
<canvas width="270" height="240" id="myCanvas" class="cloud-tag">
<p>Anything in here will be replaced on browsers that support the canvas element</p>
<ul>
<li><a href="#">Брычева Л.И.</a></li>
<li><a href="#">Нарышкин С.Е.</a></li>
<li><a href="#">Туманов Б.И.</a></li>
<li><a href="#">Жарков А.С.</a></li>
<li><a href="#">Погосян М.А.</a></li>
</ul>
</canvas>
<canvas width="270" height="240" id="myCanvas2" class="cloud-tag">
		<p>Anything in here will be replaced on browsers that support the canvas element</p>
		<ul>
		<li><a href="rogozin.html">Рогозин Д.О.</a></li>
		<li><a href="#">Остапенко О.Н.</a></li>
		<li><a href="#">Лысков Д.В.</a></li>
		<li><a href="#">Шойгу С.К.</a></li>
		<li><a href="#">Евтушенко В.П.</a></li>
		<li><a href="#">Боровков И.В.</a></li>
		<li><a href="#">Мантуров Д.В.</a></li>
		<li><a href="#">Харченко И.Н.</a></li>
		</ul>
		</canvas>
				</div>
				<div class="row">
				<canvas width="270" height="240" id="myCanvas3" class="cloud-tag">
				<p>Anything in here will be replaced on browsers that support the canvas element</p>
				<ul>
						<li><a href="#">Некрасов А.В.</a></li>
						<li><a href="#">Артюшенко А.Г.</a></li>
						</ul>
						</canvas>
						<canvas width="270" height="240" id="myCanvas4" class="cloud-tag">
						<p>Anything in here will be replaced on browsers that support the canvas element</p>
								<ul>
										<li><a href="#">Орлов В.И.</a></li>
										<li><a href="#">Шохин А.Н.</a></li>
										<li><a href="#">Кораблев А.В.</a></li>
										</ul>
										</canvas>
										</div>
										</div>
										<div class="col-xs-5">
										<nav class="navbar navbar-default" role="navigation">
										<div class="container-fluid">
										<div class="navbar-header">
												<a class="navbar-brand" href="#">
												<i class="fa fa-user"></i> Список
												</a>
														</div>
														<ul class="nav navbar-nav navbar-right user-list">
														<li><a href="#"><i class="fa fa-list-ul"></i></a></li>
														</ul>
														</div>
																</nav>
																<table class="table table-striped">
																<thead>
																		<tr class="info">
																		<th>ФИО</th>
																		<th>ДР</th>
																		<th>ИНТ.ОЦ</th>
																		<th>ПР-ТОВ</th>
																		</tr>
																		</thead>
																		<tbody>
																		<tr>
																		<td class="border-left-blue"><a href="rogozin.html">Рогозин Д.О</a></td>
																		<td>21 декабря</td>
																		<td>100</td>
																		<td>12</td>
																		</tr>
																		<tr>
																		<td class="border-left-blue">Остапенко О.Н</td>
																		<td>16 сентября</td>
																		<td>100</td>
																		<td>2</td>
																		</tr>
																		<tr>
																		<td class="border-left-blue">Лысков Д.В</td>
																		<td>3 декабря</td>
																		<td>100</td>
																		<td>1</td>
																		</tr>
																		<tr>
																		<td class="border-left-blue">Боровков И.В.</td>
																		<td>23 апреля</td>
																		<td>100</td>
																		<td>2</td>
																		</tr>
																		<tr>
																		<td>Шойгу С.К</td>
																		<td>8 февраля</td>
																		<td>90</td>
																		<td>2</td>
																		</tr>
																		<tr>
																		<td>Мантуров Д.В</td>
																		<td>10 мая</td>
																		<td>90</td>
																		<td>1</td>
																		</tr>
																		<tr>
																		<td>Евтушенко В.П</td>
																		<td>6 августа</td>
																		<td>72</td>
																		<td>3</td>
																		</tr>
																		<tr>
																		<td>Погосян М.А</td>
																		<td>18 января</td>
																		<td>48</td>
																		<td>3</td>
																		</tr>
																		<tr>
																		<td>Шохин А.Н.</td>
																		<td>9 апреля</td>
																		<td>48</td>
																		<td>-</td>
																		</tr>
																		<tr>
																		<td>Нарышкин С.Е</td>
																		<td>2 октября</td>
																		<td>24</td>
																		<td>-</td>
																		</tr>
																		<tr>
																		<td>Брычева Л.И</td>
																		<td>10 марта</td>
																		<td>6</td>
																		<td>1</td>
																		</tr>
																		</tbody>
																		</table>
																		</div>
																		</div>
																		</main><!-- .content -->

																		</div><!-- .wrapper -->

<footer class="footer">
<strong>&copy 2014 </strong>
   <div class="pull-right"><img width="120" src="<?php echo $imgDir; ?>/logot.png" alt=""></div>
   </footer><!-- .footer -->

    <!--[if lt IE 9]><script type="text/javascript" src="<?php echo '/' . $app->baseDir . '/assets/';?>js/excanvas.js"></script><![endif]-->
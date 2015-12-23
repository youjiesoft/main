<?php

require('lib/gantti.php'); 
require('data.php'); 

date_default_timezone_set('UTC');//设定以utc为默认时区
setlocale(LC_ALL, 'en_US');//函数设置地区信息

$gantti = new Gantti($data, array(
  'title'      => 'Demo',
  'cellwidth'  => 25,
  'cellheight' => 35,
  'today'      => true
));

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="styles/css/screen.css" />
  <link rel="stylesheet" href="styles/css/gantti.css" />

  <!--[if lt IE 9]>
  <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

</head>

<body>

<header>



</header>

<?php echo $gantti ?>

<article>


<p><pre><code><?php $code = "
<?php

require('lib/gantti.php'); 

date_default_timezone_set('UTC');
setlocale(LC_ALL, 'en_US');

\$data = array();

\$data[] = array(
  'label' => 'Project 1',
  'start' => '2012-04-20', 
  'end'   => '2012-05-12'
);

\$data[] = array(
  'label' => 'Project 2',
  'start' => '2012-04-22', 
  'end'   => '2012-05-22', 
  'class' => 'important',
);

\$data[] = array(
  'label' => 'Project 3',
  'start' => '2012-05-25', 
  'end'   => '2012-06-20'
  'class' => 'urgent',
);

\$gantti = new Gantti(\$data, array(
  'title'      => 'Demo',
  'cellwidth'  => 25,
  'cellheight' => 35
));

echo \$gantti;

?>

";
echo htmlentities(trim($code)); ?>
</pre></code></p>
</article>
</body>
</html>

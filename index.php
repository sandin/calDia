<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>对角线校对</title>
<link media="all" rel="stylesheet" href="css/global.css" type="text/css" />
<link media="all" rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
<?php

$inputLoop = array(
    array(
        'className' => 'quoBox_top',
        'labelText' => '顶边 a',
        'inputId'   => 'quo_a',
        'inputName' => 'a',
        'value'     => ''
    ),
    array(
        'className' => 'quoBox_right',
        'labelText' => '右边 b',
        'inputId'   => 'quo_b',
        'inputName' => 'b',
        'value'     => ''
    ),
    array(
        'className' => 'quoBox_bottom',
        'labelText' => '底边 c',
        'inputId'   => 'quo_c',
        'inputName' => 'c',
        'value'     => ''
    ),
    array(
        'className' => 'quoBox_left',
        'labelText' => '左边 d',
        'inputId'   => 'quo_d',
        'inputName' => 'd',
        'value'     => ''
    ),
    array(
        'className' => 'quoBox_X',
        'labelText' => '对角线 X',
        'inputId'   => 'quo_X',
        'inputName' => 'X',
        'value'     => ''
    ),
    array(
        'className' => 'quoBox_Y',
        'labelText' => '对角线 Y',
        'inputId'   => 'quo_Y',
        'inputName' => 'Y',
        'value'     => ''
    )
);


?>

<div id="wrapper">

    <form id="quo" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
        <div class="quoBox">
<?php foreach ($inputLoop as $i) {
extract($i);
if (isset($_GET[$inputName])) {
    $value 
}

echo <<<HTML
            <div class="$className">
                <label for="$inputName">$labelText</label>
                <input id="$inputId" name="$inputName" value="$value" type="text" class="text" autocomplete="off" />
            </div>
HTML;
}
?>
        </div>

        <input type="submit" value="计算对角线" />
        <input type="reset" value="清空" />
    </form>

</div>
<body>
</body>
</html>

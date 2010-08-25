<?php
// 超时设定
set_time_limit(10);

include("./inc/CalibrateDiagonal.php");

// 高亮
function addColor ($if, $var) {
    if ( $if ) {
        return "<b><cite>" . $var . "</cite></b>";
    } else {
        return "<b><em>" . $var . "</em></b>";
    }
}

$error = '';

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


if (isset($_GET['a']) && isset($_GET['b']) && isset($_GET['c']) && isset($_GET['d']) && isset($_GET['X']) && isset($_GET['Y']) ) {
    $inputLoop[0]['value'] = $a = $_GET['a'];
    $inputLoop[1]['value'] = $b = $_GET['b'];
    $inputLoop[2]['value'] = $c = $_GET['c'];
    $inputLoop[3]['value'] = $d = $_GET['d'];

    $X = $_GET['X'];
    $Y = $_GET['Y'];

    $CalDia = new CalibrateDiagonal();
    $CalDia->setParam($a, $b, $c, $d, $X, $Y);
    $result = $CalDia->calibrate();
    //var_dump($result);

    if (empty($result) ) $error = '计算出错,结果为空';
    else if (true === $result) {
        $error = '无需计算,提供的数据即为最佳结果';
        $inputLoop[4]['value'] = $X; // X
        $inputLoop[5]['value'] = $Y; // Y
    }
    else if (is_array($result)) {
        $inputLoop[4]['value'] = $X = $result['X']; // X
        $inputLoop[5]['value'] = $Y = $result['Y']; // Y
    }

    // 求四个角的度数
    $anglesKey = array('angle_A', 'angle_B', 'angle_C', 'angle_D');
    $anglesValue = CalibrateDiagonal::getAngle($a, $b, $c, $d, $X, $Y);

    $angles = array_combine($anglesKey, $anglesValue);
    //var_dump($angles);
}

header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>对角线校对</title>
<link media="all" rel="stylesheet" href="css/global.css" type="text/css" />
<link media="all" rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>

<div id="wrapper">

<div class="calDia clearfix">

    <form id="quo" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="get">
        <div class="quoBox">
<?php foreach ($inputLoop as $i) {
extract($i);
echo <<<HTML
            <div class="$className">
                <label for="$inputName">$labelText</label>
                <input id="$inputId" name="$inputName" value="$value" type="text" class="text" autocomplete="off" />
            </div>

HTML;
}
if (isset($angles)) {
    foreach ($angles as $className => $angle) {
    echo <<<HTML
            <div class="$className angles">
                $angle&deg;
            </div>

HTML;
    }
}
?>
        </div>

        <br />
        <input type="submit" value="校准对角线" />
        <a href="index.php">复位</a>
    </form>

    <div class="resultBox">
        <div id="error"><?php echo $error ?></div>
<?php

if (isset($result)) {

    // 高亮误差较大的结果
    $status = addColor( (FALSE !== strstr($result['status'], 'OK')), $result['status'] );
    $diffX  = addColor( ($result['diff'][0] <= 5), $result['diff'][0] );
    $diffY  = addColor( ($result['diff'][1] <= 5), $result['diff'][1] );

echo <<<HTML
        <p><span>计算结果</span>: $status</p>
        <br />

        <p><span>对角线 X</span>: {$result['X']}</p>
        <p><span>差值</span>:  $diffX</p>
        <br />

        <p><span>对角线 Y</span>: {$result['Y']}</p>
        <p><span>差值</span>:  $diffY</p>
        <br />
HTML;
} else {
    echo "<p>请输入所有数值,然后点击 [校准对角线] </p><br />";
}

?>
        <span class="tinyColorBox" style="background:green;"></span>
        <small>: 误差值在 <b>0 ~ 5</b> 之间</small> &nbsp;&nbsp;&nbsp;
        <span class="tinyColorBox" style="background:red;"></span>
        <small>: 误差值超过 <b>5</b></small>

    </div> 

</div><!-- /calDia -->

</div><!-- /wrapper -->
<body>
</body>
</html>

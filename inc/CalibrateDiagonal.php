<?php

/**
 * CalibrateDiagonal 
 * 
 * @package 
 * @version $id$
 * @copyright 
 * @author LDS <lds2012@gmail.com> 
 * @license MIT license {@link http://www.opensource.org/licenses/mit-license.php}
 */
class CalibrateDiagonal
{

    /**
     * a 顶边
     * 
     * @var int
     * @access protected
     */
    protected $a;

    /**
     * b 右边 
     * 
     * @var int
     * @access protected
     */
    protected $b;

    /**
     * c 底边
     * 
     * @var int
     * @access protected
     */
    protected $c;

    /**
     * d 右边 
     * 
     * @var int
     * @access protected
     */
    protected $d;

    /**
     * X 从左上到右下对角线
     * 
     * @var int
     * @access protected
     */
    protected $X;

    /**
     * Y 从右上到左下对角线
     * 
     * @var mixed
     * @access protected
     */
    protected $Y;

    protected $loopCount = 0;

    public function __construct($a = 0, $b = 0, $c = 0, $d = 0, $X = 0, $Y = 0)
    {
        $this->setParam($a, $b, $c, $d, $X, $Y);
    }

    public function setParam($a, $b, $c, $d, $X, $Y)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
        $this->d = $d;
        $this->X = $X;
        $this->Y = $Y;
    }

    /**
     * verifyParam 
     * 验证参数
     * 
     * @access public
     * @return void
     */
    public function verifyParam()
    {
        // 三角形任意两边之和大于第三边
        return ($this->a + $this->b > $this->Y && $this->c + $this->d > $this->X) ? true : false;
    }

    /**
     * calibrate 校准对角线
     * 
     * @access public
     * @return mixed
     */
    public function calibrate()
    {
        if (!$this->verifyParam()) throw new Exception('参数错误'); 

        $loopCount = 0;

        if (0 !== $this->a && 0 !== $this->b && 
            0 !== $this->c && 0 !== $this->d &&
            0 !== $this->X && 0 !== $this->Y) {
            return $this->loopIt($this->a, $this->b, $this->c, $this->d, $this->X);
        } else {
            throw new Exception('未提供参数');
        }
    }
    
    /**
     * getCos
     * 知三边，求夹角cosA
     * 
     * @param mixed $a 邻边
     * @param mixed $b 邻边
     * @param mixed $c 对边 
     * @static
     * @access public
     * @return float
     */
    public static function getCos($a, $b, $c) 
    {
        bcscale(8);

        // (a^2 + b^2 - c^2) / (2*a*b)
        $a_2 = bcpow(strval($a),'2');
        $b_2 = bcpow(strval($b),'2');
        $c_2 = bcpow(strval($c),'2');
        $ab_2 = bcmul('2',  $a);
        $ab_2 = bcmul($ab_2, $b);

        $tmp = bcadd($a_2, $b_2);
        $tmp = bcsub($tmp, $c_2);
        $tmp = bcdiv($tmp, $ab_2);

        return floatval($tmp);
    }

    /**
     * cosToSin 
     * cos^2(x) + sin^2(x) = 1
     * 
     * @param mixed $cos 
     * @static
     * @access public
     * @return float
     */
    public static function cosToSin($cos) 
    {
        // 1 - $cos^2
        $a = bcsub( '1', bcpow(strval($cos), '2', 8), 8 );
        // 开根号2
        if ($a >= 0)
            return floatval(bcsqrt($a, 8));
    }

    /**
     * cosines 
     * 余弦定理 知两边及夹角,求对边
     * 
     * @param mixed $a 边长 
     * @param mixed $b 边长
     * @param mixed $cosA cos(夹角)
     * @static
     * @access public
     * @return float
     */
    public static function cosines($a, $b, $cosA)
    {
        bcscale(8);
        
        $a = strval($a);
        $b = strval($b);
        $cosA = strval($cosA);

        //  cosA = a^2 + b^2 - 2*a*b*cosA
        $a_2 = bcpow($a, '2');          // a^2
        $b_2 = bcpow($b, '2');          // b^2
        $ab2cosA = bcmul(2, $a);        // 2*a
        $ab2cosA = bcmul($b, $ab2cosA); // 2*a*b
        $ab2cosA = bcmul($ab2cosA, $cosA);  // 2*a*b*cosA

        $tmp = bcadd($a_2, $b_2, 8);     // a^2 + b^2
        $tmp = bcsub($tmp, $ab2cosA, 8); // a^2 + b^2 - 2*a*b*cosA
        if ($tmp > 0) {
            $result = bcsqrt($tmp);
            return floatval($result);
        } else {
            return false;
        }
    }

    /**
     * cosX_plus_Y 
     * 已知cosX和cosY,求cos(X+Y) 
     * 
     * @param mixed $cosX 
     * @param mixed $cosY 
     * @static
     * @access public
     * @return float
     */
    public static function cosX_plus_Y($cosX, $cosY)
    {
        $sinX = self::cosToSin($cosX);
        $sinY = self::cosToSin($cosY);
        
        // cosX * cosY
        $a = bcmul(strval($cosX), strval($cosY), 8);
        // sinX * sinY
        $b = bcmul(strval($sinX), strval($sinY), 8);

        // cos(X+Y) = cosX * cosY - sinX * sinY
        return floatval(bcsub($a, $b, 8));

    }

    /**
     * getB
     * 已知四边形四边及一条对角线，求另一个对角线 
     *
     * 
     * @param mixed $e 
     * @param mixed $f 
     * @param mixed $a 
     * @param mixed $c 
     * @param mixed $b 
     * @static
     * @access public
     * @return float
     */
    public static function getB($e, $f, $a, $c, $b)
    {

        // $e 顶边
        // $f 右边
        // $a 底边
        // $c 左边
        //
        // X 为 c,b 夹角
        // Y 为 e,b 夹角 
        //
        // $b 从左上到右下对角线
        // $B 从右上到左下对角线

        $cosX = self::getCos($c, $b, $a);
        $cosY = self::getCos($e, $b, $f);

        $cosXplusY = self::cosX_plus_Y($cosX, $cosY);

        // 知两边夹角求对边
        $B = self::cosines($e, $c, $cosXplusY );
        return $B;
    }

    /**
     * getRad 
     * 已知三边,求夹角(角度)
     * 
     * @param mixed $a 邻边
     * @param mixed $b 邻边
     * @param mixed $c 对边
     * @static
     * @access public
     * @return float
     */
    public static function getRad($a, $b, $c) {
        $cos = self::getCos($a, $b, $c);
        return rad2deg(acos($cos));
    }

    /**
     * getAngle 
     * 只四角形四边和两对角线,求四角(角度)
     * 
     * @param mixed $a 
     * @param mixed $b 
     * @param mixed $c 
     * @param mixed $d 
     * @param mixed $X 
     * @param mixed $Y 
     * @static
     * @access public
     * @return array
     */
    public static function getAngle($a, $b, $c, $d, $X, $Y) {
        // 右上角
        $angleA = self::getRad($a, $d, $Y);
        // 左上角
        $angleB = self::getRad($a, $b, $X);
        // 左下角
        $angleC = self::getRad($b, $c, $Y);

        bcscale(8);
        $angleD = bcsub('360', $angleA);
        $angleD = bcsub($angleD, $angleB); 
        $angleD = bcsub($angleD, $angleC); 
        $angleD = floatval($angleD);

        return array($angleA, $angleB, $angleC, $angleD);
    }

    /**
     * loopIt 
     * 递归寻找最优的一组对角线
     * 
     * @param mixed $a 
     * @param mixed $b 
     * @param mixed $c 
     * @param mixed $d 
     * @param mixed $X 
     * @access public
     * @return mixed
     */
    public function loopIt( $a, $b, $c, $d, $X )
    {
        // 如果递归超过5次,则扩大差值范围
        if ($this->loopCount++ > 5) $e = 6;
        else if ($this->loopCount > 50) {
            throw new Exception('递归超时,请检查输入数据');
        }
        else $e = 5;

        // 计算出的对角线长度
        $B = self::getB($a, $b, $c, $d, $X);
        // 计算出的对角线和期望对角线的差值
        $diffY = abs( $this->Y - $B );
        $diffX = abs( $this->X - $X );
        //var_dump('Y',$diffY);
        //var_dump('X',$X - $this->X);

        // 如果差值小于5,则停止递归,返回一组对角线
        if ( $diffY <= $e && $diffX <= $e ) {
            $this->loopCount = 0;
            return array(floatval($X), floatval($B));
        }
        // 如果计算出的对角线 大于 期望对角线
        else if ($B > $this->Y) $X++; 
        // 如果计算出的对角线 小于 期望对角线
        else if ($B < $this->Y) $X--; 

        return $this->loopIt( $a, $b, $c, $d, $X );
    }

}


?>

<?php
/**
 * Created by PhpStorm.
 * User: Sean
 * Date: 2018/5/15
 * Time: 17:45
 */

/**
 * Normal random variate generator
 * @param $m
 * @param $s
 * @return float|int
 */
function box_muller_polar($m, $s)
{
    static $y2;
    static $use_last = 0;

    if ($use_last) {
        $y1 = $y2;
        $use_last = 0;
    } else {
        do {
            $range = mt_getrandmax();
            $x1 = 2.0 * mt_rand(1, $range) / $range - 1.0;
            $x2 = 2.0 * mt_rand(1, $range) / $range - 1.0;
            $w = $x1 * $x1 + $x2 * $x2;
        } while ($w >= 1.0);

        $w = sqrt((-2.0 * log($w)) / $w);
        $y1 = $x1 * $w;
        $y2 = $x2 * $w;
        $use_last = 1;
    }
    return ($m + $y1 * $s);
}

/**
 * Wechat Red Packet Pre-generate
 * @param $sum_mon
 * @param $pics
 * @param int $min
 * @param int $max
 * @param int $sigma
 * @return array
 */
function generateMoney($sum_mon, $pics, $min = 1, $max = 10, $sigma = 1)
{
    if ($sum_mon < $pics * 1) {
        echo "NOT ENOUGH MONEY!";
        exit();
    } else {
        if ($pics == 1) {
            return [$sum_mon];
        }
        $valueArr = [];
        $moneyLeft = ($sum_mon - $pics * $min);
        $mean = round(($moneyLeft / $pics), 2);
        for ($i = 0; $i < $pics - 1; $i++) {
            $picValue = round(box_muller_polar($mean, $sigma), 2);
            if ($picValue - $min < 0) {
                $picValue = $min;
            }
            if ($picValue > $max - $min) {
                $picValue = $max - $min;
            }
            if ($picValue > $moneyLeft) {
                $picValue = $moneyLeft;
            }
            array_push($valueArr, $picValue + $min);
            $moneyLeft -= $picValue;
        }
        $lastPic = round($sum_mon - array_sum($valueArr), 2);
        if ($lastPic <= $max) {
            array_push($valueArr, round($sum_mon - array_sum($valueArr), 2));
        } else {
            $j = 0;
            do {
                if (($valueArr[$j] + 0.1) < $max) {
                    $valueArr[$j] += 0.1;
                    $lastPic -= 0.1;
                }
                $j++;
            } while ($lastPic > $max);
            array_push($valueArr, $lastPic);
        }
        shuffle($valueArr);
        return $valueArr;
    }
}

$n_all = generateMoney(25000, 5000);

foreach ($n_all as $key => $value) {
    echo round($value, 2).PHP_EOL;
}

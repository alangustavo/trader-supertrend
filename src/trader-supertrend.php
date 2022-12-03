<?php

namespace Alangustavo\TraderSupertrend;

$extension = "trader";
$php_ini = php_ini_loaded_file();
$not_load = <<<EOF
To use the trader-supertrend extension you need to enable the php trader extension in your php.ini.
To do this, open the file $php_ini find and change the line with:
;extension=php_trader 
to
extension=php_trader 
# remove ; to uncomment
EOF;
if (!extension_loaded($extension)) {
    die($not_load);
}
/**
 * Input Parametres
 * @param array $high - High price, array of real values.
 * @param array $low - Low price, array of real values.
 * @param array $close - Closing price, array of real values.
 * @param integer $timePeriod - Number of period. Valid range from 2 to 100000.
 * @param integer $multiplier
 * @return array
 */
function trader_supertrend(
    array $high,
    array $low,
    array $close,
    int $timePeriod,
    int $multiplier
) {
    if (count($high) <= $timePeriod) {
        throw new Exception("The number of elements in the input arrays must be greater than the timePeriod", 1);
    }
    $previous_final_upperband = 0;
    $previous_final_lowerband = 0;
    $final_upperband = 0;
    $final_lowerband = 0;
    $previous_close = 0;
    $previous_supertrend = 0;
    $supertrend = [];
    $supertrendc = 0;

    $history = [];

    $trader_atr = trader_atr($high, $low, $close, $timePeriod);
    // var_dump($trader_atr);
    // die();
    foreach ($trader_atr as $index => $atr) {
        if (is_nan($atr)) $atr = 0;
        $closec = $close[$index];
        $basic_upperband = ($high[$index] + $low[$index]) / 2 + $multiplier * $atr;
        $basic_lowerband = ($high[$index] + $low[$index]) / 2 - $multiplier * $atr;

        if (($basic_upperband < $previous_final_upperband) or ($previous_close > $previous_final_upperband)) {
            $final_upperband = $basic_upperband;
        } else {
            $final_upperband = $previous_final_upperband;
        }
        if ($basic_lowerband > $previous_final_lowerband or $previous_close < $previous_final_lowerband) {
            $final_lowerband = $basic_lowerband;
        } else {
            $final_lowerband = $previous_final_lowerband;
        }

        if ($previous_supertrend == $previous_final_upperband and $closec <= $final_upperband) {
            $supertrendc = $final_upperband;
        } else {
            if ($previous_supertrend == $previous_final_upperband and $closec >= $final_upperband) {
                $supertrendc = $final_lowerband;
            } else {
                if ($previous_supertrend == $previous_final_lowerband and $closec >= $final_lowerband) {
                    $supertrendc = $final_lowerband;
                } elseif ($previous_supertrend == $previous_final_lowerband and $closec <= $final_lowerband) {
                    $supertrendc = $final_upperband;
                }
            }
        }

        $supertrend[$index]       = [
            'close' => $supertrendc,
            'type'  => ($supertrendc == $final_upperband ? '-1' : '1')
        ];

        $previous_close           = $closec;
        $previous_final_upperband = $final_upperband;
        $previous_final_lowerband = $final_lowerband;
        $previous_supertrend      = $supertrendc;
    }

    return $supertrend;
}

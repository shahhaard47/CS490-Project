<?php

/*function constructQuestions(&$rawArr) {
    // $outputarr = array();
    // foreach ($rawArr as $que) {
    for ($a = 0; $a < count($rawArr); $a++) {
        $que = $rawArr[$a];
        $funcName = $rawArr[$a]["functionName"];
        $params = $rawArr[$a]["params"]; // array

        $outparams = "";
        if (count($params) == 1) {
            $ptmp = $params[0];
            $outparams = "parameter <$ptmp>";
        }
        elseif (count($params) == 2) {
            $ptmp0 = $params[0]; $ptmp1 = $params[1];
            $outparams = "parameters <$ptmp0> and <$ptmp1>";
        }
        else {
            $outparams .= "parameters ";
            for ($i = 0; $i < count($params)-2; $i++) {
                $outparams .= "<$params[$i]>, ";
            }
            $outparams .= "<$params[$i]> and "; $i++;
            $outparams .= "<$params[$i]>";
        }
        $does = $rawArr[$a]["functionDescription"];
        $prints = $rawArr[$a]["output"];
        $tmp = "Write a function named \"$funcName\" that takes $outparams, $does and prints $prints.";

        // add constructed question attribute
        $rawArr[$a]["constructed"] = $tmp;
    }
}*/

function constructQuestions(&$rawArr) {
    // $outputarr = array();
    // foreach ($rawArr as $que) {
    for ($a = 0; $a < count($rawArr); $a++) {
        $que = $rawArr[$a];
        $funcName = $rawArr[$a]["functionName"];
        $params = $rawArr[$a]["params"]; // array
        $outparams = "";
        if (count($params) == 1) {
            $outparams = "parameter <$params[0]>";
        }
        elseif (count($params) == 2) {
            $outparams = "parameters <$params[0]> and <$params[1]>";
        }
        else {
            $outparams .= "parameters ";
            for ($i = 0; $i < count($params)-2; $i++) {
                $outparams .= "<$params[$i]>, ";
            }
            $outparams .= "<$params[$i]> and "; $i++;
            $outparams .= "<$params[$i]>";
        }
        $does = $rawArr[$a]["does"];
        $prints = $rawArr[$a]["prints"];
        $tmp = "Write a function named \"$funcName\" that takes $outparams, $does and returns $prints.";
        // add constructed question attribute
        $rawArr[$a]["constructed"] = $tmp;
    }
    // return $outputarr;
}

?>
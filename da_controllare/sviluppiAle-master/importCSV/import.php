<?php
/**
 * Created by PhpStorm.
 * User: alessandro
 * Date: 09/01/18
 * Time: 12.09
 */

function csv_to_array($filename='', $delimiter=';')
{
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;

    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        fgetcsv($handle);
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            $data[] = $row;
        }
        fclose($handle);
    }
    return $data;
}
/**
 * Example
 */
print_r(csv_to_array('test.csv'));
?>
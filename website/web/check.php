<?php
    //echo "check.php!!!!!!";
    error_reporting(E_ALL ^ E_DEPRECATED);
    $mac_client = $_GET["mac_addr"];
    $con = mysql_connect("localhost", "root", "root");
    if (!$con)
    {
    die("Could not connect: " . mysql_error());
    }
    mysql_select_db("wms98", $con);
    $result = mysql_query("select * from t_mac_addr");
    $found = 0;
    $count = 0;
    while($row = mysql_fetch_array($result))
    {
    $count ++;
    if ($row["mac_addr"] == $mac_client) {
    $found = 1;
    echo json_encode(array("ret_val" => "1"));
    break;
    }
    }

    if ($found == 0)
    {
    if ($count < 3)
    {
    //$result = mysql_query("insert into t_mac_addr(mac_addr) values('" . $mac_client . "')");
    echo json_encode(array("ret_val" => "2"));
    }
    else
    {
    echo json_encode(array("ret_val" => "3"));
    }
    }

    mysql_close($con);
?>
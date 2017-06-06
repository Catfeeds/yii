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

    $result = mysql_query("insert into t_mac_addr(mac_addr) values('" . $mac_client . "')");
    echo json_encode(array("ret_val" => "2"));

    mysql_close($con);
?>
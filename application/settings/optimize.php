<?php
// Create connection
        $con=mysqli_connect("localhost","wffbnbcg_45drf","z5^2Xw0qk(lX","wffbnbcg_45drf");

// Check connection
        if (mysqli_connect_errno($con))
        {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }    

    $sql = "TRUNCATE TABLE engine4_core_referrers";
    $sql1 = "TRUNCATE TABLE engine4_user_online";
    $sql2 = "TRUNCATE TABLE engine4_core_session";
    $sql3 = "TRUNCATE TABLE engine4_user_logins";
    $sql4 = "TRUNCATE TABLE engine4_chat_events";
    $sql5 = "TRUNCATE TABLE engine4_activity_notifications";
    $sql6 = "TRUNCATE TABLE engine4_credit_logs";
    $sql7 = "TRUNCATE TABLE cometchat";
    $sql8 = "TRUNCATE TABLE engine4_core_mail";
    $sql9 = "TRUNCATE TABLE engine4_core_mailrecipients";
    mysqli_query($con, $sql);
    mysqli_query($con, $sql1);
    mysqli_query($con, $sql2);
    mysqli_query($con, $sql3);
    mysqli_query($con, $sql4);
    mysqli_query($con, $sql5);
    mysqli_query($con, $sql6);
    mysqli_query($con, $sql7);
    mysqli_query($con, $sql8);
?>
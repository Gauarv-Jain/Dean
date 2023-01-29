<?php require_once 'includes/header.php' ?>
<?php
   
    $data = json_decode(file_get_contents("php://input"), true);
    
    $stu_id  = $data['stu_id'];
    $sem  = $data['sem'];
    $sub_code  = $data['sub_code'];
    $type  = $data['type'];
    $m1  = $data['m1'];
    $m2  = $data['m2'];
    $m3  = $data['m3'];

    $sql = "INSERT INTO result (stu_id, sem, sub_code, type, m1, m2, m3) VALUES ($stu_id, $sem, $sub_code, '$type', $m1, $m2, $m3)";
    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("'.$sql.'")</script>';
        echo "New record created successfully";
    }else {
        echo '<script>alert("some shit went wrong VERY WRONG")</script>';
        //echo '<script>alert("Error: '. $conn->error .'")</script>';
        exit();
    }
?>
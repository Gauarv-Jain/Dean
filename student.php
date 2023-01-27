<?php
    require_once 'includes/header.php'
?>

<!-- inilitize -->
<?php 
    $stu_id = intval($_GET['stu_id']);

    $sql = "SELECT * FROM student WHERE stu_id = $stu_id";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);
    if ($rowCount > 0) {
        while ($row = mysqli_fetch_assoc($result)) {          
            $name = $row['name'];
            $program = $row['program'];
        }
    } else {
        echo "No results found for the student.";
    }

    $sql = "SELECT * FROM admin";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);
    if ($rowCount > 0) {
        while ($row = mysqli_fetch_assoc($result)) {          
            $sem = $row['curr_sem'];
            $cource_bool = $row['course_entry'];
            $grade_bool = $row['grade_entry'];
        }
    } else {
        echo "No results found for admin.";
    }

?>

<link rel="stylesheet" href="student.css">

<h2>Student portal</h2>
<h2>Current Sem = <?php echo "$sem" ?></h2>

<!-- transcript -->
<h2>Student transcript</h2>
<?php 
    echo "
    <table>
    <h2> SEM : 1 </h2>
    <tr>
        <th>Sem</th>
        <th>Subject Code</th>
        <th>Type</th>
        <th>Grade</th>
    </tr>
    ";

    $sql = "SELECT * FROM result WHERE stu_id = $stu_id ORDER BY sem";

    $result = $conn->query($sql);

    $prev_sem=1;

    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            $m1 = $row['m1'];
            $m2 = $row['m2'];
            $m3 = $row['m3'];
            $sum = $m1 + $m2 + $m3;

            if(85<=$sum && $sum<=100){
                $grade = "A+";
            }else if(75<=$sum && $sum<85){
                $grade = "A";
            }else if(65<=$sum && $sum<75){
                $grade = "B+";
            }else if(55<=$sum && $sum<65){
                $grade = "B";
            }else if(45<=$sum && $sum<55){
                $grade = "C";
            }else if(30<=$sum && $sum<45){
                $grade = "D";
            }else if(15<=$sum && $sum<30){
                $grade = "E";
            }else{
                $grade = "F";
            }

            if($prev_sem!=$row['sem']){
                $prev_sem=$row['sem'];
                echo "
                </table>
                <table>
                <h2> SEM : $prev_sem </h2>
                <tr>
                    <th>Sem</th>
                    <th>Subject Code</th>
                    <th>Type</th>
                    <th>Grade</th>
                </tr>
                ";
            }
            echo "<tr><td>".$row['sem'] . "</td><td>" . $row['sub_code'] . "</td><td>" . $row['type'] . "</td><td>" . $grade . "</td></tr>" ;
        }
    }
    echo "</table>";
?>

<!-- the cources the student is enrolled in  -->
<h2 class="mt-2">Cources Enrolled IN</h2>
<table>
    <tr>
        <th>Subject Name</th>
        <th>Subject Code</th>
        <th>Type</th>
        <th>Prof_id</th>
    </tr>
    <?php 
    $sql = "SELECT * FROM prof WHERE program='$program'";
    $result = $conn->query($sql);

    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            echo "<tr><td>".$row['sub_name'] . "</td><td>" . $row['sub_code'] . "</td><td>" . $row['type'] . "</td><td>" . $row['program'] . "</td></tr>" ;
        }
    }
    ?>
</table>

<?php
    require_once 'includes/footer.php'
?>
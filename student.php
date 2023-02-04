<?php require_once 'includes/header.php' ?>

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
<div class="rounded outline m-6">
    <div class="grid grid-cols-2 p-6">
        <?php 
            echo '
            <div>
                <div class="text-xl font-bold text-center"> SEM : 1 </div>
                <table class="rounded">
                    <tr>
                        <th>Subject Code</th>
                        <th>Type</th>
                        <th>Grade</th>
                    </tr>
            ';

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

                        $sqql = "SELECT * FROM stu_spi WHERE stu_id =$stu_id AND sem=$prev_sem";
                        $rus = $conn->query($sqql);
                        if($rus->num_rows>0){
                            while($rowr = $rus->fetch_assoc()){
                                $spiii = $rowr['spi'];
                            } 
                        }

                        $prev_sem=$row['sem'];
                        if($prev_sem != $sem){
                            echo "
                            </table>
                            <div class='text-xl font-bold text-center'>SPI : $spiii</div>
                            </div>
                            <div>
                            <div class='text-xl font-bold text-center'> SEM : $prev_sem </div>
                            <table>
                            <tr>
                                <th>Subject Code</th>
                                <th>Type</th>
                                <th>Grade</th>
                            </tr>
                            ";
                        }
                    }

                    if($prev_sem != $sem){
                        echo "<tr><td>" . $row['sub_code'] . "</td><td>" . $row['type'] . "</td><td>" . $grade . "</td></tr>" ;
                    }
                }
            }

            $sqqql = "SELECT * FROM stu_spi WHERE stu_id =$stu_id AND sem=$prev_sem";
            $ruus = $conn->query($sqqql);
            if($ruus->num_rows>0){
                while($rowwr = $ruus->fetch_assoc()){
                    $spiiii = $rowwr['spi'];
                } 
            }else{
                $prev_sem-=1;
                $sqqqll = "SELECT * FROM stu_spi WHERE stu_id =$stu_id AND sem=$prev_sem";
                $ruuss = $conn->query($sqqqll);
                if($ruuss->num_rows>0){
                    while($roowwr = $ruuss->fetch_assoc()){
                        $spiiii = $roowwr['spi'];
                    } 
                }
            }

            echo "</table> 
            <div class='text-xl font-bold text-center'>SPI : $spiiii</div>
            </div>";
        ?>
    </div>

    <!-- CPI div -->
    <?php
        $sl = "SELECT * FROM stu_cpi WHERE stu_id =$stu_id ";
        $rs = $conn->query($sl);
        if($rs->num_rows>0){
            while($rr = $rs->fetch_assoc()){
                $cppi = $rr['cpi'];
            } 
        }
        echo "<div class='text-4xl font-bold text-center'>CPI : $cppi</div>";
    ?>
</div>

<!-- the cources the student is enrolled in  -->
<h2 class="mt-2">Cources Enrolled IN</h2>
<table class="rounded">
    <tr>
        <th>Subject Name</th>
        <th>Subject Code</th>
        <th>Type</th>
        <th>Prof_id</th>
    </tr>
    <?php 
    $sql = "SELECT * FROM prof WHERE program='$program' AND sem=$sem";
    $result = $conn->query($sql);

    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            echo "<tr><td>".$row['sub_name'] . "</td><td>" . $row['sub_code'] . "</td><td>" . $row['type'] . "</td><td>" . $row['prof_id'] . "</td></tr>" ;
        }
    }
    ?>
</table>

<?php require_once 'includes/footer.php' ?>
<?php
    require_once 'includes/header.php'
?>

<!-- initilize -->
<?php 
    $prof_id = intval($_GET['prof_id']);

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
        echo "No results found.";
    }

?>

<link rel="stylesheet" href="employee.css">

<h2>Employee portal</h2>
<h2>Current Sem = <?php echo "$sem" ?></h2>

<!-- button click handeler -->
<?php

     //subject addition
    if(isset($_POST['subbut'])) {
        $subname = $_POST['ssubname'];
        $subcode = $_POST['ssubcode'];
        if(isset($_POST['sprogram'])){
            $program = $_POST['sprogram'];
        }
        if(isset($_POST['stype'])){
            $type = $_POST['stype'];
        }

        $m1 = $_POST['sm1'];
        $m2 = $_POST['sm2'];
        $m3 = $_POST['sm3'];
        $sum = $m1 + $m2 + $m3;

        if(empty($subname) || empty($subcode) || empty($program) || empty($type) || empty($m1) || empty($m2)) {
            echo '<script>alert("Pls fill all the fields")</script>';
            exit();
        }
        else if($type == "Theory" && empty($m3)){
            echo '<script>alert("Pls fill all the fields")</script>';
            exit();
        }else if($sum>100){
            echo '<script>alert("The values in the fields add up to greater than 100")</script>';
            exit();
        }
        else{

            $sql1 = "INSERT INTO `prof` (prof_id, sub_name, sub_code, program, type) VALUES ($prof_id, ?, $subcode, '$program', '$type');";
            if($type == "Theory"){
                $sql2 = "INSERT INTO `subject_theory` (sub_code, m1, m2, m3) VALUES ($subcode, $m1, $m2, $m3);";
            }else{
                $sql2 = "INSERT INTO `subject_lab` (sub_code, m1, m2) VALUES ($subcode, $m1, $m2);";
            }

            //inserting into the prof table
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql1)){
                echo '<script>alert("SQL error")</script>';
                exit();
            }else {
                mysqli_stmt_bind_param($stmt, "s", $subname);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
            }

            //inserting in the subject table
            if (mysqli_query($conn, $sql2)) {
                echo '<script>alert("Subject added")</script>';
                exit();
            } else {
                //echo "Error updating record: " . mysqli_error($conn);
                $error = mysqli_error($conn);
                echo "<script>alert(Error updating record:" . $error .")</script>";
                exit();
            }

        }
    }

    //grade subbmition
    if(isset($_POST['grabut'])) {
        $stu_id = $_POST['stu_id'];
        $subcode = $_POST['subcode'];
        if(isset($_POST['type'])){
            $type = $_POST['type'];
        }
        $m1 = $_POST['m1'];
        $m2 = $_POST['m2'];
        $m3 = $_POST['m3'];

        if(empty($stu_id) || empty($subcode) || empty($type) || empty($m1) || empty($m2)) {
            echo '<script>alert("Pls fill all the fields")</script>';
            exit();
        }
        else if($type == "Theory" && empty($m3)){
            echo '<script>alert("Pls fill all the fields")</script>';
            exit();
        }

        //checking if the student exists or not
        $sq1 = "SELECT * FROM student WHERE stu_id = $stu_id";
        $result = mysqli_query($conn, $sq1);
        $rowCount = mysqli_num_rows($result);
        if($rowCount == 0){
            echo '<script>alert("No such student exists")</script>';
            exit();
        }

        //checking if the subject exists and if it does make a query to inser the result 
        if($type == "theory"){
            $sq2 = "SELECT * FROM subject_theory WHERE sub_code = $subcode";
            $result = mysqli_query($conn, $sq2);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount > 0) {
                while ($row = mysqli_fetch_assoc($result)) {          
                    $mm1 = $row['m1'];
                    $mm2 = $row['m2'];
                    $mm3 = $row['m3'];
                    if($m1>$mm1 || $m2>$mm2 || $m3>$mm3 || $m1<0 || $m2<0 || $m3<0) {
                        echo '<script>alert("The marks do not follow the predefined schema")</script>';
                        exit();
                    }
                }
            } else {
                echo '<script>alert("No Such subject exists")</script>';
                exit();
            }

            $sql1 = "INSERT INTO `result` (stu_id, sem, sub_code, type, m1, m2, m3) VALUES ($stu_id, $sem, $subcode, '$type', $m1, $m2, $m3);";
        }else{
            $sq2 = "SELECT * FROM subject_lab WHERE sub_code = $subcode";
            $result = mysqli_query($conn, $sq2);
            $rowCount = mysqli_num_rows($result);
            if ($rowCount > 0) {
                while ($row = mysqli_fetch_assoc($result)) {          
                    $mm1 = $row['m1'];
                    $mm2 = $row['m2'];
                    if($m1>$mm1 || $m2>$mm2  || $m1<0 || $m2<0 ) {
                        echo '<script>alert("The marks do not follow the predefined schema")</script>';
                        exit();
                    }
                }
            } else {
                echo '<script>alert("No Such subject exists !!!!!!!!!!!!!")</script>';
                exit();
            }

            $sql1 = "INSERT INTO `result` (stu_id, sem, sub_code, type, m1, m2) VALUES ($stu_id, $sem, $subcode, '$type', $m1, $m2);";
        }

        //inserting in the subject table
        if (mysqli_query($conn, $sql1)) {
            echo '<script>alert("Grade added")</script>';
        } else {
            $error = mysqli_error($conn);
            echo "<script>alert(Error updating record:" . $error .")</script>";
            exit();
        }
    }
?>

<?php if($cource_bool=="true"){echo '
<form class="flex items-center gap-2" method="post">
    <label class="self-center" for="subname">Subject name:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="text" id="subname" name="ssubname" value="">
    <label class="self-center" for="subcode">Sublect code:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="subcode" name="ssubcode" value="">
    
    <div>
        <div>
            <input type="radio" id="B.Tech" name="sprogram" value="B.Tech" checked>
            <label class="self-center" for="B.Tech">B.Tech</label>
        </div>
        <div>
            <input type="radio" id="M.Tech" name="sprogram" value="M.Tech">
            <label class="self-center" for="M.Tech">M.Tech</label>
        </div>
        <div>
            <input type="radio" id="MCA" name="sprogram" value="MCA">
            <label class="self-center" for="MCA">MCA</label>
        </div>
        <div>
            <input type="radio" id="PhD" name="sprogram" value="PhD">
            <label class="self-center" for="PhD">PhD</label><br>
        </div>
    </div>

    <div>
        <div>
            <input type="radio" id="Lab" name="stype" value="lab" checked>
            <label class="self-center" for="Lab">Lab</label>
        </div>
        <div>
            <input type="radio" id="Theory" name="stype" value="theory">
            <label class="self-center" for="Theory">Theory</label><br>
        </div>
    </div>

    <label class="self-center" for="m1">M1:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="m1" name="sm1" value="">
    <label class="self-center" for="m2">M2:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="m2" name="sm2" value="">
    <label class="self-center" for="m3">M3:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="m3" name="sm3" value="">

    <input class="rounded" type="submit" name="subbut" value="Submit">
</form>
';} ?>

<?php if($grade_bool=="true"){echo '
<form class="flex items-center gap-2" method="post">
    <label class="self-center" for="stu_id">Student ID:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="stu_id" name="stu_id" value=""><br>
    <label class="self-center" for="subcode">Sublect code:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="subcode" name="subcode" value=""><br><br>

    <div class="flex flex-col justify-center content-center">
        <div>
            <input type="radio" id="Lab" name="type" value="lab">
            <label class="self-center rounded h-10" for="Lab">Lab</label>
        </div>
        <div>
            <input type="radio" id="Theory" name="type" value="theory">
            <label class="self-center rounded h-10" for="Theory">Theory</label><br>
        </div>
    </div>

    <label class="self-center" for="m1">M1:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="m1" name="m1" value=""><br><br>
    <label class="self-center" for="m2">M2:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="m2" name="m2" value=""><br><br>
    <label class="self-center" for="m3">M3:</label>
    <input class="bg-gray-200 rounded h-10 hover:outline " type="number" id="m3" name="m3" value=""><br><br>

    <input class="self-center rounded" type="submit" name="grabut" value="Submit">
</form>
';}?>

<!-- list of cources/subjects the prof has  -->
<h2>Course list</h2>
<table>
    <tr>
        <th>Subject Name</th>
        <th>Subject Code</th>
        <th>Program</th>
        <th>Type</th>
    </tr>
    <?php 
    $sql = "SELECT * FROM prof WHERE prof_id=$prof_id";
    $result = $conn->query($sql);

    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            echo "<tr><td>".$row['sub_name'] . "</td><td>" . $row['sub_code'] . "</td><td>" . $row['program'] . "</td><td>" . $row['type'] . "</td></tr>" ;
        }
    }
    ?>
</table>

<?php
    require_once 'includes/footer.php'
?>

<?php require_once 'includes/header.php' ?>

<!-- initilizing values  -->
<?php 
    $sql = "SELECT * FROM admin";
    $result = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($result);
    if ($rowCount > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            
            $currentsem = $row['curr_sem'];
            $cource_bool = $row['course_entry'];
            if($row['course_entry']=="true"){
                $cource_entry = "Stop Cource Entry";
            }else{
                $cource_entry = "Start Cource Entry";
            }
            
            $grade_bool = $row['grade_entry'];
            if($row['grade_entry']=="true"){
                $grade_entry = "Stop Grade Entry";
            }else{
                $grade_entry = "Start Grade Entry";
            }
        }
    } else {
        echo "No results found.";
    }
?>

<!-- button click handeler -->
<?php
     //sem change
     if(isset($_POST['sembut'])) {
        $semnum = $_POST['sem'];

        if($semnum == null){
            echo '<script>alert("No!!!, please write a sem num")</script>';
            exit();
        }else if($semnum<1 || $semnum>8){
            echo '<script>alert("sem can only be between 1 and 8")</script>';
            exit();
        }else{

            //SPI-----------------------------------------------------------------------------
            $sq1 = "SELECT * FROM student";
            $result1 = mysqli_query($conn, $sq1);
            $rowCount1 = mysqli_num_rows($result1);
            if ($rowCount1 > 0) {
                while ($row1 = mysqli_fetch_assoc($result1)) {          
                    $stu_id = $row1['stu_id'];

                    $sq2 = "SELECT * FROM result WHERE stu_id = $stu_id AND sem = $currentsem";
                    $result2 = mysqli_query($conn, $sq2);
                    $rowCount2 = mysqli_num_rows($result2);
                    if ($rowCount2 > 0) {
                        $sum=0;
                        while ($row2 = mysqli_fetch_assoc($result2)) { 
                            $suum = $row2['m1'] + $row2['m2'];
                            if($row2['m3']){
                                $suum += $row2['m3'];
                            }

                            if(85<=$suum && $suum<=100){
                                $myltipl = 10;
                            }else if(75<=$suum && $suum<85){
                                $myltipl = 9;
                            }else if(65<=$suum && $suum<75){
                                $myltipl = 8;
                            }else if(55<=$suum && $suum<65){
                                $myltipl = 7;
                            }else if(45<=$suum && $suum<55){
                                $myltipl = 6;
                            }else if(30<=$suum && $suum<45){
                                $myltipl = 4;
                            }else if(15<=$suum && $suum<30){
                                $myltipl = 2;
                            }else{
                                $myltipl = 0;
                            }

                            $sum += $myltipl;
                        }
                        $spi = $sum/$rowCount2;

                        $sq3 = "INSERT INTO stu_spi (stu_id, sem, spi) VALUES ($stu_id, $currentsem, $spi) ON DUPLICATE KEY UPDATE spi=$spi;";
                        if (mysqli_query($conn, $sq3)) {
                        } else {
                            $error = mysqli_error($conn);
                            echo "<script>alert(Error updating record:  $error)</script>";
                            exit();
                        }
                    }

                }
            } else {
                echo "No results found for the student.";
            }

            //CPI--------------------------------------------------------------
            $sql1 = "SELECT * FROM student";
            $resultl1 = mysqli_query($conn, $sql1);
            $rowCountl1 = mysqli_num_rows($resultl1);
            if ($rowCountl1 > 0) {
                while ($rowl1 = mysqli_fetch_assoc($resultl1)) {          
                    $stu_id = $rowl1['stu_id'];

                    $sql2 = "SELECT * FROM result WHERE stu_id = $stu_id";
                    $resultl2 = mysqli_query($conn, $sql2);
                    $rowCountl2 = mysqli_num_rows($resultl2);
                    if ($rowCountl2 > 0) {
                        $sum=0;
                        while ($rowl2 = mysqli_fetch_assoc($resultl2)) { 
                            $suum = $rowl2['m1'] + $rowl2['m2'];
                            if($rowl2['m3']){
                                $suum += $rowl2['m3'];
                            }

                            if(85<=$suum && $suum<=100){
                                $myltipl = 10;
                            }else if(75<=$suum && $suum<85){
                                $myltipl = 9;
                            }else if(65<=$suum && $suum<75){
                                $myltipl = 8;
                            }else if(55<=$suum && $suum<65){
                                $myltipl = 7;
                            }else if(45<=$suum && $suum<55){
                                $myltipl = 6;
                            }else if(30<=$suum && $suum<45){
                                $myltipl = 4;
                            }else if(15<=$suum && $suum<30){
                                $myltipl = 2;
                            }else{
                                $myltipl = 0;
                            }

                            $sum += $myltipl;
                        }
                        $cpi = $sum/$rowCountl2;

                        $sql3 = "INSERT INTO stu_cpi (stu_id, cpi) VALUES ($stu_id, $cpi) ON DUPLICATE KEY UPDATE cpi=$cpi;";
                        if (mysqli_query($conn, $sql3)) {
                        } else {
                            $error = mysqli_error($conn);
                            echo "<script>alert(Error updating record:  $error)</script>";
                            exit();
                        }
                    }

                }
            } else {
                echo "No results found for the student.";
            }


            //change the SEMESTER
            $sql = "UPDATE `admin` SET curr_sem = $semnum;";
            if (mysqli_query($conn, $sql)) {
                echo '<script>alert("Semester updated successfully")</script>';
                exit();
            } else {
                $error = mysqli_error($conn);
                echo "<script>alert(Error updating record:  $error)</script>";
                exit();
            }
        }
    }

    //cource toggel
    if(isset($_POST['courcebut'])) {
        if($cource_bool == "true"){
            $toup = 'false';
        }else {
            $toup = 'true';
        }
        $sql = "UPDATE `admin` SET course_entry = '$toup';";
        if (mysqli_query($conn, $sql)) {
            if($cource_bool){
                echo '<script>alert("cource entry stopped ")</script>';
                header("Refresh:0");
                exit();
            }else{
                echo '<script>alert("cource entry started ")</script>';
                header("Refresh:0");
                exit();
            }
            
        } else {
            $error = mysqli_error($conn);
            echo "<script>alert(Error updating record:  $error)</script>";
            exit();
        }
    }

    //grade toggel
    if(isset($_POST['gradebut'])) {
        if($grade_bool == "true"){
            $toup = 'false';
        }else {
            $toup = 'true';
        }
        $sql = "UPDATE `admin` SET grade_entry = '$toup';";
        if (mysqli_query($conn, $sql)) {
            if($cource_bool){
                echo '<script>alert("Grade entry stopped ")</script>';
                header("Refresh:0");
                exit();
            }else{
                echo '<script>alert("Grade entry started ")</script>';
                header("Refresh:0");
                exit();
            }
            
        } else {
            $error = mysqli_error($conn);
            echo "<script>alert(Error updating record:  $error)</script>";
            exit();
        }
    }
?>

<link rel="stylesheet" href="admin.css">

<h2>Admin Page</h2>
<h2>Current Sem = <?php echo "$currentsem" ?></h2>

<!-- sem change, subject and grade toggler -->
<form method="post">

    <div class="flex items-center gap-3 m-4">
        <label class="" for="sem">Change sem:</label>
        <input class="bg-gray-200 rounded hover:outline " type="number" id="sem" name="sem">
        <input class="rounded bg-green-600 hover:bg-green-800" type="submit" name="sembut" value="Submit"><br>
    </div>

    <div class="flex items-center gap-3 m-4">

        <input class="rounded <?php
        if($cource_bool=="false"){
            echo "bg-green-600 hover:bg-green-800";
        }else{
            echo "bg-red-600 hover:bg-red-800";
        }
        ?>" type="submit" name="courcebut" value="<?php echo $cource_entry;?>"/><br>
        
        
        <input class="rounded <?php
        if($grade_bool=="false"){
            echo "bg-green-600 hover:bg-green-800";
        }else{
            echo "bg-red-600 hover:bg-red-800";
        }
        ?>" type="submit" name="gradebut" value="<?php echo $grade_entry;?>"/><br>
    
    </div>

</form>

<!-- Prof who have not entered the marks -->
<h2>Prof who have not entered the marks yet:</h2>
<table class="text-center rounded mb-4">
    <tr>
        <th class="text-center">Prof Code</th>
    </tr>
    <?php 
    $sql = "SELECT DISTINCT P.prof_ID FROM prof as P WHERE (SELECT COUNT(*) FROM result WHERE sem=$currentsem AND sub_code = P.sub_code AND P.sem=$currentsem) != (SELECT COUNT(*) FROM student WHERE program = P.program AND P.sem=$currentsem)";
    $result = $conn->query($sql);

    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            echo "<tr><td>". $row['prof_ID'] . "</td></tr>" ;
        }
    }else{
        echo "<tr><td> No Prof Left </td></tr>";
    }
    ?>
</table>

<?php require_once 'includes/footer.php' ?>




<!-- 
SELECT P.prof_ID FROM prof as P WHERE (SELECT COUNT(*) FROM result WHERE sem=$currentsem AND sub_code = P.sub_code ) != (SELECT COUNT(*) FROM student WHERE program = P.program)

46,4,50,8,34,38,42

INSERT INTO `result` (`stu_id`, `sem`, `sub_code`, `type`, `m1`, `m2`, `m3`) 
VALUES ('46', '4', '112', 'theory', '12', '12', '33'), 
('4', '4', '112', 'theory', '12', '12', '33'), 
('50', '4', '112', 'theory', '12', '12', '33'),
('8', '4', '112', 'theory', '12', '12', '33'),
('34', '4', '112', 'theory', '12', '12', '33'),
('38', '4', '112', 'theory', '12', '12', '33')
 -->

<!-- Error: MySQL shutdown unexpectedly.
This may be due to a blocked port, missing dependencies, 
improper privileges, a crash, or a shutdown by another method.
Press the Logs button to view error logs and check
the Windows Event Viewer for more clues
If you need more help, copy and post this
entire log window on the forums -->

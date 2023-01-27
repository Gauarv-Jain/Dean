<?php
    require_once 'includes/header.php'
?>

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

<h2>Admin</h2>

<form method="post">

    <label for="sem">Change sem:</label>
    <input class="bg-gray-200 rounded hover:outline " type="number" id="sem" name="sem">
    <input class="bg-green-600 hover:bg-green-800" type="submit" name="sembut" value="Submit"><br>
    
    <input class="<?php
    if($cource_bool=="false"){
        echo "bg-green-600 hover:bg-green-800";
    }else{
        echo "bg-red-600 hover:bg-red-800";
    }
    ?>" type="submit" name="courcebut" value="<?php echo $cource_entry;?>"/><br>
    
    
    <input class="<?php
    if($grade_bool=="false"){
        echo "bg-green-600 hover:bg-green-800";
    }else{
        echo "bg-red-600 hover:bg-red-800";
    }
    ?>" type="submit" name="gradebut" value="<?php echo $grade_entry;?>"/><br>

</form>

<h2>Prof who have not entered the marks</h2>
<table>
    <tr>
        <th>Prof Code</th>
    </tr>
    <?php 
    $sql = "SELECT P.prof_ID FROM prof as P WHERE (SELECT COUNT(*) FROM result WHERE sem=$currentsem AND sub_code = P.sub_code ) != (SELECT COUNT(*) FROM student WHERE program = P.program)";
    $result = $conn->query($sql);

    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            echo "<tr><td>". $row['prof_ID'] . "</td></tr>" ;
        }
    }
    ?>
</table>

<?php
    require_once 'includes/footer.php'
?>




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
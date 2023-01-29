<?php require_once 'includes/header.php' ?>

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

            //checking if the subject is alredy entered or not
            $sq1 = "SELECT * FROM prof WHERE sub_code = $subcode";
            $result = mysqli_query($conn, $sq1);
            $rowCount = mysqli_num_rows($result);
            if($rowCount != 0){
                while ($row = mysqli_fetch_assoc($result)) {          
                    echo '<script>alert("This subjects is alredy Entered by prof'. $row['prof_id'] .'")</script>';
                    exit();
                }
            }

            $sql1 = "INSERT INTO `prof` (prof_id, sub_name, sub_code, program, sem, type, m1, m2, m3) VALUES ($prof_id, ?, $subcode, '$program', $sem, '$type', $m1, $m2, $m3);";

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
        }else if($type == "Lab" && !empty($m3)){
            echo '<script>alert("Lab subjects does not have a M3 field")</script>';
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

        //checking if the prof has this pirticular subject
        $sq1 = "SELECT * FROM prof WHERE prof_id=$prof_id AND sub_code=$subcode";
        $result = mysqli_query($conn, $sq1);
        $rowCount = mysqli_num_rows($result);
        if($rowCount == 0){
            echo '<script>alert("Not Your Subject")</script>';
            exit();
        }

        //checking if the subject exists and if it does make a query to inser the result 
        if($type == "theory"){
            $sq2 = "SELECT * FROM prof WHERE sub_code = $subcode";
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
            $sq2 = "SELECT * FROM prof WHERE sub_code = $subcode";
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

<!-- script for the hiding of the M3 block -->
<script type="text/javascript">
    //for subject form
    function getValue(x) {
    if(x.value == 'lab'){
        document.getElementById("m3").style.display = 'none'; // you need a identifier for changes
        document.getElementById("lablem3").style.display = 'none';
    }
    else{
        document.getElementById("m3").style.display = 'block';  // you need a identifier for changes
        document.getElementById("lablem3").style.display = 'block';
    }
    }

    //for grade form
    function getValu(x) {
    if(x.value == 'lab'){
        document.getElementById("gm3").style.display = 'none'; // you need a identifier for changes
        document.getElementById("glablem3").style.display = 'none';
    }
    else{
        document.getElementById("gm3").style.display = 'block';  // you need a identifier for changes
        document.getElementById("glablem3").style.display = 'block';
    }
    }
</script>

<!-- subject entry form  -->
<?php if($cource_bool=="true"){echo '
    <form class="grid font-bold justify-center" method="post">

        <div class="flex flex-wrap w-fit justify-center items-center m-2 p-4 bg-slate-300 rounded">
            <div>
                <label class="self-center" for="subname">Subject name:</label>
                <input class="bg-gray-200 rounded h-10 hover:outline px-3" type="text" id="subname" name="ssubname" value="">
                <label class="self-center" for="subcode">Sublect code:</label>
                <input class="bg-gray-200 rounded h-10 hover:outline px-3" type="number" id="subcode" name="ssubcode" value="">
                
                <div class="flex justify-around m-2">
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
                            <input type="radio" id="Theory" name="stype" value="theory" checked onChange="getValue(this)">
                            <label class="self-center" for="Theory">Theory</label><br>
                        </div>
                        <div>
                            <input type="radio" id="Lab" name="stype" value="lab" onChange="getValue(this)">
                            <label class="self-center" for="Lab">Lab</label>
                        </div>
                    </div>
                </div>

            </div>

            <div class="grid gap-2">
                <div class="flex">
                    <label class="self-center mx-2" for="m1">M1:</label>
                    <input class="bg-gray-200 rounded h-10 hover:outline px-3" type="number" id="m1" name="sm1" value="">
                </div>

                <div class="flex">
                    <label class="self-center mx-2" for="m2">M2:</label>
                    <input class="bg-gray-200 rounded h-10 hover:outline px-3" type="number" id="m2" name="sm2" value="">
                </div>

                <div class="flex">
                    <label class="self-center mx-2" for="m3" id="lablem3">M3:</label>
                    <input class="bg-gray-200 rounded h-10 hover:outline px-3" type="number" id="m3" name="sm3" value="">
                </div>
            </div>

        </div>   

        <input class="rounded w-fit justify-self-center bg-green-600 hover:bg-green-800" type="submit" name="subbut" value="Submit">
    </form>
';} ?>

<!-- options for subjects -->
<form class="w-fit mx-auto my-4 font-bold" method="post">
    <select name="someDropdown" id="someDropdown"> 
        <?php
        $sql = "SELECT * FROM prof WHERE prof_id=$prof_id";
        $result = $conn->query($sql);

        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                print('<option value ='. $row['sub_code'] .'>'.$row['sub_name']. " (" .$row['sub_code'].")" .'</option>'); 
            } 
        }
        ?> 
    </select> 

    <input class="rounded w-fit justify-self-center bg-green-600 hover:bg-green-800" type="submit" name="subsele" value="Submit">
</form>

<!-- droupdown button handeler -->
<?php 
    if(isset($_POST['subsele'])) {
        
        $sub__code = $_POST['someDropdown'];
        $sql = "SELECT * FROM prof WHERE sub_code=$sub__code";
        $result = $conn->query($sql);

        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $program=$row['program'];
                $types=$row['type'];
                $m1=$row['m1'];
                $m2=$row['m2'];
                $m3=$row['m3'];
            }
        }
        if($types=="lab"){
            $che = 1;
        }else{
            $che = 0;
        }

        echo '
        <form class="grid" method="post">
            <table id="myTable" class="text-center">
                <tr>
                    <th class="text-center">Student ID</th>
                    <th class="text-center">Student Name</th>
                    <th class="text-center">M1('.$m1.')</th>
                    <th class="text-center">M2('.$m2.')</th>
                    <th class="text-center">M3('.$m3.')</th>
                </tr>
        ';

        $sql = "SELECT * FROM student WHERE program='$program' ORDER BY `stu_id` ";
        $result = $conn->query($sql);

        if($result->num_rows>0){
            while($row = $result->fetch_assoc()){
                $temp1=$row['stu_id'];
                $temp2=$sub__code;
                
                $sql11 = "SELECT * FROM result WHERE stu_id=$temp1 AND sem=$sem AND sub_code=$sub__code";
                $result11 = $conn->query($sql11);
                if($result11->num_rows>0){
                    while($row11 = $result11->fetch_assoc()){
                        echo '
                        <tr>
                            <td>'.$row['stu_id'].'</td>
                            <td>'.$row['name'].'</td>
                            <td contenteditable="true">'.$row11['m1'].'</td>
                            <td contenteditable="true">'.$row11['m2'].'</td>
                            <td contenteditable="true">'.$row11['m3'].'</td>
                        </tr>   
                        ';
                    }
                }else{
                    echo '
                    <tr>
                        <td>'.$row['stu_id'].'</td>
                        <td>'.$row['name'].'</td>
                        <td contenteditable="true"></td>
                        <td contenteditable="true"></td>
                        <td contenteditable="true"></td>
                    </tr>   
                    ';
                }
            }
        }
        
        echo '
            </table>
            <input class="rounded w-fit justify-self-center my-4 bg-green-600 hover:bg-green-800" type="submit" name="tabbut" value="Submit" onclick="checkVal('.$sub__code.', '.$che.')">
        </form>
        ';
    }

?>

<!-- marks submit button handeler script -->
<script type="text/javascript">
    function checkVal(subcodeva, typ){

        if(typ===1){
            typ = "lab";
        }else{
            typ="theory";
        }
        
        alert("sdasadasdads");
        console.log("i was here breofaasdfa");
        //gets table
        var oTable = document.getElementById('myTable');

        //gets rows of table
        var rowLength = oTable.rows.length;

        //loops through rows    
        for (i = 1; i < rowLength; i++){

            //gets cells of current row  
            var oCells = oTable.rows.item(i).cells;

            //gets amount of cells of current row
            var cellLength = oCells.length;

            //alert(oCells.item(0).innerHTML);
            
            var data = {
                stu_id : oCells.item(0).innerHTML,
                sem: <?php echo $sem; ?>,
                sub_code: subcodeva,
                type: typ,
                m1 : oCells.item(2).innerHTML,
                m2 : oCells.item(3).innerHTML,
                m3 : oCells.item(4).innerHTML
            };

            fetch("test.php", {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                    "Content-Type": "application/json",
                },
            })
            //👇 receive the response
            .then((response) => response.text())
            .then((data) => alert(data));
        }
    }
</script>


<!-- list of cources/subjects the prof has  -->
<h2>Course list</h2>
<table>
    <tr>
        <th>Subject Name</th>
        <th>Subject Code</th>
        <th>Program</th>
        <th>Type</th>
        <th>M1</th>
        <th>M2</th>
        <th>M3</th>
    </tr>
    <?php 
    $sql = "SELECT * FROM prof WHERE prof_id=$prof_id";
    $result = $conn->query($sql);

    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            echo "<tr id=”row1”><td>".$row['sub_name'] . "</td><td>" . $row['sub_code'] . "</td><td>" . $row['program'] . "</td><td>" . $row['type'] ."</td><td>" . $row['m1'] . "</td><td>" . $row['m2'] . "</td><td>" . $row['m3'] . "</td></tr>" ;
        }
    }
    ?>
</table>

<?php require_once 'includes/footer.php' ?>
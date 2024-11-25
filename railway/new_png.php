<html>
<body style=" background-image: url(rp15.jpg);
    height: 100%; 
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;" >

<?php 
session_start();
require "db.php";

$pname = $_POST["pname"];
$page = $_POST["page"];
$pgender = $_POST["pgender"];

$tno = $_SESSION["tno"];
$doj = $_SESSION["doj"];
$sp = $_SESSION["sp"];
$dp = $_SESSION["dp"];
$class = $_SESSION["class"];

$query = "SELECT fare FROM classseats WHERE trainno='$tno' AND class='$class' AND doj='$doj' AND sp='$sp' AND dp='$dp'";
$result = mysqli_query($conn, $query) or die(mysqli_error($conn));

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    $fare = $row[0];
} else {
    echo "Error: No matching fare found for the given train and class.";
    die();
}

$tempfare = 0;
$temp = 0;

for ($i = 0; $i < $_SESSION["nos"]; $i++) {
    if ($page[$i] >= 18 && $page[$i] < 60) {
        $temp++;
        $tempfare += $fare;
    } elseif ($page[$i] < 18 || $page[$i] >= 60) {
        $tempfare += 0.5 * $fare;
    }
}

if ($temp == 0) {
    echo "<br><br>At least one adult must accompany!!!";
    echo "<br><br><a href=\"http://localhost/railway/enquiry.php\">Back to Enquiry</a> <br>";
    die();
}

echo "Total fare is Rs." . $tempfare . "/-";

$sql = "INSERT INTO resv(id,trainno,sp,dp,doj,tfare,class,nos) VALUES ('".$_SESSION["id"]."','$tno','$sp','$dp','$doj','$tempfare','$class','".$_SESSION["nos"]."')";
if ($conn->query($sql) === TRUE) {
    echo "<br><br>Reservation Successful";
} else {
    echo "<br><br>Error: " . $conn->error;
}

$tid = $_SESSION["id"];
$ttno = $_SESSION["tno"];
$tdoj = $_SESSION["doj"];

$query = "SELECT pnr FROM resv WHERE id='$tid' AND trainno='$ttno' AND doj='$tdoj'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
    $rpnr = $row['pnr'];
} else {
    echo "Error: Unable to retrieve PNR.";
    die();
}

$tpname = $_POST['pname'];
$tpage = $_POST["page"];
$tpgender = $_POST["pgender"];

for ($i = 0; $i < $_SESSION["nos"]; $i++) {
    $sql = "INSERT INTO pd(pnr,pname,page,pgender) VALUES ('$rpnr', '".$tpname[$i]."', '".$tpage[$i]."', '".$tpgender[$i]."')";
    if ($conn->query($sql) === TRUE) {
        echo "<br><br>Passenger details added!!!";
    } else {
        echo "<br><br>Error: " . $conn->error;
    }
}

echo "<br><br><a href=\"http://localhost/railway/index.htm\">Go Back!!!</a> <br>";
$conn->close(); 
?>

</body>
</html>

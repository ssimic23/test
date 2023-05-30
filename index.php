INDEX.PHP
<?php
$msg=isset($msg)?$msg:"";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <form action="controller/controller.php" method="post">

    <p>Marka:</p>
    <input type="text" name="marka"><br>
    <p>Cena:</p>
    <input type="text" name="cena"><br>
    <input type="submit" value="unos" name="action">


    </form>
    <?=$msg ?>
    
</body>
</html>
------------------------------------------------------------------------------------------------------------------------------------------------------------------
PRIKAZ.PHP

<?php
if(!isset($_SESSION['telefon'])) session_start();
var_dump($_SESSION['telefon']);
$telefoni=$_SESSION['telefon'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<p>Prikaz</p>

<table>

<tr>
    
    <th>Marka</th>
    <th>Cena</th>
</tr>

<?php foreach ($telefoni as $pom): ?>
<tr>
     <td><?php echo $pom['marka'] ?></td>
     <td><?php echo $pom['cena'] ?></td>
</tr>
<?php endforeach; ?>

</table>
<a href="./controller/controller.php?action=logout"><button>Logout</button></a>
</body>
</html>
--------------------------------------------------------------------------------------------------------
TEST.PHP
<?php

include './model/DAO.php';

$obj= new DAO();

//$telefoni=$obj->insertTelefon("Apple",1500);


//$fon=$obj->getTelefon("samsung",1600);
//var_dump($fon);
?>
--------------------------------------------------------------------------------------------------------
DAO.PHP
<?php
require_once '../confing/db.php';

class DAO {
	private $db;

	// za 2. nacin resenja
	private $INSERTTELEFON = "INSERT INTO telefoni (marka, cena) VALUES (?, ?)";
	private $GETTELEFON = "SELECT *FROM telefoni WHERE marka=? AND cena > ?";
	
	public function __construct()
	{
		$this->db = DB::createInstance();
	}

	public function insertTelefon($marka,$cena)
	{
		$statement = $this->db->prepare($this->INSERTTELEFON);
		$statement->bindValue(1, $marka);
		$statement->bindValue(2, $cena);
		
		
		$statement->execute();

	}

	public function getTelefon($marka,$cena)
	{
		$statement=$this->db->prepare($this->GETTELEFON);
		$statement->bindValue(1,$marka);
		$statement->bindValue(2,$cena);

		$statement->execute();
		$result = $statement->fetchAll();
		return $result;
	}


}
?>
--------------------------------------------------------------------------------------------------------
CONTROLLER.PHP
<?php
include '../model/DAO.php';

$action=isset($_POST['action'])?$_POST['action']:"";
$marka=isset($_POST['marka'])?$_POST['marka']:"";
$cena=isset($_POST['cena'])?$_POST['cena']:"";
//var_dump($_POST);
//die('jjjj');

if($action=='unos')
{
    if(!empty($marka) && !empty($cena))
    {
        if(is_numeric($cena))
        {
        $dao = new DAO();
        $dao->insertTelefon($marka,$cena);
        $obj=$dao->getTelefon($marka,$cena);
        session_start();
        $_SESSION['telefon'] = $obj;
        header("Location: ../prikaz.php");
        }
        else
        {
            $msg="Cena mora biti numerik";
            //header("Location: ../index.php");
            include '../index.php';
        }
    }
}
    else if($_GET['action']=='logout')
    {
        session_unset();
        session_destroy();
        header("Location: ../index.php");
    }

?>
--------------------------------------------------------------------------------------------------------
API INDEX.PHP
<?php
require 'flight/Flight.php';
require_once '../model/DAO.php';

Flight::route('/', function(){
    echo 'Pozdrav!';
});

Flight::route('GET /telefon/@marka/@cena', function($marka, $cena){
    $dao = new DAO();
    echo json_encode($dao->getTelefon($marka, $cena));
});
Flight::route('POST /telefon/', function(){
    $dao = new DAO();
    $marka = Flight::request()->data->marka;
    $cena = Flight::request()->data->cena;
    echo json_encode($dao->insertTelefon($marka, $cena));
});
// Flight::route('GET ', function(){
//     $dao=new DAOStudent();
//     $result=$dao->getStudentById($id);
//     echo json_encode($result);
// });
// Flight::route('PUT ', function(){
//     $dao=new DAOStudent();
//     var_dump(Flight::request()->data->ime);
//     $ime=Flight::request()->data->ime;
//     $prezime=Flight::request()->data->prezime;
//     $brIndexa=Flight::request()->data->brIndexa;
//     $result=$dao->update($id,$ime,$prezime,$brIndexa);
//     echo json_encode($result);
// });


Flight::start();

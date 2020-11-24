<?php

require __DIR__ . '/vendor/autoload.php';
use chillerlan\QRCode\QRCode; //biblioteka do tworzenia qr code

class User {
    public $data;
    public $decodedData;
    
    function __construct(){     //pobieranie danych JSON w konstruktorze
        $this-> data = file_get_contents("https://jsonplaceholder.typicode.com/users/1");
        $this -> decodedData = json_decode($this -> data);
    }

    function getDomain(){
        $email = $this->decodedData-> email;
        $explode = explode("@",$email);
        $domain = array_pop($explode);
        return $domain;
    }

    function getPersonData(){
        
        return "dane: <pre>". $this->data . "</pre><br/>". '<img src="'.(new QRCode)->render($this->data).'" alt="QR Code" />'; //wyswietlanie preformatowanych danych JSON i tworzenie kodu QR za pomocą biblioteki
        
    }
}
$user = new User();

echo "domena: " . $user -> getDomain()."<br/>";
echo  $user -> getPersonData();

?>


<br/>

<?php

//połączenie z serwerem 
$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error){
    die("Połączenie nie powiodło się". $conn->connect_error);
}

//tworzenie bazy danych
$dbname = "baza";
$sql = "CREATE DATABASE " . $dbname;

if ($conn->query($sql) === TRUE){
    echo "Baza danych utworzona z powodzeniem <br/>";
} else{
    echo "Błąd przy tworzeniu bazy danych: ". $conn->errno. "<br/>";
}
$conn->close();

$conn = new mysqli('localhost', 'root', '', $dbname); //łączenie się do bazy danych

$sql = "CREATE TABLE dane (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name_of_the_user VARCHAR(30) NOT NULL,
    username VARCHAR(30) NOT NULL ,
    email VARCHAR(50) NOT NULL UNIQUE,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ";

if ($conn->query($sql) === TRUE){
    echo "tabela utworzona<br/>";
} else{
    echo "Błąd przy tworzeniu tabeli: ". $conn->errno. "<br/>";
}

$name = $user->decodedData->name;
$usrname = $user->decodedData->username;
$email = $user->decodedData->email;


$insert = "INSERT INTO dane (name_of_the_user, username, email) VALUES ('$name', '$usrname', '$email')";

if ($conn->query($insert) === TRUE){
    echo "insert się udał<br/>";
} else{
    if($conn->errno == 1062){ //pole email w bazie jest unikatowe, ten error oznacza że cos co mialo byc unikatowe, powtarza się, 
        echo "ten email juz istnieje";
    }
    else{

    echo "Błąd przy insercie: ". $conn->errno. "<br/>";
    }
}



$conn->close();
?>
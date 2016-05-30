
<?php
/*
criado por: Victor Ratts
Autor: victor Ratts
Versão: 1.0
uso: open-source
email: victor.ratts13@gmail.com
site: https://www.gitbit.epizy.com/

*/

error_reporting(0);
$servername = "localhost";
$username = "username_database";
$password = "Password_database";
$dbname = "database_name";

// Criar Conecção
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//------------------user e pass - nomeação de formularios------------------------- 
$user = $_POST['user'];
$password  = $_POST['pass'];





/**********************************************************************/
define("PBKDF2_HASH_ALGORITHM", "sha256");
define("PBKDF2_ITERATIONS", 1000);
define("PBKDF2_SALT_BYTE_SIZE", 24);
define("PBKDF2_HASH_BYTE_SIZE", 24);
define("HASH_SECTIONS", 4);
define("HASH_ALGORITHM_INDEX", 0);
define("HASH_ITERATION_INDEX", 1);
define("HASH_SALT_INDEX", 2);
define("HASH_PBKDF2_INDEX", 3);
define("USE_OPENSSL_RANDOM", false);
function pbkdf2_create_hash($password)
{
    // Formato: Algoritimo:interação:salt:hash
    $salt = base64_encode(mcrypt_create_iv(PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM));
    return PBKDF2_HASH_ALGORITHM . ":" . PBKDF2_ITERATIONS . ":" .  $salt . ":" .
    base64_encode(pbkdf2_apply(
        PBKDF2_HASH_ALGORITHM,
        $password,
        $salt,
        PBKDF2_ITERATIONS,
        PBKDF2_HASH_BYTE_SIZE,
        true
    ));
}
/*
O sistema de algorimo simples baseado em 32 
caracteres de criptografia sha256:
interação de dados pode ser auterada a md5 ou sha1.

após esse espaço, aconselha-se a não alterar quaisquer dado.
 */
 function pbkdf2_apply($algorithm, $password, $salt, $count, $key_length, $raw_output = false)
{
    $algorithm = strtolower($algorithm);
    if(!in_array($algorithm, hash_algos(), true))
        die('PBKDF2 ERROR: Invalid hash algorithm.');
    if($count <= 0 || $key_length <= 0)
        die('PBKDF2 ERROR: Invalid parameters.');
    $hash_length = strlen(hash($algorithm, "", true));
    $block_count = ceil($key_length / $hash_length);
    $output = "";
    for ($i = 1; $i <= $block_count; $i++) {
        // $i encoded as 4 bytes, big endian.
        $last = $salt . pack("N", $i);
        // first iteration
        $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
        // perform the other $count - 1 iterations
        for ($j = 1; $j < $count; $j++) {
            $xorsum ^= ($last = hash_hmac($algorithm, $last, $password, true));
        }
        $output .= $xorsum;
    }
    if($raw_output)
        return substr($output, 0, $key_length);
    else
        return bin2hex(substr($output, 0, $key_length));
}
$hash = pbkdf2_create_hash($password);
/*
após o fim da criptografia, o retorno da conecção com  banco de dados é recolocada para dar
suporte ao fim do cadastro onde, se o o formulario efetuar o cadastro, ele retorna com uma mensagem de sucesso.
*/
//--------------------login link----------------------------------------------------------------------------
$login = '<a href="index.php">Faça Login</a>';
//----------------------------------------------------------------------------------------------------------
$sql = "INSERT INTO ajxp_users (login, password, groupPath)
VALUES ('$user', '$hash', '/')";
//-----------------------------------------------------------------------------------------------------------
if($_POST['pass']){
$conn->query($sql);
echo "Cadastro efetuado ".$login;
}
$conn->close();

?>
<html>
<title>cadastro</title>
<head>
<style>
input{
	width:200px;
	height:30px;
	size:30px;
	text-align:center;
	color:rgba(0,0,0,0.6);
}
.button{
	background-color:rgba(0,255,204,0.5);
	border-bottom:none;
	border:0;
}
.font{
	color:rgba(255,255,255,1);
	font-family:Arial, Helvetica, sans-serif;
	font-size:18px;
}
.conduta{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	color:rgba(255,255,255,0.8);
	text-decoration:none;
}
a{
	color:rgba(0,255,102,0.7);
	text-decoration:none;
	-webkit-transition:0.5s;
}
a:hover{
	color:rgba(0,255,0,1);
	text-decoration:none;
	-webkit-transition:0.5s;
}
.body{
	text-align:center;
}
</style>
</head>

<body class="body">
<div class="font"><center><form action="register.php?" method="post">
<table width="200" border="0">
  <tr>
    <td><label for="textfield"></label>
      <input type="text" name="user" value="username" onFocus="if(this.value=='username')this.value='';" /></td>
  </tr>
  <tr>
    <td><label for="textfield2"></label>
      <input type="password" name="pass" value="pass" onFocus="if(this.value=='pass')this.value='';" /></td>
  </tr>
  <tr>
    <td><input class="button" type="submit" value="enviar" /></td>
  </tr>
</table>
</form><div class="conduta"><br><br>
Ao clicar em enviar, você aceita todos os nossos<a href="#"> termos de conduta e serviço</a>, em caso de quebra de termos, sua conta será cancelada.
</div>
</center>
</div>
</body>

</html>
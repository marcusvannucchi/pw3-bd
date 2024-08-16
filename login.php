<?php
 
session_start();
 
//verifica se o usuário está logado
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){
    header("Location: welcome.php");
    exit();
}
 
//inclui o arquivo de configuração
require_once "config.php";
 
//definindo variáveis e inicializando com valores vazios
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
//processamento de dados do formulário quando o formulário é submetido
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    //verifica se o nome de usuário está vazio
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor, insira o nome de usuário.";
    } else{
        $username = trim($_POST["username"]);
    }
 
    //verifica se a senha está vazia
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor, insira a senha.";
    } else{
        $password = trim($_POST["password"]);
    }
 
    //validação de credenciais
    if(empty($username_err) && empty($password_err)){
        //prepara a declaração de seleção
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
 
        if($stmt = $pdo->prepare($sql)){
            //vincula variáveis à declaração preparada como parâmetros
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
 
            //definindo parâmetros
            $param_username = trim($_POST["username"]);
 
            //tentativa de execução da declaração preparada
            if($stmt->execute()){
                //verifica se o nome de usuário existe, se sim, verifica a senha
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)){
                            //senha correta, inicia uma nova sessão
                            session_start();
 
                            //armazena dados em variáveis de sessão
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
 
                            //redireciona o usuário para a página de boas-vindas
                            header("Location: welcome.php");
                        } else{
                            //exibe uma mensagem de erro se a senha não for válida
                            $login_err = "Nome de usuário ou senha inválidos.";
                        }
                    }
                } else{
                    //exibe uma mensagem de erro se o nome de usuário não existir
                    $login_err = "Nome de usuário ou senha inválidos.";
                }
            } else{
                echo "Oops! Algo deu errado. Por favor, tente novamente mais tarde.";
            }
 
            //fecha a declaração
            unset($stmt);
        }
    }
 
    //fecha a conexão
    unset($pdo);
}
?>

<!DOCTYPE html><html lang="pt-br">
    <head><meta charset="UTF-8"><title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>body { font: 14px sans-serif; }       
      .wrapper{ width: 360px; padding: 20px; } 
      </style></head><body><div class="wrapper">
        <h2>Login</h2>
        <p>Por favor, preencha os campos para fazer o login.</p>
        <?php      
           if(!empty($login_err)){             echo '<div class="alert alert-danger">' . $login_err . '</div>';         }         ?>
           <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">       
                  <div class="form-group">
                    <label>Nome do usuário</label><input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback">
                        <?php echo $username_err; ?>
                    </span>
                </div>
                <div class="form-group">
                    <label>Senha</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?>
            </span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Entrar"></div><p>Não tem uma conta? <a href="register.php">Inscreva-se agora!</a>.</p></form>
        </div>
    </body>
    </html>
 
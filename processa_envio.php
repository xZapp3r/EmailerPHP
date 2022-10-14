<?php
  //Chamando arquivos onde contem a biblioteca PHPMailer
  require "./library/Mailer/Exception.php";
  //require "./library/Mailer/OAuth.php";
  //require "./library/Mailer/OAuthTokenProvider.php";
  require "./library/Mailer/PHPMailer.php";
  //require "./library/Mailer/POP3.php";
  require "./library/Mailer/SMTP.php";
  //Usando namespace e importando funcoes
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;


  //print_r($_POST);
  // Criando classe da mensagem com destinatario, assunto e mensagem
  class Mensagem {
    private $para = null;
    private $assunto = null;
    private $mensagem = null;
    public $status = array('codigo' => null, 'descricao_status' => null);

    //Get para chamar os atributos, Set para inserir valores
    public function __get($atributo) {
      return $this->$atributo;
    }
    public function __set($atributo, $valor) {
      $this->$atributo = $valor;
    }
    //Validando atributos nao vazios
    public function mensagemValida() {
      if(empty($this->para) || empty($this->assunto) || empty($this->mensagem)){
        return false;
      }
      return true;
    }


  }
  $mensagem = new Mensagem();
  //Chamando chaves e inserindo dados recolhidos do POST em nome de variaveis
  $mensagem->__set('para', $_POST['para']);
  $mensagem->__set('assunto', $_POST['assunto']);
  $mensagem->__set('mensagem', $_POST['mensagem']);

  //print_r($mensagem);
  //chamando funcao de mensagem valida e executando envio da mensagem via SMTP na library do PHP MAILer
  if(!$mensagem->mensagemValida()) {
    echo('Mensagem nao e valida');
    header('Location: index.php');
  } 
    //Create an instance; passing `true` enables exceptions
  $mail = new PHPMailer(true);

  try {
    //Server settings
    $mail->SMTPDebug = false;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = '';                     //SMTP username *Colocar dados do host email
    $mail->Password   = '';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients Dados de destinatario, e tambem de origem, futuramente inserir usuario do proprio site e utilizar o host para encaminhar (Webmail creation)
    $mail->setFrom('xzapper07@gmail.com', 'zapper tests');
    $mail->addAddress($mensagem->__get('para'), 'Destino');     //Add a recipient
    $mail->addReplyTo('xzapper07@gmail.com', 'Information');
    //$mail->addCC('cc@example.com'); copia
    //$mail->addBCC('bcc@example.com'); copia oculta

    //Attachments para anexos
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content Inserido dinamicamente o assunto e mensagem
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $mensagem->__get('assunto');
    $mail->Body    = $mensagem->__get('mensagem');
    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  * Caso nao aceite emails html

    $mail->send();
    $mensagem->status['codigo'] = 1;
    $mensagem->status['descricao_status'] = 'Mensagem enviada com sucesso';

  } catch (Exception $e) {
    $mensagem->status['codigo'] = 2;
    $mensagem->status['descricao_status'] = "Nao foi possivel enviar esta mensagem: {$mail->ErrorInfo}";
    //Alguma logica que4 armazene o erro para posterior analise
    
  }


?>

<html>
  <head>
    <meta charset="utf-8" />
    <title>App Mail Send</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  </head>
  
  <body>

    <div class="container">  

			<div class="py-3 text-center">
				<img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
				<h2>Send Mail</h2>
				<p class="lead">Seu app de envio de e-mails particular!</p>
			</div>

      <div class="row">
        <div class="col-md-12">
          <?php if($mensagem->status['codigo'] == 1) { ?>
            <div>
              <h1 class="display-4 text-success"> Sucesso </h1>
              <p> <?= $mensagem->status['descricao_status'] ?> </p>
              <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
            </div>
          <?php } ?>

          <?php if($mensagem->status['codigo'] == 2) { ?>
            <div>
              <h1 class="display-4 text-danger"> Ops! </h1>
              <p> <?= $mensagem->status['descricao_status'] ?> </p>
              <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </body>
</html>  
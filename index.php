<?php
@set_time_limit(0);

function depura($mixed_var){
    ?><PRE><DIV style="display: list-item;font-size: 14px; font-weight: bold;	color: #FF0000;	background-color: #FFFFCC; border: 1px dotted #000000;" align="left"><?php print_r($mixed_var); ?></DIV></PRE><?php
}

function envia_mensagem(){
    $vet_telefone = array();
    if(isset($_POST['celular']) && $_POST['celular']){
        $vet_telefone[] = array('nome' => 'Cliente', 'celular' => $_POST['celular']);
    }
    else{
        if(isset($_FILES) && isset($_FILES['arquivo'])){
            $csv = file($_FILES['arquivo']['tmp_name']);
            if(count($csv)){
                if(count($csv) < 100){
                    foreach($csv as $linha){
                        $vet = explode(';', $linha);
                        depura($vet);
                        if(count($vet)){
                            $vet_telefone[] = array('nome' => trim($vet[1]), 'celular' => trim($vet[0]));
                        }
                    }
                }
                else{
                    depura('tamanho maximo de linha excedido.');
                }
            }
        }
    }

    if(count($vet_telefone) && $_POST['mensagem']){
        // depura($vet_telefone);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $_SERVER['APP_URL']);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded'
        ));

        foreach($vet_telefone as $key => $cliente){
            $mensagem = $_POST['mensagem'];
            $mensagem = str_replace("{NOME}", $cliente['nome'], $mensagem);
            $mensagem = str_replace("{CELULAR}", $cliente['celular'], $mensagem);
            $mensagem = str_replace("{DATA}", date('Y-m-d H:i:s'), $mensagem);

            $params = array(
                'token' => $_SERVER['APP_TOKEN'],
                'cmd' => 'chat',
                'to' => $cliente['celular'].'@c.us',
                'msg' => $mensagem
            );

            if(isset($_POST['thumb']) && $_POST['thumb']){
                $params['thumb'] = $_POST['thumb'];
                $params['link'] = $_POST['link'];
                $params['title'] = $_POST['titulo'];
                $params['desc'] = $mensagem;
                $params['cmd'] = 'link';
            }
            else{
                if(isset($_POST['link']) && $_POST['link']){
                    $params['link'] = $_POST['link'];
                    $params['cmd'] = 'media';
                }
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

            $body = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // depura($params);
            // print("Code: ".$http_code."\n");
            // print("body: ".$body."\n\n");

            if($key%10 == 0){
                $rand = rand(1, 3);
                // depura('rand: '.$rand);
                sleep($rand);
            }
        }

        curl_close($ch);
    }
    else{
        depura('nao existe mensagem a ser enviada');
    }
}

// depura($_SERVER);
// depura($_POST);
// depura($_FILES);

envia_mensagem();
////////////////////////////////////////////////////////////////////////////////
?>
<html>
    <form method="POST" enctype='multipart/form-data' action="index.php">
        <label>Insira um telefone:</label><input type="text" name="celular" value="<?php echo (isset($_POST['celular']))? $_POST['celular'] : "" ?>" /><br />
        <label>Insira o titulo:</label><input type="text" name="titulo" value="<?php echo (isset($_POST['titulo']))? $_POST['titulo'] : "" ?>" /><br />
        <label>Insira o thumb:</label><input type="text" name="thumb" value="<?php echo (isset($_POST['thumb']))? $_POST['thumb'] : "" ?>" /><br />
        <label>Insira o link:</label><input type="text" name="link" value="<?php echo (isset($_POST['link']))? $_POST['link'] : "" ?>" /><br />
        <label>Insira a mensagem:</label><textarea name="mensagem" cols="40" rows="10">Texto da mensagem {NOME} - {CELULAR} - {DATA}</textarea><br />
        <label>Insira um arquivo com os nomes e os telefones:</label><input type="file" name="arquivo" />
        <input type="submit" value="Enviar" />
    </form>
</html>
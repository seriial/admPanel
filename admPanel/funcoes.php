<?php
inicializa();
protegeArquivo(basename(__FILE__));
function inicializa(){
    if(file_exists(dirname(__FILE__).'/config.php')):
        require_once(dirname(__FILE__).'/config.php');
    else:
        die(utf8_decode('O arquivo de configuração não foi localizado, contate o administrador'));
    endif;
    $constantes = array('BASEPATH', 'BASEURL', 'ADMURL', 'CLASSESPATH', 'MODULOSPATH', 'CSSPATH', 'JSPATH', 'DBHOST', 'DBUSER', 'DBPASS', 'DBNAME');    
    foreach($constantes as $valor):
        if(!defined($valor)):
            die(utf8_decode('Uma configuração do sistema está ausente: ' . $valor));       
        endif;
    endforeach;
    require_once(BASEPATH.CLASSESPATH.'autoload.php');
    if($_GET['logoff']==TRUE):
        //faz logoff
        $user = new usuarios();
        $user->doLogout();
        exit;
    endif;
}

function loadCSS($arquivo=NULL, $media='screen',$import=FALSE){
    if($arquivo != NULL):
        if($import == TRUE):
            echo '<style type="text/css">@import url("'.BASEURL.CSSPATH.$arquivo.'.css");</style>'."\n";
        else:
            echo '<link rel="stylesheet" type="text/css" href="'.BASEURL.CSSPATH.$arquivo.'.css" media="'.$media.'"/>'."\n";
        endif;
    endif;
}

function loadJS($arquivo=NULL,$remoto=FALSE){
    if($arquivo != NULL):
        if($remoto == FALSE) $arquivo = BASEURL.JSPATH.$arquivo.'.js'."\n";
            echo '<script type="text/javascript" src="'.$arquivo.'"></script>'."\n";
    endif;
}

function loadModulo($modulo=NULL, $tela=NULL){
    if($modulo == NULL || $tela == NULL):
        echo '<p> Erro na função <strong>'.__FUNCTION__.'</strong> faltam parâmetros para execução.</p>';
    else:
        if (file_exists(MODULOSPATH."$modulo.php")):
            include_once(MODULOSPATH."$modulo.php");
        else:
            echo '<p>Módulo inexistente neste sistema!</p>';
        endif;
    endif;
}

function protegeArquivo($nomeArquivo,$redirPara='index.php?erro=3'){
    $url = $_SERVER["PHP_SELF"];
    if(preg_match("/$nomeArquivo/i", $url)):
        //redirecionar para outra url
        redireciona($redirPara);
    endif;
}

function redireciona($url=''){
    header("Location: ".BASEURL.$url);
}

function codificaSenha($senha){
    return md5($senha);
}

function verificaLogin(){
    $sessao = new sessao();
    if($sessao->getNvars() <= 0 || $sessao->getVar('logado') != TRUE || $sessao->getVar('ip') != $_SERVER['REMOTE_ADDR']):
        redireciona('?erro=3');
    endif;
}

function printMSG($msg = NULL, $tipo = NULL){
    if($msg != NULL):
        switch($tipo):
            case 'erro':
                echo '<div class="erro">'.$msg.'</div>';
                break;
            case 'alerta':
                echo '<div class="alerta">'.$msg.'</div>';
                break;
            case 'pergunta':
                echo '<div class="pergunta">'.$msg.'</div>';
                break;
            case 'sucesso':
                echo '<div class="sucesso">'.$msg.'</div>';
                break;    
            default:
                echo '<div class="sucesso">'.$msg.'</div>';
                break;            
        endswitch;
    endif;
}

function isAdmin(){
    verificaLogin();
    $sessao = new sessao();
    $user = new usuarios(array(
        'administrador' => NULL,
    ));
    $iduser = $sessao->getVar('iduser');
    $user->extras_select = "WHERE id=$iduser";
    $user->selecionaCampos($user);
    $res = $user->retornaDados();
    if(strtolower($res->administrador) == 's'):
        return TRUE;
    else:
        return FALSE;
    endif;
}
?>
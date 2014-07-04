<?php
require_once(dirname(__FILE__).'/autoload.php');
protegeArquivo(basename(__FILE__));
class usuarios extends base{
    public function __construct($campos=array()) {
        parent::__construct();
        $this->tabela="adm_usuarios";
        if(sizeof($campos)<= 0):
            $this->campos_valores = array(
                "nome" => null,
                "email" => null,
                "login" => null,
                "senha" => null,
                "ativo" => null,
                "administrador" => null,
                "datacad" => null,
            );
        else:
            $this->campos_valores = $campos;
        endif;
        $this->campopk = "id";
    }
    
    public function doLogin($objeto){
        $objeto->extras_select = "WHERE login='".$objeto->getValor('login')."' AND senha= '".codificaSenha($objeto->getValor('senha'))."' AND ativo='s'";
        $this->selecionaTudo($objeto);
        $sessao = new sessao();
        if($this->linhasafetadas == 1):
            $usLogado = $objeto->retornaDados();
            $sessao->setVar('iduser', $usLogado->id);
            $sessao->setVar('nomeuser', $usLogado->nome);
            $sessao->setVar('loginuser', $usLogado->login);
            $sessao->setVar('logado', TRUE);
            $sessao->setVar('ip', $_SERVER['REMOTE_ADDR']);
            return TRUE;
        else:
            $sessao->destroy(TRUE);
            return FALSE;
        endif;
    }
    
    public function doLogout(){
        $sessao = new sessao();
        $sessao->destroy(TRUE);
        redireciona('?erro=1');
    }
    
    public function existeRegistro($campo=NULL, $valor=NULL){
        if($campo!=NULL && $valor!=NULL):
            is_numeric($valor) ? $valor = $valor : $valor = "'" . $valor . "'";
            $this->extras_select = "WHERE $campo=$valor";
            $this->selecionaTudo($this);
            if($this->linhasafetadas > 0):
                return TRUE;
            else:
                return FALSE;
            endif;
        else:
            $this->trataerro(__FILE__, __FUNCTION__, NULL, 'Faltam parâmetros para executar a função', TRUE);
        endif;
    }
}
?>

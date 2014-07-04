<?php
require_once(dirname(__FILE__).'/autoload.php');
protegeArquivo(basename(__FILE__));
abstract class base extends banco{
    //propriedade
    public $tabela = "";
    public $campos_valores = array();
    public $campopk = NULL;
    public $valorpk = NULL;
    public $extras_select = "";
    
    //métodos
    public function addCampo($campo=null,$valor=null) {
        if($campo!=NULL):
            $this->campos_valores[$campo] = $valor;        
        endif;
    }//addCampo
    
    public function delCampo($campo=null) {
        if(array_key_exists($campo, $this->campos_valores)):
            unset($this->campos_valores[$campo]);
        endif;
    }//delCampo
    
    public function setValor($campo=NULL,$valor=NULL){
        if($campo!=NULL && $valor!=NULL):
            $this->campos_valores[$campo] = $valor;
        endif;
    }//setValor
    
    public function getValor($campo=NULL) {
        if($campo!=NULL && array_key_exists($campo, $this->campos_valores)):
            return $this->campos_valores[$campo];
        else:
            return false;
        endif;
    }//getValor
    
}//fim classe base
?>
<?
require_once(dirname(__FILE__).'/autoload.php');
protegeArquivo(basename(__FILE__));
abstract class banco{
    //propriedades
    public $servidor        = DBHOST;
    public $usuario         = DBUSER;
    public $senha           = DBPASS;
    public $nomebanco       = DBNAME;
    public $conexao         = NULL;
    public $dataset         = NULL;
    public $linhasafetadas  = -1;
    
    //metodos
    public function __construct() {
        $this->conecta();
    }//construct   
    public function __destruct() {
        if($this->conexao != NULL):
            mysql_close($this->conexao);
        endif;        
    }//destruct
    public function conecta(){
        $this->conexao = mysql_connect($this->servidor,$this->usuario,$this->senha,TRUE) 
        or die ($this->trataerro(__FILE__,__FUNCTION__,  mysql_errno(), mysql_error(),TRUE));
        mysql_select_db($this->nomebanco) or die ($this->trataerro(__FILE__,__FUNCTION__,  mysql_errno(), mysql_error(),TRUE));
        mysql_query("SET NAMES 'utf8'");
        mysql_query("SET character_set_connection=utf8");
        mysql_query("SET character_set_client=utf8");
        mysql_query("SET character_set_results=utf8");
        //echo "metodo conecta foi chamado"; //debugger
    }//conecta
    public function inserir($objeto){
        $sql = "INSERT INTO ".$objeto->tabela." (";
        for($i=0; $i < count($objeto->campos_valores);$i++):
           // echo '<br/>'.key($objeto->campos_valores); // se for chamado dessa forma, ele pega todos nomes do array <<=
            $sql .= key($objeto->campos_valores);
            if($i < (count($objeto->campos_valores)-1)):
                $sql .= ", ";
            else:
                $sql .= ") ";
            endif;
            next($objeto->campos_valores);        
        endfor;
        reset($objeto->campos_valores);
        $sql .= "VALUES (";
        for($i=0; $i < count($objeto->campos_valores);$i++):
            //echo '<br/>'. $objeto->campos_valores[key($objeto->campos_valores)]; //se for chamado dessa forma ele pega os valores do array, com seus nomes respectivamente. =>>
            $sql .= is_numeric($objeto->campos_valores[key($objeto->campos_valores)]) ? 
                    $objeto->campos_valores[key($objeto->campos_valores)] :
                    "'".$objeto->campos_valores[key($objeto->campos_valores)]."'";
            if($i < (count($objeto->campos_valores)-1)):
                $sql .= ", ";
            else:
                $sql .= ") ";
            endif;
            next($objeto->campos_valores);
        endfor;
        return $this->executaSQL($sql);
        //echo $sql;
    }//inserir
    public function atualizar($objeto){        
        $sql = "UPDATE ".$objeto->tabela." SET ";
        for($i=0; $i < count($objeto->campos_valores);$i++):
           // echo '<br/>'.key($objeto->campos_valores); // se for chamado dessa forma, ele pega todos nomes do array <<=
            $sql .= key($objeto->campos_valores)."=";
            $sql .= is_numeric($objeto->campos_valores[key($objeto->campos_valores)]) ? 
                    $objeto->campos_valores[key($objeto->campos_valores)] :
                    "'".$objeto->campos_valores[key($objeto->campos_valores)]."'";
            if($i < (count($objeto->campos_valores)-1)):
                $sql .= ", ";
            else:
                $sql .= " ";
            endif;
            next($objeto->campos_valores);        
        endfor;
        $sql.= "WHERE ".$objeto->campopk."=";
        $sql.= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk ."'";
        return $this->executaSQL($sql);
        //echo $sql;        
    }//atualizar   
    public function deletar($objeto){
        $sql = "DELETE FROM ".$objeto->tabela;      
        $sql.= " WHERE ".$objeto->campopk."=";
        $sql.= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk ."'";
        return $this->executaSQL($sql);
        //echo $sql;               
    }//deletar
    public function selecionaTudo($objeto){
       $sql = "SELECT * FROM ".$objeto->tabela; 
       if($objeto->extras_select!=NULL):
           $sql .= " ".$objeto->extras_select;
       endif;
       return $this->executaSQL($sql);
    }//selecionaTudo
    public function selecionaCampos($objeto){
       $sql = "SELECT "; 
        for($i=0; $i < count($objeto->campos_valores);$i++):
            $sql .= key($objeto->campos_valores);
            if($i < (count($objeto->campos_valores)-1)):
                $sql .= ", ";
            else:
                $sql .= " ";
            endif;
            next($objeto->campos_valores);        
        endfor;                
       $sql .= " FROM ".$objeto->tabela; 
       if($objeto->extras_select!=NULL):
           $sql .= " ".$objeto->extras_select;
       endif;
       return $this->executaSQL($sql);        
    }//selecionaCampos
    public function executaSQL($sql=NULL){
        if($sql!=NULL):
            $query = mysql_query($sql) or $this->trataerro(__FILE__,__FUNCTION__);
            $this->linhasafetadas = mysql_affected_rows($this->conexao);
            if(substr(trim(strtolower($sql)),0, 6) == 'select'):
                $this->dataset = $query;
                return $query;
            else:
                return $this->linhasafetadas;
            endif;
        else:
            $this->trataerro(__FILE__,__FUNCTION__,null,'Comando SQL nao informado na rotina!', false);
        endif;
    }//executaSQL
    public function retornaDados($tipo=NULL){
        switch (strtolower($tipo)):
            case "array":
                return mysql_fetch_array($this->dataset);
                break;
            case "assoc":
                return mysql_fetch_assoc($this->dataset);
                break;
            case "object":
                return mysql_fetch_object($this->dataset);
                break;
            default:
                return mysql_fetch_object($this->dataset);
                break;
        endswitch;
    }//retornaDados
    public function trataerro($arquivo=NULL,$rotina=NULL,$numero=NULL,$msgerro=NULL,$geraexcept=false){
        if($arquivo==NULL) $arquivo="Nao informado";
        if($rotina==NULL) $rotina="Nao informada";
        if($numero==NULL) $numero=mysql_errno($this->conexao);
        if($msgerro==NULL) $msgerro=  mysql_error($this->conexao);
        $resultado = 'Ocorreu um erro com os seguintes detalhes:<br/>
            <strong>Arquivo: </strong> '.$arquivo.' <br/>
            <strong>Rotina: </strong> '.$rotina.' <br/>
            <strong>Codigo: </strong> '.$numero.' <br/>
            <strong>Mensagem: </strong> '.$msgerro;
        if($geraexcept=false):
            echo ($resultado);
        else:            
            die ($resultado);
        endif;
    }//trataerro
}//fim classe banco
?>
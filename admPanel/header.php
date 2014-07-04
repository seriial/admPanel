<?php 
    require_once("funcoes.php");
    protegeArquivo(basename(__FILE__));
    verificaLogin();
    $sessao = new sessao();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-stict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt_BR" lang="pt_BR"/>
    <head>
        <meta http-equiv="Contet-Type" content="text/html; charset=utf-8"/>
        <meta name="description" content=""/>
        <meta name="keywords" content=""/>
        <title>Painel Administrativo</title>
        <link rel="shortcut icon" href="images/favicom.ico"/>
        <?
            loadCSS('reset');
            loadCSS('style');
            loadJS('jquery');
            loadJS('geral');
        ?>
    </head>
    <body class="painel">
        <div id="wrapper">
            <div id="header">
                <h1>Painel de Administração</h1>
            </div><!-- header -->
            <div id="wrap-content">     
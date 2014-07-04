<?
    include('header.php');
  
    if(isset($_GET['m'])) $modulo = $_GET['m'];
    if(isset($_GET['t'])) $tela = $_GET['t'];
?>
<div id="content">
<?
    if($modulo && $tela):
        loadModulo($modulo, $tela);
    else:
        echo '<p>Escolha uma opção no menu ao lado.';
    endif;
?>
</div><!-- content -->
<?
    include('sidebar.php');
?>

<?
    include('footer.php');
?>
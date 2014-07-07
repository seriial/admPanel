<?php
    require_once(dirname(dirname(__FILE__))."/funcoes.php");
    protegeArquivo(basename(__FILE__));
    loadJS('jquery-validate');
    loadJS('jquery-validate-messages');
    switch ($tela) {
    case 'login': 
        $sessao = new sessao();
        if($sessao->getNvars() > 0 || $sessao->getVar('logado') == TRUE || $sessao->getVar('ip') == $_SERVER['REMOTE_ADDR'] ) redireciona('painel.php');
        if(isset($_POST['logar'])):
            $user = new usuarios();
            $user->setValor('login', $_POST['usuario']);
            $user->setValor('senha', $_POST['senha']);
            if($user->doLogin($user)):
                redireciona('painel.php');
            else:
                redireciona('?erro=2');
            endif;
        endif;
        ?>
    <script type="text/javascript">
        $(document).ready(function(){
           $(".userform").validate({
               rules:{
                   usuario:{required:true, minlength:3},
                   senha:{required:true, rangelength:[4,10]},
               }
           }); 
        });
    </script>
        <div id="loginform">
            <form class="userform" method="post" action="">
                <fieldset>
                    <legend>Acesso restrito, identifique-se</legend>
                    <ul>
                        <li>
                            <label for="usuario">Usuário:</label>
                            <input type="text" size="35" name="usuario" value="<? echo $_POST['usuario'];?>"/>
                        </li>
                        <li>
                            <label for="senha">Senha:</label>
                            <input type="password" size="35" name="senha" value="<? echo $_POST['senha']; ?>"
                        </li>
                        <li class="center">
                            <input type="submit" name="logar" value="Login"/>
                        </li>
                    </ul>
                    <?
                        $erro = $_GET['erro'];
                        switch ($erro) {
                            case 1:
                                echo '<div class="sucesso">Você fez logoff do sistema.</div>';
                                break;
                            case 2:
                                echo '<div class="erro">Dados incorretos ou usuário inativo.</div>';
                                break;                            
                            case 3:
                                echo '<div class="erro">Faça login antes de acessar a pagina solicitada.</div>';
                                break;
                        }
                    ?>
                </fieldset>
            </form>
        </div>        
        <?
        break;
    case 'incluir':
        echo '<h2> Cadastro de usuarios </h2>';
        if(isset($_POST['cadastrar'])):
            $user = new usuarios(array(
                'nome'=>$_POST['nome'],
                'email'=>$_POST['email'],
                'login'=>$_POST['login'],
                'senha'=>codificaSenha($_POST['senha']),
                'administrador'=>($_POST['adm']=='on') ? 's' : 'n',
            ));
            if($user->existeRegistro('login', $_POST['login'])):
                printMSG('Este login já está cadastrado, escolha outro endereço', 'erro');
                $duplicado = TRUE;
            endif;        
            if($user->existeRegistro('email', $_POST['email'])):
                printMSG('Este e-mail já está cadastrado, escolha outro endereço', 'erro');
                $duplicado = TRUE;
            endif;
            if($duplicado != TRUE):
                $user->inserir($user);
                if($user->linhasafetadas ==1):
                     printMSG('Dados inseridos com sucesso. <a href="'.ADMURL.'?m=usuarios&t=listar">Exibir cadastros</a>');
                    unset($_POST);
                endif;                
            endif;
        endif;
        
        ?>
        <script type="text/javascript">
            $(document).ready(function(){
               $(".userform").validate({
                   rules:{
                       nome:{required:true, minlength:3},
                       email:{required:true, email:true},
                       login:{required:true, minlength:5},
                       senha:{required:true, rangelength:[4,10]},
                       senhaconf:{required:true, equalTo:"#senha"},
                   }
               }); 
            });
        </script>    
        <form class="userform" method="post" action="">
            <fieldstep>
                <legend>Informe os dados para cadastro</legend>
                <ul>
                    <li><label for="nome">Nome:</label>
                    <input type="text" size="50" name="nome" value="<? echo $_POST['nome']?>"/></li>
                    <li><label for="email">Email:</label>
                    <input type="text" size="50" name="email" value="<? echo $_POST['email']?>"/></li>
                    <li><label for="login">Login:</label>
                    <input type="text" size="35" name="login" value="<? echo $_POST['login']?>"/></li>
                    <li><label for="senha">Senha:</label>
                    <input type="password" size="25" name="senha" id="senha" value="<? echo $_POST['senha']?>"/></li>
                    <li><label for="senhaconf">Repita a senha:</label>
                    <input type="password" size="25" name="senhaconf" value="<? echo $_POST['senhaconf']?>"/></li>                        
                    <li><label for="adm">Administrador</label>
                        <input type="checkbox" name="adm" <? if(!isAdmin()) echo 'disabled="disabled"'; if($_POST['adm']) echo 'checked="checked"'; ?>/> dar controle total ao usuário</li> 
                    <li class="center"><input type="button" onclick="location.href='?m=usuarios&t=listar'" value="Cancelar"/><input type="submit" name="cadastrar" value="Salvar dados"></li>                                                
                  </ul>
            </fieldstep>
        </form>
        <?
        break;
    case 'listar':
        echo '<h2> Usuarios cadastrados </h2>';
        loadCSS('data-table', null, TRUE);
        loadJS('jquery-datatable');
        ?>
        <script type="text/javascript">
            $(document).ready(function(){
               $("#listausers").dataTable({
                    "oLanguage": {
                        "lengthMenu": "Display _MENU_ records per page",
                        "sZeroRecords": "Nenhum dado encontrado para exibição",
                        "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ de registros",
                        "sInfoEmpty": "Nenhum registro para ser exibido",
                        "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
                        "sSearch" : "Pesquisar",
                    },
                    "sScrollY" : "400px",
                    "bPaginate" : false,
                    "aaSorting" : [[0, "asc"]]
               });
            });
        </script>
        <table cellspacing="0" cellpadding="0" border="0" class="display" id="listausers">
            <thead>
                <tr>
                    <th>Nome</th><th>Email</th><th>Login</th><th>Ativo/Adm</th><th>Cadastro</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?
                $user = new usuarios();
                $user->selecionaTudo($user);
                while($res = $user->retornaDados()):
                    echo '<tr>';
                    printf('<td>%s</td>', $res->nome);
                    printf('<td>%s</td>', $res->email);
                    printf('<td>%s</td>', $res->login);
                    printf('<td class="center">%s/%s</td>', strtoupper($res->ativo), strtoupper($res->administrador));
                    printf('<td class="center">%s</td>', date("d/m/Y", strtotime($res->datacad)));
                    printf('<td class="center"> <a href="?m=usuarios&t=editar&id=%s" tittle="Editar"><img src="images/edit.png" alt="Editar"/></a> <a href="?m=usuarios&t=senha&id=%s" tittle="Mudar senha"><img src="images/pass.png" alt="Mudar senha"/></a> <a href="?m=usuarios&t=excluir&id=%s" tittle="Excluir"><img src="images/delete.png" alt="excluir"/></a></td>', $res->id, $res->id, $res->id);
                    echo '</tr>';
                endwhile;
                ?>
            </tbody>
        </table>
        <?
            
        break; 
    case 'editar':
        echo '<h2>Edição de usuários</h2>';
        $sessao = new sessao();
        if(isAdmin() == TRUE || $sessao->getVar('iduser') == $_GET['id']):
            if(isset($_GET['id'])):
                $id = $_GET['id'];  
                if(isset($_POST['editar'])):
                    $user = new usuarios(array(
                        'nome' => $_POST['nome'],
                        'email' => $_POST['email'],
                        'ativo' => ($_POST['ativo'] == 'on') ? 's' : 'n',
                        'administrador' => ($_POST['adm'] == 'on') ? 's' : 'n',
                    ));
                    $user->valorpk = $id;
                    $user->extras_select = "WHERE id=$id";
                    $user->selecionaTudo($user);
                    $res = $user->retornaDados();
                    if($res->email != $_POST['email']):
                        if($user->existeRegistro('email', $_POST['email'])):
                            printMSG('Este email já existe no sistema, esclha outro endereço!', 'erro');
                            $duplicado = TRUE;
                        endif;
                    endif;
                    if($duplicado != TRUE):
                        $user->atualizar($user);
                        if($user->linhasafetadas == 1):
                            printMSG('Dados alterados com sucesso. <a href="?m=usuarios&t=listar">Exibir cadastros</a>');
                            unset($_POST);
                        else:
                            printMSG('Nenhum dado foi alterado. <a href="?m=usuarios&t=listar">Exibir cadastros</a>', 'alerta');
                        endif;
                    endif;                
                endif;                              
                $userbd = new usuarios();
                $userbd->extras_select = "WHERE id=$id";
                $userbd->selecionaTudo($userbd);
                $resbd = $userbd->retornaDados();
            else:                
                printMSG('Usuário não definido, <a href="?m=usuarios*t=listar">escolha um usuário para alterar</a>', 'erro');
            endif;
            ?>
            <script type="text/javascript">
                $(document).ready(function(){
                   $(".userform").validate({
                       rules:{
                           nome:{required:true, minlength:3},
                           email:{required:true, email:true},
                       }
                   }); 
                });
            </script>    
            <form class="userform" method="post" action="">
                <fieldstep>
                    <legend>Informe os dados para alteração</legend>
                    <ul>
                        <li><label for="nome">Nome:</label>
                        <input type="text" size="50" name="nome" value="<? if($resbd) echo $resbd->nome ?>"/></li>
                        <li><label for="email">Email:</label>
                        <input type="text" size="50" name="email" value="<? if($resbd) echo $resbd->email ?>"/></li>
                        <li><label for="login">Login:</label>
                        <input type="text" size="35" name="login" disabled="disabled" value="<? if($resbd) echo $resbd->login ?>"/></li>
                        <li><label for="ativo">Ativo:</label>
                        <input type="checkbox" name="ativo" <? if(!isAdmin()) echo 'disabled="disabled"'; if($resbd->ativo == 's') echo 'checked="checked"'; ?>/> habilitar ou desabilitar o usuário.</li> 
                        <li><label for="adm">Administrador</label>
                        <input type="checkbox" name="adm" <? if(!isAdmin()) echo 'disabled="disabled"'; if($resbd->administrador == 's') echo 'checked="checked"'; ?>/> dar controle total ao usuário</li> 
                        <li class="center"><input type="button" onclick="location.href='?m=usuarios&t=listar'" value="Cancelar"/><input type="submit" name="editar" value="Salvar Alterações"></li>                                                
                      </ul>
                </fieldstep>
            </form>                
            <?
        else:
            printMSG('Você não tem permissão para acessar esta pagina. <a href="#" onclick="history.back()">Voltar</a>', 'erro');
        endif;
        break;        
   case 'senha':
        echo '<h2>Alteração de senha</h2>';
        $sessao = new sessao();
        if(isAdmin() == TRUE || $sessao->getVar('iduser') == $_GET['id']):
            if(isset($_GET['id'])):
                $id = $_GET['id'];  
                if(isset($_POST['mudasenha'])):
                    $user = new usuarios(array(
                        'senha' => codificaSenha($_POST['senha']),
                    ));
                    $user->valorpk = $id;
                    $user->atualizar($user);
                    if($user->linhasafetadas == 1):
                        printMSG('Senha alterada com sucesso. <a href="?m=usuarios&t=listar">Exibir cadastros</a>');
                        unset($_POST);
                    else:
                        printMSG('Nenhum dado foi alterado. <a href="?m=usuarios&t=listar">Exibir cadastros</a>', 'alerta');
                    endif;               
                endif;                              
                $userbd = new usuarios();
                $userbd->extras_select = "WHERE id=$id";
                $userbd->selecionaTudo($userbd);
                $resbd = $userbd->retornaDados();
            else:                
                printMSG('Usuário não definido, <a href="?m=usuarios*t=listar">escolha um usuário para alterar</a>', 'erro');
            endif;
            ?>
            <script type="text/javascript">
                $(document).ready(function(){
                   $(".userform").validate({
                       rules:{
                        senha:{required:true, rangelength:[4,10]},
                        senhaconf:{required:true, equalTo:"#senha"},
                       }
                   }); 
                });
            </script>    
            <form class="userform" method="post" action="">
                <fieldstep>
                    <legend>Informe os dados para alteração</legend>
                    <ul>
                        <li><label for="nome">Nome:</label>
                        <input type="text" size="50" name="nome" disabled="disabled" value="<? if($resbd) echo $resbd->nome ?>"/></li>
                        <li><label for="email">Email:</label>
                        <input type="text" size="50" name="email" disabled="disabled" value="<? if($resbd) echo $resbd->email ?>"/></li>
                        <li><label for="login">Login:</label>
                        <input type="text" size="35" name="login" disabled="disabled" value="<? if($resbd) echo $resbd->login ?>"/></li>
                        <li><label for="senha">Senha:</label>
                        <input type="password" size="25" name="senha" id="senha" value="<? echo $_POST['senha']?>"/></li>
                        <li><label for="senhaconf">Repita a senha:</label>
                        <input type="password" size="25" name="senhaconf" value="<? echo $_POST['senhaconf']?>"/></li>                        
                        <li class="center"><input type="button" onclick="location.href='?m=usuarios&t=listar'" value="Cancelar"/><input type="submit" name="mudasenha" value="Salvar Alterações"></li>                                                
                      </ul>
                </fieldstep>
            </form>                
            <?
        else:
            printMSG('Você não tem permissão para acessar esta pagina. <a href="#" onclick="history.back()">Voltar</a>', 'erro');
        endif;
        break;
        
    case 'excluir':
        echo '<h2>Exclusão de usuários</h2>';
        $sessao = new sessao();
        if(isAdmin() == TRUE):
            if(isset($_GET['id'])):
                $id = $_GET['id'];  
                if(isset($_POST['excluir'])):
                    $user = new usuarios();
                    $user->valorpk = $id;
                    $user->deletar($user);
                    if($user->linhasafetadas == 1):
                        printMSG('Registro excluido com sucesso. <a href="?m=usuarios&t=listar">Exibir cadastros</a>');
                        unset($_POST);
                    else:
                        printMSG('Nenhum registro foi excluido. <a href="?m=usuarios&t=listar">Exibir cadastros</a>', 'alerta');
                    endif;
                endif;                                             
                $userbd = new usuarios();
                $userbd->extras_select = "WHERE id=$id";
                $userbd->selecionaTudo($userbd);
                $resbd = $userbd->retornaDados();
            else:                
                printMSG('Usuário não definido, <a href="?m=usuarios*t=listar">escolha um usuário para excluir</a>', 'erro');
            endif;
            ?>   
            <form class="userform" method="post" action="">
                <fieldstep>
                    <legend>Confira os dados para exclusão</legend>
                    <ul>
                        <li><label for="nome">Nome:</label>
                        <input type="text" size="50" name="nome" disabled="disabled" value="<? if($resbd) echo $resbd->nome ?>"/></li>
                        <li><label for="email">Email:</label>
                        <input type="text" size="50" name="email" disabled="disabled" value="<? if($resbd) echo $resbd->email ?>"/></li>
                        <li><label for="login">Login:</label>
                        <input type="text" size="35" name="login" disabled="disabled" value="<? if($resbd) echo $resbd->login ?>"/></li>
                        <li><label for="ativo">Ativo:</label>
                        <input type="checkbox" name="ativo" disabled="disabled" <?  if($resbd->ativo == 's') echo 'checked="checked"'; ?>/> habilitar ou desabilitar o usuário.</li> 
                        <li><label for="adm">Administrador</label>
                        <input type="checkbox" name="adm" disabled="disabled" <?  if($resbd->administrador == 's') echo 'checked="checked"'; ?>/> dar controle total ao usuário</li> 
                        <li class="center"><input type="button" onclick="location.href='?m=usuarios&t=listar'" value="Cancelar"/><input type="submit" name="excluir" value="Confirmar Exclusão"></li>                                                
                      </ul>
                </fieldstep>
            </form>                
            <?
        else:
            printMSG('Você não tem permissão para acessar esta pagina. <a href="#" onclick="history.back()">Voltar</a>', 'erro');
        endif;
        break;                
    default:
        echo '<p>A tela solicitada não existe.<p/>';
        break;
}
?>
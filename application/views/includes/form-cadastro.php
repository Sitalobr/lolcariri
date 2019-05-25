<form method="post" action="<?=base_url('rank/cadastrar')?>">
    <div class="ui form warning basic segment">
        <?php 
        $message = $this->session->flashdata('message');
        $tipoMensagem = $this->session->flashdata('tipoMensagem');
        if(function_exists('validation_errors') && validation_errors() != ''){
            echo '<div class="ui warning message">';
            echo validation_errors('<div class="list"><li>', '</li></div>');
            echo '</div>';
        } else if(!empty($message) && $tipoMensagem == 'sucesso'){
            echo '<div class="ui ignored positive icon message">';
            echo '<div class="content">';
            echo '<h4 class="header">Sucesso!</h4>';
            echo '<p>' . $message . '</p>';
            echo '</div>';
            echo '</div>';
        } else if(!empty($message) && $tipoMensagem == 'info'){
            echo '<div class="ui warning message">';
            echo '<div class="list"><li>'. $message .'</li></div>';
            echo '</div>';
        }
        ?>
        <div class="field">
            <label>Nome:</label>
            <div class="ui input">
                <input type="text" name="nome" placeholder="Digite o seu nome" required>
                <div class="ui corner label">
                    <i class="icon asterisk"></i>
                </div>
            </div>
        </div>
        <div class="field">
            <label>Nick:</label>
            <div class="ui input">
                <input type="text" name="nickname" placeholder="Digite o seu nick do LOL" required>
                <div class="ui corner label">
                    <i class="icon asterisk"></i>
                </div>
            </div>
        </div>
        <div class="field">
            <label>E-mail:</label>
            <div class="ui input">
                <input type="text" name="email" placeholder="Digite o seu e-mail" required>
                <div class="ui corner label">
                    <i class="icon asterisk"></i>
                </div>
            </div>
        </div>
        <div class="field">
            <label>Cidade em que mora:</label>
            <div class="ui selection dropdown fluid">
                <input id="cidade" name="cidade" type="hidden" required/>
                <div class="default text">Selecione</div>
                <i class="dropdown icon"></i>
                <div class="menu">
                    <div class='item' data-value='Barbalha'>Barbalha</div>
                    <div class='item' data-value='Barro'>Barro</div>
                    <div class='item' data-value='Caririaçu'>Caririaçu</div>
                    <div class='item' data-value='Crato'>Crato</div>
                    <div class='item' data-value='Farias Brito'>Farias Brito</div>
                    <div class='item' data-value='Jardim'>Jardim</div>
                    <div class='item' data-value='J. do Norte'>Juazeiro do Norte</div>
                    <div class='item' data-value='Mauriti'>Mauriti</div>
                    <div class='item' data-value='Missão Velha'>Missão Velha</div>
                    <div class='item' data-value='Nova Olinda'>Nova Olinda</div>
                    <div class='item' data-value='Santana do Cariri'>Santana do Cariri</div>
                </div>
            </div>
        </div>
        <button id="confirmar" class="ui green submit fluid button">
            <i class="icon checkmark"></i>
            Cadastrar
        </button>
    </div>
</form>

<script>
    $('.ui.dropdown').dropdown();
</script>
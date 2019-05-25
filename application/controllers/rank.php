<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rank extends CI_Controller {
    
    private $key = "";
    
    public function __construct(){
        parent::__construct();
        $this->key = "edb6022b-7d04-4e34-9dde-96e69846dbdf";
        $this->form_validation->set_message('required', 'O campo %s não foi informado!');
        $this->form_validation->set_message('is_unique', 'O %s informado já está cadastrado!');
        $this->form_validation->set_message('valid_email', 'O %s informado não é um endereço válido!');
    }

	public function index(){
		$this->load->view('rank/cadastrar');
	}
    
    public function cadastrar(){
        // regras para validação do formulário
        $this->form_validation->set_rules('nome', 'Nome', 'required|max_length[55]|alpha_dash|ucwords');
        $this->form_validation->set_rules('nickname', 'Nickname', 'required|max_length[16]|min_length[3]|is_unique[player.nickname]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|mas_lenght[30]|valid_email');
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        
        // verifica se todos os campos foram preenchidos corretamente de acordo com as regras acima
        if($this->form_validation->run()){
            $this->load->model('player'); // carrega o modelo para inserção do player no banco de dados
            $this->load->model('status'); // carrega o modelo para inserção de status no banco de dados
            
            $nick = strtolower($this->input->post('nickname')); // acesso à variável nickname via post
            $dadosSummoner = $this->getSummonerByName($nick); // requisição API riot
            
            $summonerID = $dadosSummoner['summoner_id']; // guarda valor da variável summoner id para posterior cadastro do status
            $summonerName = $dadosSummoner['nickname']; // acesso ao nickname do player
            
            $idPlayer = $this->player->inserir($dadosSummoner); // insere os dados
            $dadosInfo = $this->getLeagueStatusBySummonerID($summonerID, $idPlayer, $summonerName); // requisição API riot
            $this->status->inserir($dadosInfo); // insere os dados

            $this->session->set_flashdata('tipoMensagem', 'sucesso');
            $this->session->set_flashdata('message', 'Cadastrado com sucesso! Por favor, aguarde uns dias enquanto o ranking está sendo preparado...');
            redirect('rank/cadastrar');
        } else {
            $this->load->view('rank/cadastrar');
        }
    }
    
    private function getSummonerByName($nick){
        $nick = strtolower($this->input->post('nickname')); // acesso à variável nickname via post
        $nick = str_replace(" ", "", $nick); // tira todos os espaços digitado no campo nickname
        $urlGETSummoner = 'https://br.api.pvp.net/api/lol/br/v1.4/summoner/by-name/'.$nick.'?api_key='.$this->key;
        
        while(TRUE){ // o while é executado até que a requisição funcione
            $respostaSummoner = $this->requestAPI($urlGETSummoner); // resposta recebida da função de requisição
            
            if(!is_numeric($respostaSummoner)){ // verifica se foi retornado um array de dados da requisição externa
                $summoner = $respostaSummoner->$nick; // acesso aos dados trazidos pela requisição
                $summonerLevel = $summoner->summonerLevel; // acesso ao level do invocador
                if($summonerLevel < 30){
                    $this->session->set_flashdata('tipoMensagem', 'info');
                    $this->session->set_flashdata('message', 'Por favor, aguarde até que você chegue ao level 30 e comece a jogar filas ranqueadas!');
                    redirect('rank/cadastrar');
                    break; // quebra o loop de resposta summoner
                }
                $summonerName = $summoner->name; // acesso ao summoner name
                $summonerID = $summoner->id; // acesso ao summoner id
                $iconID = $summoner->profileIconId; // acesso ao id do ícone de invocador
            } else if($respostaSummoner == 404) { // verifica se não foi encontrada informações sobre o nickname informado
                $this->session->set_flashdata('tipoMensagem', 'info');
                $this->session->set_flashdata('message', 'Não foi encontrado nenhum registro para o nickname informado!');
                redirect('rank/cadastrar');
                break; // quebra o loop de resposta summoner
            }
            
            // preenche array com os dados do jogador
            $dados = array('nome' => $this->input->post('nome'), 'nickname' => $summonerName, 'email' => $this->input->post('email'), 'cidade' => $this->input->post('cidade'), 'icone_perfil_id' => $iconID, 'summoner_id' => $summonerID);
            
            return $dados;
        }
    }
    
    private function getLeagueStatusBySummonerID($summonerID, $idPlayer="", $summonerName=""){
        $urlGETInfo = 'https://br.api.pvp.net/api/lol/br/v2.5/league/by-summoner/'.$summonerID.'/entry?api_key='.$this->key;
        
        while(TRUE){ // o while é executado até que a requisição funcione
            $respostaInfo = $this->requestAPI($urlGETInfo); // resposta recebida da função de requisição
            
            if(!is_numeric($respostaInfo)){ // verifica se foi retornado um array de dados da requisição externa
                $info = $respostaInfo->$summonerID; // acesso aos dados trazidos pela requisição
                
                if(is_array($info) && count($info) > 0){
                    if(property_exists($info[0], "entries")){
                        $entries = $info[0]->entries;
                        foreach ( $entries as $e ){
                            $divisao = $e->division; // acesso à divisão do player
                            $lp = $e->leaguePoints; // acesso aos pontos de liga do player
                        }
                    }
                    $leagueName = $info[0]->name; // acesso ao nome da liga do player
                    $tier = $info[0]->tier; // acesso ao tier do player
                }
                
            } else if($respostaInfo == 404) { // verifica se não foi encontrada informações de liga sobre o id informado
                $this->session->set_flashdata('tipoMensagem', 'info');
                $this->session->set_flashdata('message', 'Cadastrado! Porém não foi encontrado nenhum registro de liga para o nickname informado!');
                redirect('rank/cadastrar');
                break; // quebra o loop de resposta summoner
            }
            
            $dados = array(
                'player_name' => $summonerName,
                'division' => $divisao,
                'league_name' => $leagueName,
                'league_points' => $lp,
                'tier' => $tier,
                'id_player' => $idPlayer
            );
            
            return $dados;
        }
    }
    
    private function requestAPI($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // isso vai retornar o conteúdo como uma string
        $result = curl_exec($ch);
        $statuscode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $jsonArray = json_decode($result);
        curl_close($ch);
        if($statuscode == 404){
            return $statuscode;
        } else if($statuscode == 200){
            return $jsonArray;
        } else {
            return $statuscode;
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
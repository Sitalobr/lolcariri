<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Player extends CI_Model {
    
    public function inserir($dados = NULL){
        if(is_array($dados) && $dados != NULL){
            $this->db->insert('player', $dados);
            return $this->db->insert_id();
        }
    }
    
}
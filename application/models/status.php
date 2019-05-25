<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Status extends CI_Model {
    
    public function inserir($dados = NULL){
        if(is_array($dados) && $dados != NULL){
            $this->db->insert('last_status', $dados);
            return $this->db->insert_id();
        }
    }
    
}
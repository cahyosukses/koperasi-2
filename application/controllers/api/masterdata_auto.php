<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/libraries/REST_Controller.php';

class Masterdata_auto extends REST_Controller{
    function __construct(){
        parent::__construct();
        $this->limit = 20;

        $id_user = $this->session->userdata('id_user');
        if (empty($id_user)) {
            $this->response(array('error' => 'Anda belum login'), 401);
        }
    }

    private function start($page){
        return (($page - 1) * $this->limit);
    }
    
    function norek_pinjaman_auto_get() {
        $param['search']    = get_safe('q');
        $start = $this->start(get_safe('page'));
        $data = $this->m_transaksi->get_auto_rekening_pinjaman($param, $start, $this->limit);
        if ((get_safe('page') == 1) & (get_safe('q') == '')) {
            $pilih[] = array('id'=>'', 'nomor_rekening' => '', 'nama' =>'', 'alamat' => '');
            $data['data'] = array_merge($pilih, $data['data']);
            $data['total'] += 1;
        }
        $this->response($data, 200);
    }
    
    function norek_tabungan_auto_get() {
        $param['search']    = get_safe('q');
        $start = $this->start(get_safe('page'));
        $data = $this->m_transaksi->get_auto_rekening_tabungan($param, $start, $this->limit);
        if ((get_safe('page') == 1) & (get_safe('q') == '')) {
            $pilih[] = array('id'=>'', 'no_rekening' => '', 'nama' =>'', 'alamat' => '');
            $data['data'] = array_merge($pilih, $data['data']);
            $data['total'] += 1;
        }
        $this->response($data, 200);
    }
    
    function rka_auto_get() {
        $param['search']    = get_safe('q');
        $start = $this->start(get_safe('page'));
        $data = $this->m_masterdata->get_auto_rka($param, $start, $this->limit);
        if ((get_safe('page') == 1) & (get_safe('q') == '')) {
            $pilih[] = array('id'=>'', 'nomor_rekening' => '', 'nama' =>'', 'alamat' => '');
            $data['data'] = array_merge($pilih, $data['data']);
            $data['total'] += 1;
        }
        $this->response($data, 200);
    }
    
}
<?php

class M_laporan extends CI_Model {
    
    function id_tahun_anggaran() {
        return $this->db->get_where('tb_tahun_anggaran', array('aktifasi' => 'Ya'))->row()->id;
    }
    
    function data_header() {
        return $this->db->get('tb_sekolah')->row();
    }
    
    function get_list_kas_harian($limit = null, $start = null, $search = null) {
        $q = null;
        if ($search['id'] !== '') {
            $q.=" and id = '".$search['id']."'";
        }
        if ($search['awal'] !== '') {
            $q.=" and date(waktu) = '".$search['awal']."'";
        }
        
        $select = "select * ";
        $count  = "select count(*) as count ";
        $sql = " 
            from tb_arus_kas
            where id is not NULL";
        $limitation = null;
        if ($limit !== NULL) {
            $limitation.=" limit $start , $limit";
        }
        $order=" order by id asc";
        $result = $this->db->query($select.$sql.$q.$order.$limitation)->result();
        foreach ($result as $key => $value) {
            $sql_child = "select IFNULL(SUM(masuk)-SUM(keluar),0) as awal from tb_arus_kas where id < '".$value->id."'";
            $result[$key]->awal = $this->db->query($sql_child)->row()->awal;
            
            $sql_child = "select IFNULL(SUM(masuk)-SUM(keluar),0) as sisa_saldo from tb_arus_kas where id <= '".$value->id."'";
            $result[$key]->sisa_saldo = $this->db->query($sql_child)->row()->sisa_saldo;
        }
        $data['data'] = $result;
        $data['jumlah'] = $this->db->query($count.$sql.$q)->row()->count;
        $sql_awal = "select IFNULL(SUM(masuk)-SUM(keluar),0) as sisa from tb_arus_kas where date(waktu) < '".$search['awal']."'";
        $data['awal'] = $this->db->query($sql_awal)->row()->sisa;
        $date = explode('-', $search['awal']);
        $varia = mktime(0, 0, 0, $date[1], $date[2]-1, $date[0]);
        $data['kemaren'] = date("d/m/Y",$varia);
        $data['today'] = date("d/m/Y");
        return $data;
    }
    
    function get_list_terlambat_angsuran($limit = null, $start = null, $search = null) {
        $q = null;
        if ($search['id'] !== '') {
            $q.=" and id = '".$search['id']."'";
        }
        
        $select = "select dp.*, db.nama, db.alamat, db.nomor_rekening ";
        $count  = "select count(*) as count ";
        $sql = " 
            from tb_detail_pinjaman dp
            join tb_pinjaman pj on (dp.id_pinjaman = pj.id)
            join tb_debitur db on (pj.id_debitur = db.id)
            where dp.tgl_bayar is NULL and status_bayar = 'Belum' and dp.jatuh_tempo <= NOW()";
        $limitation = null;
        if ($limit !== NULL) {
            $limitation.=" limit $start , $limit";
        }
        $order=" order by id asc";
        //echo $count.$sql.$q;
        $result = $this->db->query($select.$sql.$q.$order.$limitation)->result();
        $data['data'] = $result;
        $data['jumlah'] = $this->db->query($count.$sql.$q)->row()->count;
        return $data;
    }
    
    function get_list_pendapatan_administrasi($limit, $start, $search) {
        $q = null;
        if ($search['id'] !== '') {
            $q.=" and d.id = '".$search['id']."'";
        }
        if ($search['awal'] !== '' and $search['akhir'] !== '') {
            $q.=" and ap.tgl_input between '".date2mysql($search['awal'])."' and '".  date2mysql($search['akhir'])."'";
        }
        if ($search['nama'] !== '') {
            $q.=" and d.nama like ('%".$search['nama']."%')";
        }
        if ($search['norek'] !== '') {
            $q.=" and d.nomor_rekening = '".$search['norek']."'";
        }
        
        $select = "select ap.*, d.nama, d.nomor_rekening";
        $count  = "select count(p.id) as count ";
        $sql = "
            from tb_adpro ap 
            join tb_pinjaman p on (ap.id_pinjaman = p.id)
            join tb_debitur d on (p.id_debitur = d.id)
            join tb_detail_debitur dd on (dd.id_debitur = d.id)
            where p.id is not NULL ";
        $limitation = null;
        if ($limit !== NULL) {
            $limitation.=" limit $start , $limit";
        }
        $order=" order by p.id desc";
        //echo $sql . $q . $order. $limitation;
        
        $data['data'] = $this->db->query($select . $sql . $q . $order. $limitation)->result();
        $data['jumlah'] = $this->db->query($count . $sql . $q)->row()->count;
        return $data;
    }
}
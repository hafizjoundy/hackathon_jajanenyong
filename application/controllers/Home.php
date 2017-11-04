<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('kuliner');
		session_start();
	}

	public function signin($username,$password){ //halaman login
		$signin = $this->kuliner->signin($username);
		if(count($signin) == 1){
			if($signin[0]['level'] == 3){
				if(password_verify($password, $signin[0]['password'])){
					$_SESSION['pembeli_usersid'] = $signin[0]['id'];
					$_SESSION['pembeli_username'] = $signin[0]['username'];
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}

	public function signup($username,$password){ //halaman daftar
		$level = 3; //level pembeli
		$password = password_hash($password, PASSWORD_DEFAULT);
		$data = [
			'username' => $username,
			'password' => $password,
			'level' => $level
		];
		return $this->kuliner->signup($data);
	}

	public function logout(){
		session_unset();
		session_destroy();
	}

	public function index() //halaman index/ menu utama
	{
		$d['kuliner_makanan'] = $this->kuliner->getkuliner('makanan');
		$d['kuliner_minuman'] = $this->kuliner->getkuliner('minuman');
		$this->load->view('home',$d);
	}

	public function users($id){ //halaman profile users
		$users = $this->kuliner->users($id);
		if($users['level'] == 2){
			// halaman penjual
			echo "penjual";
		}
		else if($users['level'] == 3){
			// halaman pembeli
			echo "pembeli";
		}
		
	}

	public function kuliner($id) //halaman kuliner khas
	{
		$d['kulinerbyid'] = $this->kuliner->getkulinerbyid($id);
		$d['komentar'] = $this->kuliner->getcomentkuliner($id);
		$d['penjual'] = $this->kuliner->getpenjualkuliner($id);
		$this->load->view('getkuliner',$d);
	}

	public function insertkomentar(){
		$users = $_SESSION['users_id'];
		$kuliner = $this->input->post('id_kuliner');
		$komentar = $this->input->post('komentar');
		$data = [
			'users' => $users,
			'kuliner' => $kuliner,
			'komentar' => $komentar
		];
	}

	// public function deletekomentar(){
		
	// }

	public function makanan(){ //laman more kuliner makanan
		$config['base_url'] = base_url().'home/makanan/page/';
		$config['total_rows'] = $this->kuliner->getrowskuliner('makanan');
		$config['per_page'] = 1;
		
		$this->pagination->initialize($config);
		$data['makanan'] = $this->kuliner->getkulinerlimit('makanan',$config['per_page'],$this->uri->segment(4));

		// var_dump($data['makanan']);
		echo $this->pagination->create_links();
	}

	public function minuman(){ //halaman more kuliner minuman
		$config['base_url'] = base_url().'home/minuman/page/';
		$config['total_rows'] = $this->kuliner->getrowskuliner('minuman');
		$config['per_page'] = 1;
		
		$this->pagination->initialize($config);
		$data['minuman'] = $this->kuliner->getkulinerlimit('minuman',$config['per_page'],$this->uri->segment(4));

		// var_dump($data['minuman']);
		echo $this->pagination->create_links();
	}
}
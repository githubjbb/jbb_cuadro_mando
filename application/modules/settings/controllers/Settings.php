<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(FCPATH.'vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Settings extends CI_Controller {
	
    public function __construct() {
        parent::__construct();
        $this->load->model("settings_model");
        $this->load->model("general_model");
		$this->load->helper('form');
    }
	
	/**
	 * users List
     * @since 15/12/2016
     * @author BMOTTAG
	 */
	public function users($state=1)
	{			
			$data['state'] = $state;
			
			if($state == 1){
				$arrParam = array("filtroState" => TRUE);
			}else{
				$arrParam = array("state" => $state);
			}
			
			$data['info'] = $this->general_model->get_user($arrParam);
			
			$data["view"] = 'users';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario Users
     * @since 15/12/2016
     */
    public function cargarModalUsers() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idEmployee"] = $this->input->post("idEmployee");	
			
			$arrParam = array("filtro" => TRUE);
			$data['roles'] = $this->general_model->get_roles($arrParam);

			$arrParam = array(
				"table" => "param_dependencias",
				"order" => "dependencia",
				"id" => "x"
			);
			$data['dependencias'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "param_area_responsable",
				"order" => "area_responsable",
				"id" => "x"
			);
			$data['lista_area_responsable'] = $this->general_model->get_basic_search($arrParam);

			if ($data["idEmployee"] != 'x') {
				$arrParam = array(
					"table" => "usuarios",
					"order" => "id_user",
					"column" => "id_user",
					"id" => $data["idEmployee"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("users_modal", $data);
    }
	
	/**
	 * Update User
     * @since 15/12/2016
     * @author BMOTTAG
	 */
	public function save_user()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idUser = $this->input->post('hddId');
			$msj = "Se adicionó un nuevo Usuario!";
			if ($idUser != '') {
				$msj = "Se actualizó el Usuario!";
			}			
			$log_user = $this->input->post('user');
			$email_user = $this->input->post('email');
			$result_user = false;
			$result_email = false;
			$result_ldap = false;
			//verificar si ya existe el usuario
			$arrParam = array(
				"idUser" => $idUser,
				"column" => "log_user",
				"value" => $log_user
			);
			$result_user = $this->settings_model->verifyUser($arrParam);
			//verificar si ya existe el correo
			$arrParam = array(
				"idUser" => $idUser,
				"column" => "email",
				"value" => $email_user
			);
			$result_email = $this->settings_model->verifyUser($arrParam);
			$data["state"] = $this->input->post('state');
			if ($idUser == '') {
				$data["state"] = 1;//para el direccionamiento del JS, cuando es usuario nuevo no se envia state

				$ldapuser = $this->session->userdata('logUser');
				$ldappass = ldap_escape($this->session->userdata('password'), null, LDAP_ESCAPE_FILTER);
				$ds = ldap_connect("192.168.0.44", "389") or die("No es posible conectar con el directorio activo.");  // Servidor LDAP!
		        if (!$ds) {
		            echo "<br /><h4>Servidor LDAP no disponible</h4>";
		            @ldap_close($ds);
		        } else {
		            $ldapdominio = "jardin";
		            $ldapusercn = $ldapdominio . "\\" . $ldapuser;
		            $binddn = "dc=jardin, dc=local";
		            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            		ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
		            $r = @ldap_bind($ds, $ldapusercn, $ldappass);
		            if (!$r) {
		                @ldap_close($ds);
		                $data["msj"] = "Error de autenticación. Por favor revisar usuario y contraseña de red.";
		                $this->session->sess_destroy();
						$this->load->view('login', $data);
		            } else {
		            	$filter = "(&(sAMAccountName=" . $log_user . ")(mail=" . $email_user . "))";
		            	$attributes = array('sAMAccountName', 'mail');
		            	$result = @ldap_search($ds, $binddn, $filter, $attributes);
		            	if (@ldap_count_entries($ds, $result) == 1) {
		            		$result_ldap = false;
		            	} else {
		            		$result_ldap = true;
		            	}
		            }
		        }

			}
			if ($result_user || $result_email || $result_ldap)
			{
				$data["result"] = "error";
				if($result_user) {
					$data["mensaje"] = " Error. El usuario ya existe.";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> El usuario ya existe.');
				}
				if($result_email) {
					$data["mensaje"] = " Error. El correo ya existe.";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> El correo ya existe.');
				}
				if($result_user && $result_email) {
					$data["mensaje"] = " Error. El usuario y el correo ya existen.";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> El usuario y el correo ya existen.');
				}

				if ($result_ldap) {
					$data["mensaje"] = " Error. El usuario no existe en el directorio activo.";
					$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> El usuario no esta creado en el directorio activo.');
				}

			} else {
					if ($this->settings_model->saveUser()) {
						$data["result"] = true;					
						$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
					} else {
						$data["result"] = "error";					
						$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
					}
			}
			echo json_encode($data);
    }
	
	/**
	 * Reset employee password
	 * Reset the password to '123456'
	 * And change the status to '0' to changue de password 
     * @since 11/1/2017
     * @author BMOTTAG
	 */
	public function resetPassword($idUser)
	{
			if ($this->settings_model->resetEmployeePassword($idUser)) {
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> You have reset the Employee pasword to: 123456');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
			
			redirect("/settings/employee/",'refresh');
	}	

	/**
	 * Change password
     * @since 15/4/2017
     * @author BMOTTAG
	 */
	public function change_password($idUser)
	{
			if (empty($idUser)) {
				show_error('ERROR!!! - You are in the wrong place. The ID USER is missing.');
			}
			
			$arrParam = array(
				"table" => "usuarios",
				"order" => "id_user",
				"column" => "id_user",
				"id" => $idUser
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);
		
			$data["view"] = "form_password";
			$this->load->view("layout", $data);
	}
	
	/**
	 * Update user´s password
	 */
	public function update_password()
	{
			$data = array();			
			
			$newPassword = $this->input->post("inputPassword");
			$confirm = $this->input->post("inputConfirm");
			$userState = $this->input->post("hddState");
			
			//Para redireccionar el usuario
			if($userState!=2){
				$userState = 1;
			}
			
			$passwd = str_replace(array("<",">","[","]","*","^","-","'","="),"",$newPassword); 
			
			$data['linkBack'] = "settings/users/" . $userState;
			$data['titulo'] = "<i class='fa fa-unlock fa-fw'></i>CAMBIAR CONTRASEÑA";
			
			if($newPassword == $confirm)
			{					
					if ($this->settings_model->updatePassword()) {
						$data['msj'] = 'Se actualizó la contraseña del usuario.';
						$data['msj'] .= '<br>';
						$data['msj'] .= '<br><strong>Nombre Usuario: </strong>' . $this->input->post('hddUser');
						$data['msj'] .= '<br><strong>Contraseña: </strong>' . $passwd;
						$data['clase'] = 'alert-success';
					}else{
						$data['msj'] = '<strong>Error!!!</strong> Ask for help.';
						$data['clase'] = 'alert-danger';
					}
			}else{
				//definir mensaje de error
				echo "pailas no son iguales";
			}
						
			$data["view"] = "template/answer";
			$this->load->view("layout", $data);
	}
	
	/**
	 * Lista de proyectos
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function proyectos()
	{
			$arrParam = array(
				"table" => "proyecto_inversion",
				"order" => "id_proyecto_inversion",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'proyectos';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario proyectos
     * @since 15/04/2022
     */
    public function cargarModalProyecto() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idProyecto"] = $this->input->post("idProyecto");	
			
			if ($data["idProyecto"] != 'x') {
				$arrParam = array(
					"table" => "proyecto_inversion",
					"order" => "numero_proyecto_inversion",
					"column" => "id_proyecto_inversion",
					"id" => $data["idProyecto"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("proyectos_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar proyectos
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function save_proyecto()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idProyecto = $this->input->post('hddId');
			
			$msj = "Se adicionó el Proyecto de Inversión!";
			if ($idProyecto != '') {
				$msj = "Se actualizó el Proyecto de Inversión!";
			}

			if ($idProyecto = $this->settings_model->saveProyecto()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de estrategias
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function estrategias()
	{
			$arrParam = array(
				"table" => "estrategias",
				"order" => "id_estrategia",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'estrategias';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario estrategias
     * @since 15/04/2022
     */
    public function cargarModalEstrategias() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idEstrategia"] = $this->input->post("idEstrategia");	
			
			if ($data["idEstrategia"] != 'x') {
				$arrParam = array(
					"table" => "estrategias",
					"order" => "estrategia",
					"column" => "id_estrategia",
					"id" => $data["idEstrategia"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("estrategias_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar estrategias
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function save_estrategias()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idEstrategia = $this->input->post('hddId');
			
			$msj = "Se adicionó la Estrategia!";
			if ($idEstrategia != '') {
				$msj = "Se actualizó la Estrategia!";
			}

			if ($idEstrategia = $this->settings_model->saveEstrategia()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de propositos
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function propositos()
	{
			$arrParam = array(
				"table" => " propositos",
				"order" => "numero_proposito",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'propositos';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario propositos
     * @since 15/04/2022
     */
    public function cargarModalPropositos() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idProposito"] = $this->input->post("idProposito");	
			
			if ($data["idProposito"] != 'x') {
				$arrParam = array(
					"table" => "propositos",
					"order" => "numero_proposito",
					"column" => "id_proposito",
					"id" => $data["idProposito"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("propositos_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar propositos
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function save_propositos()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idProposito = $this->input->post('hddId');
			
			$msj = "Se adicionó el Propósito!";
			if ($idProposito != '') {
				$msj = "Se actualizó el Propósito!";
			}

			if ($idProposito = $this->settings_model->saveProposito()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de logros
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function logros()
	{
			$arrParam = array(
				"table" => " logros",
				"order" => "numero_logro",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'logros';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario logros
     * @since 15/04/2022
     */
    public function cargarModalLogros() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idLogro"] = $this->input->post("idLogro");	
			
			if ($data["idLogro"] != 'x') {
				$arrParam = array(
					"table" => " logros",
					"order" => "numero_logro",
					"column" => "id_logros",
					"id" => $data["idLogro"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("logros_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar logros
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function save_logros()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idLogro = $this->input->post('hddId');
			
			$msj = "Se adicionó el Logro!";
			if ($idLogro != '') {
				$msj = "Se actualizó el Logro!";
			}

			if ($idLogro = $this->settings_model->saveLogro()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de _programas
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function programas()
	{
			$arrParam = array(
				"table" => " programa_estrategico",
				"order" => "numero_programa_estrategico",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'programa';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario _programas
     * @since 15/04/2022
     */
    public function cargarModalProgramas() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idPrograma"] = $this->input->post("idPrograma");	
			
			if ($data["idPrograma"] != 'x') {
				$arrParam = array(
					"table" => "programa_estrategico",
					"order" => "numero_programa_estrategico",
					"column" => "id_programa_estrategico",
					"id" => $data["idPrograma"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("programa_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar _programas
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function save_programas()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idPrograma = $this->input->post('hddId');
			
			$msj = "Se adicionó el Programa Estratégico!";
			if ($idPrograma != '') {
				$msj = "Se actualizó el Programa Estratégico!";
			}

			if ($idPrograma = $this->settings_model->saveProgramas()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de _metas_pdd
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function metas_pdd()
	{
			$arrParam = array(
				"table" => "meta_pdd",
				"order" => "numero_meta_pdd",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'metas_pdd';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario _metas_pdd
     * @since 15/04/2022
     */
    public function cargarModalMetasPDD() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idMetaPDD"] = $this->input->post("idMetaPDD");	
			
			if ($data["idMetaPDD"] != 'x') {
				$arrParam = array(
					"table" => "meta_pdd",
					"order" => "numero_meta_pdd",
					"column" => "id_meta_pdd",
					"id" => $data["idMetaPDD"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("metas_pdd_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar _metas_pdd
     * @since 15/04/2022
     * @author BMOTTAG
	 */
	public function save_metas_pdd()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idMetaPDD = $this->input->post('hddId');
			
			$msj = "Se adicionó la Meta PDD!";
			if ($idMetaPDD != '') {
				$msj = "Se actualizó la Meta PDD!";
			}

			if ($idMetaPDD = $this->settings_model->saveMetasPDD()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de ODS
     * @since 16/04/2022
     * @author BMOTTAG
	 */
	public function ods()
	{
			$arrParam = array(
				"table" => "ods",
				"order" => "numero_ods",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'ods';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario ODS
     * @since 16/04/2022
     */
    public function cargarModalODS() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idODS"] = $this->input->post("idODS");	
			
			if ($data["idODS"] != 'x') {
				$arrParam = array(
					"table" => "ods",
					"order" => "numero_ods",
					"column" => "id_ods",
					"id" => $data["idODS"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("ods_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar ODS
     * @since 16/04/2022
     * @author BMOTTAG
	 */
	public function save_ods()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idODS = $this->input->post('hddId');
			
			$msj = "Se adicionó la ODS!";
			if ($idODS != '') {
				$msj = "Se actualizó la ODS!";
			}

			if ($idODS = $this->settings_model->saveODS()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de metas_proyectos
     * @since 16/04/2022
     * @author BMOTTAG
	 */
	public function metas_proyectos($vigencia='x')
	{
			$vig = $this->general_model->get_vigencia();
			if($vigencia == 'x'){
				$data['vigencia']  = $vig['vigencia'];
			} else {
				$data['vigencia']  = $vigencia;
			}
			$arrParam = array('vigencia'=>$data['vigencia']);
			$data['info'] = $this->general_model->get_meta_proyecto($arrParam);
			
			$data["view"] = 'meta_proyectos';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario metas_proyectos
     * @since 16/04/2022
     */
    public function cargarModalMetasProyectos() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idMetaProyecto"] = $this->input->post("idMetaProyecto");	

			$arrParam = array(
				"table" => "proyecto_inversion",
				"order" => "id_proyecto_inversion",
				"id" => "x"
			);
			$data['listaProyectos'] = $this->general_model->get_basic_search($arrParam);
			$arrParam = array(
				"table" => "param_tipologia_anualidad",
				"order" => "id_tipologia",
				"id" => "x"
			);
			$data['listaTipologia'] = $this->general_model->get_basic_search($arrParam);
			
			if ($data["idMetaProyecto"] != 'x') {
				$arrParam = array(
					"idMetaProyecto" => $data["idMetaProyecto"]
				);
				$data['information'] = $this->general_model->get_meta_proyecto($arrParam);
			}
			
			$this->load->view("meta_proyectos_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar metas_proyectos
     * @since 16/04/2022
     * @author BMOTTAG
	 */
	public function save_metas_proyectos()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idMetaProyecto = $this->input->post('hddId');
			
			$msj = "Se adicionó la Meta Proyecto de Inversión!";
			if ($idMetaProyecto != '') {
				$msj = "Se actualizó la la Meta Proyecto de Inversión!";
			}

			if ($idMetaProyecto = $this->settings_model->saveMetasProyectos()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Evio de correo
     * @since 11/3/2021
     * @author BMOTTAG
	 */
	public function email($idUsuario)
	{
			$arrParam = array('idUser' => $idUsuario);
			$infoUsuario = $this->general_model->get_user($arrParam);
			$to = $infoUsuario[0]['email'];

			//reiniciar primero la contraseña del usuario a Jardin2021 y estado colocarlo en cero
			$arrParam['passwd'] = 'Jardin2022';
			$resetPassword = $this->settings_model->resetEmployeePassword($arrParam);

			//busco datos parametricos de configuracion para envio de correo
			$arrParam2 = array(
				"table" => "parametros",
				"order" => "id_parametro",
				"id" => "x"
			);
			$parametric = $this->general_model->get_basic_search($arrParam2);

			$paramHost = $parametric[0]["parametro_valor"];
			$paramUsername = $parametric[1]["parametro_valor"];
			$paramPassword = $parametric[2]["parametro_valor"];
			$paramFromName = $parametric[3]["parametro_valor"];
			$paramCompanyName = $parametric[4]["parametro_valor"];
			$paramAPPName = $parametric[5]["parametro_valor"];

			//mensaje del correo
			$msj = '<p>Sr.(a) ' . $infoUsuario[0]['first_name'] . ' se activo su ingreso a la plataforma de Programa Institucional - Cuadro de Mando del ' . $paramCompanyName . ',';
			$msj .= ' siga el enlace con las credenciales para acceder.</p>';
			$msj .= '<p>Recuerde cambiar su contraseña para activar su cuenta.</p>';
			$msj .= '<p><strong>Enlace: </strong>' . base_url();
			$msj .= '<br><strong>Usuario: </strong>' . $infoUsuario[0]['log_user'];
			$msj .= '<br><strong>Contraseña: </strong>' . $arrParam['passwd'];
									
			$mensaje = "<p>$msj</p>
						<p>Cordialmente,</p>
						<p><strong>$paramCompanyName</strong></p>";		

			require_once(APPPATH.'libraries/PHPMailer/class.phpmailer.php');
            $mail = new PHPMailer(true);

            try {
                    $mail->IsSMTP(); // set mailer to use SMTP
                    $mail->Host = $paramHost; // specif smtp server
                    $mail->SMTPSecure= "tls"; // Used instead of TLS when only POP mail is selected
                    $mail->Port = 587; // Used instead of 587 when only POP mail is selected
                    $mail->SMTPAuth = true;
					$mail->Username = $paramUsername; // SMTP username
                    $mail->Password = $paramPassword; // SMTP password
                    $mail->FromName = $paramFromName;
                    $mail->From = $paramUsername;
                    $mail->AddAddress($to, 'Usuario JBB Bienes');
                    $mail->WordWrap = 50;
                    $mail->CharSet = 'UTF-8';
                    $mail->IsHTML(true); // set email format to HTML
                    $mail->Subject = $paramCompanyName . ' - ' . $paramAPPName;

                    $mail->Body = nl2br ($mensaje,false);

                    $data['linkBack'] = "settings/users";
					$data['titulo'] = "<i class='fa fa-unlock fa-fw'></i>CAMBIAR CONTRASEÑA";

                    if($mail->Send())
                    {
						$data['msj'] = 'Se actualizó la contraseña del usuario.';
						$data['msj'] .= '<br>';
						$data['msj'] .= '<br><strong>Nombre Usuario: </strong>' . $infoUsuario[0]['first_name'];
						$data['msj'] .= '<br><strong>Contraseña: </strong>' . $arrParam['passwd'];
						$data['msj'] .= '<br><br><p>La información con los datos de ingreso fue enviada al correo electrónico del usuario, quien debe cambiar la contraseña para activar la cuenta.</p>';
						$data['clase'] = 'alert-success';

                        $this->session->set_flashdata('retorno_exito', 'Creaci&oacute;n de usuario exitosa!. La informaci&oacute;n para activar su cuenta fu&eacute; enviada al correo registrado, recuerde aceptar los t&eacute;rminos y condiciones y cambiar su contrase&ntilde;a');
                        //redirect(base_url(), 'refresh');
                        //exit;

                    }else{
						$data['msj'] = 'Se actualizó la contraseña del usuario, sin embargo no se pudo enviar el correo electrónico.';
						$data['msj'] .= '<br>';
						$data['msj'] .= '<br><strong>Nombre Usuario: </strong>' . $infoUsuario[0]['first_name'];
						$data['msj'] .= '<br><strong>Contraseña: </strong>' . $arrParam['passwd'];
						$data['clase'] = 'alert-success';

                        $this->session->set_flashdata('retorno_error', 'Se creo la persona, sin embargo no se pudo enviar el correo electr&oacute;nico');
                       // redirect(base_url(), 'refresh');
                       //exit;

                    }

					$data["view"] = "template/answer";
					$this->load->view("layout", $data);

                }catch (Exception $e){
                                print_r($e->getMessage());
                                        exit;
                }

	}

	/**
	 * Genera todas las imagenes de QR de os equipos
     * @since 20/3/2021
     * @author BMOTTAG
	 */
	public function generarImagenesQREquipos()
	{
				//primero eliminar imagenes de QR
				$files = glob('images/equipos/QR/*.png'); //obtenemos todos los nombres de los ficheros

				foreach($files as $file){
				    if(is_file($file))
				    unlink($file); //elimino el fichero
				}

				//informacion equipos
				$arrParam = array('estadoEquipo' => 1);	
				$infoEquipos = $this->general_model->get_equipos_info($arrParam);

				$this->load->library('ciqrcode');

				$tot = count($infoEquipos);
				for ($i = 0; $i < $tot; $i++) 
				{
					//INCIO - genero imagen con la libreria y la subo 
					$valorQRcode = base_url('login/index/' . $infoEquipos[$i]['qr_code_encryption']);
					$rutaImagen = $infoEquipos[$i]['qr_code_img'];
					
					$params['data'] = $valorQRcode;
					$params['level'] = 'H';
					$params['size'] = 10;
					$params['savename'] = FCPATH.$rutaImagen;
									
					$this->ciqrcode->generate($params);
					//FIN - genero imagen con la libreria y la subo
				}
				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se actualizarón las imagenes de QR de los equipos');
				
				redirect("/equipos",'refresh');
	}

	/**
	 * objetivos_estrategicos
     * @since 26/04/2022
     * @author BMOTTAG
	 */
	public function objetivos_estrategicos()
	{			
			$arrParam = array();
			$data['info'] = $this->general_model->get_objetivos_estrategicos($arrParam);
			
			$data["view"] = 'objetivos_estrategicos';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario objetivos_estrategicos
     * @since 26/04/2022
     */
    public function cargarModalObjetivosEstrategicos() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idObjetivoEstrategico"] = $this->input->post("idObjetivoEstrategico");	
	
			$arrParam = array(
				"table" => "estrategias",
				"order" => "id_estrategia",
				"id" => "x"
			);
			$data['estrategias'] = $this->general_model->get_basic_search($arrParam);

			if ($data["idObjetivoEstrategico"] != 'x') {
				$arrParam = array(
					"idObjetivoEstrategico" => $data["idObjetivoEstrategico"]
				);
				$data['information'] = $this->general_model->get_objetivos_estrategicos($arrParam);
			}			
			$this->load->view("objetivos_estrategicos_modal", $data);
    }
	
	/**
	 * Update objetivos_estrategicos
     * @since 26/04/2022
     * @author BMOTTAG
	 */
	public function save_objetivos_estrategicos()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idObjetivoEstrategico = $this->input->post('hddId');

			$msj = "Se adicionó el Objetivo Estratégico!";
			if ($idObjetivoEstrategico != '') {
				$msj = "Se actualizó el Objetivo Estratégico!";
			}			

			if ($idObjetivoEstrategico = $this->settings_model->saveObjetivo()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);
    }

	/**
	 * Delete ODS
     * @since 26/4/2022
	 */
	public function delete_ods()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idODS = $this->input->post('identificador');

			$arrParam = array(
				"idODS" => $idODS
			);
			$infoCuadroBase = $this->general_model->get_lista_cuadro_mando($arrParam);

			if($infoCuadroBase){
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! No se puede eliminar porque la ODS ya esta relacionada en un Plan de Desarrollo Distrital.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}else{
				$arrParam = array(
					"table" => "ods",
					"primaryKey" => "id_ods",
					"id" => $idODS
				);
				
				if ($this->general_model->deleteRecord($arrParam)) 
				{
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó la ODS.');
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }

	/**
	 * Delete meta pdd
     * @since 26/4/2022
	 */
	public function delete_meta_pdd()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idMetaPDD = $this->input->post('identificador');

			$arrParam = array(
				"idMetaPDD" => $idMetaPDD
			);
			$infoCuadroBase = $this->general_model->get_lista_cuadro_mando($arrParam);

			if($infoCuadroBase){
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! No se puede eliminar porque la Meta PDD ya esta relacionada en un Plan de Desarrollo Distrital.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}else{
				$arrParam = array(
					"table" => "meta_pdd",
					"primaryKey" => "id_meta_pdd",
					"id" => $idMetaPDD
				);
				
				if ($this->general_model->deleteRecord($arrParam)) 
				{
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó la Meta PDD.');
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }

	/**
	 * Delete programa
     * @since 26/4/2022
	 */
	public function delete_programa()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idPrograma = $this->input->post('identificador');

			$arrParam = array(
				"idPrograma" => $idPrograma
			);
			$infoCuadroBase = $this->general_model->get_lista_cuadro_mando($arrParam);

			if($infoCuadroBase){
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! No se puede eliminar porque el Programa Estratégico ya esta relacionada en un Plan de Desarrollo Distrital.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}else{
				$arrParam = array(
					"table" => "programa_estrategico",
					"primaryKey" => "id_programa_estrategico",
					"id" => $idPrograma
				);
				
				if ($this->general_model->deleteRecord($arrParam)) 
				{
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó el Programa Estratégico.');
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }

	/**
	 * Delete logro
     * @since 26/4/2022
	 */
	public function delete_logro()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idLogro = $this->input->post('identificador');

			$arrParam = array(
				"idLogro" => $idLogro
			);
			$infoCuadroBase = $this->general_model->get_lista_cuadro_mando($arrParam);

			if($infoCuadroBase){
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! No se puede eliminar porque el Logro ya esta relacionada en un Plan de Desarrollo Distrital.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}else{
				$arrParam = array(
					"table" => "logros",
					"primaryKey" => "id_logros",
					"id" => $idLogro
				);
				
				if ($this->general_model->deleteRecord($arrParam)) 
				{
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó el Logro.');
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }

	/**
	 * Delete proposito
     * @since 26/4/2022
	 */
	public function delete_proposito()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idProposito = $this->input->post('identificador');

			$arrParam = array(
				"idProposito" => $idProposito
			);
			$infoCuadroBase = $this->general_model->get_lista_cuadro_mando($arrParam);

			if($infoCuadroBase){
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! No se puede eliminar porque el Propósito ya esta relacionada en un Plan de Desarrollo Distrital.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}else{
				$arrParam = array(
					"table" => "propositos",
					"primaryKey" => "id_proposito",
					"id" => $idProposito
				);
				
				if ($this->general_model->deleteRecord($arrParam)) 
				{
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó el Propósito.');
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }

	/**
	 * Delete meta proyecto inversion
     * @since 26/4/2022
	 */
	public function delete_meta_proyecto()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idMetaProyecto = $this->input->post('identificador');

			$arrParam = array(
				"idMetaProyecto" => $idMetaProyecto
			);
			$infoCuadroBase = $this->general_model->get_lista_cuadro_mando($arrParam);

			if($infoCuadroBase){
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! No se puede eliminar porque la Meta Proyecto Inversión ya esta relacionada en un Plan de Desarrollo Distrital.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}else{
				$arrParam = array(
					"table" => "meta_proyecto_inversion",
					"primaryKey" => "id_meta_proyecto_inversion",
					"id" => $idMetaProyecto
				);
				
				if ($this->general_model->deleteRecord($arrParam)) 
				{
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó el Propósito.');
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }

	/**
	 * PLAN ESTRATEGICO
	 * @since 30/04/2022
	 */
	public function plan_estrategico()
	{				
			$arrParam = array();
			$data['listaObjetivosEstrategicos'] = $this->general_model->get_objetivos_estrategicos($arrParam);

			$arrParam = array(
				"table" => "estrategias",
				"order" => "estrategia",
				"id" => "x"
			);
			$data['listaEstrategias'] = $this->general_model->get_basic_search($arrParam);
			$vigencia = $this->general_model->get_vigencia();
			$arrParam = array(
				'vigencia' => $vigencia['vigencia']
			);
			$data['listaActividades'] = $this->general_model->get_actividades($arrParam);
			$data['cantActividades'] = 0;
			if ($data['listaActividades']) {
				$data['cantActividades'] = count($data['listaActividades']);
			}
			$data['vigencia'] = $this->general_model->get_vigencia();
			
			$data["view"] = "plan_estrategico";
			$this->load->view("layout_calendar", $data);
	}

    /**
     * Cargo modal - formulario cuadro base
     * @since 16/04/2022
     */
    public function cargarModalCuadroBase() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["numeroObjetivoEstrategico"] = $this->input->post("numeroObjetivoEstrategico");
			$data["idCuadroBase"] = $this->input->post("idCuadroBase");

			if ($data["idCuadroBase"] != 'x') {
				$arrParam = array("idCuadroBase" => $data["idCuadroBase"]);
				$data['information'] = $this->general_model->get_lista_cuadro_mando($arrParam);//info bloques
				
				$data["numeroObjetivoEstrategico"] = $data['information'][0]['fk_numero_objetivo_estrategico'];
			}

			$arrParam = array("numeroObjetivoEstrategico" => $data["numeroObjetivoEstrategico"]);
			$data['infoObjetivoEstrategico'] = $this->general_model->get_objetivos_estrategicos($arrParam);

			$arrParam = array(
				"table" => "proyecto_inversion",
				"order" => "id_proyecto_inversion",
				"id" => "x"
			);
			$data['listaProyectos'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "meta_proyecto_inversion",
				"order" => "numero_meta_proyecto",
				"id" => "x"
			);
			$data['listaMetasProyectos'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => " propositos",
				"order" => "numero_proposito",
				"id" => "x"
			);
			$data['listaPropositos'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => " logros",
				"order" => "numero_logro",
				"id" => "x"
			);
			$data['listaLogros'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => " programa",
				"order" => "numero_programa",
				"id" => "x"
			);
			$data['listaProgramaSEGPLAN'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => " programa_estrategico",
				"order" => "numero_programa_estrategico",
				"id" => "x"
			);
			$data['listaPrograma'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "meta_pdd",
				"order" => "numero_meta_pdd",
				"id" => "x"
			);
			$data['listaMetasPDD'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "ods",
				"order" => "numero_ods",
				"id" => "x"
			);
			$data['listaODS'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "param_dimensiones_mipg",
				"order" => "id_dimension",
				"id" => "x"
			);
			$data['listaDimensionesMIPG'] = $this->general_model->get_basic_search($arrParam);
						
			$this->load->view("plan_estrategico_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar cuadro base
     * @since 16/04/2022
     * @author BMOTTAG
	 */
	public function save_cuadro_base()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idCuadroBase = $this->input->post('hddIdCuadroBase');
		
			$msj = "Se adicionó la información!";
			if ($idCuadroBase != 'x') {
				$msj = "Se actualizó la información!";
			}

			if ($this->settings_model->savePlanEstrategico()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Delete Plan Estrategico
     * @since 26/4/2022
	 */
	public function delete_plan_estrategico()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idCuadroBase = $this->input->post('identificador');

			$arrParam = array(
				"idCuadroBase" => $idCuadroBase
			);
			$infoActividades = $this->general_model->get_actividades($arrParam);

			if($infoActividades){
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! No se puede eliminar porque el Plan de Desarrollo Distrital ya tiene asignas Actividades.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}else{
				$arrParam = array(
					"table" => "cuadro_base ",
					"primaryKey" => "id_cuadro_base",
					"id" => $idCuadroBase
				);
				
				if ($this->general_model->deleteRecord($arrParam)) 
				{
					$data["result"] = true;
					$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó el Plan de Desarrollo Distrital.');
				} else {
					$data["result"] = "error";
					$data["mensaje"] = "Error!!! Ask for help.";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
				}
			}
			
			echo json_encode($data);
    }

	/**
	 * Lista de propositos
     * @since 1/06/2022
     * @author BMOTTAG
	 */
	public function area_responsable()
	{
			$arrParam = array(
				"table" => "param_area_responsable",
				"order" => "area_responsable ",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'area_responsable';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario propositos
     * @since 1/06/2022
     */
    public function cargarModalAreaResponsable() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idAreaResponsable"] = $this->input->post("idAreaResponsable");	
			
			if ($data["idAreaResponsable"] != 'x') {
				$arrParam = array(
					"table" => "param_area_responsable",
					"order" => "area_responsable",
					"column" => "id_area_responsable",
					"id" => $data["idAreaResponsable"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("area_responsable_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar propositos
     * @since 1/06/2022
     * @author BMOTTAG
	 */
	public function save_area_responsable()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idAreaResponsable = $this->input->post('hddId');
			
			$msj = "Se adicionó el Área Responsable!";
			if ($idAreaResponsable != '') {
				$msj = "Se actualizó el Área Responsable!";
			}

			if ($idAreaResponsable = $this->settings_model->saveAreaResponsable()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista Meta Proyecto filtrada por Proyecto de Inversión
     * @since 08/06/2022
     * @author BMOTTAG
	 */
    public function metaProyectoList() {
        header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
		$numeroProyecto = $this->input->post('numeroProyecto');

		$vigencia = $this->general_model->get_vigencia();
		$arrParam = array(
			"numeroProyecto" => $numeroProyecto,
			"vigencia" => $vigencia['vigencia']
		);
		$lista= $this->general_model->get_meta_proyecto($arrParam);

		echo "<option value=''>Seleccione...</option>";
		if ($lista) {
			foreach ($lista as $fila) {
				echo "<option value='" . $fila["nu_meta_proyecto"] . "' >" . $fila["vigencia"] . '-' . $fila["numero_meta_proyecto"] . ' ' . $fila["meta_proyecto"] . "</option>";
			}
		}			

    }

	/**
	 * metas_objetivos_estrategicos
     * @since 20/06/2022
     * @author BMOTTAG
	 */
	public function metas_objetivos_estrategicos()
	{			
			$arrParam = array();
			$data['info'] = $this->general_model->get_lista_metas($arrParam);
			
			$data["view"] = 'metas_objetivos_estrategicos';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario metas_objetivos_estrategicos
     * @since 20/06/2022
     */
    public function cargarModalMetasObjetivosEstrategicos() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idMetaObjetivoEstrategico"] = $this->input->post("idMetaObjetivoEstrategico");	
	
			$arrParam = array();
			$data['listaObjetivosEstrategicos'] = $this->general_model->get_objetivos_estrategicos($arrParam);

			if ($data["idMetaObjetivoEstrategico"] != 'x') {
				$arrParam = array(
					"idMetaObjetivoEstrategico" => $data["idMetaObjetivoEstrategico"]
				);
				$data['information'] = $this->general_model->get_lista_metas($arrParam);
			}			
			$this->load->view("metas_objetivos_estrategicos_modal", $data);
    }
	
	/**
	 * Update mestas_objetivos_estrategicos
     * @since 20/04/2022
     * @author BMOTTAG
	 */
	public function save_metas_objetivos_estrategicos()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idMetaObjetivoEstrategico = $this->input->post('hddId');

			$msj = "Se adicionó la Meta del Objetivo Estratégico!";
			if ($idMetaObjetivoEstrategico != '') {
				$msj = "Se actualizó la Meta del Objetivo Estratégico!";
			}			

			if ($idMetaObjetivoEstrategico = $this->settings_model->saveMetaObjetivo()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);
    }

	/**
	 * Formulario para eliminar registros de la base de datos
     * @since 20/06/2022
	 */
	public function atencion_eliminar()
	{
			$userRol = $this->session->role;
			if ($userRol != 99 &&  $userRol != 1) { 
				show_error('ERROR!!! - You are in the wrong place.');	
			}
		
			$data["view"] = 'eliminar_db';
			$this->load->view("layout_calendar", $data);
	}

	/**
	 * Eliminar registros de la base de datos
	 * @since 20/06/2022
	 */
	public function eliminar_db()
	{
			header('Content-Type: application/json');
			$data = array();

			if ($this->settings_model->eliminarRegistrosActividades()) {
				
				$data["msj"] = "las tablas actividades, actividad_historial, actividad_estado, actividad_ejecucion, actividades";
				
				$data["result"] = true;
				$data["mensaje"] = "Se eliminaron los registros.";
				$this->session->set_flashdata('retornoExito', 'Se eliminó los registros de ' . $data["msj"]);
			}else{
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Contactarse con el Administrador');
			}

			echo json_encode($data);
	}

	/**
	 * Eliminar metas objetivos estrategicos
	 * @since 20/06/2022
	 */
	public function eliminar_metas_objetivos_db()
	{
			header('Content-Type: application/json');
			$data = array();

			if ($this->settings_model->eliminarMetasObjetivos()) {
				
				$data["msj"] = " la tabla objetivos_estrategicos_metas.";

				$data["result"] = true;
				$data["mensaje"] = "Se eliminaron los registros.";
				$this->session->set_flashdata('retornoExito', 'Se eliminó los registros de ' . $data["msj"]);
			}else{
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Contactarse con el Administrador');
			}

			echo json_encode($data);
	}

	/**
	 * Eliminar INDICADORES objetivos estrategicos
	 * @since 20/06/2022
	 */
	public function eliminar_indicadores_objetivos_db()
	{
			header('Content-Type: application/json');
			$data = array();

			if ($this->settings_model->eliminarIndicadoresObjetivos()) {
				
				$data["msj"] = " la tabla objetivos_estrategicos_indicadores.";

				$data["result"] = true;
				$data["mensaje"] = "Se eliminaron los registros.";
				$this->session->set_flashdata('retornoExito', 'Se eliminó los registros de ' . $data["msj"]);
			}else{
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Contactarse con el Administrador');
			}

			echo json_encode($data);
	}

	/**
	 * Eliminar RESULTADOS objetivos estrategicos
	 * @since 20/06/2022
	 */
	public function eliminar_resultados_objetivos_db()
	{
			header('Content-Type: application/json');
			$data = array();

			if ($this->settings_model->eliminarResultadosObjetivos()) {
				
				$data["msj"] = " la tabla objetivos_estrategicos_resultados.";

				$data["result"] = true;
				$data["mensaje"] = "Se eliminaron los registros.";
				$this->session->set_flashdata('retornoExito', 'Se eliminó los registros de ' . $data["msj"]);
			}else{
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Contactarse con el Administrador');
			}

			echo json_encode($data);
	}

	/**
	 * Cargar la informacion
     * @since 20/06/2022
	 */
	public function subir_archivo($model, $error="", $success="")
	{		
			$data["error"] = $error;
			$data["success"] = $success;
			$data["view"] = "cargar_informacion";
			$data["model"] = $model;
			$this->load->view("layout_calendar", $data);
	}

	/**
	 *Cargue de archivo
     * @since 20/06/2022
	 */
	public function do_upload($model)
	{		
            $config['upload_path'] = './tmp/';
            $config['overwrite'] = true;
            $config['allowed_types'] = 'csv';
            $config['max_size'] = '5000';
            $config['file_name'] = $model . '.csv';

            $this->load->library('upload', $config);
            $bandera = false;
            if (!$this->upload->do_upload()) {
                $error = $this->upload->display_errors();
                $msgError = html_escape(substr($error, 3, -4));
                $this->subir_archivo($msgError);
            }else {
                $file_info = $this->upload->data();
                $data = array('upload_data' => $this->upload->data());

                $archivo = $file_info['file_name'];

				$registros = array();
				if (($fichero = fopen(FCPATH . 'tmp/' . $archivo, "a+")) !== FALSE) {
					// Lee los nombres de los campos
					$nombres_campos = fgetcsv($fichero, 0, ";");
					$num_campos = count($nombres_campos);
					// Lee los registros

					while (($datos = fgetcsv($fichero, 0, ";")) !== FALSE) {
						// Crea un array asociativo con los nombres y valores de los campos
						for ($icampo = 0; $icampo < $num_campos; $icampo++) {
							$registro[$nombres_campos[$icampo]] = utf8_encode($datos[$icampo]);
						}
						// Añade el registro leido al array de registros
						$registros[] = $registro;
					}
					fclose($fichero);

					$x=0;
					$errores = array();
					$bandera = false;
					foreach ($registros as $lista) {
						$x++;
						if ($this->settings_model->$model($lista)) {
							if($model == "cargar_actividades"){
								//cargo registros en la tabla de estado actividad
								$this->settings_model->cargar_actividades_estados($lista);
							}
						}else{
							$errores["numero_registro"] = $x;
							$bandera = true;
						}
					}
				}
            }
			
			$vista = $model;

			$success = 'El archivo se cargó correctamente.';

			if($bandera){
				$registros = implode(",", $errores["numero_registro"]);
				$success = 'El archivo se cargó pero hay errores en los siguientes registros:::' . $registros;
			}
			$this->subir_archivo($vista,'', $success);
    }

	/**
	 * Indicadores_objetivos_estrategicos
     * @since 20/06/2022
     * @author BMOTTAG
	 */
	public function indicadores_objetivos_estrategicos()
	{			
			$arrParam = array();
			$data['info'] = $this->general_model->get_lista_indicadores($arrParam);
			
			$data["view"] = 'indicadores_objetivos_estrategicos';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario Indicadores_objetivos_estrategicos
     * @since 20/06/2022
     */
    public function cargarModalIndicadoresObjetivosEstrategicos() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idIndicadorObjetivoEstrategico"] = $this->input->post("idIndicadorObjetivoEstrategico");	
	
			$arrParam = array();
			$data['listaObjetivosEstrategicos'] = $this->general_model->get_objetivos_estrategicos($arrParam);

			if ($data["idIndicadorObjetivoEstrategico"] != 'x') {
				$arrParam = array(
					"idIndicadorObjetivoEstrategico" => $data["idIndicadorObjetivoEstrategico"]
				);
				$data['information'] = $this->general_model->get_lista_indicadores($arrParam);
			}			
			$this->load->view("indicadores_objetivos_estrategicos_modal", $data);
    }
	
	/**
	 * Update indicadores_objetivos_estrategicos
     * @since 20/04/2022
     * @author BMOTTAG
	 */
	public function save_indicadores_objetivos_estrategicos()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idIndicadorObjetivoEstrategico = $this->input->post('hddId');

			$msj = "Se adicionó el Indicador del Objetivo Estratégico!";
			if ($idIndicadorObjetivoEstrategico != '') {
				$msj = "Se actualizó el Indicador del Objetivo Estratégico!";
			}			

			if ($idIndicadorObjetivoEstrategico = $this->settings_model->saveIndicadorObjetivo()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);
    }

	/**
	 * resultados_objetivos_estrategicos
     * @since 20/06/2022
     * @author BMOTTAG
	 */
	public function resultados_objetivos_estrategicos()
	{			
			$arrParam = array();
			$data['info'] = $this->general_model->get_lista_resultados($arrParam);
			
			$data["view"] = 'resultados_objetivos_estrategicos';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario resultados_objetivos_estrategicos
     * @since 20/06/2022
     */
    public function cargarModalResultadosObjetivosEstrategicos() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idResultadoObjetivoEstrategico"] = $this->input->post("idResultadoObjetivoEstrategico");	
	
			$arrParam = array();
			$data['listaObjetivosEstrategicos'] = $this->general_model->get_objetivos_estrategicos($arrParam);

			if ($data["idResultadoObjetivoEstrategico"] != 'x') {
				$arrParam = array(
					"idResultadoObjetivoEstrategico" => $data["idResultadoObjetivoEstrategico"]
				);
				$data['information'] = $this->general_model->get_lista_resultados($arrParam);
			}			
			$this->load->view("resultados_objetivos_estrategicos_modal", $data);
    }
	
	/**
	 * Update resultados_objetivos_estrategicos
     * @since 20/04/2022
     * @author BMOTTAG
	 */
	public function save_resultados_objetivos_estrategicos()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idResultadoObjetivoEstrategico = $this->input->post('hddId');

			$msj = "Se adicionó el Resultado del Objetivo Estratégico!";
			if ($idResultadoObjetivoEstrategico != '') {
				$msj = "Se actualizó el Resultado del Objetivo Estratégico!";
			}			

			if ($idResultadoObjetivoEstrategico = $this->settings_model->saveResultadoObjetivo()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);
    }
	
    /**
     * Cargo modal - formulario para importar una actividad de otro cuadro base
     * @since 23/06/2022
     */
    public function cargarModalImportarActividad() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["numeroObjetivoEstrategico"] = $this->input->post("numeroObjetivoEstrategico");
			$data["idCuadroBase"] = $this->input->post("idCuadroBase");

			if ($data["idCuadroBase"] != 'x') {
				$vigencia = $this->general_model->get_vigencia();
				$arrParam = array(
					"idCuadroBase" => $data["idCuadroBase"],
					'vigencia' => $vigencia['vigencia']
				);
				$data['information'] = $this->general_model->get_lista_cuadro_mando($arrParam);//info bloques
				
				$data["numeroObjetivoEstrategico"] = $data['information'][0]['fk_numero_objetivo_estrategico'];
			}

			$arrParam = array("numeroObjetivoEstrategico" => $data["numeroObjetivoEstrategico"]);
			$data['infoObjetivoEstrategico'] = $this->general_model->get_objetivos_estrategicos($arrParam);

			$vigencia = $this->general_model->get_vigencia();
			$arrParam = array(
				"NOTidCuadroBase" => $data["idCuadroBase"],
				'vigencia' => $vigencia['vigencia']
			);
			$data['listaActividades'] = $this->general_model->get_actividades($arrParam);

			$this->load->view("importar_actividdades_modal", $data);
    }

	/**
	 * Importar Actividad
     * @since 24/06/2022
     * @author BMOTTAG
	 */
	public function save_importar_actividad()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$msj = "Se importó la Actividad al Plan Estratégico!";

			if ($this->settings_model->saveImportarActividad()) {
				/*
FALTA GUARDA EL CAMBIO PARA UNA AUDITORIA 
				*/
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de propositos X vigencia
     * @since 16/04/2022
     * @author BMOTTAG
	 */
	public function propositos_x_vigencia($vigencia='x')
	{
			$vig = $this->general_model->get_vigencia();
			if($vigencia == 'x'){
				$data['vigencia']  = $vig['vigencia'];
			} else {
				$data['vigencia']  = $vigencia;
			}
			$arrParam = array('vigencia'=>$data['vigencia']);
			$data['info'] = $this->general_model->get_propositos_x_vigencia($arrParam);
			for ($i=0; $i<count($data['info']); $i++) {
				$arrParam = array(
					'numeroProposito' => $data['info'][$i]['numero_proposito'],
					'vigencia' => $data['vigencia']
				);
				$programado = $this->general_model->get_sumPresupuestoProgramado($arrParam);
				$ejecutado = $this->general_model->get_sumRecursoEjecutado($arrParam);
				$data['info'][$i]['recurso_programado_proposito'] = $programado['presupuesto_meta'];
				$data['info'][$i]['recurso_ejecutado_proposito'] = $ejecutado['recurso_ejecutado_meta'];
				if ($programado['presupuesto_meta'] != 0) {
					$data['info'][$i]['porcentaje_cumplimiento_proposito'] = ($ejecutado['recurso_ejecutado_meta']*100)/$programado['presupuesto_meta'];
				} else {
					$data['info'][$i]['porcentaje_cumplimiento_proposito'] = 0;
				}
			}

			$data["view"] = 'propositos_x_vigencia';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario porpositos
     * @since 16/04/2022
     */
    public function cargarModalPropositosXVigencia() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idPropositoVigencia"] = $this->input->post("idPropositoVigencia");	

			$arrParam = array(
				"table" => " propositos",
				"order" => "numero_proposito",
				"id" => "x"
			);
			$data['listaProposito'] = $this->general_model->get_basic_search($arrParam);
			
			if ($data["idPropositoVigencia"] != 'x') {
				$arrParam = array(
					"idPropositoVigencia" => $data["idPropositoVigencia"]
				);
				$data['information'] = $this->general_model->get_propositos_x_vigencia($arrParam);
			}
			
			$this->load->view("propositos_x_vigencia_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar metas_proyectos
     * @since 16/04/2022
     * @author BMOTTAG
	 */
	public function save_propositos_x_vigencia()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idPropositoVigencia = $this->input->post('hddId');
			
			$msj = "Se adicionó el Registro!";
			if ($idPropositoVigencia != '') {
				$msj = "Se actualizó Registro!";
			}

			if ($idPropositoVigencia = $this->settings_model->savePropositosXVigencia()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Fechas limite de registro de ejecución
     * @since 18/08/2022
     * @author BMOTTAG
	 */
	public function fechas_limite()
	{
			$arrParam = array(
				"table" => "param_fechas_limites",
				"order" => "id_fecha",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			$data['vigencia'] = $this->general_model->get_vigencia();

			$data["view"] = 'fechas';
			$this->load->view("layout_calendar", $data);
	}

	/**
	 * Cambiar Vigencia
     * @since 04/01/2023
     * @author AOCUBILLOSA
	 */
	public function cambiarVigencia()
	{
			header('Content-Type: application/json');
			$msj = "Se realizó cambio de vigencia";
			if($this->general_model->cambiar_vigencia()){
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito2', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError2', '<strong>Error!</strong> Ask for help');
			}
			echo json_encode($data);	
    }

    /**
     * Cargo modal - formulario fechas limite registro ejecución
     * @since 15/04/2022
     */
    public function cargarModalFechas() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$idFecha = $this->input->post("idFecha");	
			
			$arrParam = array(
				"table" => "param_fechas_limites",
				"order" => "id_fecha",
				"column" => "id_fecha",
				"id" => $idFecha
			);
			$data['information'] = $this->general_model->get_basic_search($arrParam);

			$this->load->view("fechas_modal", $data);
    }

	/**
	 * Actualizar fechas
     * @since 18/07/2022
     * @author BMOTTAG
	 */
	public function save_fechas()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idFecha = $this->input->post('hddId');
			$fecha = $this->input->post('fecha');
			
			$msj = "Se actualizó la información!";

			$arrParam = array(
				"table" => "param_fechas_limites",
				"primaryKey" => "id_fecha",
				"id" => $idFecha,
				"column" => "fecha",
				"value" => $fecha
			);

			if($this->general_model->updateRecord($arrParam)){
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);
    }

	/**
	 * Lista de PROYECETO X vigencia
     * @since 24/07/2022
     * @author BMOTTAG
	 */
	public function proyectos_x_vigencia($vigencia='x')
	{
			$vig = $this->general_model->get_vigencia();
			if($vigencia == 'x'){
				$data['vigencia']  = $vig['vigencia'];
			} else {
				$data['vigencia']  = $vigencia;
			}
			$arrParam = array('vigencia'=>$data['vigencia']);
			$data['info'] = $this->general_model->get_proyectos_x_vigencia($arrParam);
			for ($i=0; $i<count($data['info']); $i++) {
				$arrParam = array(
					'numeroProyecto' => $data['info'][$i]['numero_proyecto_inversion'],
					'vigencia' => $data['vigencia']
				);
				$programado = $this->general_model->get_sumPresupuestoProgramado($arrParam);
				$ejecutado = $this->general_model->get_sumRecursoEjecutado($arrParam);
				$data['info'][$i]['recurso_programado_proyecto'] = $programado['presupuesto_meta'];
				$data['info'][$i]['recurso_ejecutado_proyecto'] = $ejecutado['recurso_ejecutado_meta'];
				if ($programado['presupuesto_meta'] != 0) {
					$data['info'][$i]['porcentaje_cumplimiento_proyecto'] = ($ejecutado['recurso_ejecutado_meta']*100)/$programado['presupuesto_meta'];
				} else {
					$data['info'][$i]['porcentaje_cumplimiento_proyecto'] = 0;
				}
			}

			$data["view"] = 'proyectos_x_vigencia';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario proyectos
     * @since 24/07/2022
     */
    public function cargarModalProyectosXVigencia() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idProyectoVigencia"] = $this->input->post("idProyectoVigencia");	

			$arrParam = array(
				"table" => " proyecto_inversion",
				"order" => "numero_proyecto_inversion",
				"id" => "x"
			);
			$data['listaProyecto'] = $this->general_model->get_basic_search($arrParam);
			
			if ($data["idProyectoVigencia"] != 'x') {
				$arrParam = array(
					"idProyectoVigencia" => $data["idProyectoVigencia"]
				);
				$data['information'] = $this->general_model->get_proyectos_x_vigencia($arrParam);
			}
			
			$this->load->view("proyectos_x_vigencia_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar proyectos X vigencia
     * @since 24/07/2022
     * @author BMOTTAG
	 */
	public function save_proyectos_x_vigencia()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idProyectoVigencia = $this->input->post('hddId');
			
			$msj = "Se adicionó el Registro!";
			if ($idProyectoVigencia != '') {
				$msj = "Se actualizó Registro!";
			}

			if ($idProyectoVigencia = $this->settings_model->saveProyectosXVigencia()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }	

	/**
	 * Lista de metas_pdd X vigencia
     * @since 24/07/2022
     * @author BMOTTAG
	 */
	public function metas_pdd_x_vigencia($vigencia='x')
	{
			$vig = $this->general_model->get_vigencia();
			if($vigencia == 'x'){
				$data['vigencia']  = $vig['vigencia'];
			} else {
				$data['vigencia']  = $vigencia;
			}
			$arrParam = array('vigencia'=>$data['vigencia']);
			$data['info'] = $this->general_model->get_metas_pdd_x_vigencia($arrParam);
			for ($i=0; $i<count($data['info']); $i++) {
				$arrParam = array(
					'numeroMetaPDD' => $data['info'][$i]['numero_meta_pdd'],
					'vigencia' => $data['vigencia']
				);
				$programado = $this->general_model->get_sumPresupuestoProgramado($arrParam);
				$ejecutado = $this->general_model->get_sumRecursoEjecutado($arrParam);
				$data['info'][$i]['recurso_programado_meta_pdd'] = $programado['presupuesto_meta'];
				$data['info'][$i]['recurso_ejecutado_meta_pdd'] = $ejecutado['recurso_ejecutado_meta'];
				if ($programado['presupuesto_meta'] != 0) {
					$data['info'][$i]['porcentaje_cumplimiento_meta_pdd'] = ($ejecutado['recurso_ejecutado_meta']*100)/$programado['presupuesto_meta'];
				} else {
					$data['info'][$i]['porcentaje_cumplimiento_meta_pdd'] = 0;
				}
			}

			$data["view"] = 'metas_pdd_x_vigencia';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario metas_pdd
     * @since 24/07/2022
     */
    public function cargarModalMetasPDDXVigencia() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idMetaPDDVigencia"] = $this->input->post("idMetaPDDVigencia");

			$arrParam = array(
				"table" => "meta_pdd",
				"order" => "numero_meta_pdd",
				"id" => "x"
			);
			$data['listaMetasPDD'] = $this->general_model->get_basic_search($arrParam);
	
			if ($data["idMetaPDDVigencia"] != 'x') {
				$arrParam = array(
					"idMetaPDDVigencia" => $data["idMetaPDDVigencia"]
				);
				$data['information'] = $this->general_model->get_metas_pdd_x_vigencia($arrParam);
			}
			
			$this->load->view("metas_pdd_x_vigencia_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar metas_pdd X vigencia
     * @since 24/07/2022
     * @author BMOTTAG
	 */
	public function save_metas_pdd_x_vigencia()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idMetaPDDVigencia = $this->input->post('hddId');
			
			$msj = "Se adicionó el Registro!";
			if ($idMetaPDDVigencia != '') {
				$msj = "Se actualizó Registro!";
			}

			if ($idMetaPDDVigencia = $this->settings_model->saveMetasPDDXVigencia()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de programas segplan
     * @since 24/07/2022
     * @author BMOTTAG
	 */
	public function programas_sp()
	{
			$arrParam = array(
				"table" => "programa",
				"order" => "numero_programa",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'programa_sp';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario programas segplan
     * @since 24/07/2022
     */
    public function cargarModalProgramaSP() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idPrograma"] = $this->input->post("idPrograma");	
			
			if ($data["idPrograma"] != 'x') {
				$arrParam = array(
					"table" => "programa",
					"order" => "numero_programa",
					"column" => "id_programa",
					"id" => $data["idPrograma"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("programa_sp_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar programas segplan
     * @since 24/07/2022
     * @author BMOTTAG
	 */
	public function save_programa_sp()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idPrograma = $this->input->post('hddId');
			
			$msj = "Se adicionó el Programa!";
			if ($idPrograma != '') {
				$msj = "Se actualizó el Programa!";
			}

			if ($idPrograma = $this->settings_model->saveProgramaSP()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de programas segplan X vigencia
     * @since 24/07/2022
     * @author BMOTTAG
	 */
	public function programa_sp_x_vigencia($vigencia='x')
	{
			$vig = $this->general_model->get_vigencia();
			if($vigencia == 'x'){
				$data['vigencia']  = $vig['vigencia'];
			} else {
				$data['vigencia']  = $vigencia;
			}
			$arrParam = array('vigencia'=>$data['vigencia']);
			$data['info'] = $this->general_model->get_programa_sp_x_vigencia($arrParam);
			for ($i=0; $i<count($data['info']); $i++) {
				$arrParam = array(
					'numeroProgramaSG' => $data['info'][$i]['numero_programa'],
					'vigencia' => $data['vigencia']
				);
				$programado = $this->general_model->get_sumPresupuestoProgramado($arrParam);
				$ejecutado = $this->general_model->get_sumRecursoEjecutado($arrParam);
				$data['info'][$i]['recurso_programado_programa'] = $programado['presupuesto_meta'];
				$data['info'][$i]['recurso_ejecutado_programa'] = $ejecutado['recurso_ejecutado_meta'];
				if ($programado['presupuesto_meta'] != 0) {
					$data['info'][$i]['porcentaje_cumplimiento_programa'] = ($ejecutado['recurso_ejecutado_meta']*100)/$programado['presupuesto_meta'];
				} else {
					$data['info'][$i]['porcentaje_cumplimiento_programa'] = 0;
				}
			}

			$data["view"] = 'programa_sp_x_vigencia';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario programas segplan
     * @since 24/07/2022
     */
    public function cargarModalProgramaSPXVigencia() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idProgramaSPVigencia"] = $this->input->post("idProgramaSPVigencia");

			$arrParam = array(
				"table" => "programa",
				"order" => "numero_programa",
				"id" => "x"
			);
			$data['listaProgramas'] = $this->general_model->get_basic_search($arrParam);
	
			if ($data["idProgramaSPVigencia"] != 'x') {
				$arrParam = array(
					"idProgramaSPVigencia" => $data["idProgramaSPVigencia"]
				);
				$data['information'] = $this->general_model->get_programa_sp_x_vigencia($arrParam);
			}
			
			$this->load->view("programa_sp_x_vigencia_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar programas segplan X vigencia
     * @since 24/07/2022
     * @author BMOTTAG
	 */
	public function save_programa_sp_x_vigencia()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idProgramaSPVigencia = $this->input->post('hddId');
			
			$msj = "Se adicionó el Registro!";
			if ($idProgramaSPVigencia != '') {
				$msj = "Se actualizó Registro!";
			}

			if ($idProgramaSPVigencia = $this->settings_model->saveProgramasSPXVigencia()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de indicadores segplan
     * @since 27/07/2022
     * @author BMOTTAG
	 */
	public function indicadores_sp()
	{
			$arrParam = array(
				"table" => "indicadores",
				"order" => "numero_indicador",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'indicadores_sp';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario indicadores segplan
     * @since 27/07/2022
     */
    public function cargarModalIndicadoresSP() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idIndicador"] = $this->input->post("idIndicador");	
			
			if ($data["idIndicador"] != 'x') {
				$arrParam = array(
					"table" => "indicadores",
					"order" => "numero_indicador",
					"column" => "id_indicador_sp",
					"id" => $data["idIndicador"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("indicadores_sp_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar indicadores segplan
     * @since 27/07/2022
     * @author BMOTTAG
	 */
	public function save_indicador_sp()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idIndicador = $this->input->post('hddId');
			
			$msj = "Se adicionó el Indicador!";
			if ($idIndicador != '') {
				$msj = "Se actualizó el Indicador!";
			}

			if ($idIndicador = $this->settings_model->saveIndicadorSP()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de programas indicadores X vigencia
     * @since 27/07/2022
     * @author BMOTTAG
	 */
	public function indicador_sp_x_vigencia($vigencia='x')
	{
			$vig = $this->general_model->get_vigencia();
			if($vigencia == 'x'){
				$data['vigencia']  = $vig['vigencia'];
			} else {
				$data['vigencia']  = $vigencia;
			}
			$arrParam = array('vigencia'=>$data['vigencia']);
			$data['info'] = $this->general_model->get_indicador_sp_x_vigencia($arrParam);
	
			$data["view"] = 'indicadores_sp_x_vigencia';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario indicadores segplan
     * @since 27/07/2022
     */
    public function cargarModalIndicadorSPXVigencia() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idIndicadorSPVigencia"] = $this->input->post("idIndicadorSPVigencia");

			$arrParam = array(
				"table" => "indicadores",
				"order" => "numero_indicador",
				"id" => "x"
			);
			$data['listaIndicadores'] = $this->general_model->get_basic_search($arrParam);
	
			if ($data["idIndicadorSPVigencia"] != 'x') {
				$arrParam = array(
					"idIndicadorSPVigencia" => $data["idIndicadorSPVigencia"]
				);
				$data['information'] = $this->general_model->get_indicador_sp_x_vigencia($arrParam);
			}
			
			$this->load->view("indicadores_sp_x_vigencia_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar indicadores segplan X vigencia
     * @since 27/07/2022
     * @author BMOTTAG
	 */
	public function save_indicadores_sp_x_vigencia()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idIndicadorSPVigencia = $this->input->post('hddId');
			
			$msj = "Se adicionó el Registro!";
			if ($idIndicadorSPVigencia != '') {
				$msj = "Se actualizó Registro!";
			}

			if ($idIndicadorSPVigencia = $this->settings_model->saveIndicadorSPXVigencia()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

	/**
	 * Lista de indicadores PMR
     * @since 28/07/2022
     * @author BMOTTAG
	 */
	public function indicadores_pmr()
	{
			$arrParam = array(
				"table" => "indicadores_pmr",
				"order" => "numero_indicador_pmr",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			
			$data["view"] = 'indicadores_pmr';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario indicadores PMR
     * @since 28/07/2022
     */
    public function cargarModalIndicadoresPMR() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idIndicador"] = $this->input->post("idIndicador");	
			
			if ($data["idIndicador"] != 'x') {
				$arrParam = array(
					"table" => "indicadores_pmr",
					"order" => "numero_indicador_pmr",
					"column" => "id_indicador_pmr",
					"id" => $data["idIndicador"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			
			$this->load->view("indicadores_pmr_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar indicadores PMR
     * @since 28/07/2022
     * @author BMOTTAG
	 */
	public function save_indicador_pmr()
	{			
			header('Content-Type: application/json');
			$data = array();
			
			$idIndicador = $this->input->post('hddId');
			
			$msj = "Se adicionó el Indicador!";
			if ($idIndicador != '') {
				$msj = "Se actualizó el Indicador!";
			}

			if ($idIndicador = $this->settings_model->saveIndicadorPMR()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}

			echo json_encode($data);	
    }

    /**
	 * TABLERO PMR
	 * @since 31/10/2022
	 * @author AOCUBILLOSA
	 */
	public function tablero_pmr()
	{
			$vigencia = $this->general_model->get_vigencia();
			$arrParam = array(
				'vigencia' => $vigencia['vigencia']
			);
			$data['info'] = $this->settings_model->get_tablero_pmr($arrParam);
			$data['vigencia'] = $this->general_model->get_vigencia();
			$data["view"] = "tablero_pmr";
			$this->load->view("layout_calendar", $data);
	}

    /**
     * Cargo modal - tablero PMR
     * @since 31/10/2022
     * @author AOCUBILLOSA
     */
    public function cargarModalTableroPMR()
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idPMR"] = $this->input->post("idPMR");
			if ($data["idPMR"] != 'x') {
				$arrParam = array("id_pmr" => $data["idPMR"]);
				$data['information'] = $this->settings_model->get_tablero_pmr($arrParam);
			}

			$arrParam = array(
				"table" => "param_objetivos_pmr",
				"order" => "numero_objetivo_pmr",
				"id" => "x"
			);
			$data['listaObjetivoPMR'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "param_elementos_pep_pmr",
				"order" => "id_elemento_pep_pmr",
				"id" => "x"
			);
			$data['listaElementoPEP'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "param_productos_pmr",
				"order" => "numero_producto_pmr",
				"id" => "x"
			);
			$data['listaProductoPMR'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "proyecto_inversion",
				"order" => "numero_proyecto_inversion",
				"id" => "x"
			);
			$data['listaProyectoInversion'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "indicadores_pmr",
				"order" => "numero_indicador_pmr",
				"id" => "x"
			);
			$data['listaIndicadorPMR'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "param_unidad_medida_pmr",
				"order" => "id_unidad_medida_pmr",
				"id" => "x"
			);
			$data['listaUnidadMedida'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "param_naturaleza_pmr",
				"order" => "id_naturaleza_pmr",
				"id" => "x"
			);
			$data['listaNaturalezaPMR'] = $this->general_model->get_basic_search($arrParam);

			$arrParam = array(
				"table" => "param_periodicidad_pmr",
				"order" => "id_periodicidad_pmr",
				"id" => "x"
			);
			$data['listaPeriodicidadPMR'] = $this->general_model->get_basic_search($arrParam);

			$this->load->view("tablero_pmr_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar tablero PMR
     * @since 31/10/2022
     * @author AOCUBILLOSA
	 */
	public function save_tablero_pmr()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idPMR = $this->input->post('hddIdPMR');
			$msj = "Se adicionó la información!";
			if ($idPMR != 'x') {
				$msj = "Se actualizó la información!";
			}
			if ($this->settings_model->saveTableroPMR()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
			echo json_encode($data);
    }

	/**
	 * Delete tablero PMR
     * @since 31/10/2022
     * @author AOCUBILLOSA
	 */
	public function delete_tablero_pmr()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idPMR = $this->input->post('identificador');
			$arrParam = array(
				"table" => "tablero_pmr ",
				"primaryKey" => "id_pmr",
				"id" => $idPMR
			);
			if ($this->general_model->deleteRecord($arrParam)) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó el indicador del tablero PMR.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
			echo json_encode($data);
    }
	
	/**
	 * Lista de actividades PI
     * @since 08/05/2023
     * @author AOCUBILLOSA
	 */
	public function actividadesPI($idPlanIntegrado, $numeroActividadPI = 'x', $numeroTrimestrePI = 'x')
	{
			/*$goBackInfo = $this->general_model->get_go_back();
			if($goBackInfo){
				$get_numero_objetivo = $goBackInfo['get_numero_objetivo'] != 0 ? $goBackInfo['get_numero_objetivo'] : "";
				$get_numero_proyecto = $goBackInfo['get_numero_proyecto'] != 0 ? $goBackInfo['get_numero_proyecto'] : "";
				$get_id_dependencia = $goBackInfo['get_id_dependencia'] != 0 ? $goBackInfo['get_id_dependencia'] : "";
				$get_numero_actividad = $goBackInfo['get_numero_actividad'] != 0 ? $goBackInfo['get_numero_actividad'] : "";
				$urlBotonRegresar .= "?numero_objetivo=" . $get_numero_objetivo . "&numero_proyecto=".$get_numero_proyecto."&id_dependencia=".$get_id_dependencia."&numero_actividad=" . $get_numero_actividad;
			}*/
			$dashboarURL = $this->session->userdata("dashboardURL");
			$urlBotonRegresar = $dashboarURL;
			$data['urlBotonRegresar'] = $urlBotonRegresar;
			$data['numeroActividadPI'] = $numeroActividadPI;
			$data['idPlanIntegrado'] = $idPlanIntegrado;
			$data['numeroTrimestrePI'] = false;
			$data['infoEjecucion'] = false;
			$vigencia = $this->general_model->get_vigencia();
			$arrParam = array(
				"idPlanIntegrado" => $idPlanIntegrado,
				'vigencia' => $vigencia['vigencia']
			);
			$data['listaActividadesPI'] = $this->general_model->get_actividadesPI($arrParam);
			$data['listaHistorial'] = false;
			$data['listaHistorial1'] = false;
			$data['listaHistorial2'] = false;
			$data['listaHistorial3'] = false;
			$data['listaHistorial4'] = false;
			$arrParam = array(
				"idPlanIntegrado" => $idPlanIntegrado,
				'vigencia' => $vigencia['vigencia']
			);
			$data['planInstitucional'] = $this->general_model->get_plan_institucional($arrParam);
			if($numeroActividadPI != 'x') {
				$vigencia = $this->general_model->get_vigencia();
				$arrParam = array(
					"numeroActividadPI" => $numeroActividadPI,
					'vigencia' => $vigencia['vigencia']
				);
				$data['listaActividadesPI'] = $this->general_model->get_actividadesPI($arrParam);
				$arrParam['numeroTrimestrePI'] = 1;
				$data['listaHistorial1'] = $this->general_model->get_historial_actividadPI($arrParam);
				$arrParam['numeroTrimestrePI'] = 2;
				$data['listaHistorial2'] = $this->general_model->get_historial_actividadPI($arrParam);
				$arrParam['numeroTrimestrePI'] = 3;
				$data['listaHistorial3'] = $this->general_model->get_historial_actividadPI($arrParam);
				$arrParam['numeroTrimestrePI'] = 4;
				$data['listaHistorial4'] = $this->general_model->get_historial_actividadPI($arrParam);
				if($numeroTrimestrePI != 'x') {
					$data['numeroTrimestrePI'] = $numeroTrimestrePI;
					$arrParam['numeroTrimestrePI'] = $numeroTrimestrePI;
					$data['listaHistorial'] = $this->general_model->get_historial_actividadPI($arrParam);
					$data['listaHistorial1'] = false;
					$data['listaHistorial2'] = false;
					$data['listaHistorial3'] = false;
					$data['listaHistorial4'] = false;
					//fechas limite de registro
					$arrParamFechas = array(
						"table" => "param_fechas_limites",
						"order" => "id_fecha",
						"column" => "numero_trimestre",
						"id" => $numeroTrimestrePI
					);
					$data['infoFechaLimite'] = $this->general_model->get_basic_search($arrParamFechas);
				}else{
					$arrParam = array("numeroActividadPI" => $numeroActividadPI);
				}
				$data['infoEjecucion'] = $this->general_model->get_ejecucion_actividadesPI($arrParam);
			}
			$data["activarBTN1"] = true; //para activar el boton
			$userRol = $this->session->userdata("role");
			$data["view"] = "actividades_pi";
			if($userRol == ID_ROL_ENLACE){
				$data["view"] = "actividades_pi_enlace";
			} elseif ($userRol == ID_ROL_SUPERVISOR){
				$data["view"] = "actividades_pi_supervisor";
			} elseif ($userRol == ID_ROL_CONTROL_INTERNO  || $userRol == ID_ROL_JEFEOCI){
				$data["view"] = "actividades_pi_control";
			}
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario actividades PI
     * @since 08/05/2023
     */
    public function cargarModalActividadPI() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			
			$data['information'] = FALSE;
			$data["idPlanIntegrado"] = $this->input->post("idPlanIntegrado");
			$data["idActividadPI"] = $this->input->post("idActividadPI");
			$arrParam = array(
				"table" => "param_area_responsable",
				"order" => "area_responsable",
				"id" => "x"
			);
			$data['listaAreaResponsable'] = $this->general_model->get_basic_search($arrParam);
			$arrParam = array(
				"table" => "param_proceso_calidad",
				"order" => "proceso_calidad",
				"id" => "x"
			);
			$data['proceso_calidad'] = $this->general_model->get_basic_search($arrParam);
			$arrParam = array(
				"filtro" => true
			);
			$data['listaDependencia'] = $this->general_model->get_app_dependencias($arrParam);
			$arrParam = array(
				"table" => "param_meses",
				"order" => "id_mes",
				"id" => "x"
			);
			$data['listaMeses'] = $this->general_model->get_basic_search($arrParam);
			if ($data["idActividadPI"] != 'x') 
			{
				$arrParam = array("idActividadPI" => $data["idActividadPI"]);
				$data['information'] = $this->general_model->get_actividadesPI($arrParam);
				$data["idPlanIntegrado"] = $data['information'][0]['fk_id_plan_integrado'];
			}
			$vigencia = $this->general_model->get_vigencia();
			$arrParam = array(
				"idPlanIntegrado" => $data["idPlanIntegrado"],
				'vigencia' => $vigencia['vigencia']
			);
			$data['planInstitucional'] = $this->general_model->get_plan_institucional($arrParam);
			$this->load->view("actividades_pi_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar actividades PI
     * @since 29/03/2023
     * @author AOCUBILLOSA
	 */
	public function save_actividadesPI()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idActividadPI = $this->input->post('hddId');
			$numeroActividadPI = $this->input->post('numero_actividad');
			$data["idRecord"] = $this->input->post('hddIdPlanIntegrado');
			$msj = "Se guardo la información!";

			if ($this->settings_model->guardarActividadPI())
			{	
				if ($idActividadPI == ''){
					$this->settings_model->save_programa_actividadPI($numeroActividadPI);//generar los programas
					//generar REGISTRO DE ESTADO ACTIVIDAD
					$banderaActividad = false;
					$estadoActividad = 0;
					$this->settings_model->guardarTrimestrePI($banderaActividad, $estadoActividad, $numeroActividadPI, '', 0, 1);
					//guardar el historial de los 4 trimestres
					for($i=1;$i<5;$i++) {
						$arrParam = array(
							"numeroActividadPI" => $numeroActividadPI,
							"numeroTrimestre" => $i,
							"observacion" => 'Registro de la actividad',
							"estado" => 0
						);

						//actualizo el estado del trimestre de la actividad
						$this->general_model->addHistorialActividadPI($arrParam);
					}
				}
				$data["result"] = true;		
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
		
			echo json_encode($data);
    }

    /**
	 * Lista de Planes Institucionales
     * @since 29/03/2023
     * @author AOCUBILLOSA
	 */
	public function planesInstitucionales()
	{
			$arrParam = array(
				"table" => "planes_institucionales",
				"order" => "id_plan_institucional",
				"id" => "x"
			);
			$data['info'] = $this->general_model->get_basic_search($arrParam);
			$data["view"] = 'planes_institucionales';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario actividades PI
     * @since 29/03/2023
     * @author AOCUBILLOSA
     */
    public function cargarModalPlanesInstitucionales() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			$data['information'] = FALSE;
			$data["idPlanInstitucional"] = $this->input->post("idPlanInstitucional");
			if ($data["idPlanInstitucional"] != 'x') {
				$arrParam = array(
					"table" => "planes_institucionales",
					"order" => "plan_institucional",
					"column" => "id_plan_institucional",
					"id" => $data["idPlanInstitucional"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			$this->load->view("planes_institucionales_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar actividades PI
     * @since 29/03/2023
     * @author AOCUBILLOSA
	 */
	public function save_planesInstitucionales()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idPlanInstitucional = $this->input->post('hddId');
			$msj = "Se adicionó el Plan Institucional!";
			if ($idPlanInstitucional != '') {
				$msj = "Se actualizó el Plan Institucional!";
			}
			if ($idPlanInstitucional = $this->settings_model->savePlanInstitucional()) {
				$data["result"] = true;				
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";			
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
			echo json_encode($data);
    }

    public function eliminar_planInstitucional()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idPlanInstitucional = $this->input->post('identificador');
			$arrParam = array(
				"table" => "planes_institucionales",
				"primaryKey" => "id_plan_institucional",
				"id" => $idPlanInstitucional
			);
			if ($this->general_model->deleteRecord($arrParam))
			{
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> El Plan Integrado fue eliminado exitosamente.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
			echo json_encode($data);
    }

    /**
	 * Lista de Plan Integrado
     * @since 30/03/2023
     * @author AOCUBILLOSA
	 */
	public function planIntegrado()
	{
			$data['userRol'] = $this->session->userdata("role");
			$dependencia = $this->session->userdata("dependencia");
			$vigencia = $this->general_model->get_vigencia();
			if ($data['userRol'] == ID_ROL_SUPER_ADMIN || $data['userRol'] == ID_ROL_ADMINISTRADOR || $data['userRol'] == ID_ROL_PLANEACION) {
					$arrParam = array(
					'vigencia' => $vigencia['vigencia']
				);
				$data['info'] = $this->settings_model->get_plan_integrado($arrParam);
			} else {
				$arrParam = array(
					'vigencia' => $vigencia['vigencia'],
					'dependencia' => $dependencia
				);
				$data['info'] = $this->settings_model->get_plan_integrado($arrParam);
			}
			$data['vigencia'] = $this->general_model->get_vigencia();

			$data["view"] = 'plan_integrado';
			$this->load->view("layout_calendar", $data);
	}
	
    /**
     * Cargo modal - formulario plan integrado
     * @since 30/03/2023
     * @author AOCUBILLOSA
     */
    public function cargarModalPlanIntegrado()
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			$data['information'] = FALSE;
			$data['vigencia'] = $this->general_model->get_vigencia();
			$data["idPlanIntegrado"] = $this->input->post("idPlanIntegrado");
			if ($data["idPlanIntegrado"] != 'x') {
				$arrParam = array(
					"table" => "planes_integrados",
					"order" => "id_plan_integrado",
					"column" => "id_plan_integrado",
					"id" => $data["idPlanIntegrado"]
				);
				$data['information'] = $this->general_model->get_basic_search($arrParam);
			}
			$arrParam = array(
				"table" => "planes_institucionales",
				"order" => "id_plan_institucional",
				"id" => "x"
			);
			$data['listaPlanesInstitucionales'] = $this->general_model->get_basic_search($arrParam);
			$this->load->view("plan_integrado_modal", $data);
    }
	
	/**
	 * Ingresar/Actualizar plan integrado
     * @since 30/03/2023
     * @author AOCUBILLOSA
	 */
	public function save_planIntegrado()
	{
			header('Content-Type: application/json');
			$data = array();
			$idPlanIntegrado = $this->input->post('hddId');
			$msj = "Se adicionó el Plan Integrado!";
			if ($idPlanIntegrado != '') {
				$msj = "Se actualizó el Plan Integrado!";
			}
			if ($idPlanIntegrado = $this->settings_model->savePlanIntegrado()) {
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
			echo json_encode($data);
    }

    /**
	 * Delete link acces
     * @since 1/4/2020
	 */
	public function eliminar_planIntegrado()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idPlanIntegrado = $this->input->post('identificador');
			$vigencia = $this->general_model->get_vigencia();
			$arrParam = array(
				"idPlanIntegrado" => $idPlanIntegrado,
				'vigencia' => $vigencia['vigencia']
			);
			$listaActividadesPI = $this->general_model->get_actividadesPI($arrParam);
			if ($listaActividadesPI) {
				$numeroActividad = $listaActividadesPI[0]['numero_actividad_pi'];
				$this->settings_model->eliminar_ejecucionPI($numeroActividad);
				$this->settings_model->eliminar_estadoPI($numeroActividad);
				$this->settings_model->eliminar_historialPI($numeroActividad);
				$this->settings_model->eliminar_auditoriaPI($numeroActividad);
				$this->settings_model->eliminar_actividadPI($numeroActividad);
			}
			$arrParam = array(
				"table" => "planes_integrados",
				"primaryKey" => "id_plan_integrado",
				"id" => $idPlanIntegrado
			);
			if ($this->general_model->deleteRecord($arrParam))
			{
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> El Plan Integrado fue eliminado exitosamente.');
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
			}
			echo json_encode($data);
    }

    /**
	 * Save estado de la actividad PI
     * @since 08/05/2023
     * @author AOCUBILLOSA
	 */
	public function save_estado_actividadPI()
	{			
			header('Content-Type: application/json');
			$data = array();
			$data["idPlanIntegrado"] = $this->input->post('hddIdPlanIntegrado');
			$numeroActividadPI = $this->input->post('hddNumeroActividadPI');
			$numeroTrimestre = $this->input->post('hddNumeroTrimestre');
			$observacion = $this->input->post('observacion');
			$idEstado = $this->input->post('estado');
			$data["record"] = $data["idPlanIntegrado"] . '/' . $numeroActividadPI . '/' . $numeroTrimestre;
			$msj = "Se cambio el estado de la actividad para el <b>Trimestre " . $numeroTrimestre .  "</b>.";
			
			$vigencia = $this->general_model->get_vigencia();
			$arrParam = array("
				numeroActividadPI" => $numeroActividadPI,
				'vigencia' => $vigencia['vigencia']
			);
			$listadoActividades = $this->general_model->get_actividadesPI($arrParam);

			$cumplimientoX = 0;
			$avancePOA = 0;
			$cumplimientoActual = 0;
			$ponderacion = $listadoActividades[0]['ponderacion_pi'];
			//INICIO --- DEBO TENER EN CUENTA EL TRIMESTRE DE LOS DEMAS QUE ESTAN EN 5
			$estadoActividad = $this->general_model->get_estados_actividadesPI($arrParam);

			$estadoTrimestre1 = $estadoActividad[0]["estado_trimestre_1"];
			$estadoTrimestre2 = $estadoActividad[0]["estado_trimestre_2"];
			$estadoTrimestre3 = $estadoActividad[0]["estado_trimestre_3"];
			$estadoTrimestre4 = $estadoActividad[0]["estado_trimestre_4"];

			$incluirTrimestre = 0;
			if(($numeroTrimestre != 1 && $estadoTrimestre1 == 5) || ($numeroTrimestre == 1 && $idEstado == 5)){
				$incluirTrimestre = $incluirTrimestre . "," . 1;
			}
			if(($numeroTrimestre != 2 && $estadoTrimestre2 == 5) || ($numeroTrimestre == 2 && $idEstado == 5)){
				$incluirTrimestre = $incluirTrimestre . "," . 2;
			}
			if(($numeroTrimestre != 3 && $estadoTrimestre3 == 5) || ($numeroTrimestre == 3 && $idEstado == 5)){
				$incluirTrimestre = $incluirTrimestre . "," . 3;
			}
			if(($numeroTrimestre != 4 && $estadoTrimestre3 == 5) || ($numeroTrimestre == 4 && $idEstado == 5)){
				$incluirTrimestre = $incluirTrimestre . "," . 4;
			}
			$arrParam = array(
				"numeroActividadPI" => $numeroActividadPI,
				"filtroTrimestre" => $incluirTrimestre
			);
			$sumaEjecutado = $this->general_model->sumarEjecutadoPI($arrParam);	
			//FIN --- DEBO TENER EN CUENTA EL TRIMESTRE DE LOS DEMAS QUE ESTAN EN 5

			$sumaProgramado = $this->general_model->sumarProgramadoPI($arrParam);
			if($sumaProgramado['programado'] > 0){
				$avancePOA = round(($sumaEjecutado['ejecutado']/$sumaProgramado['programado']) * $ponderacion,2);
			}

			if($sumaProgramado['programado'] > 0 && $sumaEjecutado){
				$cumplimientoActual = round(($sumaEjecutado['ejecutado']/$sumaProgramado['programado']) * 100,2);
			}

			if($idEstado == 5){
				$arrParam = array(
					"numeroActividadPI" => $numeroActividadPI,
					"numeroTrimestre" => $numeroTrimestre
				);
				$sumaProgramadoTrimestreX = $this->general_model->sumarProgramadoPI($arrParam);
				$sumaEjecutadoTrimestreX = $this->general_model->sumarEjecutadoPI($arrParam);

				if($sumaProgramadoTrimestreX['programado'] > 0){
					$cumplimientoX = round($sumaEjecutadoTrimestreX['ejecutado'] / $sumaProgramadoTrimestreX['programado'] * 100,2);
				}
			}

			$arrParam = array(
				"numeroActividadPI" => $numeroActividadPI,
				"numeroTrimestre" => $numeroTrimestre,
				"observacion" => $observacion,
				"estado" => $idEstado,
				"cumplimientoX" => $cumplimientoX,
				"avancePOA" => $avancePOA,
				"cumplimientoActual" => $cumplimientoActual
			);
			if($this->general_model->addHistorialActividadPI($arrParam)) 
			{
				//actualizo el estado del trimestre de la actividad
				if($this->general_model->updateEstadoActividadTotalesPI($arrParam)){
					//envio correos a los usuarios
					if($idEstado == 3){
						$mensaje = "se revisó la información registrada para la actividad <b>No. " . $numeroActividadPI  . "</b>, para el <b>Trimestre " . $numeroTrimestre . "</b>, fue <b>APROBADA</b> y se escalo al Área de Planeación para realizar el respectivo seguimiento.";
					}elseif($idEstado == 4){
						$mensaje = "se revisó la información registrada para la actividad <b>No. " . $numeroActividadPI  . "</b>, para el <b>Trimestre " . $numeroTrimestre . "</b>, fue <b>RECHAZADA</b>. Por favor ingresar y realizar los ajustes respectivos.";
					}elseif($idEstado == 5){
						$mensaje = "se realizó seguimiento a la información registrada para la actividad <b>No. " . $numeroActividadPI  . "</b>, para el <b>Trimestre " . $numeroTrimestre. "</b> y fue <b>APROBADA</b> por Planeación.";
					}elseif($idEstado == 6){
						$mensaje = "se realizó seguimiento a la información registrada para la actividad <b>No. " . $numeroActividadPI  . "</b>, para el <b>Trimestre " . $numeroTrimestre . "</b> y fue RECHAZADA por Planeación. Por favor ingresar y realizar los ajustes respectivos.";
					}elseif($idEstado == 7){
						$mensaje = "se realizó seguimiento a la información registrada para la actividad <b>No. " . $numeroActividadPI  . "</b>, para el <b>Trimestre " . $numeroTrimestre . "</b> y fue INCUMPLIDA. Por favor ingresar y realizar los ajustes respectivos.";
					}

					$mensaje .= "<br><br><b>Observación: </b>" . $observacion;

					//INICIO
					//SE BUSCA EL ENLACE DE LA DEPENDENCIA Y SE ENVIA CORREO
		            $arrParam2 = array(
		                "numeroActividadPI" => $numeroActividadPI,
		                "idRol" => ID_ROL_ENLACE
		            );
		            $listaUsuarios = $this->general_model->get_user_encargado_by_actividadPI($arrParam2);

		            if($listaUsuarios){
		            	foreach ($listaUsuarios as $infoUsuario):
							$arrParam = array(
								"mensaje" => $mensaje,
								"idUsuario" => $infoUsuario["id_user"]
							);
							//$this->send_email($arrParam);
						endforeach;
		            }

					//SE BUSCA USUARIOS DE PLANEACION Y SE ENVIA CORREO
					if($idEstado == 3){
			            $arrParam2 = array(
			                "idRole" => ID_ROL_PLANEACION
			            );
			            $listaUsuarios = $this->general_model->get_user($arrParam2);

			            if($listaUsuarios){
			            	foreach ($listaUsuarios as $infoUsuario):
								$arrParam = array(
									"mensaje" => $mensaje,
									"idUsuario" => $infoUsuario["id_user"]
								);
								//$this->send_email($arrParam);
							endforeach;
			            }
		        	}
		            //FIN
				}
				
				$data["result"] = true;
				$data["mensaje"] = $msj;
				$this->session->set_flashdata('retornoExito', $msj);
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Ask for help.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			echo json_encode($data);
    }

    /**
     * Datos de actividades por TRIMESTRE
     * @since 17/04/2022
     * @author BMOTTAG
     */
	public function update_trimestrePI()
	{
		header('Content-Type: application/json');

		$data["idPlanIntegrado"] = $this->input->post('idPlanIntegrado');
		$data["numeroActividadPI"] = $numeroActividadPI = $this->input->post('numeroActividadPI');
		$cumplimientoTrimestre = $this->input->post('cumplimientoTrimestre');
		$avancePOA = $this->input->post('avancePOA');
		$numeroTrimestre = $this->input->post('numeroTrimestre');

		$banderaActividad = true;
		$estadoActividad = 2;
		if ($this->settings_model->guardarTrimestrePI($banderaActividad, $estadoActividad, $numeroActividadPI, $cumplimientoTrimestre, $avancePOA, $numeroTrimestre)){

			$arrParam = array(
				"numeroActividadPI" => $numeroActividadPI,
				"numeroTrimestre" => $numeroTrimestre,
				"observacion" => 'Se cerro el trimestre por parte del ENLACE.',
				"estado" => 2
			);
			$this->general_model->addHistorialActividadPI($arrParam);

			//INICIO
			//SE BUSCA EL SUPERVISOR DE LA DEPENDENCIA Y SE ENVIA CORREO
            $arrParam2 = array(
                "numeroActividadPI" => $numeroActividadPI,
                "idRol" => ID_ROL_SUPERVISOR
            );
            $listaSupervisores = $this->general_model->get_user_encargado_by_actividad($arrParam2);

            if($listaSupervisores){
            	foreach ($listaSupervisores as $infoSupervisor):
					$arrParam = array(
						"mensaje" => 'la actividad <b>No. ' . $numeroActividadPI . '</b> fue <b>CERRADA</b> por el ENLACE para el <b>Trimeste '. $numeroTrimestre .'</b>, por favor ingresar a la plataforma y revisar la información.',
						"idUsuario" => $infoSupervisor["id_user"]
					);
					//$this->send_email($arrParam);
				endforeach;
            }
            //FIN

			$data["result"] = true;
			$data["msj"] = "Se cerro el trimestre.";
		} else {
			$data["result"] = true;
		}
		echo json_encode($data);
    }

    /**
     * Eliminar actividad PI
     * @since 08/05/2023
     * @author AOCUBILLOSA
     */
	public function delete_actividadPI()
	{
		header('Content-Type: application/json');
		$data["idActividad"] = $this->input->post('idActividad');
		$arrParam = array("idActividad" => $data["idActividad"]);
		$infoActividad = $this->general_model->get_actividadesPI($arrParam);
		$data["idPlanIntegrado"] = $infoActividad[0]['fk_id_plan_integrado'];
		$numeroActividad = $infoActividad[0]['numero_actividad_pi'];
		$this->settings_model->eliminar_ejecucionPI($numeroActividad);
		$this->settings_model->eliminar_estadoPI($numeroActividad);
		$this->settings_model->eliminar_historialPI($numeroActividad);
		$this->settings_model->eliminar_auditoriaPI($numeroActividad);
		$arrParam = array(
			"table" => " actividades_pi",
			"primaryKey" => "id_actividad_pi",
			"id" => $data["idActividad"]
		);
		if ($this->general_model->deleteRecord($arrParam)) {
			$data["result"] = true;
			$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> Se eliminó la actividad');
		} else {
			$data["result"] = true;
		}
		echo json_encode($data);
    }

    /**
     * Cargo modal - formulario programa actividad PI
     * @since 08/05/2023
     */
    public function cargarModalProgramarActividadPI() 
	{
			header("Content-Type: text/plain; charset=utf-8"); //Para evitar problemas de acentos
			$data["idActividadPI"] = $this->input->post("idActividadPI");
			$arrParam = array(
				"table" => "param_meses",
				"order" => "id_mes",
				"id" => "x"
			);
			$data['listaMeses'] = $this->general_model->get_basic_search($arrParam);
			$arrParam = array("idActividadPI" => $data["idActividadPI"]);
			$data['information'] = $this->general_model->get_actividadesPI($arrParam);
			$data["idPlanIntegrado"] = $data['information'][0]['fk_id_plan_integrado'];
			$this->load->view("actividad_pi_programar_modal", $data);
    }

    /**
	 * Guardar programado actividades PI
	 * @since 08/05/2023
     * @author AOCUBILLOSA
	 */
	public function guardar_programadoPI()
	{			
			header('Content-Type: application/json');
			$data = array();
			$idPlanIntegrado = $this->input->post('hddIdPlanIntegrado');
			$numeroActividad = $this->input->post('hddNumeroActividad');
			
			$data["idRecord"] = $idPlanIntegrado . "/" . $numeroActividad;
		
			$msj = "Se guardo la información!";

			//validar si ya exite programacion para el mes enviado
			$validarMes = false;

			$arrParam = array(
				'numeroActividad' => $numeroActividad,
				'idMes' => $this->input->post('mes')
			);
			$validarMes = $this->general_model->get_ejecucion_actividadesPI($arrParam);

			if($validarMes){
					$data["result"] = "error";
					$data["mensaje"] = " Error. Este mes ya se encuentra dentro de la programación.";
					$this->session->set_flashdata('retornoError', 'Este mes ya se encuentra dentro de la programación');
			}else{
				if ($this->settings_model->guardarProgramadoPI()) 
				{				
					$data["result"] = true;		
					$this->session->set_flashdata('retornoExito', '<strong>Correcto!</strong> ' . $msj);
				} else {
					$data["result"] = "error";
					$this->session->set_flashdata('retornoError', '<strong>Error!</strong> Ask for help');
				}
			}
		
			echo json_encode($data);
    }

    /**
	 * Actualizar programacion PI
     * @since 08/05/2023
     * @author AOCUBILLOSA
	 */
	public function update_programacion_PI()
	{					
			$numeroActividadPI = $this->input->post('hddNumeroActividadPI');
			$idPlanIntegrado = $this->input->post('hddIdPlanIntegrado');
			$jsondataForm = json_encode($_POST["form"]);

			if ($this->settings_model->guardarProgramacionPI()) {
				$arrParam = array(
					"numeroActividadPI" => $numeroActividadPI,
					"numeroTrimestre" => "",
					"jsondataForm" => $jsondataForm
				);
				//ingreso todos los cambios en la tabla de auditoria
				$this->general_model->addAuditoriaActividadEjecucionPI($arrParam);
				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "Se actualizó la información!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect(base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $numeroActividadPI), 'refresh');
    }

    /**
	 * Actualizar ejecucion
     * @since 18/06/2022
     * @author BMOTTAG
	 */
	public function update_ejecucion_PI()
	{					
			$numeroActividad = $this->input->post('hddNumeroActividad');
			$idPlanIntegrado = $this->input->post('hddIdPlanIntegrado');
			$jsondataForm = json_encode($_POST["form"]);

			$datos = $this->input->post('form');
			if($datos) {
				$tot = count($datos['id']);

				$descripcion_actividad = "";
				$evidencia = "";
				for ($i = 0; $i < $tot; $i++) 
				{	
					if($i != 0){
						if($datos['descripcion'][$i] != ""){
							$descripcion_actividad .= "<br>";
						}
						if($datos['evidencia'][$i] != ""){
							$evidencia .= "<br>";
						}
					}
					$descripcion_actividad .= $datos['descripcion'][$i];
					$evidencia .= $datos['evidencia'][$i];
				}
			}

			if ($this->settings_model->guardarEjecucionPI()) {
				//actualizo el estado del trimestre de la actividad
				$arrParam = array(
					"numeroActividadPI" => $numeroActividad,
					"numeroTrimestre" => $this->input->post('hddNumeroTrimestre'),
					"observacion" => $this->input->post('observacion'),
					"estado" => 1,
					"descripcion_actividad" => $descripcion_actividad,
					"evidencia" => $evidencia,
					"jsondataForm" => $jsondataForm

				);
				$this->general_model->addHistorialActividadPI($arrParam);

				//actualizo el estado del trimestre de la actividad
				$this->general_model->updateEstadoActividadPI($arrParam);
				//ingreso todos los cambios en la tabla de auditoria
				$this->general_model->addAuditoriaActividadEjecucionPI($arrParam);

				$data["result"] = true;
				$this->session->set_flashdata('retornoExito', "Se actualizó la información!!");
			} else {
				$data["result"] = "error";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect(base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $numeroActividad), 'refresh');
    }

    /**
     * Delete Ejecucion
     * @since 17/04/2022
     * @author BMOTTAG
     */
    public function deleteEjecucionPI($idPlanIntegrado, $idActividad, $idEjecucion) 
	{
			if (empty($idPlanIntegrado) || empty($idActividad) || empty($idEjecucion) ) {
				show_error('ERROR!!! - You are in the wrong place.');
			}
		
			$arrParam = array(
				"table" => "actividad_ejecucion_pi",
				"primaryKey" => "id_ejecucion_actividad_pi",
				"id" => $idEjecucion
			);

			if ($this->general_model->deleteRecord($arrParam)) {
				$this->session->set_flashdata('retornoExito', 'Se elimio la ejecución de la actividad.');
			} else {
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Ask for help');
			}

			redirect(base_url('settings/actividadesPI/' . $idPlanIntegrado . '/' . $idActividad), 'refresh');
    }

    /**
	 * Evio de correo
     * @since 08/05/2023
     * @author AOCUBILLOSA
	 */
	public function send_email($arrData)
	{
			$arrParam = array('idUser' => $arrData["idUsuario"]);
			$infoUsuario = $this->general_model->get_user($arrParam);
			//$to = $infoUsuario[0]['email'];
			$to = "andres.cubillos@jbb.gov.co";

			//busco datos parametricos de configuracion para envio de correo
			$arrParam2 = array(
				"table" => "parametros",
				"order" => "id_parametro",
				"id" => "x"
			);
			$parametric = $this->general_model->get_basic_search($arrParam2);

			$paramHost = $parametric[0]["parametro_valor"];
			$paramUsername = $parametric[1]["parametro_valor"];
			$paramPassword = $parametric[2]["parametro_valor"];
			$paramFromName = $parametric[3]["parametro_valor"];
			$paramCompanyName = $parametric[4]["parametro_valor"];
			$paramAPPName = $parametric[5]["parametro_valor"];

			//mensaje del correo
			$msj = 'Sr.(a) ' . $infoUsuario[0]['first_name'] . ', ';
			$msj .= $arrData["mensaje"] . '</br></br>';
			$msj .= '<strong>Enlace aplicación: </strong>';

			$msj .= "<a href='" . base_url() . "'>APP Programa Institucional - Cuadro de Mando</a>";
									
			$mensaje = "<p>$msj</p>
						<p>Cordialmente,</p>
						<p><strong>$paramCompanyName</strong></p>";		

			require_once(APPPATH.'libraries/PHPMailer/class.phpmailer.php');
            $mail = new PHPMailer(true);

            $mail->IsSMTP(); // set mailer to use SMTP
            $mail->Host = $paramHost; // specif smtp server
            $mail->SMTPSecure= "tls"; // Used instead of TLS when only POP mail is selected
            $mail->Port = 587; // Used instead of 587 when only POP mail is selected
            $mail->SMTPAuth = true;
			$mail->Username = $paramUsername; // SMTP username
            $mail->Password = $paramPassword; // SMTP password
            $mail->FromName = $paramFromName;
            $mail->From = $paramUsername;
            $mail->AddAddress($to, 'Usuario JBB Bienes');
            $mail->WordWrap = 50;
            $mail->CharSet = 'UTF-8';
            $mail->IsHTML(true); // set email format to HTML
            $mail->Subject = $paramCompanyName . ' - ' . $paramAPPName;
            $mail->Body = nl2br ($mensaje,false);
            $mail->Send();
			return true;
	}

	/**
	 * RESUMEN
	 * @since 30/05/2023
	 */
	public function reporteMatrizPAI()
	{	
		$fechaActual = date('Y-m-d');
		$vigencia = $this->general_model->get_vigencia();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition:attachment;filename=matriz_PAI_'.$fechaActual.'.xlsx');

		$arrParam = array();
		$listaActividades = $this->general_model->get_actividades_pi_full($arrParam);
		$arrParam = array(
			'vigencia' => $vigencia['vigencia']
		);
		$indicadoresGestion = $this->settings_model->get_indicadores_gestion($arrParam);

		/**
			INDICADORES DE GESTION
		**/

		$spreadsheet = new Spreadsheet();
		$spreadsheet->getActiveSheet()->setTitle('Indicadores de Gestión');

		$img1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$img1->setPath('images/logo_alcaldia.png');
		$img1->setCoordinates('A1');
		$img1->setOffsetX(20);
		$img1->setOffsetY(10);
		$img1->setWorksheet($spreadsheet->getActiveSheet());

		$img2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$img2->setPath('images/logo_bogota.png');
		$img2->setCoordinates('BA1');
		$img2->setOffsetX(10);
		$img2->setOffsetY(10);
		$img2->setWorksheet($spreadsheet->getActiveSheet());

		$spreadsheet->getActiveSheet()->mergeCells('A1:A5');
		$spreadsheet->getActiveSheet()->mergeCells('BA1:BA5');
		$spreadsheet->getActiveSheet()->mergeCells('B1:AZ1');
		$spreadsheet->getActiveSheet()->mergeCells('B2:AZ2');
		$spreadsheet->getActiveSheet()->mergeCells('B3:AZ3');
		$spreadsheet->getActiveSheet()->mergeCells('B4:T4');
		$spreadsheet->getActiveSheet()->mergeCells('U4:AJ4');
		$spreadsheet->getActiveSheet()->mergeCells('AK4:AT4');
		$spreadsheet->getActiveSheet()->mergeCells('AU4:AZ4');
		$spreadsheet->getActiveSheet()->mergeCells('B5:T5');
		$spreadsheet->getActiveSheet()->mergeCells('U5:AJ5');
		$spreadsheet->getActiveSheet()->mergeCells('AK5:AT5');
		$spreadsheet->getActiveSheet()->mergeCells('AU5:AZ5');
		$spreadsheet->getActiveSheet()->mergeCells('A6:BA6');

		$spreadsheet->getActiveSheet(0)
							->setCellValue('B1', 'MANUAL DE PROCESOS Y PROCEDIMIENTOS')
							->setCellValue('B2', 'DYP - DIRECCIONAMIENTO Y PLANEACIÓN')
							->setCellValue('B3', 'MATRIZ PLAN DE ACCIÓN INSTITUCIONAL')
							->setCellValue('B4', 'Código:')
							->setCellValue('U4', 'Versión:')
							->setCellValue('AK4', 'Fecha:')
							->setCellValue('AU4', 'Página:')
							->setCellValue('B5', 'DYP.PR.17.F.01')
							->setCellValue('U5', '3')
							->setCellValue('AK5', '05/07/2023')
							->setCellValue('AU5', '4 de 5')
							->setCellValue('A6', 'Vigencia: ' . $vigencia['vigencia']);


		$spreadsheet->getActiveSheet(0)
							->setCellValue('A7', 'Código')
							->setCellValue('B7', 'Nombre')
							->setCellValue('C7', 'Estado')
							->setCellValue('D7', 'Objetivo Indicador')
							->setCellValue('E7', 'Dependencia')
							->setCellValue('F7', 'Proceso')
							->setCellValue('G7', 'Objetivo Proceso')
							->setCellValue('H7', 'Responsable')
							->setCellValue('I7', 'Tipo indicador')
							->setCellValue('J7', 'Tendencia')
							->setCellValue('K7', 'Periodicidad')
							->setCellValue('L7', 'Fuente Información')
							->setCellValue('M7', 'Línea base')
							->setCellValue('N7', 'Unidad de Medida')
							->setCellValue('O7', 'Fecha Creación')
							->setCellValue('P7', 'Fórmula')
							->setCellValue('Q7', 'Enero')
							->setCellValue('R7', 'Seguimiento')
							->setCellValue('S7', 'Seguimiento OAP')
							->setCellValue('T7', 'Febrero')
							->setCellValue('U7', 'Seguimiento')
							->setCellValue('V7', 'Seguimiento OAP')
							->setCellValue('W7', 'Marzo')
							->setCellValue('X7', 'Seguimiento')
							->setCellValue('Y7', 'Seguimiento OAP')
							->setCellValue('Z7', 'Abril')
							->setCellValue('AA7', 'Seguimiento')
							->setCellValue('AB7', 'Seguimiento OAP')
							->setCellValue('AC7', 'Mayo')
							->setCellValue('AD7', 'Seguimiento')
							->setCellValue('AE7', 'Seguimiento OAP')
							->setCellValue('AF7', 'Junio')
							->setCellValue('AG7', 'Seguimiento')
							->setCellValue('AH7', 'Seguimiento OAP')
							->setCellValue('AI7', 'Julio')
							->setCellValue('AJ7', 'Seguimiento')
							->setCellValue('AK7', 'Seguimiento OAP')
							->setCellValue('AL7', 'Agosto')
							->setCellValue('AM7', 'Seguimiento')
							->setCellValue('AN7', 'Seguimiento OAP')
							->setCellValue('AO7', 'Septiembre')
							->setCellValue('AP7', 'Seguimiento')
							->setCellValue('AQ7', 'Seguimiento OAP')
							->setCellValue('AR7', 'Octubre')
							->setCellValue('AS7', 'Seguimiento')
							->setCellValue('AT7', 'Seguimiento OAP')
							->setCellValue('AU7', 'Noviembre')
							->setCellValue('AV7', 'Seguimiento')
							->setCellValue('AW7', 'Seguimiento OAP')
							->setCellValue('AX7', 'Diciembre')
							->setCellValue('AY7', 'Seguimiento')
							->setCellValue('AZ7', 'Seguimiento OAP')
							->setCellValue('BA7', 'Ejecución Acumulada');

		$j=8;
		if($indicadoresGestion){
			foreach ($indicadoresGestion as $lista):
				$spreadsheet->getActiveSheet()
							->setCellValue('A'.$j, $lista['codigo'])
							->setCellValue('B'.$j, $lista['nombre'])
							->setCellValue('C'.$j, $lista['estado'])
							->setCellValue('D'.$j, $lista['objetivo_indicador'])
							->setCellValue('E'.$j, $lista['dependencia'])
							->setCellValue('F'.$j, $lista['proceso'])
							->setCellValue('G'.$j, $lista['objetivo_proceso'])
							->setCellValue('H'.$j, $lista['responsable'])
							->setCellValue('I'.$j, $lista['tipo_indicador'])
							->setCellValue('J'.$j, $lista['tendencia'])
							->setCellValue('K'.$j, $lista['periodicidad'])
							->setCellValue('L'.$j, $lista['fuente_informacion'])
							->setCellValue('M'.$j, $lista['linea_base'])
							->setCellValue('N'.$j, $lista['unidad_medida'])
							->setCellValue('O'.$j, $lista['fecha_creacion'])
							->setCellValue('P'.$j, $lista['formula'])
							->setCellValue('Q'.$j, $lista['enero'])
							->setCellValue('R'.$j, $lista['seguimiento_ene'])
							->setCellValue('S'.$j, $lista['seguimiento_oap_ene'])
							->setCellValue('T'.$j, $lista['febrero'])
							->setCellValue('U'.$j, $lista['seguimiento_feb'])
							->setCellValue('V'.$j, $lista['seguimiento_oap_feb'])
							->setCellValue('W'.$j, $lista['marzo'])
							->setCellValue('X'.$j, $lista['seguimiento_mar'])
							->setCellValue('Y'.$j, $lista['seguimiento_oap_mar'])
							->setCellValue('Z'.$j, $lista['abril'])
							->setCellValue('AA'.$j, $lista['seguimiento_abr'])
							->setCellValue('AB'.$j, $lista['seguimiento_oap_abr'])
							->setCellValue('AC'.$j, $lista['mayo'])
							->setCellValue('AD'.$j, $lista['seguimiento_may'])
							->setCellValue('AE'.$j, $lista['seguimiento_oap_may'])
							->setCellValue('AF'.$j, $lista['junio'])
							->setCellValue('AG'.$j, $lista['seguimiento_jun'])
							->setCellValue('AH'.$j, $lista['seguimiento_oap_jun'])
							->setCellValue('AI'.$j, $lista['julio'])
							->setCellValue('AJ'.$j, $lista['seguimiento_jul'])
							->setCellValue('AK'.$j, $lista['seguimiento_oap_jul'])
							->setCellValue('AL'.$j, $lista['agosto'])
							->setCellValue('AM'.$j, $lista['seguimiento_ago'])
							->setCellValue('AN'.$j, $lista['seguimiento_oap_ago'])
							->setCellValue('AO'.$j, $lista['septiembre'])
							->setCellValue('AP'.$j, $lista['seguimiento_sep'])
							->setCellValue('AQ'.$j, $lista['seguimiento_oap_sep'])
							->setCellValue('AR'.$j, $lista['octubre'])
							->setCellValue('AS'.$j, $lista['seguimiento_oct'])
							->setCellValue('AT'.$j, $lista['seguimiento_oap_oct'])
							->setCellValue('AU'.$j, $lista['noviembre'])
							->setCellValue('AV'.$j, $lista['seguimiento_nov'])
							->setCellValue('AW'.$j, $lista['seguimiento_oap_nov'])
							->setCellValue('AX'.$j, $lista['diciembre'])
							->setCellValue('AY'.$j, $lista['seguimiento_dic'])
							->setCellValue('AZ'.$j, $lista['seguimiento_oap_dic'])
							->setCellValue('BA'.$j, $lista['ejecucion_acumulada']);
				$j++;
			endforeach;
		}

		// Set column widths
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(35);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(35);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(20);
		$spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AB')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AC')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AD')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AE')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AF')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AG')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AH')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AI')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AJ')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AK')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AL')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AM')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AN')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AO')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AP')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AQ')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AR')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AS')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AT')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AU')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AV')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AW')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AX')->setWidth(15);
		$spreadsheet->getActiveSheet()->getColumnDimension('AY')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('AZ')->setWidth(50);
		$spreadsheet->getActiveSheet()->getColumnDimension('BA')->setWidth(40);

		// Set fonts
		$spreadsheet->getActiveSheet()->getStyle('B1:AZ3')->getFont()->setSize(14);
		$spreadsheet->getActiveSheet()->getStyle('B1:AZ3')->getFont()->setBold(true);
 		$spreadsheet->getActiveSheet()->getStyle('B1:AZ3')->getFill()->setFillType(Fill::FILL_SOLID);
 		$spreadsheet->getActiveSheet()->getStyle('B1:AZ3')->getFill()->getStartColor()->setARGB('236e09');
 		$spreadsheet->getActiveSheet()->getStyle('B1:AZ3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
		$spreadsheet->getActiveSheet()->getStyle('B1:AZ3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('B1:AZ3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->getActiveSheet()->getStyle('B4:AZ5')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('B4:AZ5')->getFont()->setBold(true);
 		$spreadsheet->getActiveSheet()->getStyle('B4:AZ5')->getFill()->setFillType(Fill::FILL_SOLID);
 		$spreadsheet->getActiveSheet()->getStyle('B4:AZ5')->getFill()->getStartColor()->setARGB('236e09');
 		$spreadsheet->getActiveSheet()->getStyle('B4:AZ5')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
		$spreadsheet->getActiveSheet()->getStyle('B4:AZ5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('B4:AZ5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
 		$spreadsheet->getActiveSheet()->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->getActiveSheet()->getStyle('A7:BA7')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('A7:BA7')->getFont()->setBold(true);
		$spreadsheet->getActiveSheet()->getStyle('A7:BA7')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
 		$spreadsheet->getActiveSheet()->getStyle('A7:BA7')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('A7:BA7')->getFill()->getStartColor()->setARGB('808080');
		$spreadsheet->getActiveSheet()->getStyle('A7:BA7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A7:BA7')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('7')->setRowHeight(30);

		$spreadsheet->getActiveSheet()->getStyle('A1:BA5')->applyFromArray(
		    [
		        'borders' => [
		            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
		        ],
		    ]
		);

		$spreadsheet->getActiveSheet()->getStyle('A7:BA7')->applyFromArray(
		    [
		        'borders' => [
		            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
		        ],
		    ]
		);

		$spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray(
		    [
			    'alignment' => [
			        'wrapText' => TRUE
			    ]
		    ]
		);

		/**
			PLAN INTEGRADO
		**/

		$spreadsheet->createSheet();
		$spreadsheet->setActiveSheetIndex(1);
		$spreadsheet->getActiveSheet()->setTitle('Plan Integrado');

		$img1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$img1->setPath('images/logo_alcaldia.png');
		$img1->setCoordinates('A1');
		$img1->setOffsetX(100);
		$img1->setOffsetY(10);
		$img1->setWorksheet($spreadsheet->getActiveSheet());

		$img2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
		$img2->setPath('images/logo_bogota.png');
		$img2->setCoordinates('AP1');
		$img2->setOffsetX(10);
		$img2->setOffsetY(10);
		$img2->setWorksheet($spreadsheet->getActiveSheet());

		$spreadsheet->getActiveSheet()->mergeCells('A1:A5');
		$spreadsheet->getActiveSheet()->mergeCells('AP1:AP5');
		$spreadsheet->getActiveSheet()->mergeCells('B1:AO1');
		$spreadsheet->getActiveSheet()->mergeCells('B2:AO2');
		$spreadsheet->getActiveSheet()->mergeCells('B3:AO3');
		$spreadsheet->getActiveSheet()->mergeCells('B4:V4');
		$spreadsheet->getActiveSheet()->mergeCells('W4:AH4');
		$spreadsheet->getActiveSheet()->mergeCells('AI4:AL4');
		$spreadsheet->getActiveSheet()->mergeCells('AM4:AO4');
		$spreadsheet->getActiveSheet()->mergeCells('B5:V5');
		$spreadsheet->getActiveSheet()->mergeCells('W5:AH5');
		$spreadsheet->getActiveSheet()->mergeCells('AI5:AL5');
		$spreadsheet->getActiveSheet()->mergeCells('AM5:AO5');
		$spreadsheet->getActiveSheet()->mergeCells('A6:AP6');
		$spreadsheet->getActiveSheet()->mergeCells('A7:A8');
		$spreadsheet->getActiveSheet()->mergeCells('B7:B8');
		$spreadsheet->getActiveSheet()->mergeCells('C7:C8');
		$spreadsheet->getActiveSheet()->mergeCells('D7:D8');
		$spreadsheet->getActiveSheet()->mergeCells('E7:E8');

		$spreadsheet->getActiveSheet(0)->setCellValue('F7', 'Programación');
		$spreadsheet->getActiveSheet()->mergeCells('F7:Q7');

		$spreadsheet->getActiveSheet(0)->setCellValue('R7', 'Ejecución');
		$spreadsheet->getActiveSheet()->mergeCells('R7:AC7');

		$spreadsheet->getActiveSheet()->mergeCells('AD7:AD8');
		$spreadsheet->getActiveSheet()->mergeCells('AE7:AE8');
		$spreadsheet->getActiveSheet()->mergeCells('AF7:AF8');
		$spreadsheet->getActiveSheet()->mergeCells('AG7:AG8');
		$spreadsheet->getActiveSheet()->mergeCells('AH7:AH8');
		$spreadsheet->getActiveSheet()->mergeCells('AI7:AI8');
		$spreadsheet->getActiveSheet()->mergeCells('AJ7:AJ8');
		$spreadsheet->getActiveSheet()->mergeCells('AK7:AK8');
		$spreadsheet->getActiveSheet()->mergeCells('AL7:AL8');
		$spreadsheet->getActiveSheet()->mergeCells('AM7:AM8');
		$spreadsheet->getActiveSheet()->mergeCells('AN7:AN8');
		$spreadsheet->getActiveSheet()->mergeCells('AO7:AO8');
		$spreadsheet->getActiveSheet()->mergeCells('AP7:AP8');

		$spreadsheet->getActiveSheet(0)
							->setCellValue('B1', 'MANUAL DE PROCESOS Y PROCEDIMIENTOS')
							->setCellValue('B2', 'DYP - DIRECCIONAMIENTO Y PLANEACIÓN')
							->setCellValue('B3', 'MATRIZ PLAN DE ACCIÓN INSTITUCIONAL')
							->setCellValue('B4', 'Código:')
							->setCellValue('W4', 'Versión:')
							->setCellValue('AI4', 'Fecha:')
							->setCellValue('AM4', 'Página:')
							->setCellValue('B5', 'DYP.PR.17.F.01')
							->setCellValue('W5', '3')
							->setCellValue('AI5', '05/07/2023')
							->setCellValue('AM5', '5 de 5')
							->setCellValue('A6', 'Vigencia: ' . $vigencia['vigencia']);


		$spreadsheet->getActiveSheet(0)
							->setCellValue('A7', 'Nombre del Plan Institucional y Estratégico')
							->setCellValue('B7', 'Dependencia')
							->setCellValue('C7', 'Actividad')
							->setCellValue('D7', 'ID Actividad')
							->setCellValue('E7', 'Ponderación')
							->setCellValue('F8', 'Enero')
							->setCellValue('G8', 'Febrero')
							->setCellValue('H8', 'Marzo')
							->setCellValue('I8', 'Abril')
							->setCellValue('J8', 'Mayo')
							->setCellValue('K8', 'Junio')
							->setCellValue('L8', 'Julio')
							->setCellValue('M8', 'Agosto')
							->setCellValue('N8', 'Septiembre')
							->setCellValue('O8', 'Octubre')
							->setCellValue('P8', 'Noviembre')
							->setCellValue('Q8', 'Diciembre')
							->setCellValue('R8', 'Enero')
							->setCellValue('S8', 'Febrero')
							->setCellValue('T8', 'Marzo')
							->setCellValue('U8', 'Abril')
							->setCellValue('V8', 'Mayo')
							->setCellValue('W8', 'Junio')
							->setCellValue('X8', 'Julio')
							->setCellValue('Y8', 'Agrosto')
							->setCellValue('Z8', 'Septiembre')
							->setCellValue('AA8', 'Octubre')
							->setCellValue('AB8', 'Noviembre')
							->setCellValue('AC8', 'Diciembre')
							->setCellValue('AD7', 'Total')
							->setCellValue('AE7', 'Descripcion Trimestre I')
							->setCellValue('AF7', 'Descripcion Trimestre II')
							->setCellValue('AG7', 'Descripcion Trimestre III')
							->setCellValue('AH7', 'Descripcion Trimestre IV')
							->setCellValue('AI7', 'Evidencia Trimestre I')
							->setCellValue('AJ7', 'Evidencia Trimestre II')
							->setCellValue('AK7', 'Evidencia Trimestre III')
							->setCellValue('AL7', 'Evidencia Trimestre IV')
							->setCellValue('AM7', 'Observacion OAP Trimestre I')
							->setCellValue('AN7', 'Observacion OAP Trimestre II')
							->setCellValue('AO7', 'Observacion OAP Trimestre III')
							->setCellValue('AP7', 'Observacion OAP Trimestre IV');

		$j=9;
		if($listaActividades){
			foreach ($listaActividades as $lista):
				$arrParam = array("numeroActividadPI" => $lista['numero_actividad_pi']);
				$infoEjecucion = $this->general_model->get_ejecucion_actividadesPI($arrParam);

				$arrParam = array("numeroActividadPI" => $lista['numero_actividad_pi']);
				$ejecucionActividadesPI = $this->general_model->get_ejecucion_actividadesPI($arrParam);

				if (!empty($ejecucionActividadesPI[0]['descripcion_actividades']) && !empty($ejecucionActividadesPI[1]['descripcion_actividades']) && !empty($ejecucionActividadesPI[2]['descripcion_actividades'])) {
					$descTrim1 = $ejecucionActividadesPI[0]['mes'] . ': ' . $ejecucionActividadesPI[0]['descripcion_actividades'] . "\n" . $ejecucionActividadesPI[1]['mes'] . ': ' . $ejecucionActividadesPI[1]['descripcion_actividades'] . "\n" . $ejecucionActividadesPI[2]['mes'] . ': ' . $ejecucionActividadesPI[2]['descripcion_actividades'];
				} else {
					$descTrim1 = '';
				}

				if (!empty($ejecucionActividadesPI[3]['descripcion_actividades']) && !empty($ejecucionActividadesPI[4]['descripcion_actividades']) && !empty($ejecucionActividadesPI[5]['descripcion_actividades'])) {
					$descTrim2 = $ejecucionActividadesPI[3]['mes'] . ': ' . $ejecucionActividadesPI[3]['descripcion_actividades'] . "\n" . $ejecucionActividadesPI[4]['mes'] . ': ' . $ejecucionActividadesPI[4]['descripcion_actividades'] . "\n" . $ejecucionActividadesPI[5]['mes'] . ': ' . $ejecucionActividadesPI[5]['descripcion_actividades'];
				} else {
					$descTrim2 = '';
				}

				if (!empty($ejecucionActividadesPI[6]['descripcion_actividades']) && !empty($ejecucionActividadesPI[7]['descripcion_actividades']) && !empty($ejecucionActividadesPI[8]['descripcion_actividades'])) {
					$descTrim3 = $ejecucionActividadesPI[6]['mes'] . ': ' . $ejecucionActividadesPI[6]['descripcion_actividades'] . "\n" . $ejecucionActividadesPI[7]['mes'] . ': ' . $ejecucionActividadesPI[7]['descripcion_actividades'] . "\n" . $ejecucionActividadesPI[8]['mes'] . ': ' . $ejecucionActividadesPI[8]['descripcion_actividades'];
				} else {
					$descTrim3 = '';
				}

				if (!empty($ejecucionActividadesPI[9]['descripcion_actividades']) && !empty($ejecucionActividadesPI[10]['descripcion_actividades']) && !empty($ejecucionActividadesPI[11]['descripcion_actividades'])) {
					$descTrim4 = $ejecucionActividadesPI[9]['mes'] . ': ' . $ejecucionActividadesPI[9]['descripcion_actividades'] . "\n" . $ejecucionActividadesPI[10]['mes'] . ': ' . $ejecucionActividadesPI[10]['descripcion_actividades'] ."\n" . $ejecucionActividadesPI[11]['mes'] . ': ' . $ejecucionActividadesPI[11]['descripcion_actividades'];
				} else {
					$descTrim4 = '';
				}

				if (!empty($ejecucionActividadesPI[0]['evidencias']) && !empty($ejecucionActividadesPI[1]['evidencias']) && !empty($ejecucionActividadesPI[2]['evidencias'])) {
					$evidTrim1 = $ejecucionActividadesPI[0]['mes'] . ': ' . $ejecucionActividadesPI[0]['evidencias'] . "\n" . $ejecucionActividadesPI[1]['mes'] . ': ' . $ejecucionActividadesPI[1]['evidencias'] . "\n" . $ejecucionActividadesPI[2]['mes'] . ': ' . $ejecucionActividadesPI[2]['evidencias'];
				} else {
					$evidTrim1 = '';
				}

				if (!empty($ejecucionActividadesPI[3]['evidencias']) && !empty($ejecucionActividadesPI[4]['evidencias']) && !empty($ejecucionActividadesPI[5]['evidencias'])) {
					$evidTrim2 = $ejecucionActividadesPI[3]['mes'] . ': ' . $ejecucionActividadesPI[3]['evidencias'] . "\n" . $ejecucionActividadesPI[4]['mes'] . ': ' . $ejecucionActividadesPI[4]['evidencias'] . "\n" . $ejecucionActividadesPI[5]['mes'] . ': ' . $ejecucionActividadesPI[5]['evidencias'];
				} else {
					$evidTrim2 = '';
				}

				if (!empty($ejecucionActividadesPI[6]['evidencias']) && !empty($ejecucionActividadesPI[7]['evidencias']) && !empty($ejecucionActividadesPI[8]['evidencias'])) {
					$evidTrim3 = $ejecucionActividadesPI[6]['mes'] . ': ' . $ejecucionActividadesPI[6]['evidencias'] . "\n" . $ejecucionActividadesPI[7]['mes'] . ': ' . $ejecucionActividadesPI[7]['evidencias'] . "\n" . $ejecucionActividadesPI[8]['mes'] . ': ' . $ejecucionActividadesPI[8]['evidencias'];
				} else {
					$evidTrim3 = '';
				}

				if (!empty($ejecucionActividadesPI[9]['evidencias']) && !empty($ejecucionActividadesPI[10]['evidencias']) && !empty($ejecucionActividadesPI[11]['evidencias'])) {
					$evidTrim4 = $ejecucionActividadesPI[9]['mes'] . ': ' . $ejecucionActividadesPI[9]['evidencias'] . "\n" . $ejecucionActividadesPI[10]['mes'] . ': ' . $ejecucionActividadesPI[10]['evidencias'] ."\n" . $ejecucionActividadesPI[11]['mes'] . ': ' . $ejecucionActividadesPI[11]['evidencias'];
				} else {
					$evidTrim4 = '';
				}

				$spreadsheet->getActiveSheet()
							->setCellValue('A'.$j, $lista['plan_institucional'])
							->setCellValue('B'.$j, $lista['dependencia'])
							->setCellValue('C'.$j, $lista['descripcion_actividad_pi'])
							->setCellValue('D'.$j, $lista['numero_actividad_pi'])
							->setCellValue('E'.$j, $lista['ponderacion_pi']);

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 1){
						$spreadsheet->getActiveSheet()->setCellValue('F'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('R'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 2){
						$spreadsheet->getActiveSheet()->setCellValue('G'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('S'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 3){
						$spreadsheet->getActiveSheet()->setCellValue('H'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('T'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 4){
						$spreadsheet->getActiveSheet()->setCellValue('I'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('U'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 5){
						$spreadsheet->getActiveSheet()->setCellValue('J'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('V'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 6){
						$spreadsheet->getActiveSheet()->setCellValue('K'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('W'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 7){
						$spreadsheet->getActiveSheet()->setCellValue('L'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('X'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 8){
						$spreadsheet->getActiveSheet()->setCellValue('M'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('Y'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 9){
						$spreadsheet->getActiveSheet()->setCellValue('N'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('Z'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 10){
						$spreadsheet->getActiveSheet()->setCellValue('O'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('AA'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 11){
						$spreadsheet->getActiveSheet()->setCellValue('P'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('AB'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;

				foreach ($infoEjecucion as $ejecucion):
					if($ejecucion['fk_id_mes'] == 12){
						$spreadsheet->getActiveSheet()->setCellValue('Q'.$j, $ejecucion['programado']);
						$spreadsheet->getActiveSheet()->setCellValue('AC'.$j, $ejecucion['ejecutado']);
						break;
					}
				endforeach;
				$spreadsheet->getActiveSheet()->setCellValue('AD'.$j, $lista['ponderacion_pi']);
				$spreadsheet->getActiveSheet()->setCellValue('AE'.$j, $descTrim1);
				$spreadsheet->getActiveSheet()->getStyle('AE'.$j)->getAlignment()->setWrapText(true);
				$spreadsheet->getActiveSheet()->setCellValue('AF'.$j, $descTrim2);
				$spreadsheet->getActiveSheet()->getStyle('AF'.$j)->getAlignment()->setWrapText(true);
				$spreadsheet->getActiveSheet()->setCellValue('AG'.$j, $descTrim3);
				$spreadsheet->getActiveSheet()->getStyle('AG'.$j)->getAlignment()->setWrapText(true);
				$spreadsheet->getActiveSheet()->setCellValue('AH'.$j, $descTrim4);
				$spreadsheet->getActiveSheet()->getStyle('AH'.$j)->getAlignment()->setWrapText(true);
				$spreadsheet->getActiveSheet()->setCellValue('AI'.$j, $evidTrim1);
				$spreadsheet->getActiveSheet()->getStyle('AI'.$j)->getAlignment()->setWrapText(true);
				$spreadsheet->getActiveSheet()->setCellValue('AJ'.$j, $evidTrim2);
				$spreadsheet->getActiveSheet()->getStyle('AJ'.$j)->getAlignment()->setWrapText(true);
				$spreadsheet->getActiveSheet()->setCellValue('AK'.$j, $evidTrim3);
				$spreadsheet->getActiveSheet()->getStyle('AK'.$j)->getAlignment()->setWrapText(true);
				$spreadsheet->getActiveSheet()->setCellValue('AL'.$j, $evidTrim4);
				$spreadsheet->getActiveSheet()->getStyle('AL'.$j)->getAlignment()->setWrapText(true);
				$spreadsheet->getActiveSheet()
								->setCellValue('AM'.$j, $lista['mensaje_poa_trimestre_1'])
								->setCellValue('AN'.$j, $lista['mensaje_poa_trimestre_2'])
								->setCellValue('AO'.$j, $lista['mensaje_poa_trimestre_3'])
								->setCellValue('AP'.$j, $lista['mensaje_poa_trimestre_4']);
				$j++;
			endforeach;
		}

		// Set column widths
		$spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(45);
		$spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(12);
		$spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(12);
		$spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('AA')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('AB')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('AC')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('AD')->setWidth(10);
		$spreadsheet->getActiveSheet()->getColumnDimension('AE')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AF')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AG')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AH')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AI')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AJ')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AK')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AL')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AM')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AN')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AO')->setWidth(40);
		$spreadsheet->getActiveSheet()->getColumnDimension('AP')->setWidth(40);

		// Set fonts
		$spreadsheet->getActiveSheet()->getStyle('B1:AO3')->getFont()->setSize(14);
		$spreadsheet->getActiveSheet()->getStyle('B1:AO3')->getFont()->setBold(true);
 		$spreadsheet->getActiveSheet()->getStyle('B1:AO3')->getFill()->setFillType(Fill::FILL_SOLID);
 		$spreadsheet->getActiveSheet()->getStyle('B1:AO3')->getFill()->getStartColor()->setARGB('236e09');
 		$spreadsheet->getActiveSheet()->getStyle('B1:AO3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
		$spreadsheet->getActiveSheet()->getStyle('B1:AO3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('B1:AO3')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->getActiveSheet()->getStyle('B4:AO5')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('B4:AO5')->getFont()->setBold(true);
 		$spreadsheet->getActiveSheet()->getStyle('B4:AO5')->getFill()->setFillType(Fill::FILL_SOLID);
 		$spreadsheet->getActiveSheet()->getStyle('B4:AO5')->getFill()->getStartColor()->setARGB('236e09');
 		$spreadsheet->getActiveSheet()->getStyle('B4:AO5')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
		$spreadsheet->getActiveSheet()->getStyle('B4:AO5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('B4:AO5')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
 		$spreadsheet->getActiveSheet()->getStyle('A6')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->getActiveSheet()->getStyle('A7:AP8')->getFont()->setSize(11);
		$spreadsheet->getActiveSheet()->getStyle('A7:AP8')->getFont()->setBold(true);
		$spreadsheet->getActiveSheet()->getStyle('A7:AP8')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
 		$spreadsheet->getActiveSheet()->getStyle('A7:AP8')->getFill()->setFillType(Fill::FILL_SOLID);
		$spreadsheet->getActiveSheet()->getStyle('A7:AP8')->getFill()->getStartColor()->setARGB('808080');
		$spreadsheet->getActiveSheet()->getStyle('A7:AP8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$spreadsheet->getActiveSheet()->getStyle('A7:AP8')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

		$spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('5')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('6')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('7')->setRowHeight(20);
		$spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(70);

		$spreadsheet->getActiveSheet()->getStyle('A1:AP5')->applyFromArray(
		    [
		        'borders' => [
		            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
		        ],
		    ]
		);

		$spreadsheet->getActiveSheet()->getStyle('A7:AP8')->applyFromArray(
		    [
		        'borders' => [
		            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
		        ],
		    ]
		);

		$spreadsheet->getActiveSheet()->getStyle('F8:AC8')->applyFromArray(
		    [
			    'alignment' => [
			        'textRotation' => 90,
			        'readOrder' => Alignment::READORDER_RTL,
			        'wrapText' => TRUE
			    ]
		    ]
		);

		$spreadsheet->getActiveSheet()->getStyle('A1:A5')->applyFromArray(
		    [
			    'alignment' => [
			        'wrapText' => TRUE
			    ]
		    ]
		);

		$spreadsheet->setActiveSheetIndex(0);
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}

	/**
	 * Indicadores de Gestion
	 * @since 28/07/2023
	 * @author AOCUBILLOSA
	 */
	public function indicadores_gestion()
	{
			$vigencia = $this->general_model->get_vigencia();
			$arrParam = array(
				'vigencia' => $vigencia['vigencia']
			);
			$data['info'] = $this->settings_model->get_indicadores_gestion($arrParam);
			$data['vigencia'] = $this->general_model->get_vigencia();
			$data["view"] = "indicadores_gestion";
			$this->load->view("layout_calendar", $data);
	}

	/**
	 * Eliminar indicadores de gestion
	 * @since 28/07/2023
	 */
	public function delete_indicadores_gestion()
	{
			header('Content-Type: application/json');
			$data = array();
			$vigencia = $this->general_model->get_vigencia();
			if ($this->settings_model->eliminarIndicadoresGestion($vigencia['vigencia'])) {
				$data["msj"] = " la tabla indicadores_gestion.";
				$data["result"] = true;
				$data["mensaje"] = "Se eliminaron los registros.";
				$this->session->set_flashdata('retornoExito', 'Se eliminó los registros de ' . $data["msj"]);
			} else {
				$data["result"] = "error";
				$data["mensaje"] = "Error!!! Contactarse con el Administrador.";
				$this->session->set_flashdata('retornoError', '<strong>Error!!!</strong> Contactarse con el Administrador');
			}
			echo json_encode($data);
	}
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Settings_model extends CI_Model {

	    
		/**
		 * Verify if the user already exist by the social insurance number
		 * @author BMOTTAG
		 * @since  8/11/2016
		 * @review 10/12/2020
		 */
		public function verifyUser($arrData) 
		{
				if (array_key_exists("idUser", $arrData)) {
					$this->db->where('id_user !=', $arrData["idUser"]);
				}			

				$this->db->where($arrData["column"], $arrData["value"]);
				$query = $this->db->get("usuarios");

				if ($query->num_rows() >= 1) {
					return true;
				} else{ return false; }
		}
		
		/**
		 * Add/Edit USER
		 * @since 8/11/2016
		 */
		public function saveUser() 
		{
				$idUser = $this->input->post('hddId');
				
				$data = array(
					'first_name' => $this->input->post('firstName'),
					'last_name' => $this->input->post('lastName'),
					'log_user' => $this->input->post('user'),
					'movil' => $this->input->post('movilNumber'),
					'email' => $this->input->post('email'),
					'fk_id_user_role' => $this->input->post('id_role'),
					'fk_id_dependencia_u' => $this->input->post('idDependencia')
				);

				//revisar si es para adicionar o editar
				if ($idUser == '') {
					$data['state'] = 1;
					$data['password'] = 'be52d7c1a5e18013492be5fd8ff5f898';//Jardin2021
					$query = $this->db->insert('usuarios', $data);
				} else {
					$data['state'] = $this->input->post('state');
					$this->db->where('id_user', $idUser);
					$query = $this->db->update('usuarios', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
	    /**
	     * Reset user´s password
	     * @author BMOTTAG
	     * @since  20/3/2021
	     */
	    public function resetEmployeePassword($arrData)
		{
				$passwd = md5($arrData['passwd']);
				$data = array(
					'password' => $passwd,
					'state' => 0
				);
				$this->db->where('id_user', $arrData['idUser']);
				$query = $this->db->update('usuarios', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
	    }

	    /**
	     * Update user´s password
	     * @author BMOTTAG
	     * @since  8/11/2016
	     */
	    public function updatePassword()
		{
				$idUser = $this->input->post("hddId");
				$newPassword = $this->input->post("inputPassword");
				$passwd = str_replace(array("<",">","[","]","*","^","-","'","="),"",$newPassword); 
				$passwd = md5($passwd);
				
				$data = array(
					'password' => $passwd
				);

				$this->db->where('id_user', $idUser);
				$query = $this->db->update('usuarios', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
	    }
		
		/**
		 * Add/Edit PROYECTO
		 * @since 15/04/2022
		 */
		public function saveProyecto() 
		{
				$idProyecto = $this->input->post('hddId');
				
				$data = array(
					'numero_proyecto_inversion' => $this->input->post('numero_proyecto_inversion'),
					'nombre_proyecto_inversion' => $this->input->post('proyecto')
				);
				
				//revisar si es para adicionar o editar
				if ($idProyecto == '') {
					$query = $this->db->insert('proyecto_inversion', $data);		
				} else {
					$this->db->where('id_proyecto_inversion', $idProyecto);
					$query = $this->db->update('proyecto_inversion', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit OBJETIVO ESTRATEGICO
		 * @since 15/04/2022
		 */
		public function saveEstrategia() 
		{
				$idObjetivo= $this->input->post('hddId');
				
				$data = array(
					'estrategia' => $this->input->post('estrategia'),
					'descripcion_estrategia' => $this->input->post('descripcion')
				);
				
				//revisar si es para adicionar o editar
				if ($idObjetivo == '') {
					$query = $this->db->insert('estrategias', $data);		
				} else {
					$this->db->where('id_estrategia', $idObjetivo);
					$query = $this->db->update('estrategias', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit PORPOSITOS
		 * @since 15/04/2022
		 */
		public function saveProposito() 
		{
				$idProposito = $this->input->post('hddId');
				
				$data = array(
					'numero_proposito' => $this->input->post('numero_proposito'),
					'proposito' => $this->input->post('proposito')
				);
				
				//revisar si es para adicionar o editar
				if ($idProposito == '') {
					$query = $this->db->insert('propositos', $data);		
				} else {
					$this->db->where('id_proposito', $idProposito);
					$query = $this->db->update('propositos', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit OBJETIVO ESTRATEGICO
		 * @since 15/04/2022
		 */
		public function saveLogro() 
		{
				$idLogro= $this->input->post('hddId');
				
				$data = array(
					'numero_logro' => $this->input->post('numero_logro'),
					'logro' => $this->input->post('logro')
				);
				
				//revisar si es para adicionar o editar
				if ($idLogro == '') {
					$query = $this->db->insert('logros', $data);		
				} else {
					$this->db->where('id_logros', $idLogro);
					$query = $this->db->update('logros', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit OBJETIVO ESTRATEGICO
		 * @since 15/04/2022
		 */
		public function saveProgramas() 
		{
				$idPrograma= $this->input->post('hddId');
				
				$data = array(
					'numero_programa_estrategico' => $this->input->post('numero_programa_estrategico'),
					'programa_estrategico' => $this->input->post('programa_estrategico')
				);
				
				//revisar si es para adicionar o editar
				if ($idPrograma == '') {
					$query = $this->db->insert('programa_estrategico', $data);		
				} else {
					$this->db->where('id_programa_estrategico', $idPrograma);
					$query = $this->db->update('programa_estrategico', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit OBJETIVO ESTRATEGICO
		 * @since 15/04/2022
		 */
		public function saveMetasPDD() 
		{
				$idObjetivo= $this->input->post('hddId');
				
				$data = array(
					'numero_meta_pdd' => $this->input->post('numero_meta_pdd'),
					'meta_pdd' => $this->input->post('meta_pdd')
				);
				
				//revisar si es para adicionar o editar
				if ($idObjetivo == '') {
					$query = $this->db->insert('meta_pdd ', $data);		
				} else {
					$this->db->where('id_meta_pdd', $idObjetivo);
					$query = $this->db->update('meta_pdd ', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit ODS
		 * @since 16/04/2022
		 */
		public function saveODS() 
		{
				$idODS= $this->input->post('hddId');
				
				$data = array(
					'numero_ods' => $this->input->post('numero_ods'),
					'ods' => $this->input->post('ods')
				);
				
				//revisar si es para adicionar o editar
				if ($idODS == '') {
					$query = $this->db->insert('ods ', $data);		
				} else {
					$this->db->where('id_ods', $idODS);
					$query = $this->db->update('ods ', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit ODS
		 * @since 16/04/2022
		 */
		public function saveMetasProyectos() 
		{
				$idMetaProyecto= $this->input->post('hddId');
				$vigencia= $this->input->post('vigencia');
				$numeroProyecto= $this->input->post('numeroProyecto');
				$numero_meta_proyecto= $this->input->post('numero_meta_proyecto');
				$nu_meta_proyecto = $vigencia . "-" . $numeroProyecto . "-" . $numero_meta_proyecto;
				
				$data = array(
					'numero_meta_proyecto' => $numero_meta_proyecto,
					'nu_meta_proyecto' => $nu_meta_proyecto,
					'fk_numero_proyecto' => $numeroProyecto,
					'meta_proyecto' => $this->input->post('meta_proyecto'),
					'presupuesto_meta' => $this->input->post('presupuesto_meta'),
					'vigencia_meta_proyecto' => $vigencia,
					'programado_meta_proyecto' => $this->input->post('programado_meta_proyecto'),
					'unidad_meta_proyecto' => $this->input->post('unidad_meta'),
					'fk_id_tipologia' => $this->input->post('id_tipologia')
				);
				
				//revisar si es para adicionar o editar
				if ($idMetaProyecto == '') {
					$query = $this->db->insert('meta_proyecto_inversion ', $data);		
				} else {
					$this->db->where('id_meta_proyecto_inversion', $idMetaProyecto);
					$query = $this->db->update('meta_proyecto_inversion', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit objetivos_estrategicos
		 * @since 26/04/2022
		 */
		public function saveObjetivo() 
		{
				$idObjetivoEstrategico = $this->input->post('hddId');
				
				$data = array(
					'fk_id_estrategia' => $this->input->post('idEstrategia'),
					'numero_objetivo_estrategico' => $this->input->post('numero_objetivo_estrategico'),
					'objetivo_estrategico' => $this->input->post('objetivo_estrategico')
				);
				
				//revisar si es para adicionar o editar
				if ($idObjetivoEstrategico == '') {
					$query = $this->db->insert('objetivos_estrategicos', $data);		
				} else {
					$this->db->where('id_objetivo_estrategico', $idObjetivoEstrategico);
					$query = $this->db->update('objetivos_estrategicos', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Guardar cuadro base
		 * @since 16/04/2022
		 */
		public function savePlanEstrategico() 
		{
				$numeroObjetivoEstrategico = $this->input->post('hddObjetivoEstrategico');
				$idCuadroBase = $this->input->post('hddIdCuadroBase');
		
				$vigencia = $this->general_model->get_vigencia();
				$data = array(
					'fk_numero_proyecto_inversion' => $this->input->post('id_proyecto_inversion'),
					'fk_nu_meta_proyecto_inversion' => $this->input->post('id_meta_proyecto_inversion'),
					'fk_numero_proposito' => $this->input->post('id_proposito'),
					'fk_numero_logro' => $this->input->post('id_logros'),
					'fk_numero_programa' => $this->input->post('id_programa_sp'),
					'fk_numero_programa_estrategico' => $this->input->post('id_programa_estrategico'),
					'fk_numero_meta_pdd' => $this->input->post('id_meta_pdd'),
					'indicador_1' => $this->input->post('id_indicador_1'),
					'indicador_2' => $this->input->post('id_indicador_2'),
					'fk_objetivo_general' => $this->input->post('id_objetivo_general'),
					'fk_objetivo_especifico' => $this->input->post('id_objetivo_especifico'),
					'fk_numero_ods' => $this->input->post('id_ods'),
					'fk_id_dimension' => $this->input->post('id_dimension'),
					'vigencia' => $vigencia['vigencia']
				);

				//revisar si es para adicionar o editar
				if ($idCuadroBase == 'x') {
					$data['fk_numero_objetivo_estrategico'] = $numeroObjetivoEstrategico;
					$query = $this->db->insert('cuadro_base', $data);
				} else {
					$this->db->where('id_cuadro_base', $idCuadroBase);
					$query = $this->db->update('cuadro_base', $data);
				}

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit AREA RESPINSABLE
		 * @since 1/06/2022
		 */
		public function saveAreaResponsable() 
		{
				$idAreaResponsable = $this->input->post('hddId');
				
				$data = array(
					'area_responsable' => $this->input->post('area_responsable')
				);
				
				//revisar si es para adicionar o editar
				if ($idAreaResponsable == '') {
					$query = $this->db->insert('param_area_responsable', $data);		
				} else {
					$this->db->where('id_area_responsable', $idAreaResponsable);
					$query = $this->db->update('param_area_responsable', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit META objetivos_estrategicos
		 * @since 20/06/2022
		 */
		public function saveMetaObjetivo() 
		{
				$idMetaObjetivoEstrategico = $this->input->post('hddId');
				
				$data = array(
					'fk_numero_objetivo_estrategico' => $this->input->post('numeroObjetivoEstrategico'),
					'meta' => $this->input->post('meta')
				);
				
				//revisar si es para adicionar o editar
				if ($idMetaObjetivoEstrategico == '') {
					$query = $this->db->insert('objetivos_estrategicos_metas', $data);		
				} else {
					$this->db->where('id_meta', $idMetaObjetivoEstrategico);
					$query = $this->db->update('objetivos_estrategicos_metas', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit INDICADORE objetivos_estrategicos
		 * @since 20/06/2022
		 */
		public function saveIndicadorObjetivo() 
		{
				$idIndicadorObjetivoEstrategico = $this->input->post('hddId');
				
				$data = array(
					'fk_numero_objetivo_estrategico' => $this->input->post('numeroObjetivoEstrategico'),
					'indicador' => $this->input->post('indicador')
				);
				
				//revisar si es para adicionar o editar
				if ($idIndicadorObjetivoEstrategico == '') {
					$query = $this->db->insert('objetivos_estrategicos_indicadores', $data);		
				} else {
					$this->db->where('id_indicador', $idIndicadorObjetivoEstrategico);
					$query = $this->db->update('objetivos_estrategicos_indicadores', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit RESULTADO objetivos_estrategicos
		 * @since 20/06/2022
		 */
		public function saveResultadoObjetivo() 
		{
				$idResultadoObjetivoEstrategico = $this->input->post('hddId');
				
				$data = array(
					'fk_numero_objetivo_estrategico' => $this->input->post('numeroObjetivoEstrategico'),
					'resultado' => $this->input->post('resultado')
				);
				
				//revisar si es para adicionar o editar
				if ($idResultadoObjetivoEstrategico == '') {
					$query = $this->db->insert('objetivos_estrategicos_resultados', $data);		
				} else {
					$this->db->where('id_resultado', $idResultadoObjetivoEstrategico);
					$query = $this->db->update('objetivos_estrategicos_resultados', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Eliminar registros de la tabla actividad_historial,actividad_estado, actividad_ejecucion, actividades
		 * @since  20/06/2022
		 */
		public function eliminarRegistrosActividades()
		{
				$sql = "TRUNCATE actividad_historial;";
				$query = $this->db->query($sql);
			
				$sql = "TRUNCATE actividad_estado";
				$query = $this->db->query($sql);

				$sql = "TRUNCATE actividad_ejecucion";
				$query = $this->db->query($sql);

				$sql = "DELETE FROM actividades";
				$query = $this->db->query($sql);
				
				$sql = "ALTER TABLE actividad_ejecucion AUTO_INCREMENT=1";
				$sql = "ALTER TABLE actividad_estado AUTO_INCREMENT=1";
				$sql = "ALTER TABLE actividad_historial AUTO_INCREMENT=1";
				$sql = "ALTER TABLE actividades AUTO_INCREMENT=1";
				$query = $this->db->query($sql);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Eliminar registros de la tabla objetivos_estrategicos_metas
		 * @since  20/06/2022
		 */
		public function eliminarMetasObjetivos()
		{
				$sql = "TRUNCATE objetivos_estrategicos_metas";
				$query = $this->db->query($sql);
				
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Eliminar registros de la tabla eliminarIndicadoresObjetivos
		 * @since  20/06/2022
		 */
		public function eliminarIndicadoresObjetivos()
		{
				$sql = "TRUNCATE objetivos_estrategicos_indicadores";
				$query = $this->db->query($sql);
				
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Eliminar registros de la tabla objetivos_estrategicos_resultados
		 * @since  20/06/2022
		 */
		public function eliminarResultadosObjetivos()
		{
				$sql = "TRUNCATE objetivos_estrategicos_resultados";
				$query = $this->db->query($sql);
				
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 14/8/2017
		 */
		public function cargar_actividades($lista) 
		{
				$query = $this->db->insert('actividades', $lista);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar el mensaje de POA a la tabla de actividad estado
		 * @since 30/6/2022
		 */
		public function cargar_mensaje_poa($lista) 
		{
				$data = array(
					'mensaje_poa_trimestre_1' => $lista["mensaje_poa"]
				);
				$this->db->where('fk_numero_actividad', $lista["numero_actividad"]);
				$query = $this->db->update('actividad_estado', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar Plan Institucional
		 * @since 11/7/2022
		 */
		public function cargar_plan_institucional($lista) 
		{
				$data = array(
					'plan_archivos' => $lista["plan_archivos"],
					'plan_adquisiciones' => $lista["plan_adquisiciones"],
					'plan_vacantes' => $lista["plan_vacantes"],
					'plan_recursos' => $lista["plan_recursos"],
					'plan_talento' => $lista["plan_talento"],
					'plan_capacitacion' => $lista["plan_capacitacion"],
					'plan_incentivos' => $lista["plan_incentivos"],
					'plan_trabajo' => $lista["plan_trabajo"],
					'plan_anticorrupcion' => $lista["plan_anticorrupcion"],
					'plan_tecnologia' => $lista["plan_tecnologia"],
					'plan_riesgos' => $lista["plan_riesgos"],
					'plan_informacion' => $lista["plan_informacion"]
				);
				$this->db->where('numero_actividad', $lista["numero_actividad"]);
				$query = $this->db->update('actividades', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 14/8/2017
		 */
		public function cargar_actividades_estados($lista) 
		{
				$data = array(
					'fk_numero_actividad' => $lista["numero_actividad"],
					'estado_trimestre_1' => 0,
					'estado_trimestre_2' => 0,
					'estado_trimestre_3' => 0,
					'estado_trimestre_4' => 0
				);

				$query = $this->db->insert('actividad_estado', $data);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 14/8/2017
		 */
		public function cargar_actividades_ejecucion($lista) 
		{
				$query = $this->db->insert('actividad_ejecucion', $lista);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 14/8/2017
		 */
		public function cargar_metas_objetivos_estrategicos($lista) 
		{
				$query = $this->db->insert('objetivos_estrategicos_metas', $lista);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 14/8/2017
		 */
		public function cargar_indicadores_objetivos_estrategicos($lista) 
		{
				$query = $this->db->insert('objetivos_estrategicos_indicadores', $lista);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 14/8/2017
		 */
		public function cargar_resultados_objetivos_estrategicos($lista) 
		{
				$query = $this->db->insert('objetivos_estrategicos_resultados', $lista);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 25/07/2023
		 */
		public function cargar_indicadores_gestion($lista) 
		{
				$query = $this->db->insert('indicadores_gestion', $lista);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 20/08/2023
		 */
		public function cargar_meta_proyectos_inversion($lista) 
		{
				$query = $this->db->insert('meta_proyecto_inversion', $lista);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Cargar informacion 
		 * @since 21/08/2023
		 */
		public function cargar_indicadores_segplan($lista) 
		{
				$query = $this->db->insert('indicadores_x_vigencia', $lista);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * IMPORTAR ACTIVIDAD
		 * @since 24/06/2022
		 */
		public function saveImportarActividad() 
		{
				$idActividad = $this->input->post('id_actividad');
				$data = array(
					'fk_id_cuadro_base' => $this->input->post('hddIdCuadroBase')
				);
				$this->db->where('id_actividad', $idActividad);
				$query = $this->db->update('actividades', $data);

				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit Proposito
		 * @since 24/07/2022
		 */
		public function savePropositosXVigencia() 
		{
				$idPropositoVigencia= $this->input->post('hddId');
				$vigencia= $this->input->post('vigencia');
				$numeroProposito= $this->input->post('proposito');
				$nu_proposito_vigencia = $vigencia . "-" . $numeroProposito;
				
				$data = array(
					'nu_proposito_vigencia' => $nu_proposito_vigencia,
					'fk_numero_proposito' => $numeroProposito,
					'vigencia_proposito' => $vigencia,
					'recurso_programado_proposito' => $this->input->post('recurso_programado_proposito')
				);
				
				//revisar si es para adicionar o editar
				if ($idPropositoVigencia == '') {
					$query = $this->db->insert('proposito_x_vigencia ', $data);		
				} else {
					$this->db->where('id_proposito_vigencia', $idPropositoVigencia);
					$query = $this->db->update('proposito_x_vigencia', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit Proyecto Vigencia
		 * @since 24/07/2022
		 */
		public function saveProyectosXVigencia() 
		{
				$idProyectoVigencia= $this->input->post('hddId');
				$vigencia= $this->input->post('vigencia');
				$numeroProyecto= $this->input->post('proyecto');
				$nu_proyecto_vigencia = $vigencia . "-" . $numeroProyecto;
				
				$data = array(
					'nu_proyecto_vigencia' => $nu_proyecto_vigencia,
					'fk_numero_proyecto_inversion' => $numeroProyecto,
					'vigencia_proyecto' => $vigencia,
					'recurso_programado_proyecto' => $this->input->post('recurso_programado_proyecto')
				);
				
				//revisar si es para adicionar o editar
				if ($idProyectoVigencia == '') {
					$query = $this->db->insert('proyecto_inversion_x_vigencia', $data);		
				} else {
					$this->db->where('id_proyecto_vigencia', $idProyectoVigencia);
					$query = $this->db->update('proyecto_inversion_x_vigencia', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit Meta PDD Vigencia
		 * @since 24/07/2022
		 */
		public function saveMetasPDDXVigencia() 
		{
				$idMetaPDDVigencia= $this->input->post('hddId');
				$vigencia= $this->input->post('vigencia');
				$numeroMetaPDD= $this->input->post('metaPDD');
				$nu_meta_pdd_vigencia = $vigencia . "-" . $numeroMetaPDD;
				
				$data = array(
					'nu_meta_pdd_vigencia ' => $nu_meta_pdd_vigencia,
					'fk_numero_meta_pdd' => $numeroMetaPDD,
					'vigencia_meta_pdd' => $vigencia,
					'recurso_programado_meta_pdd' => $this->input->post('recurso_programado_meta_pdd')
				);
				
				//revisar si es para adicionar o editar
				if ($idMetaPDDVigencia == '') {
					$query = $this->db->insert('meta_pdd_x_vigencia', $data);		
				} else {
					$this->db->where('id_meta_pdd_vigencia', $idMetaPDDVigencia);
					$query = $this->db->update('meta_pdd_x_vigencia', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit PROGRAMA SEGPLAN
		 * @since 24/74/2022
		 */
		public function saveProgramaSP() 
		{
				$idPrograma = $this->input->post('hddId');
				
				$data = array(
					'numero_programa' => $this->input->post('numero_programa'),
					'programa' => $this->input->post('programa')
				);
				
				//revisar si es para adicionar o editar
				if ($idPrograma == '') {
					$query = $this->db->insert('programa', $data);		
				} else {
					$this->db->where('id_programa', $idPrograma);
					$query = $this->db->update('programa', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit Meta PDD Vigencia
		 * @since 24/07/2022
		 */
		public function saveProgramasSPXVigencia() 
		{
				$idProgramaSPVigencia= $this->input->post('hddId');
				$vigencia= $this->input->post('vigencia');
				$numeroProgramaSP= $this->input->post('programa');
				$nu_programa_sp_vigencia = $vigencia . "-" . $numeroProgramaSP;
				
				$data = array(
					'nu_programa_vigencia' => $nu_programa_sp_vigencia,
					'fk_numero_programa' => $numeroProgramaSP,
					'vigencia_programa' => $vigencia,
					'recurso_programado_programa' => $this->input->post('recurso_programado_programa')
				);
				
				//revisar si es para adicionar o editar
				if ($idProgramaSPVigencia == '') {
					$query = $this->db->insert('programa_x_vigencia', $data);		
				} else {
					$this->db->where('id_programa_vigencia', $idProgramaSPVigencia);
					$query = $this->db->update('programa_x_vigencia', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit INDICADOR SEGPLAN
		 * @since 26/74/2022
		 */
		public function saveIndicadorSP() 
		{
				$idIndicador = $this->input->post('hddId');
		
				$data = array(
					'numero_indicador' => $this->input->post('numero_indicador'),
					'indicador_sp' => $this->input->post('indicador_sp')
				);
				
				//revisar si es para adicionar o editar
				if ($idIndicador == '') {
					$query = $this->db->insert('indicadores', $data);		
				} else {
					$this->db->where('id_indicador_sp', $idIndicador);
					$query = $this->db->update('indicadores', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit INDICADOR Vigencia
		 * @since 26/07/2022
		 */
		public function saveIndicadorSPXVigencia() 
		{
				$idIndicadorSPVigencia= $this->input->post('hddId');
				$vigencia= $this->input->post('vigencia');
				$numeroIndicadorSP= $this->input->post('indicador');
				$nu_indicador_sp_vigencia = $vigencia . "-" . $numeroIndicadorSP;
				
				$data = array(
					'nu_indicador_vigencia' => $nu_indicador_sp_vigencia,
					'fk_numero_indicador' => $numeroIndicadorSP,
					'vigencia_indicador' => $vigencia,
					'programado_indicador_pdd' => $this->input->post('programado_indicador_pdd'),
					'programado_indicador_real' => $this->input->post('programado_indicador_real')
				);
				
				//revisar si es para adicionar o editar
				if ($idIndicadorSPVigencia == '') {
					$query = $this->db->insert('indicadores_x_vigencia', $data);		
				} else {
					$this->db->where('id_indicador_vigencia', $idIndicadorSPVigencia);
					$query = $this->db->update('indicadores_x_vigencia', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Add/Edit INDICADOR PMR
		 * @since 28/74/2022
		 */
		public function saveIndicadorPMR() 
		{
				$idIndicador = $this->input->post('hddId');
		
				$data = array(
					'numero_indicador_pmr' => $this->input->post('numero_indicador'),
					'indicador_pmr' => $this->input->post('indicador_pmr')
				);
				
				//revisar si es para adicionar o editar
				if ($idIndicador == '') {
					$query = $this->db->insert('indicadores_pmr', $data);		
				} else {
					$this->db->where('id_indicador_pmr', $idIndicador);
					$query = $this->db->update('indicadores_pmr', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
		
		/**
		 * Consulta lista tablero PMR
		 * @since 31/10/2022
		 * @author AOCUBILLOSA
		 */
		public function get_tablero_pmr($arrData) {
				$this->db->select();
				$this->db->join('param_objetivos_pmr O', 'O.numero_objetivo_pmr = T.fk_numero_objetivo_pmr', 'INNER');
				$this->db->join('param_elementos_pep_pmr E', 'E.id_elemento_pep_pmr = T.fk_id_elemento_pep_pmr', 'INNER');
				$this->db->join('param_productos_pmr P', 'P.numero_producto_pmr = T.fk_numero_producto_pmr', 'INNER');
				$this->db->join('proyecto_inversion PI', 'PI.numero_proyecto_inversion = T.fk_numero_proyecto_inversion', 'INNER');
				$this->db->join('indicadores_pmr I', 'I.numero_indicador_pmr = T.fk_numero_indicador_pmr', 'INNER');
				$this->db->join('param_unidad_medida_pmr U', 'U.id_unidad_medida_pmr = T.fk_id_unidad_medida_pmr', 'INNER');
				$this->db->join('param_naturaleza_pmr N', 'N.id_naturaleza_pmr = T.fk_id_naturaleza_pmr', 'INNER');
				$this->db->join('param_periodicidad_pmr PP', 'PP.id_periodicidad_pmr = T.fk_id_periodicidad_pmr', 'INNER');
				if (array_key_exists("id_pmr", $arrData)) {
					$this->db->where('T.id_pmr', $arrData['id_pmr']);
				}
				if (array_key_exists("vigencia", $arrData)) {
					$this->db->where('T.vigencia', $arrData['vigencia']);
				}
				$this->db->order_by('fk_numero_indicador_pmr', 'asc');
				$query = $this->db->get('tablero_pmr T');
				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Consulta lista de actividades por indicador PMR
		 * @since 31/10/2022
		 * @author AOCUBILLOSA
		 */
		public function get_actividades($arrParams) {
				$this->db->select('numero_actividad');
				$this->db->where('fk_numero_indicador_pmr', $arrParams['fk_numero_indicador_pmr']);
				$this->db->where('vigencia', $arrParams['vigencia']);
				$this->db->order_by('numero_actividad', 'asc');
				$query = $this->db->get('actividades');
				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Sumar metas de las actividades por indicador PMR
		 * @author AOCUBILLOSA
		 * @since  31/10/2022
		 */
		public function sumatoria_metas($indicador_pmr)
		{
				$this->db->select_sum('meta_plan_operativo_anual');
				$this->db->where('fk_numero_indicador_pmr', $indicador_pmr);
				$this->db->order_by('fk_numero_indicador_pmr', 'asc');
				$query = $this->db->get('actividades');
				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Sumar avance de las actividades por indicador PMR y mes
		 * @author AOCUBILLOSA
		 * @since  31/10/2022
		 */
		public function sumatoria_mes($indicador_pmr, $mes)
		{
				$this->db->select_sum('ejecutado');
				$this->db->join('actividad_ejecucion E', 'A.numero_actividad = E.fk_numero_actividad', 'INNER');
				$this->db->where('fk_numero_indicador_pmr', $indicador_pmr);
				$this->db->where('fk_id_mes', $mes);
				$this->db->order_by('fk_numero_indicador_pmr', 'asc');
				$query = $this->db->get('actividades A');
				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Guardar tablero PMR
		 * @author AOCUBILLOSA
		 * @since 31/10/2022
		 */
		public function saveTableroPMR() 
		{
				$idPMR = $this->input->post('hddIdPMR');
				$vigencia = $this->general_model->get_vigencia();
				$data = array(
					'fk_numero_objetivo_pmr' => $this->input->post('id_objetivo_pmr'),
					'fk_id_elemento_pep_pmr' => $this->input->post('id_elemento_pep_pmr'),
					'fk_numero_producto_pmr' => $this->input->post('id_producto_pmr'),
					'fk_numero_proyecto_inversion' => $this->input->post('id_proyecto_inversion'),
					'fk_numero_indicador_pmr' => $this->input->post('id_indicador_pmr'),
					'fk_id_unidad_medida_pmr' => $this->input->post('id_unidad_medida_pmr'),
					'fk_id_naturaleza_pmr' => $this->input->post('id_naturaleza_pmr'),
					'fk_id_periodicidad_pmr' => $this->input->post('id_periodicidad_pmr'),
					'vigencia' => $vigencia['vigencia']
				);
				//revisar si es para adicionar o editar
				if ($idPMR == 'x') {
					$query = $this->db->insert('tablero_pmr', $data);
				} else {
					$this->db->where('id_pmr', $idPMR);
					$query = $this->db->update('tablero_pmr', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}
	    
	    /**
		 * Add/Edit ACTIVIDAD PI
		 * @since 08/05/2023
		 * @author AOCUBILLOSA
		 */
		public function guardarActividadPI() 
		{
				$idActividad = $this->input->post('hddId');
				$idPlanIntegrado = $this->input->post('hddIdPlanIntegrado');
				$idUser = $this->session->userdata("id");
				$vigencia = $this->general_model->get_vigencia();
		
				$data = array(
					'numero_actividad_pi' => $this->input->post('numero_actividad'),
					'descripcion_actividad_pi' => $this->input->post('descripcion'),
					'meta_plan_operativo_anual_pi' => $this->input->post('meta_plan'),
					'unidad_medida_pi' => $this->input->post('unidad_medida'),
					'ponderacion_pi' => $this->input->post('ponderacion'),
					'fecha_inicial_pi' => $this->input->post('fecha_inicial'),
					'fecha_final_pi' => $this->input->post('fecha_final'),
					'fk_id_area_responsable' => $this->input->post('id_responsable'),
					'fk_id_dependencia' => $this->input->post('id_dependencia'),
					'vigencia' => $vigencia['vigencia']
				);	

				//revisar si es para adicionar o editar
				if ($idActividad == '') 
				{
					$data['fk_id_plan_integrado'] = $idPlanIntegrado;
					$query = $this->db->insert('actividades_pi', $data);
					$idActividad = $this->db->insert_id();	
				} else {
					$this->db->where('id_actividad_pi', $idActividad);
					$query = $this->db->update('actividades_pi', $data);
				}
				if ($query) {
					return $idActividad;
				} else {
					return false;
				}
		}

		/**
		 * Adicionar registros de programacion por mes
	     * @since 08/05/2023
	     * @author AOCUBILLOSA
		 */
		public function save_programa_actividadPI($numeroActividad) 
		{
			//add the new record
			$query = 1;
			$mesInicial = $this->input->post('fecha_inicial');
			$mesFinal = $this->input->post('fecha_final');
			$idUser = $this->session->userdata("id");

			for ($i = $mesInicial; $i <= $mesFinal; $i++) {
					$data = array(
						'fk_id_mes' => $i,
						'fk_numero_actividad_pi' => $numeroActividad,
						'fk_id_user' => $idUser,
						'fecha_creacion' => date("Y-m-d G:i:s")
					);	
					$query = $this->db->insert('actividad_ejecucion_pi', $data);
			}

			if($query) {
				return true;
			} else{
				return false;
			}
		}

		/**
		 * Add/Edit PLAN INSTITUCIONAL
		 * @since 29/03/2023
		 * @author AOCUBILLOSA
		 */
		public function savePlanInstitucional() 
		{
				$idPlanInstitucional = $this->input->post('hddId');
				$data = array(
					'plan_institucional' => $this->input->post('plan_institucional')
				);
				//revisar si es para adicionar o editar
				if ($idPlanInstitucional == '') {
					$query = $this->db->insert('planes_institucionales', $data);		
				} else {
					$this->db->where('id_plan_institucional', $idPlanInstitucional);
					$query = $this->db->update('planes_institucionales', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Consulta lista de actividades por indicador PMR
		 * @since 31/10/2022
		 * @author AOCUBILLOSA
		 */
		public function get_plan_integrado($arrParams) {
				$this->db->select();
				$this->db->join('planes_institucionales I', 'I.id_plan_institucional = P.fk_id_plan_institucional', 'LEFT');
				$this->db->join('actividades_pi A', 'P.id_plan_integrado = A.fk_id_plan_integrado', 'LEFT');
				$this->db->join('actividad_estado_pi E', 'A.numero_actividad_pi = E.fk_numero_actividad_pi', 'LEFT');
				$this->db->join('param_dependencias D', 'A.fk_id_dependencia = D.id_dependencia', 'LEFT');
				$this->db->where('P.vigencia', $arrParams['vigencia']);
				if (array_key_exists("idPlanIntegrado", $arrParams)) {
					$this->db->where('P.id_plan_integrado', $arrParams['idPlanIntegrado']);
				}
				if (array_key_exists("vigencia", $arrParams)) {
					$this->db->where('P.vigencia', $arrParams['vigencia']);
				}
				if (array_key_exists("dependencia", $arrParams)) {
					$this->db->where('A.fk_id_dependencia', $arrParams['dependencia']);
				}
				$this->db->order_by('P.fk_id_plan_institucional', 'asc');
				$query = $this->db->get('planes_integrados P');
				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit PLAN INTEGRADO
		 * @since 30/03/2023
		 * @author AOCUBILLOSA
		 */
		public function savePlanIntegrado() 
		{
				$idPlanIntegrado = $this->input->post('hddId');
				$data = array(
					'fk_id_plan_institucional' => $this->input->post('plan_institucional'),
					'vigencia' => $this->input->post('hddVigencia')
				);
				//revisar si es para adicionar o editar
				if ($idPlanIntegrado == '') {
					$query = $this->db->insert('planes_integrados', $data);		
				} else {
					$this->db->where('id_plan_integrado', $idPlanIntegrado);
					$query = $this->db->update('planes_integrados', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Guardar Ejecucion Actividades PI
		 * @since 08/05/2023
		 */
		public function guardarProgramadoPI()
		{
				$numeroActividad = $this->input->post('hddNumeroActividad');
				$idEjecucion = $this->input->post('hddId');
				$idUser = $this->session->userdata("id");
		
				$data = array(
					'programado' => $this->input->post('programado'),
					'fecha_creacion' => date("Y-m-d G:i:s")
				);	

				//revisar si es para adicionar o editar
				if ($idEjecucion == '') 
				{
					$data['fk_numero_actividad_pi'] = $numeroActividad;
					$data['fk_id_user'] = $idUser;
					$data['fk_id_mes'] = $this->input->post('mes');
					$query = $this->db->insert('actividad_ejecucion_pi', $data);
				} else {
					$this->db->where('id_ejecucion_actividad_pi', $idEjecucion);
					$query = $this->db->update(' actividad_ejecucion_pi', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Guardar Programacion Actividades
		 * @since 23/04/2022
		 */
		public function guardarProgramacionPI() 
		{
				//update states
				$query = 1;
				
				$datos = $this->input->post('form');
				if($datos) {
					$tot = count($datos['id']);
					for ($i = 0; $i < $tot; $i++) 
					{					
						$data = array(
							'programado' => $datos['programado'][$i],
							'ejecutado' => $datos['ejecutado'][$i],
							'descripcion_actividades' => $datos['descripcion'][$i],
							'evidencias' => $datos['evidencia'][$i],
							'fecha_actualizacion' => date("Y-m-d G:i:s")
						);
						$this->db->where('id_ejecucion_actividad_pi', $datos['id'][$i]);
						$query = $this->db->update('actividad_ejecucion_pi', $data);
					}
				}
				
				if ($query){
					$numeroActividadPI = $this->input->post('hddNumeroActividadPI');
					$arrParam = array("numeroActividadPI" => $numeroActividadPI);
					$ponderacion = $this->general_model->get_actividadesPI($arrParam);
					$estadoActividad = $this->general_model->get_estados_actividadesPI($arrParam);
					$sumaProgramado = $this->general_model->sumarProgramadoPI($arrParam);

					$arrParam['numeroTrimestre'] = 1;
					$sumaProgramadoTrimestre1 = $this->general_model->sumarProgramadoPI($arrParam);
					$sumaEjecutadoTrimestre1 = $this->general_model->sumarEjecutadoPI($arrParam);
					$arrParam['numeroTrimestre'] = 2;
					$sumaProgramadoTrimestre2 = $this->general_model->sumarProgramadoPI($arrParam);
					$sumaEjecutadoTrimestre2 = $this->general_model->sumarEjecutadoPI($arrParam);
					$arrParam['numeroTrimestre'] = 3;
					$sumaProgramadoTrimestre3 = $this->general_model->sumarProgramadoPI($arrParam);
					$sumaEjecutadoTrimestre3 = $this->general_model->sumarEjecutadoPI($arrParam);
					$arrParam['numeroTrimestre'] = 4;
					$sumaProgramadoTrimestre4 = $this->general_model->sumarProgramadoPI($arrParam);
					$sumaEjecutadoTrimestre4 = $this->general_model->sumarEjecutadoPI($arrParam);

					$sumaEjecutado['ejecutado'] = 0;
					if ($estadoActividad[0]['estado_trimestre_1'] == 5){
						$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre1['ejecutado'];
					}
					if ($estadoActividad[0]['estado_trimestre_2'] == 5){
						$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre2['ejecutado'];
					}
					if ($estadoActividad[0]['estado_trimestre_3'] == 5){
						$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre3['ejecutado'];
					}
					if ($estadoActividad[0]['estado_trimestre_4'] == 5){
						$sumaEjecutado['ejecutado'] += $sumaEjecutadoTrimestre4['ejecutado'];
					}

					$valorProgramadoTotal = round($sumaProgramado['programado'],2);
					$valorProgramadoTrimestre1 = round($sumaProgramadoTrimestre1['programado'],2);
					$valorProgramadoTrimestre2 = round($sumaProgramadoTrimestre2['programado'],2);
					$valorProgramadoTrimestre3 = round($sumaProgramadoTrimestre3['programado'],2);
					$valorProgramadoTrimestre4 = round($sumaProgramadoTrimestre4['programado'],2);
					
					$cumplimiento1 = 0;
					$cumplimiento2 = 0;
					$cumplimiento3 = 0;
					$cumplimiento4 = 0;

					$avancePOA = 0;
					if($sumaProgramado['programado'] > 0){
						$avancePOA = round(($sumaEjecutado['ejecutado']/$sumaProgramado['programado']) * $ponderacion[0]['ponderacion_pi'],2);
					}
					if ($estadoActividad[0]['estado_trimestre_1'] == 5){
						if($sumaProgramadoTrimestre1['programado'] > 0) {
							$cumplimiento1 = round($sumaEjecutadoTrimestre1['ejecutado'] / $sumaProgramadoTrimestre1['programado'] * 100,2);
						} else {
							if($sumaEjecutadoTrimestre1['ejecutado'] > 0) {
								$cumplimiento1 = 100;
							} else {
								$cumplimiento1 = 0;
							}
						}
					} else {
						$cumplimiento1 = 0;
					}
					if ($estadoActividad[0]['estado_trimestre_2'] == 5){
						if($sumaProgramadoTrimestre2['programado'] > 0) {
							$cumplimiento2 = round($sumaEjecutadoTrimestre2['ejecutado'] / $sumaProgramadoTrimestre2['programado'] * 100,2);
						} else {
							if($sumaEjecutadoTrimestre2['ejecutado'] > 0) {
								$cumplimiento2 = 100;
							} else {
								$cumplimiento2 = 0;
							}
						}
					} else {
						$cumplimiento2 = 0;
					}
					if ($estadoActividad[0]['estado_trimestre_3'] == 5){
						if($sumaProgramadoTrimestre3['programado'] > 0) {
							$cumplimiento3 = round($sumaEjecutadoTrimestre3['ejecutado'] / $sumaProgramadoTrimestre3['programado'] * 100,2);
						} else {
							if($sumaEjecutadoTrimestre3['ejecutado'] > 0) {
								$cumplimiento3 = 100;
							} else {
								$cumplimiento3 = 0;
							}
						}
					} else {
						$cumplimiento3 = 0;
					}
					if ($estadoActividad[0]['estado_trimestre_4'] == 5){
						if($sumaProgramadoTrimestre4['programado'] > 0) {
							$cumplimiento4 = round($sumaEjecutadoTrimestre4['ejecutado'] / $sumaProgramadoTrimestre4['programado'] * 100,2);
						} else {
							if($sumaEjecutadoTrimestre4['ejecutado'] > 0) {
								$cumplimiento4 = 100;
							} else {
								$cumplimiento4 = 0;
							}
						}
					} else {
						$cumplimiento4 = 0;
					}

					$cumplimiento = ($valorProgramadoTrimestre1*($cumplimiento1/100)) + ($valorProgramadoTrimestre2*($cumplimiento2/100)) + ($valorProgramadoTrimestre3*($cumplimiento3/100)) + ($valorProgramadoTrimestre4*($cumplimiento4/100));

					$data = array(
						'avance_poa' => $avancePOA,
						'cumplimiento' => $cumplimiento
					);
					$this->db->where('fk_numero_actividad_pi', $numeroActividadPI);
					$query = $this->db->update('actividad_estado_pi', $data);
					if ($query){
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}
		}

		/**
		 * Guardar estado del Trimestre
		 * @since 17/04/2022
		 */
		public function guardarTrimestrePI($banderaActividad, $estadoActividad, $numeroActividad, $cumplimientoTrimestre, $avancePOA, $numeroTrimestre) 
		{	
				$data = array(
					'estado_trimestre_' . $numeroTrimestre => $estadoActividad,
				);	

				//revisar si es para adicionar o editar
				if ($banderaActividad) 
				{
					$this->db->where('fk_numero_actividad_pi', $numeroActividad);
					$query = $this->db->update('actividad_estado_pi', $data);
				} else {
					$data['fk_numero_actividad_pi'] = $numeroActividad;
					$query = $this->db->insert('actividad_estado_pi', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Guardar Ejecucion Actividades
		 * @since 18/06/2022
		 */
		public function guardarEjecucionPI() 
		{
				//update states
				$query = 1;
				$idUser = $this->session->userdata("id");
				
				$datos = $this->input->post('form');
				if($datos) {
					$tot = count($datos['id']);
					for ($i = 0; $i < $tot; $i++) 
					{					
						$data = array(
							'fk_id_responsable' => $idUser,
							'ejecutado' => $datos['ejecutado'][$i],
							'descripcion_actividades' => $datos['descripcion'][$i],
							'evidencias' => $datos['evidencia'][$i],
							'fecha_actualizacion' => date("Y-m-d G:i:s")
						);
						$this->db->where('id_ejecucion_actividad_pi', $datos['id'][$i]);
						$query = $this->db->update('actividad_ejecucion_pi', $data);
					}
				}
				
				if ($query){
					return true;
				} else{
					return false;
				}
		}

		public function eliminar_ejecucionPI($numeroActividad) 
		{
			$this->db->where('fk_numero_actividad_pi', $numeroActividad);
	        $query = $this->db->delete('actividad_ejecucion_pi');
	        if ($query){
				return true;
			} else{
				return false;
			}
	    }

	    public function eliminar_estadoPI($numeroActividad) 
		{
			$this->db->where('fk_numero_actividad_pi', $numeroActividad);
	        $query = $this->db->delete('actividad_estado_pi');
	        if ($query){
				return true;
			} else{
				return false;
			}
	    }

	    public function eliminar_historialPI($numeroActividad) 
		{
			$this->db->where('fk_numero_actividad_pi', $numeroActividad);
	        $query = $this->db->delete('actividad_historial_pi');
	        if ($query){
				return true;
			} else{
				return false;
			}
	    }

	    public function eliminar_auditoriaPI($numeroActividad) 
		{
			$this->db->where('fk_numero_actividad_pi', $numeroActividad);
	        $query = $this->db->delete('auditoria_actividad_ejecucion_pi');
	        if ($query){
				return true;
			} else{
				return false;
			}
	    }

	    public function eliminar_actividadPI($numeroActividad) 
		{
			$this->db->where('numero_actividad_pi', $numeroActividad);
	        $query = $this->db->delete('actividades_pi');
	        if ($query){
				return true;
			} else{
				return false;
			}
	    }

	    /**
		 * Consulta lista indicadores de gestion
		 * @since 28/07/2023
		 * @author AOCUBILLOSA
		 */
		public function get_indicadores_gestion($arrData) {
				$this->db->select();
				if (array_key_exists("vigencia", $arrData)) {
					$this->db->where('I.vigencia', $arrData['vigencia']);
				}
				$this->db->order_by('id_indicador_g', 'asc');
				$query = $this->db->get('indicadores_gestion I');
				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Eliminar registros de la tabla indicadores_gestion
		 * @since  28/07/2023
		 */
		public function eliminarIndicadoresGestion($vigencia)
		{
				$sql = "DELETE FROM indicadores_gestion WHERE vigencia = $vigencia";
				$query = $this->db->query($sql);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Eliminar registros de la tabla meta_proyectos
		 * @since  20/08/2023
		 */
		public function eliminarMetaProyectosInversion($vigencia)
		{
				$sql = "DELETE FROM meta_proyecto_inversion WHERE vigencia_meta_proyecto = $vigencia";
				$query = $this->db->query($sql);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Eliminar registros de la tabla indicadores_x_vigencia
		 * @since  21/08/2023
		 */
		public function eliminarIndicadoresSegplan($vigencia)
		{
				$sql = "DELETE FROM indicadores_x_vigencia WHERE vigencia_indicador = $vigencia";
				$query = $this->db->query($sql);
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Consulta lista mestas sectoriales
		 * @since 28/08/2023
		 * @author AOCUBILLOSA
		 */
		public function get_metas_sectoriales($arrData) {
				$this->db->select();
				$this->db->join('param_objetivos_pmr O', 'O.numero_objetivo_pmr = T.fk_numero_objetivo_pmr', 'INNER');
				$this->db->join('param_elementos_pep_pmr E', 'E.id_elemento_pep_pmr = T.fk_id_elemento_pep_pmr', 'INNER');
				$this->db->join('param_productos_pmr P', 'P.numero_producto_pmr = T.fk_numero_producto_pmr', 'INNER');
				$this->db->join('proyecto_inversion PI', 'PI.numero_proyecto_inversion = T.fk_numero_proyecto_inversion', 'INNER');
				$this->db->join('indicadores_pmr I', 'I.numero_indicador_pmr = T.fk_numero_indicador_pmr', 'INNER');
				$this->db->join('param_unidad_medida_pmr U', 'U.id_unidad_medida_pmr = T.fk_id_unidad_medida_pmr', 'INNER');
				$this->db->join('param_naturaleza_pmr N', 'N.id_naturaleza_pmr = T.fk_id_naturaleza_pmr', 'INNER');
				$this->db->join('param_periodicidad_pmr PP', 'PP.id_periodicidad_pmr = T.fk_id_periodicidad_pmr', 'INNER');
				if (array_key_exists("id_pmr", $arrData)) {
					$this->db->where('T.id_pmr', $arrData['id_pmr']);
				}
				if (array_key_exists("vigencia", $arrData)) {
					$this->db->where('T.vigencia', $arrData['vigencia']);
				}
				$this->db->order_by('fk_numero_indicador_pmr', 'asc');
				$query = $this->db->get('tablero_pmr T');
				if ($query->num_rows() > 0) {
					return $query->result_array();
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit OBJETIVO GENERAL
		 * @since 25/09/2023
		 */
		public function saveObjetivoGeneral() 
		{
				$idGeneral = $this->input->post('hddId');
				$data = array(
					'numero_objetivo_general' => $this->input->post('numero_objetivo_general'),
					'objetivo_general' => $this->input->post('objetivo_general')
				);
				//revisar si es para adicionar o editar
				if ($idGeneral == '') {
					$query = $this->db->insert('objetivos_generales', $data);		
				} else {
					$this->db->where('id_objetivo_general', $idGeneral);
					$query = $this->db->update('objetivos_generales', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		/**
		 * Add/Edit OBJETIVO ESPECIFICO
		 * @since 25/09/2023
		 */
		public function saveObjetivoEspecifico()
		{
				$idEspecifico = $this->input->post('hddId');
				$data = array(
					'numero_objetivo_especifico' => $this->input->post('numero_objetivo_especifico'),
					'objetivo_especifico' => $this->input->post('objetivo_especifico')
				);
				//revisar si es para adicionar o editar
				if ($idEspecifico == '') {
					$query = $this->db->insert('objetivos_especificos', $data);		
				} else {
					$this->db->where('id_objetivo_especifico', $idEspecifico);
					$query = $this->db->update('objetivos_especificos', $data);
				}
				if ($query) {
					return true;
				} else {
					return false;
				}
		}

		
		/**
		 * Obtener fecha limite
		 * @since 05/10/2023
		 */
		public function get_fecha_limite($arrData) 
		{
				$this->db->where('numero_trimestre', $arrData["numeroTrimestre"]);
				$this->db->where('vigencia', $arrData["vigencia"]);
				$query = $this->db->get("param_fechas_limites");
				if ($query->num_rows() > 0) {
					return $query->row_array();
				} else { 
					return false;
				}
		}
	}
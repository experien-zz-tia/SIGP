<?php
include_once 'Utils/Util.php';
class Noticia extends ActiveRecord{
	protected $id;
	protected $decanato_id;
	protected $titulo;
	protected $contenido;
	protected $empleado_id;
	protected $fchPublicacion_at;
	protected $estatus;

	protected function initialize(){
		$this->belongsTo("empleado_id","empleado","id");
	}
	public function getId() {
		return $this->id;
	}
	public function getDecanato_id() {
		return $this->decanato_id;
	}
	public function getTitulo() {
		return $this->titulo;
	}
	public function getContenido() {
		return $this->contenido;
	}
	public function getEmpleado_id() {
		return $this->empleado_id;
	}
	public function getFchPublicacion_at() {
		return $this->fchPublicacion_at;
	}
	public function getEstatus() {
		return $this->estatus;
	}
	public function setId($x) {
		$this->id = $x;
	}
	public function setDecanato_id($x) {
		$this->decanato_id = $x;
	}
	public function setTitulo($x) {
		$this->titulo = $x;
	}
	public function setContenido($x) {
		$this->contenido = $x;
	}
	public function setEmpleado_id($x) {
		$this->empleado_id = $x;
	}
	public function setFchPublicacion_at($x) {
		$this->fchPublicacion_at = $x;
	}
	public function setEstatus($x) {
		$this->estatus = $x; }

	/**
	 * Retorna arry con el listado de las noticias por partes (start-limit) segun su estatus
	 * @param int $start
	 * @param int $limit
	 * @param int $estatus
	 * @return  <multitype:, string>
	 */
	public function getNoticias($start,$limit,$estatus='A') {
		$aux = array();
		$i=0;
		$sql= " SELECT n.id as id, titulo,contenido, fchPublicacion_at,nombre,apellido ";
		$sql.= " FROM noticia n,empleado e ";
		$sql.= " WHERE empleado_id=e.id AND  n.estatus='$estatus' ";
		$sql.= " ORDER BY fchPublicacion_at DESC";
		$sql.= " LIMIT ".$start.",".$limit." ";
		$db = Db::rawConnect();
		$result = $db->query($sql);
		while($row = $db->fetchArray($result)){
			$aux[$i]['id'] = $row['id'];
			$aux[$i]['titulo'] =  utf8_encode($row['titulo']);
			$aux[$i]['contenido'] = utf8_encode($row['contenido']);
			$aux[$i]['fchPublicacion'] = Util::cambiarFechaDMY($row['fchPublicacion_at']);
			$aux[$i]['nombre'] =  utf8_encode($row['nombre']);
			$aux[$i]['apellido'] =  utf8_encode($row['apellido']);
			$i++;
		}
			
		return $aux;
	}

	/**
	 * Retorna el numero de noticias segun su estatus.
	 * @param string $estatus Default 'A'
	 * @return int
	 */
	public function getTotalNoticias($estatus='A') {
		return $this->count("estatus='$estatus'");
	}

	/**
	 * Retorna los datos de una noticia en especifico
	 * @param int $id
	 * @return  array
	 */
	public function getNoticia ($id,$estatus='A'){
		$aux = array();
		$noticia = $this->findFirst("id='$id' AND estatus='$estatus'");
		if ($noticia){
			$aux['id'] = $noticia->getId();
			$aux['titulo'] = utf8_encode($noticia->getTitulo());
			$aux['fchPublicacion'] = Util::cambiarFechaDMY($noticia->getFchPublicacion_at());
			$aux['contenido'] = utf8_encode($noticia->getContenido());
			$aux['empleado_id'] = utf8_encode($noticia->getEmpleado_id());
			$empleado= $noticia->getEmpleado();
			$aux['nombre'] =utf8_encode($empleado->getNombre());
			$aux['apellido'] = utf8_encode($empleado->getApellido());
			
		}
		return  $aux;
	 	
	}
	
	/**
	 * Registra una noticia con las informacion suministrada
	 * @param int $idUsuario
	 * @param string $titulo
	 * @param string $contenido
	 * @param int $decanato
	 * @return boolean
	 */
	public function guardarNoticia($idUsuario,$titulo,$contenido,$decanato=1) {
		$success = false;
		$this->setDecanato_id($decanato); // Estatico 1:Ciencias y tecnologia
		$this->setEmpleado_id($idUsuario);
		$this->setTitulo($titulo);
		$this->setContenido($contenido);
		$this->setEstatus('A');
		$success= $this->save();
		return $success;
	}
	/**
	 * Actualiza la noticia asociada al id pasado.
	 * @param int $id
	 * @param string $titulo
	 * @param string $contenido
	 * @return boolean
	 */
	public function actualizarNoticia($id,$titulo,$contenido) {
		$success = false;
		$noticia = $this->findFirst("id='$id'");
		if ($noticia){
			$noticia->setTitulo($titulo);
			$noticia->setContenido($contenido);
			$success = $noticia->update();
		}
		return $success;
	}
	/**
	 * Elimina de manera logica el registro
	 * @param int $id
	 * @return boolean
	 */
	public function eliminarNoticia($id) {
		$success = false;
		$noticia = $this->findFirst("id='$id'");
		if ($noticia){
			$noticia->setEstatus('E');
			$success = $noticia->update();
		}
		return $success;
		}


}
?>

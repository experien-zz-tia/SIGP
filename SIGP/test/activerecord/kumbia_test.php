<?php

class KumbiaTest extends ActiveRecordBase {

	/**
	 * @var integer
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $nombre;

	/**
	 * @var string
	 */
	protected $texto;

	/**
	 * @var integer
	 */
	protected $cantidad;

	/**
	 * @var string
	 */
	protected $fecha;

	/**
	 * @var Date
	 */
	protected $fecha_at;

	/**
	 * @var Date
	 */
	protected $fecha_in;

	/**
	 * @var string
	 */
	protected $estado;


	/**
	 * Metodo para establecer el valor del campo id
	 * @param integer $id
	 */
	public function setId($id){
		$this->id = $id;
	}

	/**
	 * Metodo para establecer el valor del campo nombre
	 * @param string $nombre
	 */
	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	/**
	 * Metodo para establecer el valor del campo texto
	 * @param string $texto
	 */
	public function setTexto($texto){
		$this->texto = $texto;
	}

	/**
	 * Metodo para establecer el valor del campo cantidad
	 * @param integer $cantidad
	 */
	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	/**
	 * Metodo para establecer el valor del campo fecha
	 * @param string $fecha
	 */
	public function setFecha($fecha){
		$this->fecha = $fecha;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_at
	 * @param Date $fecha_at
	 */
	public function setFechaAt($fecha_at){
		$this->fecha_at = $fecha_at;
	}

	/**
	 * Metodo para establecer el valor del campo fecha_in
	 * @param Date $fecha_in
	 */
	public function setFechaIn($fecha_in){
		$this->fecha_in = $fecha_in;
	}

	/**
	 * Metodo para establecer el valor del campo estado
	 * @param string $estado
	 */
	public function setEstado($estado){
		$this->estado = $estado;
	}


	/**
	 * Devuelve el valor del campo id
	 * @return integer
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Devuelve el valor del campo nombre
	 * @return string
	 */
	public function getNombre(){
		return $this->nombre;
	}

	/**
	 * Devuelve el valor del campo texto
	 * @return string
	 */
	public function getTexto(){
		return $this->texto;
	}

	/**
	 * Devuelve el valor del campo cantidad
	 * @return integer
	 */
	public function getCantidad(){
		return $this->cantidad;
	}

	/**
	 * Devuelve el valor del campo fecha
	 * @return string
	 */
	public function getFecha(){
		return $this->fecha;
	}

	/**
	 * Devuelve el valor del campo fecha_at
	 * @return Date
	 */
	public function getFechaAt(){
		return new Date($this->fecha_at);
	}

	/**
	 * Devuelve el valor del campo fecha_in
	 * @return Date
	 */
	public function getFechaIn(){
		return new Date($this->fecha_in);
	}

	/**
	 * Devuelve el valor del campo estado
	 * @return string
	 */
	public function getEstado(){
		return $this->estado;
	}

	protected function beforeValidation(){
		$this->cantidad = 0;
	}

	protected function beforeValidationOnCreate(){
		$this->cantidad++;
	}

	protected function afterValidationOnCreate(){
		$this->cantidad++;
	}

	protected function afterValidation(){
		$this->cantidad++;
	}

	protected function beforeSave(){
		$this->cantidad++;
	}

	protected function beforeCreate(){
		$this->cantidad++;
	}

	protected function afterCreate(){
		$this->cantidad++;
	}

	protected function afterSave(){
		$this->cantidad++;
	}

}


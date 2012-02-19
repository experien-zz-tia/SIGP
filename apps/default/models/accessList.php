<?php

class AccessList extends ActiveRecord {
	protected $id;
	protected $role;
	protected $resource;
	protected $action;
	protected $allow;


	public function getId() { return $this->id; }
	public function getRole() { return $this->role; }
	public function getResource() { return $this->resource; }
	public function getAction() { return $this->action; }
	public function getAllow() { return $this->allow; }
	public function setId($x) { $this->id = $x; }
	public function setRole($x) { $this->role = $x; }
	public function setResource($x) { $this->resource = $x; }
	public function setAction($x) { $this->action = $x; }
	public function setAllow($x) { $this->allow = $x; }
}
?>
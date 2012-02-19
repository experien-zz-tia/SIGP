<?php

/**
 * Kumbia Enterprise Framework
 *
 * LICENSE
 *
 * This source file is subject to the New BSD License that is bundled
 * with this package in the file docs/LICENSE.txt.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kumbia@kumbia.org so we can send you a copy immediately.
 *
 * @category Kumbia
 * @package Scripts
 * @copyright Copyright (c) 2005-2009 Andres Felipe Gutierrez (gutierrezandresfelipe at gmail.com)
 * @license New BSD License
 */

require 'Library/Kumbia/Core/ClassPath/CoreClassPath.php';
require 'Library/Kumbia/Autoload.php';

ini_alter('track_errors', true);
set_exception_handler(array('Core', 'manageExceptions'));
set_error_handler(array('Core', 'manageErrors'));

require 'kumbia_test.php';

class ActiveRecordTest extends PHPUnitTestCase {

	/**
	 * Modelo de Prueba
	 *
	 * @var ActiveRecord
	 */
	private $_model;

	/**
	 * Nombre del Modelo
	 *
	 * @var string
	 */
	private $_modelName;

	/**
	 * Conexión
	 *
	 * @var resource
	 */
	private $_connection;

	/**
	 * Nombre de la tabla
	 *
	 * @var string
	 */
	private $_sourceName;

	/**
	 * Constructor del Test
	 *
	 */
	public function __construct(){
		$this->_connection = DbLoader::factory('MySQL', array(
			'hostname' => 'localhost',
			'username' => 'root',
			'password' => 'hea101',
			'name' => 'test'
			));
			$this->_sourceName = 'kumbia_test';
			$this->_connection->dropTable('kumbia_test', true);
			$this->_connection->createTable($this->_sourceName, array(
			'id' => array(
				'type' => DbMySQL::TYPE_INTEGER,
				'notNull' => true,
				'primary' => true,
				'auto' => true
			),
			'nombre' => array(
				'type' => DbMySQL::TYPE_VARCHAR,
				'notNull' => true,
				'size' => 120
			),
			'texto' => array(
				'type' => DbMySQL::TYPE_TEXT,
				'notNull' => true
			),
			'cantidad' => array(
				'type' => DbMySQL::TYPE_INTEGER,
				'notNull' => true,
				'size' => 11
			),
			'fecha' => array(
				'type' => DbMySQL::TYPE_DATETIME,
				'notNull' => true
			),
			'fecha_at' => array(
				'type' => DbMySQL::TYPE_DATE
			),
			'fecha_in' => array(
				'type' => DbMySQL::TYPE_DATE
			),
			'estado' => array(
				'type' => DbMySQL::TYPE_CHAR ,
				'notNull' => true,
				'size' => 1
			)
			));

			Core::setTimeZone('America/Bogota');
			Facility::setFacility(Facility::USER_LEVEL);

			ActiveRecordBase::disableEvents(true);
			EntityManager::addEntityByClass('KumbiaTest');
			$this->_model = new KumbiaTest();
			$this->_model->setConnection($this->_connection);
	}

	/**
	 * Obtener el data-source
	 *
	 */
	public function testObtenerSource(){
		$this->assertEquals($this->_sourceName, $this->_model->getSource());
	}

	/**
	 * Obtener atributos de la entidad
	 *
	 */
	public function testGetAttributes(){
		$this->assertEquals(count($this->_model->getAttributes()), 8);
	}

	/**
	 * Obtener atributos llave primaria
	 *
	 */
	public function testGetPrimaryKeyAttributes(){
		$this->assertEquals(count($this->_model->getPrimaryKeyAttributes()), 1);
	}

	/**
	 * Obtener atributos no llave primaria
	 *
	 */
	public function testGetNonPrimaryKeyAttributes(){
		$this->assertEquals(count($this->_model->getNonPrimaryKeyAttributes()), 7);
	}

	/**
	 * Obtener atributos con fecha automatica al insertar
	 *
	 */
	public function testGetDatesAtAttributes(){
		$this->assertEquals(count($this->_model->getDatesAtAttributes()), 1);
	}

	/**
	 * Obtener atributos con fecha automatica al actualizar
	 *
	 */
	public function testGetDatesInAttributes(){
		$this->assertEquals(count($this->_model->getDatesInAttributes()), 1);
	}

	/**
	 * Obtener campos no nulos
	 *
	 */
	public function testGetNotNullAttributes(){
		$this->assertEquals(count($this->_model->getNotNullAttributes()), 6);
	}

	/**
	 * Crea un registro (1)
	 *
	 */
	public function testCreate1(){
		$datos = array(
			'nombre' => 'prueba',
			'texto' => 'prueba',
			'fecha' => Date::getCurrentDate(),
			'cantidad' => 1,
			'estado' => 'A'
			);
			if($this->_model->create($datos)==false){
				foreach($this->_model->getMessages() as $message){
					print $message->getMessage()."\n";
				}
			}
			$this->assertEquals(1, $this->_model->count());
	}

	/**
	 * Cambia el source del modelo
	 *
	 */
	public function testChangeSource(){
		$this->_model->setSource('kumbia_test2');
		$this->assertEquals('kumbia_test2', $this->_model->getSource());
		$this->_model->setSource('kumbia_test');
	}

	/**
	 * Cambia el schema del modelo
	 *
	 */
	public function testChangeSchema(){
		$this->_model->setSchema('test2');
		$this->assertEquals('test2', $this->_model->getSchema());
		$this->_model->setSchema('test');
	}

	/**
	 * Obtener Conexión Interna
	 *
	 */
	public function testGetConnection(){
		$connection = $this->_model->getConnection();
		$this->assertResource($connection);
	}

	/**
	 * findFirst (1)
	 */
	public function testFindFirst1(){
		$model = $this->_model->findFirst();
		$this->assertEquals($model->getId(), '1');
		$this->assertEquals($model->getNombre(), 'prueba');
		$this->assertEquals($model->getTexto(), 'prueba');
		$this->assertEquals($model->getCantidad(), '1');
		$this->assertEquals((string)$model->getFechaAt(), Date::getCurrentDate());
		$this->assertEquals((string)$model->getFechaIn(), Date::getCurrentDate());
		$this->assertEquals($model->getEstado(), 'A');
		$this->assertEquals($this->_model->getId(), '1');
		$this->assertEquals($this->_model->getNombre(), 'prueba');
		$this->assertEquals($this->_model->getTexto(), 'prueba');
		$this->assertEquals($this->_model->getCantidad(), '1');
		$this->assertEquals((string)$this->_model->getFechaAt(), Date::getCurrentDate());
		$this->assertEquals((string)$this->_model->getFechaIn(), Date::getCurrentDate());
		$this->assertEquals($this->_model->getEstado(), 'A');
	}

	/**
	 * findFirst (2)
	 *
	 */
	public function testFindFirst2(){
		$model = $this->_model->findFirst('conditions: id=1');
		$this->assertEquals($model->getId(), '1');
	}

	/**
	 * findFirst (3)
	 *
	 */
	public function testFindFirst3(){
		$model = $this->_model->findFirst(array('conditions' => 'id=1'));
		$this->assertEquals($model->getId(), '1');
	}

	/**
	 * findFirst (4)
	 *
	 */
	public function testFindFirst4(){
		$model = $this->_model->findFirst(1);
		$this->assertEquals($model->getId(), '1');
	}

	/**
	 * Consulta un registro usando SQL
	 *
	 */
	public function testFindBySQL(){
		$model = $this->_model->findBySql('SELECT * FROM kumbia_test');
		$this->assertInstanceOf($this->_model, 'KumbiaTest');
	}

	/**
	 * Prueba una actualización (1)
	 *
	 */
	public function testUpdate1(){
		$model = $this->_model->findFirst();
		$model->setCantidad(2);
		$model->setFecha('2009-01-20');
		$model->update();
		$model = $this->_model->findFirst();
		$this->assertEquals('2', $model->getCantidad());
		$this->assertEquals('2009-01-20 00:00:00', (string) $model->getFecha());
	}

	/**
	 * Eliminar un registro
	 *
	 */
	public function testDeleteRecord(){
		$model = $this->_model->findFirst();
		$this->assertTrue($this->_model->delete());
	}

	/**
	 * testCreate (2)
	 *
	 */
	public function testCreate2(){
		for($i=0;$i<100;$i++){
			$model = new KumbiaTest();
			$model->setConnection($this->_connection);
			#$model->setDebug(true);
			$model->setNombre('prueba'.$i);
			$model->setTexto('texto'.$i);
			$model->setCantidad($i);
			$model->setFecha((string)Date::getDateFromTimestamp(mt_rand(time(), time()+256000)));
			$model->setEstado($i%2==0 ? 'A' : 'I');
			if($model->create()==false){
				foreach($model->getMessages() as $message){
					print $message->getMessage()."\n";
				}
			}
		}
		$this->assertEquals(100, $this->_model->count());
	}

	/**
	 * testFind (1)
	 *
	 */
	public function testFind1(){
		$models = $this->_model->find();
		$this->assertEquals(100, count($models));
	}

	/**
	 * testFind (2)
	 *
	 */
	public function testFind2(){
		$models = $this->_model->find('cantidad>50');
		$this->assertEquals(49, count($models));
	}

	/**
	 * testFind (3)
	 *
	 */
	public function testFind3(){
		$models = $this->_model->find('conditions: cantidad>50');
		$this->assertEquals(49, count($models));
	}

	/**
	 * testFind (4)
	 *
	 */
	public function testFind4(){
		$models = $this->_model->find(array('conditions' => 'cantidad>50'));
		$this->assertEquals(49, count($models));
	}

	/**
	 * testFind (5) ordenamiento ascendente
	 *
	 */
	public function testFind5(){
		$models = $this->_model->find('cantidad>50', 'order: id');
		$this->assertTrue($models->valid(), true);
		$model = $models->current();
		$this->assertEquals($model->getId(), '53');
		$this->assertTrue($models->valid(), true);
		$model = $models->current();
		$this->assertEquals($model->getId(), '54');
	}

	/**
	 * testFind (6) ordenamiento descendente
	 *
	 */
	public function testFind6(){
		$models = $this->_model->find('cantidad>50', 'order: id DESC');
		$this->assertTrue($models->valid(), true);
		$model = $models->current();
		$this->assertEquals($model->getId(), '101');
		$this->assertTrue($models->valid(), true);
		$model = $models->current();
		$this->assertEquals($model->getId(), '100');
	}

	/**
	 * testFind (7) consulta por campos
	 *
	 */
	public function testFind7(){
		$models = $this->_model->find('columns: id, cantidad', 'order: id');
		foreach($models as $model){
			$this->assertEquals('2', $model->getId());
			$this->assertNull($model->getFecha());
			break;
		}
	}

	/**
	 * Crea un registro (3) + eventos
	 *
	 */
	public function testCreate3(){
		ActiveRecordBase::disableEvents(false);
		$datos = array(
			'nombre' => 'prueba-ev',
			'texto' => 'prueba-ev',
			'fecha' => Date::getCurrentDate(),
			'cantidad' => 0,
			'estado' => 'A'
			);
			if($this->_model->create($datos)==false){
				foreach($this->_model->getMessages() as $message){
					print $message->getMessage()."\n";
				}
			}
			$this->assertEquals(7, $this->_model->getCantidad());
	}

	/**
	 * Actualiza un registro (2) + eventos
	 *
	 */
	public function testUpdate2(){
		$this->_model->findFirst(102);
		if($this->_model->update()==false){
			foreach($this->_model->getMessages() as $message){
				print $message->getMessage()."\n";
			}
		}
		$this->assertEquals(3, $this->_model->getCantidad());
	}

	/**
	 * Operacion realizada de actualizacion
	 *
	 */
	public function testOperation1(){
		$this->_model->findFirst(102);
		$this->_model->save();
		$this->assertEquals($this->_model->operationWasInsert(), false);
		$this->assertEquals($this->_model->operationWasUpdate(), true);
		$this->assertEquals($this->_model->getOperationMade(), ActiveRecordBase::OP_UPDATE);
	}

	/**
	 * Operacion realizada de insercion
	 *
	 */
	public function testOperation2(){
		$this->_model->findFirst(102);
		$this->_model->setId(103);
		$this->_model->save();
		$this->assertEquals($this->_model->operationWasInsert(), true);
		$this->assertEquals($this->_model->operationWasUpdate(), false);
		$this->assertEquals($this->_model->getOperationMade(), ActiveRecordBase::OP_CREATE);
	}

	/**
	 * Sumatoria (1)
	 *
	 */
	public function testSummatory1(){
		$this->assertEquals(4957.0, $this->_model->sum('cantidad'));
	}

	/**
	 * Sumatoria (2)
	 *
	 */
	public function testSummatory2(){
		$this->assertEquals(4957.0, $this->_model->sum(array('cantidad')));
	}

	/**
	 * Sumatoria (3)
	 *
	 */
	public function testSummatory3(){
		$this->assertEquals(760.0, $this->_model->sum('cantidad', 'conditions: cantidad<50 AND cantidad>30'));
	}

	/**
	 * Sumatoria (4)
	 *
	 */
	public function testSummatory4(){
		$this->assertEquals(760.0, $this->_model->sum(array('cantidad', 'conditions' => 'cantidad<50 AND cantidad>30')));
	}

	/**
	 * Sumatoria (5)
	 *
	 */
	public function testSummatory5(){
		$sumatories = $this->_model->sum('cantidad', 'group: estado');
		$sum = array();
		foreach($sumatories as $sumatory){
			$sum[$sumatory->estado] = $sumatory->sumatory;
		}
		$this->assertEquals((double) $sum['A'], 2457.0);
		$this->assertEquals((double) $sum['I'], 2500.0);
	}

	/**
	 * Promedio (1)
	 *
	 */
	public function testAverage1(){
		$this->assertEquals(48.5980, $this->_model->average('cantidad'));
	}

	/**
	 * Promedio (2)
	 *
	 */
	public function testAverage2(){
		$this->assertEquals(48.5980, $this->_model->average(array('cantidad')));
	}

	/**
	 * Promedio (3)
	 *
	 */
	public function testAverage3(){
		$this->assertEquals(40.00, $this->_model->average('cantidad', 'conditions: cantidad>30 AND cantidad<50'));
	}

	/**
	 * Promedio (4)
	 *
	 */
	public function testAverage4(){
		$this->assertEquals(40.00, $this->_model->average(array('cantidad', 'conditions' => 'cantidad>30 AND cantidad<50')));
	}

	/**
	 * Promedio (5)
	 *
	 */
	public function testAverage5(){
		$averages = $this->_model->average('cantidad', 'group: estado');
		$avg = array();
		foreach($averages as $average){
			$avg[$average->estado] = $average->average;
		}
		$this->assertEquals((double)$avg['A'], 47.25);
		$this->assertEquals((double)$avg['I'], 50.00);
	}

	/**
	 * Mínimo (1)
	 *
	 */
	public function testMinimum1(){
		$this->assertEquals(0.0, $this->_model->minimum('cantidad'));
	}

	/**
	 * Mínimo (2)
	 *
	 */
	public function testMinimum2(){
		$this->assertEquals(0.0, $this->_model->minimum(array('cantidad')));
	}

	/**
	 * Mínimo (3)
	 *
	 */
	public function testminimum3(){
		$this->assertEquals(31.0, $this->_model->minimum('cantidad', 'conditions: cantidad>30 AND cantidad<50'));
	}

	/**
	 * Mínimo (4)
	 *
	 */
	public function testMinimum4(){
		$this->assertEquals(31.0, $this->_model->minimum(array('cantidad', 'conditions' => 'cantidad>30 AND cantidad<50')));
	}

	/**
	 * Mínimo (5)
	 *
	 */
	public function testMinimum5(){
		$minimums = $this->_model->minimum('cantidad', 'group: estado');
		$min = array();
		foreach($minimums as $minimum){
			$min[$minimum->estado] = $minimum->minimum;
		}
		$this->assertEquals((double)$min['A'], 0.0);
		$this->assertEquals((double)$min['I'], 1.0);
	}

	/**
	 * Máximo (1)
	 *
	 */
	public function testMaximum1(){
		$this->assertEquals(99.0, $this->_model->maximum('cantidad'));
	}

	/**
	 * Máximo (2)
	 *
	 */
	public function testMaximum2(){
		$this->assertEquals(99.0, $this->_model->maximum(array('cantidad')));
	}

	/**
	 * Máximo (3)
	 *
	 */
	public function testmaximum3(){
		$this->assertEquals(49.0, $this->_model->maximum('cantidad', 'conditions: cantidad>30 AND cantidad<50'));
	}

	/**
	 * Máximo (4)
	 *
	 */
	public function testMaximum4(){
		$this->assertEquals(49.0, $this->_model->maximum(array('cantidad', 'conditions' => 'cantidad>30 AND cantidad<50')));
	}

	/**
	 * Máximo (5)
	 *
	 */
	public function testMaximum5(){
		$maximums = $this->_model->maximum('cantidad', 'group: estado');
		$max = array();
		foreach($maximums as $maximum){
			$max[$maximum->estado] = $maximum->maximum;
		}
		$this->assertEquals((double)$max['A'], 98.0);
		$this->assertEquals((double)$max['I'], 99.0);
	}

	/**
	 * Distinct (1)
	 *
	 */
	public function testDistinct1(){
		$distinct = $this->_model->distinct('estado');
		$this->assertEquals(count($distinct), 2);
	}

	/**
	 * Distinct (2)
	 *
	 */
	public function testDistinct2(){
		$distinct = $this->_model->distinct(array('estado'));
		$this->assertEquals(count($distinct), 2);
	}

	/**
	 * Distinct (3)
	 *
	 */
	public function testDistinct3(){
		$distinct = $this->_model->distinct('texto', 'conditions: cantidad>10');
		$this->assertEquals(count($distinct), 89);
	}

	/**
	 * Distinct (4)
	 *
	 */
	public function testDistinct4(){
		$distinct = $this->_model->distinct(array('texto', 'conditions' => 'cantidad>10'));
		$this->assertEquals(count($distinct), 89);
	}

	/**
	 * Valida campos nulos
	 *
	 */
	public function testValidaNulos(){
		$this->_model->clear();
		$this->assertEquals($this->_model->create(), false);
		$this->assertEquals(count($this->_model->getMessages()), 4);
	}

	/**
	 * Leer un atributo de forma uniforme
	 *
	 */
	public function testReadAttribute(){
		$this->_model->findFirst(2);
		$this->assertEquals($this->_model->readAttribute('texto'), 'texto0');
		$this->assertEquals($this->_model->readAttribute('nombre'), 'prueba0');
	}

	/**
	 * Escribir un atributo de forma uniforme
	 *
	 */
	public function testWriteAttribute(){
		$this->_model->findFirst(2);
		$this->_model->writeAttribute('texto', 1234);
		$this->assertEquals($this->_model->getTexto(), 1234);
	}

	/**
	 * Tiene un determinado campo?
	 *
	 */
	public function testHasField(){
		$this->assertEquals($this->_model->hasField('texto'), true);
		$this->assertEquals($this->_model->hasField('otro_texto'), false);
		$this->assertEquals($this->_model->isAttribute('texto'), true);
		$this->assertEquals($this->_model->isAttribute('otro_texto'), false);
	}

	/**
	 * Existe un registro (1) primaria automática
	 *
	 */
	public function testExist1(){
		$this->assertTrue($this->_model->exists(2));
	}

	/**
	 * Existe un registro (2) condición
	 *
	 */
	public function testExist2(){
		$this->assertTrue($this->_model->exists('id=2'));
	}

	/**
	 * Realiza una búsqueda mediante un campo
	 *
	 */
	public function testFindAllBy(){
		$models = $this->_model->findAllBy('estado', 'A');
		$this->assertEquals(count($models), 52);
	}

	/**
	 * Prueba una actualización (3)
	 *
	 */
	public function testUpdate3(){
		$model = $this->_model->findFirst(2);
		$model->update(array(
			'fecha' => '2009-01-01',
			'texto' => 'texto-u'
			));
			$this->_model->clear();
			$model = $this->_model->findFirst(2);
			$this->assertEquals('texto-u', $model->getTexto());
			$this->assertEquals('2009-01-01 00:00:00', (string) $model->getFecha());
	}

	/**
	 * Prueba una actualización múltiple (4)
	 *
	 */
	public function testUpdate4(){
		$this->assertTrue($this->_model->updateAll("nombre='prueba-all'"));
		$this->assertEquals($this->_model->count(), $this->_model->count("nombre='prueba-all'"));
	}

	/**
	 * Prueba una actualización múltiple (5) + condiciones
	 *
	 */
	public function testUpdate5(){
		$this->assertTrue($this->_model->updateAll("nombre='prueba-all-10'", 'conditions: cantidad>10'));
		$this->assertEquals(89, $this->_model->count("nombre='prueba-all-10'"));
	}

	/**
	 * Prueba una actualización múltiple (6) + condiciones
	 *
	 */
	public function testUpdate6(){
		$this->assertTrue($this->_model->updateAll(array("nombre='prueba-all-50'", 'conditions' => 'cantidad>50')));
		$this->assertEquals(49, $this->_model->count("nombre='prueba-all-50'"));
	}

	/**
	 * Prueba un borrado múltiple (5) + condiciones
	 *
	 */
	public function testDelete2(){
		$this->assertTrue($this->_model->deleteAll("nombre='prueba-all'"));
		$this->assertEquals(0, $this->_model->count("nombre='prueba-all'"));
	}

	/**
	 * Consulta usando __call (findAllBy)
	 *
	 */
	public function testFindAllByCall(){
		$models = $this->_model->findAllByEstado('I');
		$this->assertEquals(count($models), 45);
	}

	/**
	 * Consulta usando __call (countBy)
	 *
	 */
	public function testCountByCall(){
		$count = $this->_model->countByEstado('I');
		$this->assertEquals($count, 45);
	}

	/**
	 * Consulta todos los registros usando SQL
	 *
	 */
	public function testFindAllBySQL(){
		$models = $this->_model->findAllBySql('SELECT * FROM kumbia_test');
		$this->assertEquals(count($models), 89);
	}

	/**
	 * Contar todos los registros usando SQL
	 *
	 */
	public function testCountBySQL(){
		$count = $this->_model->countBySql('SELECT COUNT(*) FROM kumbia_test');
		$this->assertEquals($count, 89);
	}

}

print "Probando Componente ActiveRecord...\n";
PHPUnit::testClass("ActiveRecordTest");

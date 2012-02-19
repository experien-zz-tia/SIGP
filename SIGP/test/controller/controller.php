<?php

require "Library/Kumbia/PHPUnit/PHPUnitControllerTest.php";

class LoginControllerTest extends PHPUnitControllerTest {

	public function validateCredentialsDataProvider(){
		$postData = array();
		for($i=0;$i<=1000;$i++){
			$postData[] = array(
				"login" => mt_rand(100000, 200000),
				"password" => mt_rand(1, 9999)
			);
		}
		$data = array(
			"POST" => $postData
		);
		return $data;
	}

	public function validateCredentialsTest(){

	}

}

/*class ClaveControllerTest extends PHPUnitControllerTest {

public function autenticarDataProvider(){

}

public function autenticarTest(){
$parametersData = array();
for($i=0;$i<=10;$i++){
$parametersData[] = array($this->getRandomData());
}
return array(
'PARAMS' => $parametersData
);
}

}*/

PHPUnitControllerTest::test("bank", "LoginControllerTest");

//PHPUnitControllerTest::test("pos2", "ClaveControllerTest");
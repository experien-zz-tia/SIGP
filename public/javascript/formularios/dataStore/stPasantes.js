stPasantes = Ext.extend(Ext.data.JsonStore, {
			constructor : function(cfg) {
				cfg = cfg || {};
				stPasantes.superclass.constructor.call(this, Ext.apply({
									storeId : 'stPasantes',
									url : '/SIGP/pasante/consultarPasantias',
									paramNames : {
										pCarreraId : "pCarreraId"
									},
									root : 'resultado',
									fields : [{
												name : 'pasanteId',
												type : 'int'
											}, {
												name : 'pasantiaId',
												type : 'int'
											}, {
												name : 'cedulaPasante',
												type : 'string'
											}, {
												name : 'nombrePasante',
												type : 'string'
											}, {
												name : 'apellidoPasante',
												type : 'string'
											}, {
												name : 'carrera',
												type : 'string'
											}, {
												name : 'razonSocial',
												type : 'string'
											}, {
												name : 'tutorAcad',
												type : 'string'
											}, {
												name : 'tutorEmp',
												type : 'string'
											}, {
												name : 'estatusPasantia',
												type : 'string'
											}]
								}, cfg));
			}
		});
var stPasantes = new stPasantes();

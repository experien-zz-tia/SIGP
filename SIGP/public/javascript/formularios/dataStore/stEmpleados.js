stEmpleados = Ext.extend(Ext.data.JsonStore, {
			constructor : function(cfg) {
				cfg = cfg || {};
				stEmpleados.superclass.constructor.call(this, Ext.apply({
									storeId : 'stEmpleados',
									url : '/SIGP/usuario/consultarEmpleados',
									root : 'resultado',
									fields : [{
												name : 'empleadoId',
												type : 'int'
											},  {
												name : 'cedula',
												type : 'string'
											}, {
												name : 'nombre',
												type : 'string'
											}, {
												name : 'apellido',
												type : 'string'
											}, {
												name : 'tipo',
												type : 'string'
											}, {
												name : 'decanato',
												type : 'string'
											}, {
												name : 'correo',
												type : 'string'
											}, {
												name : 'estatus',
												type : 'string'
											}]
								}, cfg));
			}
		});
var stEmpleados = new stEmpleados();

stEmpleadosDec = Ext.extend(Ext.data.JsonStore, {
	constructor : function(cfg) {
		cfg = cfg || {};
		stEmpleadosDec.superclass.constructor.call(this, Ext.apply( {
			storeId : 'stEmpleadosDec',
			url : '/SIGP/empleado/getEmpleadosByDecanato',
			paramNames : {
			idDecanato : "idDecanato"
			},
			fields : [ {
				name : 'id',
				type : 'int',
				mapping : 'id'
			}, {
				name : 'nombre',
				type : 'string',
				mapping : 'nombre'
			}]
		}, cfg));
	}
});
new stEmpleadosDec();
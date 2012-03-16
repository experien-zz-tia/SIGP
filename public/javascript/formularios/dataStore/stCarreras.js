stCarreras = Ext.extend(Ext.data.JsonStore, {
	constructor : function(cfg) {
		cfg = cfg || {};
		stCarreras.superclass.constructor.call(this, Ext.apply( {
			storeId : 'stCarreras',
			url : '/SIGP/carrera/getCarrerasFull',
			paramNames : {
				idDecanato : "idDecanato"
			},
			fields : [ {
				name : 'id',
				type : 'int',
				mapping : 'id'
			}, {
				name : 'decanato_id',
				type : 'int',
				mapping : 'decanato_id'
			}, {
				name : 'decanato',
				type : 'string',
				mapping : 'decanato'
			}, {
				name : 'nombre',
				type : 'string',
				mapping : 'nombre'
			}, {
				name : 'regimen',
				type : 'string',
				mapping : 'regimen'
			}, {
				name : 'plan',
				type : 'string',
				mapping : 'plan'
			}, {
				name : 'duracion',
				type : 'int',
				mapping : 'duracion'
			} ]
		}, cfg));
	}
});
var stCarreras = new stCarreras();
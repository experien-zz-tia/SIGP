stSemestre = Ext.extend(Ext.data.JsonStore, {
	constructor : function(cfg) {
		cfg = cfg || {};
		stSemestre.superclass.constructor.call(this, Ext.apply( {
			storeId : 'stSemestre',
			url : '/SIGP/carrera/getSemestres',
			paramNames : {
				idCarrera : "idCarrera"
			},
			fields : [ {
				name : 'id',
				type : 'int',
				mapping : 'id'
			}, {
				name : 'duracion',
				type : 'int',
				mapping : 'duracion'
			} ]
		}, cfg));
	}
});
new stSemestre();
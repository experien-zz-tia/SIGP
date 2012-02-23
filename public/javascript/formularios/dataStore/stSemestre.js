stSemestre = Ext.extend(Ext.data.JsonStore, {
	constructor : function(cfg) {
		cfg = cfg || {};
		stSemestre.superclass.constructor.call(this, Ext.apply( {
			storeId : 'stSemestre',
			url : '/SIGP/carrera/getSemestres',
			paramNames : {
				idCarrera : "idCarrera"
			},
			fields : [{
				name : 'semestre',
				type : 'int',
				mapping : 'semestre'
			} ]

		}, cfg));
	}
});
new stSemestre();
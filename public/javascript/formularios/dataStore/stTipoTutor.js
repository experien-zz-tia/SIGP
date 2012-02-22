
stTipoTutor = Ext.extend(Ext.data.ArrayStore, {
			constructor : function(cfg) {
				cfg = cfg || {};
				stTipoTutor.superclass.constructor.call(this, Ext.apply({
									storeId : 'stTipoTutor',
									data : [['A', 'Academico'

											], ['E', 'Empresarial'

											]],
									fields : [{
												name : 'id'
											}, {
												name : 'nombre'
											}]
								}, cfg));
			}
		});
new stTipoTutor();
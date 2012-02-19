stTutorEmpLight = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stTutorEmpLight.superclass.constructor.call(this, Ext.apply({
            storeId: 'stTutorEmpLight',
            url: '/SIGP/tutorEmpresarial/getTutores',
            fields: [
                {
                    name: 'id',
                    mapping: 'id',
                    type: 'int'
                },
                {
                    name: 'nombre',
                    type: 'string',
                    mapping: 'nombreCompleto'
                }
            ]
        }, cfg));
    }
});
new stTutorEmpLight();
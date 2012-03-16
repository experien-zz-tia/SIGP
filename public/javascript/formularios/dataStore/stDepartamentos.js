stDepartamentos = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stDepartamentos.superclass.constructor.call(this, Ext.apply({
            storeId: 'stDepartamentos',
            url: '/SIGP/departamento/getDepartamentosFull',
//            autoLoad: true,
            fields: [
                {
                    name: 'id',
                    mapping: 'id',
                    type: 'int'
                },
                {
                    name: 'descripcion',
                    type: 'string',
                    mapping: 'descripcion'
                },
                {
                    name: 'decanato',
                    type: 'string',
                    mapping: 'decanato'
                }
            ]
        }, cfg));
    }
});
new stDepartamentos();
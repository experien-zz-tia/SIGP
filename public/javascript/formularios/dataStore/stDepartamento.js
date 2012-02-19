
stDepartamento = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stDepartamento.superclass.constructor.call(this, Ext.apply({
            storeId: 'stDepartamento',
            //autoLoad: true,
            url: '/SIGP/departamento/getDepartamentosbyDecanato',
            paramNames: {
                decanato_id: "decanato_id"
            },
            fields: [
                {
                    name: 'id',
                    type: 'int',
                    mapping: 'id'
                },
                {
                    name: 'descripcion',
                    mapping: 'descripcion',
                    type: 'string'
                }
            ]
        }, cfg));
    }
});
var stDepartamento = new stDepartamento();
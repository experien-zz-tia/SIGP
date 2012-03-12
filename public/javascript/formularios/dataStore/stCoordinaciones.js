stCoordinaciones = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stCoordinaciones.superclass.constructor.call(this, Ext.apply({
            storeId: 'stCoordinaciones',
            url: '/SIGP/coordinacion/getCoordinaciones',
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
                },{
                    name: 'ubicacion',
                    type: 'string',
                    mapping: 'ubicacion'
                },
                {
                    name: 'decanato',
                    type: 'string',
                    mapping: 'decanato'
                },
                {
                    name: 'empleado',
                    type: 'string',
                    mapping: 'empleado'
                },
                {
                    name: 'telefono',
                    type: 'string',
                    mapping: 'telefono'
                },
                {
                    name: 'email',
                    type: 'string',
                    mapping: 'email'
                }
            ]
        }, cfg));
    }
});
new stCoordinaciones();
stUniversidadFull = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stUniversidadFull.superclass.constructor.call(this, Ext.apply({
            storeId: 'stUniversidadFull',
            url: '/SIGP/universidad/getUniversidadFull',
            autoLoad: true,
            fields: [
                {
                    name: 'id',
                    mapping: 'id',
                    type: 'int'
                },
                {
                    name: 'nombre',
                    type: 'string',
                    mapping: 'nombre'
                },{
                    name: 'ciudad',
                    type: 'string',
                    mapping: 'ciudad'
                },
                {
                    name: 'direccion',
                    type: 'string',
                    mapping: 'direccion'
                },
                {
                    name: 'estado',
                    type: 'string',
                    mapping: 'estado'
                },
                {
                    name: 'telefono',
                    type: 'string',
                    mapping: 'telefono'
                },
                {
                    name: 'logo',
                    type: 'binary',
                    mapping: 'logo'
                },
                {
                    name: 'estatus',
                    type: 'string',
                    mapping: 'estatus'
                }
            ]
        }, cfg));
    }
});
new stUniversidadFull();
stDuracion = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stDuracion.superclass.constructor.call(this, Ext.apply({
            storeId: 'stDuracion',
            url: '/SIGP/carrera/getDuracion',
            fields: [
                {
                    name: 'id',
                    type: 'int',
                    mapping: 'id'
                },
                {
                    name: 'nombre',
                    mapping: 'nombre',
                    type: 'string'
                }
            ]
        }, cfg));
    }
});
var stDuracion = new stDuracion();
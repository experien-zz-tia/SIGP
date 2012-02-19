
stCarrera = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stCarrera.superclass.constructor.call(this, Ext.apply({
            storeId: 'stCarrera',
            url: '/SIGP/carrera/getCarrerasbyDecanatoLight',
            fields: [
                {
                    name: 'id',
                    type: 'int',
                    mapping: 'id'
                },
                {
                    name: 'nombre',
                    type: 'string',
                    mapping: 'nombre'
                }
            ]
        }, cfg));
    }
});
new stCarrera();
stEstadoLimit = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stEstadoLimit.superclass.constructor.call(this, Ext.apply({
            storeId: 'stEstadoLimit',
            url: '/SIGP/estado/getEstadosLimit',
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
                }
            ]
        }, cfg));
    }
});
var stEstadoLimit  = new stEstadoLimit();
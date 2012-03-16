stRegimen = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stRegimen.superclass.constructor.call(this, Ext.apply({
            storeId: 'stRegimen',
            url: '/SIGP/carrera/getRegimen',
            fields: [
                {
                    name: 'id',
                    type: 'string',
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
var stRegimen = new stRegimen();
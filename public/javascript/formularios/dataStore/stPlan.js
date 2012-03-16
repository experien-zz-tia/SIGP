stPlan = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stPlan.superclass.constructor.call(this, Ext.apply({
            storeId: 'stPlan',
            url: '/SIGP/carrera/getPlan',
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
var stPlan = new stPlan();
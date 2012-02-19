/*
 * File: stAreas.js
 * Date: Sat Jan 01 2011 20:05:31 GMT-0430 (Hora estándar de Venezuela)
 */

stAreas = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stAreas.superclass.constructor.call(this, Ext.apply({
            storeId: 'stAreas',
            url: '/SIGP/oferta/getAreas',
            fields: [
                {
                    name: 'id',
                    type: 'int',
                    mapping: 'id'
                },
                {
                    name: 'nombre',
                    mapping: 'descripcion',
                    type: 'string'
                }
            ]
        }, cfg));
    }
});
var stAreas = new stAreas();
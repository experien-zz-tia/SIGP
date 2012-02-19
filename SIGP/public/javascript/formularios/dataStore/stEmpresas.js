/*
 * File: stEmpresas.js
 * Date: Sat May 01 2011 20:05:31 GMT-0430 (Hora estándar de Venezuela)
 */

stEmpresas = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stEmpresas.superclass.constructor.call(this, Ext.apply({
            storeId: 'stEmpresas',
            url: '/SIGP/empresa/getEmpresas',
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
var stEmpresas = new stEmpresas();
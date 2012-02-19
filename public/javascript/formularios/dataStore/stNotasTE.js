stNotasTE = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stNotasTE.superclass.constructor.call(this, Ext.apply({
            storeId: 'stNotasTE',
            url: '/SIGP/pasante/getNotasParciales',
            root: 'resultado',
            fields: [
          	   {
                    name: 'id',
                    type: 'int'
                },
                {
                    name: 'cedula',
                    type: 'string'
                },
                {
                    name: 'nombre',
                    type: 'string'
                },
                {
                    name: 'apellido',
                    type: 'string'
                },
                {
                    name: 'razonSocial',
                    type: 'string'
                },
                {
                    name: 'notaEmpresaTE',
                    type: 'string'
                }
            ]
        }, cfg));
    }
});
var stNotasTE = new stNotasTE();

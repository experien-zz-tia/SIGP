

stOfertasEmpresa = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stOfertasEmpresa.superclass.constructor.call(this, Ext.apply({
            storeId: 'stOfertasEmpresa',
            url: '/SIGP/oferta/getOfertasbyEmpresa',
            baseParams: 'pEmpresa_id',
            root: 'resultado',
            fields: [
          	   {
                    name: 'empresaId',
                    type: 'int'
                },
                {
                    name: 'razonSocial',
                    type: 'string'
                },
                {
                    name: 'id',
                    type: 'int'
                },
                {
                    name: 'titulo',
                    type: 'string'
                },
                {
                    name: 'fchPublicacion',
                    type: 'date'
                },
                {
                    name: 'fchCierre',
                    type: 'date'
                },
                {
                    name: 'vacantes',
                    type: 'int'
                },
                {
                    name: 'postulados',
                    type: 'int'
                },
                {
                    name: 'area',
                    type: 'string'
                },
                {
                    name: 'fchCreacion',
                    type: 'date'
                }
            ]
        }, cfg));
    }
});
var stOfertasEmpresa = new stOfertasEmpresa();

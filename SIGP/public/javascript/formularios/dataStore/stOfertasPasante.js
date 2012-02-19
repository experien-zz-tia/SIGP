stOfertasPasante = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stOfertasPasante.superclass.constructor.call(this, Ext.apply({
            storeId: 'stOfertasPasante',
            url: '/SIGP/oferta/getOfertasPasante',
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
                    name: 'disponible',
                    type: 'int'
                },
                 {
                    name: 'cupos',
                    type: 'int'
                },
                 {
                    name: 'postulados',
                    type: 'int'
                },
                {
                    name: 'area',
                    type: 'string'
                }
                ,
                {
                    name: 'tipoOferta',
                    type: 'string'
                }
                
            ]
        }, cfg));
    }
});
var stOfertasPasante = new stOfertasPasante();

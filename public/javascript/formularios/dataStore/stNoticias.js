stNoticias = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stNoticias.superclass.constructor.call(this, Ext.apply({
            storeId: 'stNoticias',
            url: '/SIGP/noticia/getNoticias',
            root: 'resultado',
            fields: [
          	   {
                    name: 'id',
                    type: 'int'
                },
                {
                    name: 'empleado_id',
                    type: 'id'
                },
                {
                    name: 'titulo',
                    type: 'string'
                },
                {
                    name: 'contenido',
                    type: 'string'
                },
                {
                    name: 'fchPublicacion',
                    type: 'string'
                },
                {
                    name: 'autor',
                    type: 'string'
                }
            ]
        }, cfg));
    }
});
var stNoticias = new stNoticias();

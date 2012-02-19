stNotificaciones = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stNotificaciones.superclass.constructor.call(this, Ext.apply({
            storeId: 'stNotificaciones',
            url: '/SIGP/notificacion/getNotificaciones',
            root: 'resultado',
            fields: [
          	   {
                    name: 'id',
                    type: 'int'
                },
            
                {
                    name: 'remitente',
                    type: 'string'
                },
                {
                    name: 'mensaje',
                    type: 'string'
                },
                {
                    name: 'fchEnvio',
                    type: 'date'
                }
            ]
        }, cfg));
    }
});
var stNotificaciones = new stNotificaciones();

stgDetalleNotas = Ext.extend(Ext.data.GroupingStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stgDetalleNotas.superclass.constructor.call(this, Ext.apply({
            storeId: 'stgDetalleNotas',
            url: '/SIGP/pasante/getDetalleNotas',
            reader: readerNotas,
            root: 'resultado',
            baseParams: 'pPasanteId',
            groupField  : "evalDescripcion" 
        }, cfg));
    }
});
var stgDetalleNotas = new stgDetalleNotas();

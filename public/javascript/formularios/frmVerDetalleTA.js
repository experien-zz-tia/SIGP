
Ext.QuickTips.init(); 
frmVerDetalleTA = Ext.extend(frmVerDetalleTAUi, {
    initComponent: function() {
        frmVerDetalleTA.superclass.initComponent.call(this);
        Ext.getCmp('btnSalir').on('click',this.salir);
	}
	,
     salir:function(){
    	 Ext.getCmp('frmVerDetalleTAWin').close();  
    }
});

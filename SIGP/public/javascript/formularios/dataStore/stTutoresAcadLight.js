

stTutoresAcadLight = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        stTutoresAcadLight.superclass.constructor.call(this, Ext.apply({
            storeId: 'stTutoresAcadLight',
            url: '/SIGP/tutorAcademico/getTutoresAcademicosLight',
            root: 'resultado',
            fields: [
                {
                    name: 'id',
                    type: 'int'
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
                    name: 'departamento',
                    type: 'string'
                },
                {
                    name: 'cargo',
                    type: 'string'
                }
            ]
        }, cfg));
    }
});
var stTutoresAcadLight = new stTutoresAcadLight();

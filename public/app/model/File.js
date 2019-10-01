Ext.define('TestProj.model.File', {
    extend: 'Ext.data.Model',
    idProperty: 'id',
    schema: {
        namespace: 'TestProj.model'
    },
    hasMany: 'TestProj.model.ZipFile',
    fields: [{
        name: 'id',
        type: 'int',
        convert: null
    }, {
        name: 'name',
        type: 'string'
    }, {
        name: 'change_datetime',
        type: 'string'
    }, {
        name: 'cnt',
        type: 'int'
    }, {
        name: 'size',
        type: 'int'
    }],
});
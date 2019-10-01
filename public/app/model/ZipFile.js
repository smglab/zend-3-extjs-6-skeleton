Ext.define('TestProj.model.ZipFile', {
    extend: 'Ext.data.Model',
    schema: {
        namespace: 'TestProj.model'
    },
    fields: [{
        name: 'zip_id',
        type: 'int',
        reference: 'TestProj.model.File'
    },{
        name: 'name',
        type: 'string'
    }, {
        name: 'size',
        type: 'int'
    }, {
        name: 'mime',
        type: 'string'
    }],
});
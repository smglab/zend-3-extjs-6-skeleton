/**
 * This view is an example list of people.
 */
Ext.define('TestProj.view.main.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'mainlist',

    requires: [
        'TestProj.store.Files'
    ],

    title: 'Archives',
    viewModel: { type: 'archiveviewmodel' },
    selType: 'rowmodel',
    selModel:
    {
        mode: 'SINGLE'
    },
    viewConfig:
    {
        stripeRows: true
    },
    bind: '{archives}',
    tbar: [
        {
            xtype: 'button',
            text: 'Загрузить',
            handler: 'onUploadClick'
        }
    ],
    defaults: {
        sortable: true
    },
    columns: [
        {text: 'Имя архива', dataIndex: 'name'},
        {text: 'Количество файлов в архиве', dataIndex: 'cnt', flex: 1},
        {text: 'Размер архива', dataIndex: 'size', flex: 1},
        {text: 'Дата и время внесения последних изменений в архив', dataIndex: 'change_datetime', flex: 1},
        {
            text: 'Скачать',
            xtype: 'templatecolumn',
            tpl: '<a href="/download?id={id}" target="_blank">Скачать</a>',
            sortable: false,
        },
        {
            xtype: 'actioncolumn',
            sortable: false,
            items: [{
                iconCls: 'x-fa fa-trash',
                tooltip: 'Delete',
                handler: 'onRemoveClick'
            }, '-', {
                iconCls: 'x-fa fa-edit',
                tooltip: 'Edit',
                handler: 'onEditClick'
            }]
    }],
    bbar: {
        xtype: 'pagingtoolbar',
        bind: '{archives}',
        dock: 'bottom',
        displayInfo: true
    },
    listeners: {
        select: 'onItemSelected'
    },
});

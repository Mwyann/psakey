let ipcRenderer = require('electron').ipcRenderer;

window.ipc = {
    toggleDevTools: function() {
        ipcRenderer.send('toggleDevTools');
    },
    reload: function() {
        ipcRenderer.send('reload');
    }
};

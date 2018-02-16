const electron = require('electron')
// Module to control application life.
const app = electron.app
// Module to create native browser window.
const BrowserWindow = electron.BrowserWindow
const fs = require('fs')
const path = require('path')
const url = require('url')

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let mainWindow

let JScode = `
    function kd(d) {
        d.addEventListener("keydown", function (e) {
            if (e.which === 123) { // F12
                window.ipc.toggleDevTools();
            } else if (e.which === 116) { // F5
                window.ipc.reload();
            }
        });
	}
	
    var iframes = document.getElementsByTagName('IFRAME');
    for (var f in iframes) if (iframes.hasOwnProperty(f)) {
        kd(iframes[f].contentDocument);
    }
	
	document.body.style.margin="0";
`;

function loadPSA() {
    mainWindow.webContents.session.clearCache(function(){
        mainWindow.loadURL('http://pms-onboard.mpsa.com/emulator.html');
        mainWindow.webContents.executeJavaScript(JScode);
    });
}

function createWindow () {
  // Create the browser window.
  mainWindow = new BrowserWindow({
      width: 800,
      height: 480,
      useContentSize: true,
      webPreferences: {
          nodeIntegration: false,
          preload: __dirname+'/preload.js',
          textAreasAreResizable: false,
          webgl: false,
          webaudio: false
      }
  });

  // Set proxy and load the index.html of the app.
  mainWindow.webContents.session.setProxy({proxyRules:"http://SET.PSAKEY.IP.HERE:8080"}, function () {
    loadPSA();
  });

  // Open the DevTools.
  mainWindow.webContents.openDevTools({"mode":'detach'});

  // Emitted when the window is closed.
  mainWindow.on('closed', function () {
    // Dereference the window object, usually you would store windows
    // in an array if your app supports multi windows, this is the time
    // when you should delete the corresponding element.
    mainWindow = null
  })
}

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
app.on('ready', createWindow)

// Quit when all windows are closed.
app.on('window-all-closed', function () {
  // On OS X it is common for applications and their menu bar
  // to stay active until the user quits explicitly with Cmd + Q
  if (process.platform !== 'darwin') {
    app.quit()
  }
})

app.on('activate', function () {
  // On OS X it's common to re-create a window in the app when the
  // dock icon is clicked and there are no other windows open.
  if (mainWindow === null) {
    createWindow()
  }
})

const ipcMain = require('electron').ipcMain;
ipcMain.on('toggleDevTools', function(event, arg) {
  mainWindow.webContents.openDevTools({"mode":'detach'});
});

ipcMain.on('reload', function(event, arg) {
  loadPSA();
});


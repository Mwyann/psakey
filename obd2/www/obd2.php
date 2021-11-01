<!DOCTYPE html><html><head><meta charset="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"><title>Widget Engine</title>
<script type="text/javascript">
    var updateErrors = 0; // will become a function
    var errors = ''; // error list
    window.onerror = function (msg, url, lineNo, columnNo, error) {
        errors = errors + '<br/>' + msg + ' on ' + url + ' line ' +lineNo;
        if (updateErrors) updateErrors();

        return false;
    }
</script>
<style>
body {
height: 434px;
margin: 46px 0 0 0;
position:relative;
/*background:#000000;*/
    /*font-family: 'Gill Sans PSA', arial, sans-serif;
    font-size: 14px;*/
    color:#FFFFFF;
}
a{
    color:#FF0;
}

#infos {
    position: absolute;
    left: 100px;
    top: 0;
    width: 600px;
}

#infos .bloc {
    float: left;
    width: 136px;
    height: 94px;
    margin: 0 0 1px 1px;
    border: 1px white solid;
    padding: 18px 5px;
    text-align: center;
}

#infos .bloc.pos1 {
    border-color: yellow;
}

#infos .bloc.pos2 {
    border-color: cyan;
}

#infos .bloc .valeur {
    font-size: 300%;
    padding-top: 10px;
    color: white;
}

#infos .bloc.small {
    float: left;
    width: 136px;
    height: 45px;
    margin: 0 0 1px 1px;
    border: 1px white solid;
    padding: 10px 5px;
    text-align: center;
}
#infos .bloc.small .titre {
    padding-bottom: 5px;
}
#infos .bloc.small .valeur {
    font-size: 150%;
    padding-top: 5px;
    color: white;
    display:inline;
}
#infos .bloc.small .unite {
    font-size: 150%;
    padding-left: 5px;
    display:inline;
}

.graph {
    position: absolute;
    width: 296px;
    height: 130px;
    margin: 1px 1px 5px 1px;
    border-style: solid;
    border-color: white;
    border-width: 0 0 1px 1px;
    overflow-x: hidden;
}

.graph.pos1 {
    top: 0;
}

.graph.pos2 {
    top: 133px;
}

.graph.hidden {
    display:none;
}

.graph.selected {
    border-color: red;
}

.graph .graphline {
    position:absolute;
    width: 1px;
}

.graph.pos1 .graphline {
    background: yellow;
}

.graph.pos2 .graphline {
    background:cyan;
}

.graph .graphimg {
    width: 40px;
    height: 130px;
}

.graph.pos1 .graphimg {
    background-position: 0 0;
}

.graph.pos2 .graphimg {
    background-position: 0 130px;
}

.graph-inner {
    position:absolute;
    width: 0;
    height: 100%;
    bottom: 0;
    right: 0;
}

</style>
</head>
<body>
<div style="position:absolute;left:18px;top:34px"><a href="main.php" onclick="document.body.style.marginTop='-1000px'"><img src="backIconPopup.png"/></a></div>
<div style="position:absolute;right:16px;top:92px"><a href="/repo/widgetengine/2.3.16/main2-fr.html" target="_top" id="gotosmeg" onclick="document.body.style.marginTop='-1000px'"><img src="pca.png" style="width:74px"/></a></div>
<div style="position:absolute;right:16px;top:211px"><a href="main2.php" onclick="document.body.style.marginTop='-1000px'"><img src="mwyann.png" style="width:74px"/></a></div>
<div style="position:absolute;right:16px;top:330px"><a href="obd2.php" onclick="document.body.style.marginTop='-1000px'"><img src="obd2.png" style="width:74px"/></a></div>
<div id="infos">
    <div>
        <div style="float: right; margin: 5px 20px 0 0" id="obd2-status"></div>
        <h2 style="margin: 5px 0">Infos OBD2</h2>
    </div>
    <br style="clear:both"/>
    <div style="float:left; width:298px">
        <div class="bloc pos1" id="bloc-speed"><div class="titre">Vitesse</div><div id="obd2-speed" class="valeur"></div><div class="unite">Km/h</div></div>
        <div class="bloc pos2" id="bloc-rpm"><div class="titre">Compte-tours</div><div id="obd2-rpm" class="valeur"></div><div class="unite">tr/min</div></div>
        <div class="bloc" id="bloc-engine-load"><div class="titre">Charge (calculée)</div><div id="obd2-engine-load" class="valeur"></div><div class="unite">%</div></div>
        <div class="bloc" id="bloc-maf"><div class="titre">MAF</div><div id="obd2-maf" class="valeur"></div><div class="unite">g/s</div></div>
        <br style="clear:both"/>
    </div>

    <div style="position: relative;float:left;">
        <br/>
        <div class="graph pos1" id="graph-speed">
            <div class="graph-inner" id="graph-inner-speed">
            </div>
        </div>
        <div class="graph pos2" id="graph-rpm">
            <div class="graph-inner" id="graph-inner-rpm">
            </div>
        </div>
        <div class="graph hidden" id="graph-engine-load">
            <div class="graph-inner" id="graph-inner-engine-load">
            </div>
        </div>
        <div class="graph hidden" id="graph-maf">
            <div class="graph-inner" id="graph-inner-maf">
            </div>
        </div>
    </div>
    <br style="clear:both"/>
    <div>
        <div class="bloc small"><div class="titre">Air ambiant</div><div id="obd2-ambiant-temp" class="valeur"></div><div class="unite">°C</div></div>
        <div class="bloc small"><div class="titre">Air injecté</div><div id="obd2-intake-temp" class="valeur"></div><div class="unite">°C</div></div>
        <div class="bloc small"><div class="titre">Refroidissement</div><div id="obd2-coolant-temp" class="valeur"></div><div class="unite">°C</div></div>
        <div class="bloc small"><div class="titre">Code défaut</div><div id="obd2-dtc" class="valeur">0</div><div class="unite">&nbsp;</div></div>
    </div>
</div>
</body>

<script>
(function(){
    var D = document, P = /complete|loaded|interactive/,
        onready = function(a) {
            P.test(D.readyState) ? a() : D.addEventListener("DOMContentLoaded", function () {
                a()
            }, !1);
        },
        runOBD2 = function() {
            var blocs = {};
            var bl = document.getElementsByClassName('valeur');
            blocs['obd2-status'] = document.getElementById('obd2-status');
            for (var i = 0; i < bl.length; i++) blocs[bl[i].getAttribute('id')] = bl[i];

            var graphs = {
                'speed': {
                    'inner': document.getElementById('graph-inner-speed'),
                    'current': 0,
                    'value': 0,
                    'previous': 0,
                    'valuelist': [0]
                },
                'rpm': {
                    'inner': document.getElementById('graph-inner-rpm'),
                    'current': 0,
                    'value': 0,
                    'previous': 0,
                    'valuelist': [0]
                },
                'engine-load': {
                    'inner': document.getElementById('graph-inner-engine-load'),
                    'current': 0,
                    'value': 0,
                    'previous': 0,
                    'valuelist': [0]
                },
                'maf': {
                    'inner': document.getElementById('graph-inner-maf'),
                    'current': 0,
                    'value': 0,
                    'previous': 0,
                    'valuelist': [0]
                },
            }
            var graphpos = 0;
            var selectGraph = function(){
                if (this.className.toString().indexOf('selected') > 0) {
                    this.className = this.className.toString().replace('selected','').trim();
                    return true;
                }

                var gr = document.getElementsByClassName('graph');
                for (var i = 0; i < gr.length; i++) gr[i].className = gr[i].className.toString().replace('selected','').trim();

                this.className = this.className + ' selected';
            }

            var toggleGraph = function(graphName) {
                var newgr = document.getElementById('graph-'+graphName);
                if (newgr.className.toString().indexOf('hidden') <= 0) return false;

                // Trouver le graph sélectionné
                var selectedgr = 0;
                var gr = document.getElementsByClassName('graph');
                for (var i = 0; i < gr.length; i++) if (gr[i].className.toString().indexOf('selected') > 0) selectedgr = gr[i];
                if (!gr) return false;

                // Trouver la position du graph sélectionné
                var pos = '';
                if (selectedgr.className.toString().indexOf('pos1') > 0) pos = 'pos1';
                if (selectedgr.className.toString().indexOf('pos2') > 0) pos = 'pos2';
                if (pos === '') return false;

                // Cacher l'ancien graph
                selectedgr.className = selectedgr.className.toString().replace('selected','').replace('pos1','').replace('pos2','').trim() + ' hidden';

                // Afficher le nouveau graph
                newgr.className = newgr.className.toString().replace('hidden','').trim() + ' ' + pos;

                var bl = document.getElementsByClassName('bloc');
                for (i = 0; i < bl.length; i++) bl[i].className = bl[i].className.toString().replace(pos,'').trim();
                bl = document.getElementById('bloc-'+graphName);
                bl.className = bl.className.toString() + ' ' + pos;
            }

            document.getElementById('bloc-speed').addEventListener('click', function() {toggleGraph('speed')});
            document.getElementById('bloc-rpm').addEventListener('click', function() {toggleGraph('rpm')});
            document.getElementById('bloc-engine-load').addEventListener('click', function() {toggleGraph('engine-load')});
            document.getElementById('bloc-maf').addEventListener('click', function() {toggleGraph('maf')});

            var graphElems = document.getElementsByClassName('graph');
            for (i = 0; i < graphElems.length; i++) graphElems[i].addEventListener('click', selectGraph);

            var checkOBD2 = 0;
            var dataid = 0;
            var doCheckOBD2 = function () {
                checkOBD2 = 0;
                var xhr = new XMLHttpRequest();

                var processResponse = function(response) {
                    var g;
                    var gn;
                    var values = response.toString().split(';');
                    for (v = 0; v < values.length; v++) if (values[v].toString().trim() !== '') {
                        var a = values[v].split('=');
                        if (a[0] === 'id') {
                            if (a[1] <= dataid) return true;
                            dataid = parseInt(a[1],10);
                        }
                        var e = blocs['obd2-'+a[0]];
                        if (a[1] === '') a[1] = '-';
                        else {
                            if ((a[0] == 'speed') ||
                                (a[0] == 'rpm') ||
                                (a[0] == 'ambiant-temp') ||
                                (a[0] == 'intake-temp') ||
                                (a[0] == 'coolant-temp')) a[1] = Math.round(a[1]);
                        }
                        if (e) e.innerText = a[1];

                        if (a[1] !== '-') {
                            // Graph
                            g = graphs[a[0]];
                            if (g) g['value'] = a[1];
                        }
                    }

                    // Cleanage régulier des éléments à l'écran
                    if (graphpos % 40 == 0) {
                        if (graphpos > 0) {
                            // Suppression des anciens points
                            var elems = document.getElementsByClassName('graphpos-'+(Math.round(graphpos/40)-2));
                            var l = elems.length;
                            for (var i = 0; i < l; i++) elems[0].parentNode.removeChild(elems[0]);

                            // Suppression des anciennes images
                            var elems = document.getElementsByClassName('graphimg-'+(Math.round(graphpos/40)-8));
                            var l = elems.length;
                            for (var i = 0; i < l; i++) elems[0].parentNode.removeChild(elems[0]);

                            // Ajout d'un graph image
                            for (gn in graphs) if (graphs.hasOwnProperty(gn)) {
                                g = graphs[gn];
                                var img = document.createElement('div');
                                img.style.cssText = 'position:absolute;top:0;right:-'+graphpos+'px;background-image:url(obd2-graph.php?p='+g['valuelist'].join(',')+')';
                                img.className = 'graphimg graphimg-'+Math.round(graphpos/40);
                                g['inner'].appendChild(img);
                                g['valuelist'] = [g['previous']];
                            }
                        }

                        // Création d'un nouveau div par graphique pour contenir les points
                        for (gn in graphs) if (graphs.hasOwnProperty(gn)) {
                            g = graphs[gn];
                            var d = document.createElement('div');
                            d.style.cssText = 'position:absolute;bottom:0;right:0';
                            d.className = 'graphpos-'+Math.round(graphpos/40);
                            g['inner'].appendChild(d);
                            g['current'] = d;
                        }
                    }

                    // Graph
                    for (gn in graphs) if (graphs.hasOwnProperty(gn)) {
                        g = graphs[gn];
                        var v = g['value'];
                        if (gn === 'rpm') v = Math.round(v/25);
                        if (gn === 'maf') v = Math.round(v*1.5);
                        //g['inner'].style.width=graphpos+'px';
                        g['inner'].style.right=graphpos+'px';
                        var p = document.createElement('div');
                        p.className = 'graphline';
                        p.style.cssText = 'height:'+(Math.max(1,Math.round(Math.abs(v-g['previous']))))+'px;left:'+graphpos+'px;bottom:'+Math.min(v, g['previous'])+'px';
                        g['current'].appendChild(p);
                        g['valuelist'].push(v);
                        g['previous'] = v;
                    }
                    graphpos++;
                }

                xhr.onreadystatechange = function(event) {
                    if (this.readyState === 4) { // XMLHttpRequest.DONE === 4
                        if (this.status === 200) {
                            var resp = this.responseText;
                            if (resp.indexOf('EOF=1') > 0) {
                                // Cacher les graphiques pour les remontrer après (est-ce que ça accélère le traitement ?
                                for (var i = 0; i < graphElems.length; i++) graphElems[i].className = graphElems[i].className + ' hidden';
                                var r = this.responseText.toString().split('EOF=1');
                                for(var vr = 0 ; vr < r.length-1 ; vr++) processResponse(r[vr]);
                                for (i = 0; i < graphElems.length; i++) graphElems[i].className = graphElems[i].className.toString().replace('hidden', '').trim();
                            }

                        }
                        checkOBD2 = setTimeout(doCheckOBD2,100);
                    }
                };
                xhr.open('GET', 'obd2-read.php', true);
                xhr.send(null);
            };

            if (checkOBD2 != 0) clearTimeout(checkOBD2);
            checkOBD2 = setTimeout(doCheckOBD2,100);
        };

    onready(runOBD2);
    var bl = document.getElementsByClassName('valeur');
    for (var i = 0; i < bl.length; i++) bl[i].innerHTML = '-';
})();

</script>

</html>

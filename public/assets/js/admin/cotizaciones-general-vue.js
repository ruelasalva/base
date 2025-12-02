// ===========================================
// COTIZACIONES - SCRIPT GENERAL DE SOPORTE GLOBAL
// ===========================================

// FUNCIÓN PARA MOSTRAR BOTÓN DE SINCRONIZACIÓN SI HAY COTIZACIONES OFFLINE
function mostrarBotonSincronizarSiPendientes() {
    localforage.getItem('cotizaciones_offline').then(cotis => {
        const btn = document.getElementById('btn-sincronizar-cotizaciones');
        if (!btn) return;
        btn.style.display = (cotis && cotis.length > 0) ? '' : 'none';
    });
}

// CONFIGURAR BOTÓN DE SINCRONIZACIÓN SI EXISTE EN LA VISTA
if (document.getElementById('btn-sincronizar-cotizaciones')) {
    mostrarBotonSincronizarSiPendientes();
    document.getElementById('btn-sincronizar-cotizaciones').onclick = function () {
        sincronizarCotizacionesPendientes();
    };
}

// FUNCIÓN GLOBAL PARA SINCRONIZAR COTIZACIONES GUARDADAS OFFLINE
window.sincronizarCotizacionesOffline = function (callback) {
    localforage.getItem('cotizaciones_offline').then(cotis => {
        if (!cotis || cotis.length === 0) {
            if (callback) callback('no_pendientes');
            return;
        }
        let enviadas = 0, errores = 0;

        const enviarPendientes = async () => {
            for (let i = 0; i < cotis.length; i++) {
                let cot = cotis[i];
                try {
                    let urlBase = '';
                    if (window._vueCotizacion && _vueCotizacion.urlBase) {
                        urlBase = _vueCotizacion.urlBase;
                    } else {
                        let el = document.getElementById('url-location');
                        if (el && el.dataset.url) {
                            urlBase = el.dataset.url;
                        }
                    }

                    if (!urlBase) throw new Error('No se encontró urlBase para sincronizar');

                    let res = await axios.post(
                        urlBase + 'finalizar_cotizacion',
                        cot.datos,
                        { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
                    );

                    if (res.data && res.data.msg === 'ok') {
                        cotis.splice(i, 1);
                        i--; enviadas++;
                    } else {
                        errores++;
                    }
                } catch (e) {
                    errores++;
                }
            }
            await localforage.setItem('cotizaciones_offline', cotis);
            if (callback) callback('completado', enviadas, errores);
        };

        enviarPendientes();
    });
};

// ESCUCHA AUTOMÁTICA AL VOLVER A CONECTARSE
window.addEventListener('online', function () {
    window.sincronizarCotizacionesOffline(function (status, enviadas, errores) {
        if (status === 'completado' && enviadas) {
            if (window.Swal) Swal.fire('¡Sincronizado!', enviadas + ' cotizaciones se enviaron.', 'success');
        }
    });
});

// BOTÓN DE SINCRONIZACIÓN MANUAL AL INICIO
localforage.getItem('cotizaciones_offline').then(cotis => {
    const btn = document.getElementById('btn-sincronizar-cotizaciones');
    if (btn && cotis && cotis.length) {
        btn.style.display = '';
        btn.onclick = function () {
            window.sincronizarCotizacionesOffline(function (status, enviadas, errores) {
                let msg = '';
                if (status === 'completado') {
                    msg = (enviadas ? enviadas + ' cotizaciones enviadas. ' : '') +
                        (errores ? errores + ' errores.' : '');
                    if (window.Swal) Swal.fire('Resultado', msg, 'info');
                }
                btn.style.display = 'none';
            });
        };
    }
});

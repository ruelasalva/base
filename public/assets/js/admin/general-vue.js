// ===============================
// GENERAL-VUE.JS - WIDGET GLOBAL DE CATÁLOGOS OFFLINE Y SINCRONIZACIÓN
// VERSIÓN JULIO 2025
// ===============================

if (!window.generalVueApp) {
    Vue.component('progress-bar', {
        props: ['progress'],
        template: `
            <div style="width:100%;background:#f3f3f3;border-radius:4px;height:6px;overflow:hidden;">
                <div :style="{ width: progress+'%', background:'#007bff', height:'6px' }"></div>
            </div>
        `
    });

    new Vue({
        el: '#general-vue-app',
        data: {
            init: false,
            minimizado: true,
            descargando: false,
            progreso: 0,
            mensaje: '',
            hayError: false,
            onlineStatus: navigator.onLine,
            catalogos: [
                { nombre: 'Productos', key: 'catalogo_products', loaded: false },
                { nombre: 'Precios Productos', key: 'catalogo_products_prices', loaded: false },
                { nombre: 'Socios', key: 'catalogo_partners', loaded: false },
                { nombre: 'Empleados', key: 'catalogo_empleados', loaded: false },
                { nombre: 'Pagos', key: 'catalogo_pagos', loaded: false },
                { nombre: 'Impuestos', key: 'catalogo_taxes', loaded: false },
                { nombre: 'Descuentos', key: 'catalogo_discounts', loaded: false },
                { nombre: 'Retenciones', key: 'catalogo_retentions', loaded: false },
                { nombre: 'Monedas', key: 'catalogo_currencies', loaded: false },
                { nombre: 'Estados', key: 'catalogo_estados', loaded: false },
                { nombre: 'Marcas', key: 'catalogo_brands', loaded: false }
            ],
            ultimaDescarga: '',
            huboCambio: false,
            revisionTimer: null,
            urlBase: document.getElementById('url-location')?.dataset?.url || '/index.php/admin/ajax/',
            cotizacionesPendientes: 0,
            sincronizando: false,
        },

        created() {
            // Mantiene el estado minimizado entre páginas
            this.minimizado = (localStorage.getItem('cat_widget_minimizado') ?? '1') === '1';
            // Estado rápido de catálogos
            let cached = localStorage.getItem('catalogos_loaded');
            if (cached) {
                try {
                    let loaded = JSON.parse(cached);
                    this.catalogos.forEach((cat, i) => cat.loaded = !!loaded[cat.key]);
                } catch (e) { }
            }

            // Carga estado inicial
            this.verificarCatalogos().then(() => {
                this.init = true;
                localforage.getItem('catalogos_fecha_actualizacion').then(f => {
                    this.ultimaDescarga = f || '';
                    this.checkAutoReload();
                });
            });

            // Revisión cada hora
            this.revisionTimer = setInterval(() => {
                this.checkAutoReload();
            }, 1000 * 60 * 60);

            this.contarCotizacionesOffline();
            // Si se reconecta, sincroniza automáticamente
            window.addEventListener('online', () => {
                this.contarCotizacionesOffline();
                this.sincronizarCotisOffline();
            });

            window.addEventListener('online', () => {
                this.onlineStatus = true;
                console.info("[APP] Reconectado");
                this.contarCotizacionesOffline();
                this.sincronizarCotisOffline();
            });

            window.addEventListener('offline', () => {
                this.onlineStatus = false;
                console.warn("[APP] Conexión perdida (offline detectado)");
            });
        },

        beforeDestroy() {
            if (this.revisionTimer) clearInterval(this.revisionTimer);
        },

        methods: {
            // Verifica estado en localforage SOLO UNA VEZ
            async verificarCatalogos() {
                let allLoaded = {};
                for (let cat of this.catalogos) {
                    let dato = await localforage.getItem(cat.key);
                    if (Array.isArray(dato)) {
                        cat.loaded = dato.length > 0;
                    } else if (dato && typeof dato === 'object') {
                        cat.loaded = Object.keys(dato).length > 0;
                    } else {
                        cat.loaded = !!dato;
                    }
                    allLoaded[cat.key] = cat.loaded;
                }
                // Guarda en localStorage para que otros tabs/páginas lo lean rápido
                localStorage.setItem('catalogos_loaded', JSON.stringify(allLoaded));
            },

            // Revisa si requiere reload (al faltar algo o pasar 1hr)
            checkAutoReload() {
                let horaActual = new Date();
                let ultima = this.ultimaDescarga ? new Date(this.ultimaDescarga) : null;
                let falta = this.catalogos.some(c => !c.loaded);
                let horas = ultima ? ((horaActual.getTime() - ultima.getTime()) / 1000 / 60 / 60) : 999;
                this.huboCambio = (falta || horas >= 1);
            },

            // Descarga todos los catálogos y actualiza todo
            async descargarCatalogosBackground() {
                if (this.descargando) return;
                this.descargando = true;
                this.hayError = false;
                this.progreso = 0;
                this.mensaje = 'Descargando catálogos...';

                try {
                    let res = await axios.post(this.urlBase + 'catalogos_cotizaciones_completo', {
                        access_id: window.access_id || '',
                        access_token: window.access_token || ''
                    }, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

                    if (res.data.msg === 'ok') {
                        let total = this.catalogos.length;
                        let c = 0;
                        for (let cat of this.catalogos) {
                            let keyData = res.data[cat.key.replace('catalogo_', '')];
                            if (keyData) {
                                await localforage.setItem(cat.key, keyData);
                                cat.loaded = Array.isArray(keyData) ? keyData.length > 0 : !!keyData;
                            } else {
                                cat.loaded = false;
                            }
                            c++;
                            this.progreso = Math.round((c / total) * 100);
                        }
                        // Fecha de actualización
                        let fecha = (new Date()).toLocaleString('es-MX');
                        await localforage.setItem('catalogos_fecha_actualizacion', fecha);
                        this.ultimaDescarga = fecha;
                        this.mensaje = '¡Catálogos descargados!';
                        this.huboCambio = false;
                        // Actualiza caché rápido
                        let loadedFast = {};
                        this.catalogos.forEach(cat => loadedFast[cat.key] = cat.loaded);
                        localStorage.setItem('catalogos_loaded', JSON.stringify(loadedFast));
                    } else {
                        this.hayError = true;
                        this.mensaje = 'Error: ' + (res.data.msg || 'Error desconocido al descargar catálogos');
                    }
                } catch (e) {
                    this.hayError = true;
                    this.mensaje = 'Fallo de conexión al descargar catálogos';
                }
                this.descargando = false;
                setTimeout(() => { this.mensaje = ''; }, 2200);
            },

            // Descarga manual
            descargarAhora() {
                this.descargarCatalogosBackground();
                this.minimizado = false;
                localStorage.setItem('cat_widget_minimizado', '0');
            },

            // Mostrar/ocultar detalle
            toggleDetalle() {
                this.minimizado = !this.minimizado;
                localStorage.setItem('cat_widget_minimizado', this.minimizado ? '1' : '0');
                if (!this.minimizado) {
                    this.verificarCatalogos();
                    this.contarCotizacionesOffline();
                }
            },

            // Cerrar (minimiza)
            cerrar() {
                this.minimizado = true;
                localStorage.setItem('cat_widget_minimizado', '1');
            },

            // === SINCRONIZACIÓN DE COTIZACIONES OFFLINE ===
            async contarCotizacionesOffline() {
                let cotis = await localforage.getItem('cotizaciones_offline');
                this.cotizacionesPendientes = cotis ? cotis.length : 0;
            },

            async sincronizarCotisOffline() {
                if (this.sincronizando) return;
                let cotis = await localforage.getItem('cotizaciones_offline');
                if (!cotis || cotis.length === 0) {
                    this.contarCotizacionesOffline();
                    return;
                }
                this.sincronizando = true;
                let exitosas = 0, fallidas = 0;
                for (let i = 0; i < cotis.length; i++) {
                    let cot = cotis[i];
                    try {
                        let res = await axios.post('/sajor/index.php/admin/ajax/finalizar_cotizacion', cot.datos, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (res.data && res.data.msg === 'ok') {
                            cotis.splice(i, 1); i--; exitosas++;
                        } else {
                            fallidas++;
                        }
                    } catch (e) {
                        fallidas++; break; // si hay error de red, detén el ciclo
                    }
                }
                await localforage.setItem('cotizaciones_offline', cotis);
                this.contarCotizacionesOffline();
                this.sincronizando = false;
            }
        },

        template: `
        <transition name="fade">
        <div v-if="init"
            style="position:fixed;bottom:22px;right:34px;z-index:9999;min-width:55px;">

            <!-- ÍCONO FLAT MINI -->
            <div v-if="!onlineStatus"
                style="position:fixed;top:0;left:0;width:100%;background:#dc3545;color:#fff;
                        text-align:center;padding:5px;z-index:99999;font-weight:bold;">
            ⚠ Estás sin conexión. Los cambios de las cotizaciones se guardarán offline.
            </div>

            <div v-if="minimizado" @click="toggleDetalle"
                style="background:#fff;box-shadow:0 1px 8px #0002;border-radius:50%;width:54px;height:54px;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;border:1px solid #ddd;">
                <i class="fa fa-database" style="font-size:25px;color:#007bff;"></i>
                <span v-if="huboCambio" style="position:absolute;top:10px;right:10px;width:13px;height:13px;background:#28a745;border-radius:50%;box-shadow:0 0 4px #6c6c6c;border:2px solid #fff"></span>
                <span v-else-if="descargando" style="position:absolute;top:10px;right:10px;width:13px;height:13px;background:#ffc107;border-radius:50%;border:2px solid #fff"></span>
                <span v-else style="position:absolute;top:10px;right:10px;width:13px;height:13px;background:#bdbdbd;border-radius:50%;border:2px solid #fff"></span>
                <!-- NUMERO DE COTIZACIONES PENDIENTES -->
                <span v-if="cotizacionesPendientes>0"
                      style="position:absolute;bottom:6px;right:6px;min-width:16px;height:16px;line-height:16px;font-size:11px;background:#ff9800;color:#fff;border-radius:9px;text-align:center;padding:0 4px;font-weight:bold;box-shadow:0 1px 5px #0003;">
                  {{ cotizacionesPendientes }}
                </span>
            </div>

            <!-- PANEL EXPANDIDO (DETALLE) -->
            <div v-else
                style="background:#fff; border:1px solid #ddd; border-radius:16px; box-shadow:0 2px 12px #0002; padding:22px 22px 14px 22px; min-width:295px; max-width:355px; position:relative;">
                <button @click="cerrar" title="Cerrar"
                        style="background:none; border:none; color:#888; position:absolute; top:8px; right:13px; font-size:22px; cursor:pointer;">×</button>
                <div style="font-weight:bold; color:#007bff; margin-bottom:10px;">
                    <i class="fa fa-database"></i> Catálogos offline
                </div>
                <progress-bar v-if="descargando" :progress="progreso" />
                <div v-if="mensaje" :style="{color: hayError ? '#d9534f' : '#5cb85c', fontSize:'13px'}">{{ mensaje }}</div>
                <ul style="margin-bottom: 11px; padding-left: 14px;">
                    <li v-for="cat in catalogos" :key="cat.key" style="font-size:15px;">
                        <i :class="cat.loaded ? 'fa fa-check-circle text-success' : 'fa fa-times-circle text-danger'"></i>
                        {{ cat.nombre }} <span v-if="cat.loaded" style="color: #5cb85c;">(Listo)</span>
                        <span v-else style="color: #d9534f;">(No disponible)</span>
                    </li>
                </ul>
                <!-- SUBAPARTADO DE SINCRONIZACIÓN -->
                <div v-if="cotizacionesPendientes>0" style="margin:14px 0 8px 0; padding:7px 11px; background:#fffaeb; border-radius:8px; border:1px solid #ffe0b2;">
                    <b><i class="fa fa-exclamation-triangle" style="color:#ff9800;"></i> Cotizaciones pendientes por sincronizar:</b>
                    <div style="margin:7px 0 3px 0; color:#555;">Tienes <b>{{ cotizacionesPendientes }}</b> cotizaciones offline guardadas.<br>Se enviarán automáticamente al reconectarte.</div>
                    <button @click="sincronizarCotisOffline" :disabled="sincronizando"
                        style="margin-top:7px; background:#ff9800;color:#fff;border:none;padding:5px 15px;border-radius:7px;font-weight:bold;">
                        <i class="fa fa-refresh"></i> Sincronizar ahora
                    </button>
                </div>
                <div style="font-size:13px;"><b>Última actualización:</b> <span>{{ ultimaDescarga || 'Sin datos' }}</span></div>
                <button @click="descargarAhora" :disabled="descargando"
                    style="margin-top: 10px; background: #007bff; color: #fff; border: none; padding: 7px 16px; border-radius: 7px; font-weight: bold;">
                    <i class="fa fa-refresh"></i> Descargar manualmente
                </button>
            </div>
        </div>
        </transition>
        `
    });

    window.generalVueApp = Vue.prototype.$generalVueApp = document.querySelector('#general-vue-app') ? Vue : null;
}


// Asegúrate de que Vue y Axios estén cargados globalmente o importados si usas módulos.
// Por ejemplo, si usas CDN, ya estarán disponibles:
// <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
// <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

if (document.getElementById('notifications-app')) {
    new Vue({
        el: '#notifications-app',
        data: {
            notifications: [],      // Lista de notificaciones a mostrar en el dropdown
            unreadCount: 0,         // Conteo de notificaciones no leídas
            dropdownOpen: false,    // Estado del dropdown de notificaciones
            timer: null,            // Para el intervalo de actualización
            urlBase: document.getElementById('url-location')?.dataset?.url || '/index.php/admin/ajax/', // URL base para las llamadas AJAX
            loading: false,         // Indicador de carga
            shownNotificationIds: [], // IDs de notificaciones que ya se mostraron al usuario vía browser notification
        },
        created() {
            // Cargar IDs de notificaciones ya mostradas desde localStorage al iniciar
            const storedShownIds = localStorage.getItem('shownNotificationIds');
            if (storedShownIds) {
                try {
                    this.shownNotificationIds = JSON.parse(storedShownIds);
                } catch (e) {
                    console.error("Error al parsear shownNotificationIds de localStorage:", e);
                    this.shownNotificationIds = []; // Resetear si hay un error
                }
            }

            // Iniciar la primera carga de notificaciones y establecer el intervalo
            this.fetchNotifications();
            this.timer = setInterval(this.fetchNotifications, 2400000); // Cada 60 segundos

            // Solicitar permiso para notificaciones del navegador si aún no se ha otorgado
            if ('Notification' in window && Notification.permission !== 'granted') {
                Notification.requestPermission();
            }

            // Añadir listener global para cerrar el dropdown al hacer clic fuera
            document.addEventListener('click', this.handleOutsideClick);
        },
        beforeDestroy() {
            // Limpiar el temporizador y el listener de eventos al destruir el componente
            if (this.timer) clearInterval(this.timer);
            document.removeEventListener('click', this.handleOutsideClick);
        },
        methods: {
            // Método para obtener las notificaciones del backend
            fetchNotifications() {
                this.loading = true;
                axios.post(this.urlBase + 'get_notifications')
                    .then(res => {
                        if (res.data && res.data.success) {
                            const newNotificationsFromServer = res.data.notifications || [];

                            // Identifica las notificaciones que son nuevas en el servidor
                            // Y que no existen en tu lista actual de `this.notifications`
                            // Y que NO han sido mostradas ya por el navegador (usando shownNotificationIds)
                            const trulyNewNotifications = newNotificationsFromServer.filter(
                                serverNotif =>
                                    !this.notifications.some(localNotif => localNotif.id === serverNotif.id) && // No está en la lista actual de Vue
                                    !this.shownNotificationIds.includes(serverNotif.id) // NO ha sido mostrada ya en el navegador
                            );

                            // Muestra notificaciones del navegador solo para las que son '0' (no leídas)
                            trulyNewNotifications.forEach(n => {
                                if (n.status === '0') {
                                    this.showBrowserNotification(n.title, this.stripHtml(n.message));
                                    // Añadir el ID del recipient a la lista de notificaciones ya mostradas
                                    this.shownNotificationIds.push(n.id);
                                }
                            });
                            // Guardar los IDs actualizados en localStorage después de mostrar todas las nuevas
                            localStorage.setItem('shownNotificationIds', JSON.stringify(this.shownNotificationIds));


                            // Actualiza la lista completa de notificaciones en el frontend
                            this.notifications = newNotificationsFromServer;
                            // Recalcula el conteo de no leídas
                            this.unreadCount = this.notifications.filter(n => n.status === '0').length;
                        }
                    })
                    .catch(error => {
                        console.error("Error al obtener notificaciones:", error);
                        // Opcional: Mostrar un mensaje de error al usuario
                    })
                    .finally(() => this.loading = false);
            },

            // Elimina etiquetas HTML de un string para mostrar solo texto
            stripHtml(html) {
                var div = document.createElement("div");
                div.innerHTML = html;
                return div.textContent || div.innerText || "";
            },

            // Muestra una notificación nativa del navegador
            showBrowserNotification(title, message) {
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(title, {
                        body: message,
                        icon: '/assets/img/ico144.png' // Ajusta la ruta a tu icono
                    });
                }
            },

            // Alterna la visibilidad del dropdown de notificaciones
            toggleDropdown(event) {
                event.stopPropagation(); // Evita que el clic se propague y cierre el dropdown inmediatamente
                this.dropdownOpen = !this.dropdownOpen;
            },

            // Cierra el dropdown de notificaciones
            closeDropdown() {
                this.dropdownOpen = false;
            },

            // Maneja clics fuera del dropdown para cerrarlo
            handleOutsideClick(event) {
                // Si el clic no fue dentro del componente de notificaciones, cierra el dropdown
                if (this.$el && !this.$el.contains(event.target)) {
                    this.closeDropdown();
                }
            },

            // Marca una notificación como leída en el backend y actualiza el frontend
            markAsRead(notification) {
                // Prepara los datos como x-www-form-urlencoded
                const params = new URLSearchParams();
                // Envía el ID del registro de notification_recipient
                params.append('id', notification.id);

                axios.post(this.urlBase + 'mark_notification_read', params, {
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                    .then(res => {
                        if (res.data && res.data.success) {
                            // Si la operación fue exitosa en el backend, actualiza el estado en el frontend
                            const index = this.notifications.findIndex(n => n.id === notification.id);
                            if (index !== -1) {
                                this.$set(this.notifications[index], 'status', '1'); // Marcar como leída
                                this.$set(this.notifications[index], 'read_at', new Date().toISOString().slice(0, 19).replace('T', ' ')); // Opcional: actualizar read_at
                            }
                            // Recalcula el conteo de no leídas
                            this.unreadCount = this.notifications.filter(n => n.status === '0').length;

                            // Asegúrate de que esta notificación también se considere "mostrada"
                            // si el usuario la marcó como leída antes de que la notificación de navegador se disparara automáticamente.
                            if (!this.shownNotificationIds.includes(notification.id)) {
                                this.shownNotificationIds.push(notification.id);
                                localStorage.setItem('shownNotificationIds', JSON.stringify(this.shownNotificationIds));
                            }

                            // Puedes mostrar una notificación de navegador de confirmación si lo deseas
                            // this.showBrowserNotification('Notificación marcada como leída', this.stripHtml(notification.title));
                        } else {
                            console.error("Error al marcar como leída:", res.data.message || "Error desconocido");
                            // Opcional: Mostrar un mensaje de error al usuario
                        }
                    })
                    .catch(error => {
                        console.error("Error en la solicitud AJAX (marcar como leída):", error);
                        // Manejo de errores de red o del servidor
                    });
            },

            // Devuelve la clase del icono de la notificación
            notificationIcon(icon) {
                return icon ? `fa ${icon}` : 'fa fa-info-circle'; // Default a fa-info-circle si no hay icono
            }
        },
        template: `
            <div class="notifications-header" style="position: relative; display: inline-block;">
                <button @click="toggleDropdown" class="btn btn-link p-0 position-relative" style="outline:none;">
                    <i class="fa fa-bell" style="font-size:20px; color:#4267b2;"></i>
                    <span v-if="unreadCount > 0"
                    class="badge badge-danger"
                    style="position:absolute;top:-7px;right:-7px;font-size:11px;padding:3px 6px;border-radius:9px;">
                    {{ unreadCount }}
                    </span>
                </button>
                <div v-if="dropdownOpen" class="notifications-dropdown shadow"
                    style="position: absolute; right: 0; top: 115%; width: 350px; background: #fff; border:1px solid #e5e5e5; z-index:9999; border-radius:10px; box-shadow:0 4px 18px #0002;">
                    <div class="dropdown-header d-flex align-items-center p-2" style="background: #f8fafd; border-bottom:1px solid #ececec; border-radius:10px 10px 0 0;">
                        <span>
                            <i class="fa fa-bell text-warning mr-1" style="font-size:17px;"></i>
                            <span style="font-size:16px; color:#3a4678; font-weight:bold; letter-spacing:.5px;">NOTIFICACIONES</span>
                        </span>
                        <button class="btn btn-sm btn-light ml-auto" style="font-size:20px; line-height:13px;" @click="closeDropdown">&times;</button>
                    </div>
                    <div v-if="notifications.length > 0" style="max-height:320px;overflow:auto;">
                        <ul class="list-group list-group-flush">
                            <li v-for="notif in notifications" :key="notif.id"
                                class="list-group-item"
                                :style="{background: notif.status === '0' ? '#fdfdfc' : '#fafbfc', borderBottom:'1px solid #f2f2f2', padding:'15px 14px'}">
                                <div class="d-flex align-items-start">
                                    <span :class="notificationIcon(notif.icon)" style="font-size:18px; color:#007bff; margin-right:8px; margin-top:2px;"></span>
                                    <div style="flex:1;">
                                        <div style="font-weight:600;font-size:15px;color:#234;">{{ notif.title }}</div>
                                        <div style="font-size:13px; color:#555; margin:3px 0 5px 0;" v-html="notif.message"></div>
                                        <div style="font-size:12px; color:#999;">{{ notif.created_at }}</div>
                                        <div style="margin-top:5px;">
                                            <a v-if="notif.url" :href="notif.url" target="_blank"
                                            style="font-size:13px; color:#446cb3; font-weight:500; margin-right:10px;">View</a>
                                            <a v-if="notif.status === '0'" href="#" @click.prevent="markAsRead(notif)"
                                            style="font-size:13px; color:#3876d8; font-weight:500;">Marcar como leido</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div v-else class="p-4 text-center text-muted" style="font-size:14px;">
                        <i class="fa fa-inbox" style="font-size:28px;opacity:.25;"></i>
                        <div>Sin notificaciones</div>
                    </div>
                </div>
            </div>
        `
    });
}

//* ===============================
//* GESTIÓN DE COOKIES (PRIVACIDAD)
//* ===============================
if (document.getElementById('cookies-app')) {
    new Vue({
        el: '#cookies-app',
        data: {
            prefs: {
                necessary: 1, // 1 = rechazadas por defecto
                analytics: 1,
                marketing: 1,
                personalization: 1
            },
            mostrarModal: false,

            urlBase: document.getElementById('url-location')?.dataset?.url || '/index.php/admin/ajax/',
        },
        created() {
            axios.post(this.urlBase + 'get_cookies_prefs', {
                access_id: access_id,
                access_token: access_token
            }).then(res => {
                if (res.data && res.data.success && res.data.prefs) {
                    // 👇 asegurar que son enteros
                    this.prefs = {
                        necessary: parseInt(res.data.prefs.necessary),
                        analytics: parseInt(res.data.prefs.analytics),
                        marketing: parseInt(res.data.prefs.marketing),
                        personalization: parseInt(res.data.prefs.personalization),
                    };

                    console.log("DEBUG PREFS CREADAS:", this.prefs);
                    // Si rechazó necesarias → volver a mostrar modal
                    if (this.prefs.necessary != 0) {
                        this.mostrarModal = true;
                        document.body.classList.add('cookies-blocked');
                    } else {
                        this.mostrarModal = false;
                        document.body.classList.remove('cookies-blocked');
                    }
                } else {
                    // Primera vez → forzar modal
                    this.mostrarModal = true;
                    document.body.classList.add('cookies-blocked');
                }
            });
        },

        methods: {
            aceptarTodo() {
                this.prefs.necessary = 0; 
                this.prefs.analytics = 0;
                this.prefs.marketing = 0;
                this.prefs.personalization = 0;
                this.guardar();
            },
            rechazarTodo() {
                this.prefs.necessary = 1; 
                this.prefs.analytics = 1;
                this.prefs.marketing = 1;
                this.prefs.personalization = 1;
                this.guardar();
            },
            guardar() {
                axios.post(this.urlBase + 'update_cookies_prefs', this.prefs)
                    .then(res => {
                        // Si aceptó necesarias
                        if (this.prefs.necessary === 0) {
                            this.mostrarModal = false;
                            document.body.classList.remove('cookies-blocked', 'cookies-overlay');
                        } else {
                            // No aceptó necesarias → forzar logout
                            document.body.classList.remove('cookies-blocked', 'cookies-overlay');
                            window.location.href = this.urlBase.replace('ajax/', '') + 'logout';
                        }
                    })
                    .catch(() => {
                        // En caso de error quitamos el overlay para no bloquear al usuario
                        document.body.classList.remove('cookies-blocked', 'cookies-overlay');
                    });
            }
        },
        template: `
        <div v-if="mostrarModal" class="cookies-overlay">
            <div class="cookies-modal card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <i class="fa fa-cookie"></i> Preferencias de Cookies
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        Usamos cookies para mejorar tu experiencia.
                        Puedes aceptar todas, rechazarlas o configurar tus preferencias.
                    </p>

                    <div class="alert alert-danger py-2">
                        ⚠ Debes aceptar al menos las cookies necesarias para continuar en el sistema.
                        Si seleccionas <b>Rechazar todas</b>, tu sesión será cerrada.
                    </div>

                    <!-- Necessary -->
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="cookies_necessary"
                            v-model="prefs.necessary"
                            :true-value="0" :false-value="1"
                            :disabled="prefs.necessary == 0">
                        <label class="form-check-label" for="cookies_necessary">
                            Cookies necesarias (capturan IP y navegador, obligatorias para continuar)
                        </label>
                    </div>

                    <!-- Analytics -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="cookies_analytics"
                            v-model="prefs.analytics" :true-value="0" :false-value="1">
                        <label class="form-check-label" for="cookies_analytics">Permitir cookies de analítica</label>
                    </div>

                    <!-- Marketing -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="cookies_marketing"
                            v-model="prefs.marketing" :true-value="0" :false-value="1">
                        <label class="form-check-label" for="cookies_marketing">Permitir cookies de marketing</label>
                    </div>

                    <!-- Personalización -->
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="cookies_personalization"
                            v-model="prefs.personalization" :true-value="0" :false-value="1">
                        <label class="form-check-label" for="cookies_personalization">Permitir cookies de personalización</label>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button class="btn btn-secondary btn-sm" @click="rechazarTodo">Rechazar todas</button>
                    <button class="btn btn-success btn-sm" @click="aceptarTodo">Aceptar todas</button>
                    <button class="btn btn-primary btn-sm" @click="guardar">Guardar selección</button>
                </div>
            </div>
        </div>
        `
    });
}



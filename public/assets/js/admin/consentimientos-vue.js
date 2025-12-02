const urlBase = document.getElementById('url-location').dataset.url;

if (document.getElementById('consent-app')) {
    new Vue({
        el: '#consent-app',
        data: {
            user_id: '',
            document_id: '',
            accepted: 0,
            channel: 'web',
            extra: '',
            users: [],
            documents: [],
            channels: [],
            loading: true,
            saving: false,
            load_error: false
        },
        mounted() {
            this.cargarCatalogos();
        },
        methods: {
            cargarCatalogos() {
                this.loading = true;
                axios.post(urlBase + 'catalogos_consentimientos', {}, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => {
                        if (res.data.msg === 'ok') {
                            this.users = res.data.users;
                            this.documents = res.data.documents;
                            this.channels = res.data.channels;
                            this.channel = this.channels[0]?.id || 'web';
                        } else {
                            Swal.fire('Error', res.data.msg, 'error');
                            this.load_error = true;
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'No se pudieron cargar los catálogos', 'error');
                        this.load_error = true;
                    })
                    .finally(() => this.loading = false);
            },
            guardarConsent() {
                if (!this.user_id || !this.document_id) {
                    Swal.fire('Campos obligatorios', 'Selecciona usuario y documento', 'warning');
                    return;
                }
                this.saving = true;
                axios.post(urlBase + 'save', {
                    user_id: this.user_id,
                    document_id: this.document_id,
                    accepted: this.accepted,
                    channel: this.channel,
                    extra: this.extra
                }, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(res => {
                        if (res.data.status === 'ok') {
                            Swal.fire('¡Guardado!', 'El consentimiento fue registrado.', 'success');
                            this.user_id = '';
                            this.document_id = '';
                            this.accepted = 0;
                            this.channel = this.channels[0]?.id || 'web';
                            this.extra = '';
                        } else {
                            Swal.fire('Error', res.data.msg || 'No se pudo guardar', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Error', 'Error al enviar la solicitud', 'error');
                    })
                    .finally(() => this.saving = false);
            }
        }
    });
}

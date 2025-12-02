/**
 * ================================================================
 * MERCADO LIBRE – ADMINISTRACIÓN DE IMÁGENES ML (Vue.js)
 * ERP SAJOR – Módulo ML
 * ================================================================
 */

document.addEventListener('DOMContentLoaded', function () {

    if (typeof Vue === 'undefined' || typeof axios === 'undefined') {
        console.error('[ML][IMG] Vue o Axios no están cargados.');
        return;
    }

    const el = document.getElementById('ml-images-app');
    const urlLocation = document.getElementById('url-location');

    if (!el || !urlLocation) {
        console.warn('[ML][IMG] No existe ml-images-app o url-location.');
        return;
    }

    const ajaxBase = '<?= Uri::create("admin/ajax/"); ?>';

    const vm = new Vue({
        el: '#ml-images-app',

        data() {
            return {
                mlProductId: window.ML_ATTR_CONFIG.productId || null,
                images: [],
                primaryId: null,
                newUrl: '',
                loading: false,
                errors: [],
                draggingIndex: null
            };
        },

        mounted() {
            if (this.mlProductId) {
                this.fetchImages();
            } else {
                console.warn('[ML][IMG] mlProductId vacío, no se cargarán imágenes.');
            }
        },

        methods: {
            api(endpoint) {
                return ajaxBase + endpoint;
            },

            // ================================
            // 1. Usar imagen del sistema local
            // ================================
            addFromLocal(url) {
                if (!this.mlProductId) {
                    Swal.fire('Guarda primero la configuración ML del producto', '', 'warning');
                    return;
                }

                axios.post(
                    this.api('add_image_ml'),
                    {
                        ml_product_id: this.mlProductId,
                        url: url,
                        access_id: window.access_id,
                        access_token: window.access_token
                    },
                    { headers: { "X-Requested-With": "XMLHttpRequest" } }
                )
                    .then(res => {
                        if (res.data.success) {
                            this.fetchImages();
                            Swal.fire('Imagen agregada a ML', '', 'success');
                        } else {
                            Swal.fire('Error', res.data.msg || 'No se pudo agregar la imagen', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('[ML][IMG][addFromLocal][ERROR]', err);
                        Swal.fire('Error al agregar imagen', '', 'error');
                    });
            },

            // ================================
            // 2. Subir archivo local
            // ================================
            uploadImage(event) {
                const file = event.target.files[0];
                if (!file) return;

                let formData = new FormData();
                formData.append('file', file);
                formData.append('ml_product_id', this.mlProductId);
                formData.append('access_id', window.access_id);
                formData.append('access_token', window.access_token);

                axios.post(
                    this.api('upload_image_ml'),
                    formData,
                    {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Content-Type": "multipart/form-data"
                        }
                    }
                )
                    .then(res => {
                        if (res.data.success) {
                            Swal.fire('Imagen subida', '', 'success');
                            this.fetchImages();
                        } else {
                            Swal.fire('Error', res.data.msg || 'No se pudo subir la imagen', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('[ML][IMG][uploadImage][ERROR]', err);
                        Swal.fire('Error', 'No se pudo subir la imagen', 'error');
                    });
            },

            // ================================
            // 3. Obtener imágenes
            // ================================
            fetchImages() {
                this.loading = true;
                this.errors = [];

                axios.post(
                    this.api('get_images_ml'),
                    {
                        ml_product_id: this.mlProductId,
                        access_id: window.access_id,
                        access_token: window.access_token
                    },
                    { headers: { "X-Requested-With": "XMLHttpRequest" } }
                )
                    .then(res => {
                        if (!res.data.success) {
                            this.errors.push(res.data.msg || 'Error al obtener imágenes.');
                            return;
                        }

                        this.images = res.data.images || [];
                        this.primaryId = res.data.primary_id || null;
                    })
                    .catch(err => {
                        console.error('[ML][IMG][FETCH][ERROR]', err);
                        this.errors.push('Error de comunicación al obtener imágenes.');
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            },

            // ================================
            // 4. Agregar imagen por URL
            // ================================
            addImage() {
                if (!this.newUrl.trim()) {
                    Swal.fire('URL inválida', 'Ingresa la URL de la imagen.', 'warning');
                    return;
                }

                axios.post(
                    this.api('add_image_ml'),
                    {
                        ml_product_id: this.mlProductId,
                        url: this.newUrl,
                        access_id: window.access_id,
                        access_token: window.access_token
                    },
                    { headers: { "X-Requested-With": "XMLHttpRequest" } }
                )
                    .then(res => {
                        if (!res.data.success) {
                            Swal.fire('Error', res.data.msg || 'No se pudo agregar la imagen.', 'error');
                            return;
                        }

                        this.newUrl = '';
                        this.fetchImages();
                    })
                    .catch(err => {
                        console.error('[ML][IMG][ADD][ERROR]', err);
                        Swal.fire('Error', 'No se pudo agregar la imagen.', 'error');
                    });
            },

            // ================================
            // 5. Cambiar principal
            // ================================
            setPrimary(id) {
                axios.post(
                    this.api('update_image_ml'),
                    {
                        id: id,
                        is_primary: 1,
                        sort_order: 0,
                        access_id: window.access_id,
                        access_token: window.access_token
                    },
                    { headers: { "X-Requested-With": "XMLHttpRequest" } }
                )
                    .then(res => {
                        if (!res.data.success) {
                            Swal.fire('Error', res.data.msg || 'No se pudo actualizar la imagen.', 'error');
                            return;
                        }

                        this.fetchImages();
                    })
                    .catch(err => {
                        console.error('[ML][IMG][PRIMARY][ERROR]', err);
                    });
            },

            // ================================
            // 6. Actualizar orden (input)
            // ================================
            updateOrder(img) {
                axios.post(
                    this.api('update_image_ml'),
                    {
                        id: img.id,
                        is_primary: img.id === this.primaryId ? 1 : 0,
                        sort_order: img.sort_order,
                        access_id: window.access_id,
                        access_token: window.access_token
                    },
                    { headers: { "X-Requested-With": "XMLHttpRequest" } }
                )
                    .then(res => {
                        if (!res.data.success) {
                            Swal.fire('Error', res.data.msg || 'No se pudo actualizar el orden.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('[ML][IMG][ORDER][ERROR]', err);
                    });
            },

            // ================================
            // 7. Eliminar imagen
            // ================================
            deleteImage(img) {
                Swal.fire({
                    title: 'Eliminar imagen',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    axios.post(
                        this.api('delete_image_ml'),
                        {
                            id: img.id,
                            access_id: window.access_id,
                            access_token: window.access_token
                        },
                        { headers: { "X-Requested-With": "XMLHttpRequest" } }
                    )
                        .then(res => {
                            if (!res.data.success) {
                                Swal.fire('Error', res.data.msg || 'No se pudo eliminar la imagen.', 'error');
                                return;
                            }

                            this.fetchImages();
                            Swal.fire('Imagen eliminada', '', 'success');
                        })
                        .catch(err => {
                            console.error('[ML][IMG][DELETE][ERROR]', err);
                            Swal.fire('Error', 'No se pudo eliminar la imagen.', 'error');
                        });
                });
            },

            // ================================
            // 8. Drag & Drop – start
            // ================================
            onDragStart(index) {
                this.draggingIndex = index;
            },

            // ================================
            // 9. Drag & Drop – drop
            // ================================
            onDrop(index) {
                if (this.draggingIndex === null) return;
                if (this.draggingIndex === index) {
                    this.draggingIndex = null;
                    return;
                }

                const dragged = this.images[this.draggingIndex];
                this.images.splice(this.draggingIndex, 1);
                this.images.splice(index, 0, dragged);
                this.draggingIndex = null;

                this.persistOrder();
            },

            // ================================
            // 10. Persistir orden drag & drop
            // ================================
            persistOrder() {
                // Recalcular sort_order secuencial
                this.images.forEach((img, idx) => {
                    img.sort_order = idx + 1;
                });

                const payload = this.images.map(img => ({
                    id: img.id,
                    sort_order: img.sort_order
                }));

                axios.post(
                    this.api('reorder_images_ml'),
                    {
                        images: payload,
                        access_id: window.access_id,
                        access_token: window.access_token
                    },
                    { headers: { "X-Requested-With": "XMLHttpRequest" } }
                )
                    .then(res => {
                        if (!res.data.success) {
                            Swal.fire('Error', res.data.msg || 'No se pudo guardar el orden.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('[ML][IMG][REORDER][ERROR]', err);
                    });
            }
        }
    });

    // Exponer para botones "Usar en ML" de la vista
    window.ML_IMAGES_APP = vm;
});

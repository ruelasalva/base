/* ============================================================
 * PLAN DE CUENTAS (VISTA JERÁRQUICA)
 * Integración completa con FuelPHP 1.8.2
 * Autor: Integración Sajor ERP
 * ------------------------------------------------------------
 * - Usa Vue 2.x
 * - Envía FormData (FuelPHP compatible)
 * - Incluye logs de depuración detallados
 * ============================================================ */

if (document.getElementById('plan-cuentas-app')) {
    const app = new Vue({
        el: '#plan-cuentas-app',

        /* ============================================================
         * DATOS REACTIVOS
         * ============================================================ */
        data() {
            return {
                urlBase: document.getElementById('url-location').dataset.url,
                loadingTree: false,
                saving: false,
                tree: [],
                flat: {},
                filtroTipo: '',
                tiposCuenta: [],
                currencies: [],
                accountsList: [],
                filtroCuenta: '',
                formMode: 'view', // view | create | edit
                formTitle: 'Detalles',
                form: {
                    id: null,
                    code: '',
                    name: '',
                    type: '',
                    parent_id: null,
                    level: 1,
                    currency_id: null,
                    is_confidential: 0,
                    is_cash_account: 0,
                    is_active: 1,
                    annex24_code: '',
                    account_class: ''
                }
            };
        },

        /* ============================================================
         * PROPIEDADES COMPUTADAS
         * ============================================================ */
        computed: {
            isEditable() {
                return this.formMode === 'edit' || this.formMode === 'create';
            },
            cuentasFiltradas() {
                if (!this.filtroTipo) return this.tree;

                const tipo = this.filtroTipo
                    .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // elimina acentos
                    .trim().toLowerCase();

                const filtrar = (arr) =>
                    arr
                        .filter(n => {
                            const clase = (n.account_class || '')
                                .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                                .trim().toLowerCase();
                            return clase.includes(tipo);
                        })
                        .map(n => ({
                            ...n,
                            children: filtrar(n.children || [])
                        }));

                return filtrar(this.tree);
            }

        },

        /* ============================================================
         * CICLO DE VIDA
         * ============================================================ */
        mounted() {
            console.info('[PLAN_CUENTAS][INIT] Aplicación iniciada');
            this.cargarMonedas();
            this.cargarArbol();
            this.buscarCuentas();
            this.cargarTiposCuenta();
        },

        /* ============================================================
         * MÉTODOS PRINCIPALES
         * ============================================================ */
        methods: {
            cargarTiposCuenta() {
                axios.post(this.urlBase + 'get_account_classes', {
                    access_id: window.access_id,
                    access_token: window.access_token
                })
                    .then(res => {
                        this.tiposCuenta = res.data.classes || [];
                        console.log('[PLAN_CUENTAS][CLASES] Cargadas:', this.tiposCuenta);
                    })
                    .catch(err => {
                        console.error('[PLAN_CUENTAS][CLASES][ERROR]', err);
                        Swal.fire('Error', 'No fue posible cargar las clases de cuenta.', 'error');
                    });
            },


            cargarMonedas() {
                axios.post(this.urlBase + 'get_currencies', {
                    access_id: window.access_id,
                    access_token: window.access_token
                })
                    .then(res => this.currencies = res.data.rows || [])
                    .catch(err => {
                        console.error('[PLAN_CUENTAS][MONEDAS][ERROR]', err);
                        Swal.fire('Error', 'No fue posible cargar las monedas.', 'error');
                    });
            },

            cargarArbol() {
                console.info('[PLAN_CUENTAS][TREE] Cargando árbol...');
                this.loadingTree = true;

                axios.post(this.urlBase + 'get_tree', {
                    access_id: window.access_id,
                    access_token: window.access_token
                })
                    .then(res => {
                        const nodes = Array.isArray(res.data.rows) ? res.data.rows : [];
                        this.flat = {};
                        const normalize = arr => arr.forEach(n => {
                            this.$set(n, '_open', true);
                            this.$set(n, 'children', Array.isArray(n.children) ? n.children : []);
                            this.flat[n.id] = n;
                            if (n.children.length) normalize(n.children);
                        });
                        normalize(nodes);
                        this.tree = nodes;
                        console.info('[PLAN_CUENTAS][TREE] Árbol cargado con', nodes.length, 'nodos raíz.');
                    })
                    .catch(err => {
                        console.error('[PLAN_CUENTAS][TREE][ERROR]', err);
                        Swal.fire('Error', 'No fue posible cargar el árbol de cuentas.', 'error');
                    })
                    .finally(() => this.loadingTree = false);
            },

            recargarArbol() {
                this.cargarArbol();
            },

            toggleNode(node) {
                node._open = !node._open;
            },

            selectNode(node) {
                const id = node.id;
                if (!id) return;
                console.info('[PLAN_CUENTAS][SELECT] Nodo seleccionado:', id);

                this.formTitle = 'Detalles de la cuenta';
                this.formMode = 'view';
                this.obtenerCuenta(id);
            },

            addChild(node) {
                console.info('[PLAN_CUENTAS][CHILD] Agregando hija a ID:', node.id);
                this.resetForm();
                this.formTitle = 'Agregar cuenta hija';
                this.formMode = 'create';
                this.form.parent_id = node.id;
                this.form.level = (node.level || 0) + 1;
            },

            editNode(node) {
                const id = node.id;
                if (!id) return;
                console.info('[PLAN_CUENTAS][EDIT] Editando cuenta ID:', id);
                this.formTitle = 'Editar cuenta';
                this.formMode = 'edit';
                this.obtenerCuenta(id);
            },

            obtenerCuenta(id) {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('access_id', window.access_id);
                formData.append('access_token', window.access_token);

                axios.post(this.urlBase + 'get_account', formData)
                    .then(res => {
                        if (res.data && res.data.row) {
                            this.form = Object.assign({}, this.form, res.data.row);
                            console.table(this.form);
                        } else {
                            Swal.fire('Aviso', 'No se encontró la cuenta solicitada.', 'info');
                        }
                    })
                    .catch(err => console.error('[PLAN_CUENTAS][GET][ERROR]', err));
            },
            buscarCuentas() {
                console.info('[PLAN_CUENTAS][PADRES] Cargando lista de cuentas...');
                axios.post(this.urlBase + 'get_all_accounts', {
                    access_id: window.access_id,
                    access_token: window.access_token
                })
                    .then(res => {
                        if (res.data && res.data.rows) {
                            this.accountsList = res.data.rows;
                            console.info('[PLAN_CUENTAS][PADRES] Cuentas cargadas:', this.accountsList.length);
                        }
                    })
                    .catch(err => {
                        console.error('[PLAN_CUENTAS][PADRES][ERROR]', err);
                    });
            },



            guardarCuenta() {
                if (this.saving) return;
                this.saving = true;
                console.info('[PLAN_CUENTAS][SAVE] Guardando cuenta...', this.form);

                const formData = new FormData();
                for (let key in this.form) formData.append(key, this.form[key]);
                formData.append('access_id', window.access_id);
                formData.append('access_token', window.access_token);

                axios.post(this.urlBase + 'save_account', formData)
                    .then(res => {
                        if (res.data && res.data.success) {
                            Swal.fire('Guardado', 'La cuenta se guardó correctamente.', 'success');
                            this.cargarArbol();
                            this.obtenerCuenta(res.data.id);
                            this.formMode = 'view';
                            this.formTitle = 'Detalles de la cuenta';
                        } else {
                            Swal.fire('Error', res.data.msg || 'No se pudo guardar la cuenta.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('[PLAN_CUENTAS][SAVE][ERROR]', err);
                        Swal.fire('Error', 'Error al guardar la cuenta.', 'error');
                    })
                    .finally(() => this.saving = false);
            },

            removeNode(node) {
                if (node.children && node.children.length > 0) {
                    Swal.fire('No permitido', 'No puedes eliminar una cuenta con subcuentas.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Eliminar cuenta',
                    text: '¿Deseas eliminar esta cuenta?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (!result.value) return;

                    const formData = new FormData();
                    formData.append('id', node.id);
                    formData.append('access_id', window.access_id);
                    formData.append('access_token', window.access_token);

                    axios.post(this.urlBase + 'delete_account', formData)
                        .then(res => {
                            if (res.data && res.data.success) {
                                Swal.fire('Eliminado', 'La cuenta fue eliminada correctamente.', 'success');
                                this.cargarArbol();
                                this.resetForm();
                                this.formMode = 'view';
                            } else {
                                Swal.fire('Error', res.data.msg || 'No fue posible eliminar la cuenta.', 'error');
                            }
                        })
                        .catch(err => console.error('[PLAN_CUENTAS][DELETE][ERROR]', err));
                });
            },

            nuevoRegistro() {
                this.resetForm();
                this.formTitle = 'Agregar nueva cuenta';
                this.formMode = 'create';
            },

            cancelarEdicion() {
                this.formMode = 'view';
                this.formTitle = 'Detalles';
                this.resetForm();
            },

            resetForm() {
                this.form = {
                    id: null,
                    code: '',
                    name: '',
                    type: '',
                    parent_id: null,
                    level: 1,
                    currency_id: null,
                    is_confidential: 0,
                    is_cash_account: 0,
                    is_active: 1,
                    annex24_code: '',
                    account_class: ''
                };
            }
        },

        /* ============================================================
         * COMPONENTE RECURSIVO
         * ============================================================ */
        components: {
            'account-node': {
                name: 'account-node',
                props: ['node', 'depth'],
                template: `
          <div :style="{ paddingLeft: (depth*18)+'px' }" class="py-1 border-left border-light">
            <div class="d-flex align-items-center">
              <button class="btn btn-sm btn-link p-0 mr-2"
                      v-if="node.children && node.children.length"
                      @click="$emit('toggle', node)">
                <i :class="node._open ? 'fas fa-caret-down' : 'fas fa-caret-right'"></i>
              </button>
              <span class="mr-2" v-else style="width:20px"></span>

              <a href="javascript:void(0)" class="text-body" @click.stop="$emit('select', node)">
                {{ node.code }} - {{ node.name }}
                <span v-if="node.children && node.children.length" class="text-muted small">
                  ({{ node.children.length }})
                </span>
              </a>

              <div class="ml-auto">
                <button class="btn btn-sm btn-outline-success mr-1" title="Agregar hija" @click.stop="$emit('add-child', node)">
                  <i class="fas fa-plus"></i>
                </button>
                <button class="btn btn-sm btn-outline-primary mr-1" title="Editar" @click.stop="$emit('edit', node)">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" title="Eliminar" @click.stop="$emit('remove', node)">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>

            <div v-show="node._open" v-if="node.children && node.children.length">
              <account-node
                v-for="child in node.children"
                :key="child.id || child.code"
                :node="child"
                :depth="depth + 1"
                @toggle="$emit('toggle',$event)"
                @select="$emit('select',$event)"
                @add-child="$emit('add-child',$event)"
                @edit="$emit('edit',$event)"
                @remove="$emit('remove',$event)"
              />
            </div>
          </div>
        `
            }
        }
    });
    window.app = app;
}

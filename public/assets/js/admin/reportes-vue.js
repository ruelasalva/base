/* ============================================================
 * MÓDULO: Reportes - Generador de Consultas
 * Versión Vue adaptada a estructura de Sajor ERP
 * Autor: Departamento de Sistemas Sajor
 * Fecha: Octubre 2025
 * ------------------------------------------------------------
 * Este componente Vue permite:
 * - Listar tablas y campos del sistema.
 * - Construir consultas SQL tipo SELECT sin escribir código.
 * - Agregar múltiples tablas mediante JOINs.
 * - Probar y guardar reportes dinámicos.
 * ============================================================ */

new Vue({
    el: '#reportes-app',

    /* ============================================================
     * DATOS REACTIVOS
     * ============================================================ */
    data: {
        urlBase: document.getElementById('url-location').dataset.url, // Base URL de endpoints AJAX
        tablas: [],               // Listado de tablas disponibles en la BD
        tablaCampos: {},          // Diccionario: { tabla: [campos] }
        campos: [],               // Campos de la tabla actual seleccionada
        camposTablaBase: [],      // Campos de la tabla base (para los JOIN)
        camposSeleccionados: [],  // Campos incluidos en el SELECT
        condiciones: [],          // Condiciones WHERE
        joins: [],                // JOINs agregados dinámicamente
        orderBy: '',              // Campo de ordenamiento
        querySQL: '',             // Consulta SQL generada
        tablaSeleccionada: '',    // Tabla principal seleccionada
        departamentos: [],        // Catálogo de departamentos
        departamento_id: '',      // Departamento asignado al reporte
        query_name: '',           // Nombre del reporte
        description: '',          // Descripción del reporte
        resultado: [],            // Resultado de prueba de la consulta
        loading: false,           // Estado de carga
    },

    /* ============================================================
     * EVENTO MOUNTED
     * ============================================================ */
    mounted() {
        this.obtenerDepartamentos();
        this.obtenerTablas();
    },

    /* ============================================================
     * MÉTODOS
     * ============================================================ */
    methods: {

        /* ------------------------------------------------------------
         * OBTENER CATÁLOGO DE DEPARTAMENTOS
         * ------------------------------------------------------------ */
        obtenerDepartamentos() {
            axios.post(this.urlBase + 'get_departments', {}, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => { this.departamentos = res.data; })
                .catch(err => console.error('[ERROR][DEPARTAMENTOS]', err));
        },


        /* ------------------------------------------------------------
         * OBTENER TABLAS DISPONIBLES EN LA BASE DE DATOS
         * ------------------------------------------------------------ */
        obtenerTablas() {
            axios.post(this.urlBase + 'get_tables', {}, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    try {
                        this.tablas = (typeof res.data === 'string') ? JSON.parse(res.data) : res.data;
                        console.log('[INFO] Tablas cargadas:', this.tablas);
                    } catch (e) {
                        console.error('[ERROR][PARSE TABLES]', e);
                    }
                })
                .catch(err => console.error('[ERROR][TABLAS]', err));
        },


        /* ------------------------------------------------------------
         * SELECCIONAR TABLA PRINCIPAL Y CARGAR SUS CAMPOS
         * ------------------------------------------------------------ */
        seleccionarTabla() {
            if (!this.tablaSeleccionada) return;

            this.campos = [];
            this.camposSeleccionados = [];
            this.camposTablaBase = [];

            axios.post(this.urlBase + 'get_fields', { table: this.tablaSeleccionada }, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    this.campos = res.data;
                    this.camposTablaBase = res.data;
                    this.tablaCampos[this.tablaSeleccionada] = res.data;
                })
                .catch(err => console.error('[ERROR][CAMPOS TABLA BASE]', err));
        },


        /* ------------------------------------------------------------
         * OBTENER CAMPOS DE UNA TABLA (usado en los JOINs)
         * ------------------------------------------------------------ */
        obtenerCamposDeTabla(nombreTabla) {
            if (!nombreTabla) return;
            if (this.tablaCampos[nombreTabla]) return; // cache existente

            axios.post(this.urlBase + 'get_fields', { table: nombreTabla }, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => { this.$set(this.tablaCampos, nombreTabla, res.data); })
                .catch(err => console.error('[ERROR][CAMPOS TABLA JOIN]', nombreTabla, err));
        },


        /* ------------------------------------------------------------
         * AGREGAR CAMPO AL SELECT
         * ------------------------------------------------------------ */
        agregarCampo(tabla, campo) {
            const fullName = `${tabla}.${campo}`;
            const existe = this.camposSeleccionados.some(c => c.name === fullName);
            if (existe) {
                Swal.fire('Atención', 'Ese campo ya está agregado.', 'info');
                return;
            }

            this.camposSeleccionados.push({ name: fullName, alias: '' });
            this.generarSQL();
        },


        /* ------------------------------------------------------------
         * ELIMINAR CAMPO DEL SELECT
         * ------------------------------------------------------------ */
        eliminarCampo(idx) {
            this.camposSeleccionados.splice(idx, 1);
            this.generarSQL();
        },


        /* ------------------------------------------------------------
         * AGREGAR CONDICIÓN WHERE
         * ------------------------------------------------------------ */
        agregarCondicion() {
            if (this.camposSeleccionados.length === 0) {
                Swal.fire('Atención', 'Debe seleccionar al menos un campo antes de agregar condiciones.', 'warning');
                return;
            }
            this.condiciones.push({ campo: '', operador: '=', valor: '' });
        },


        /* ------------------------------------------------------------
         * ELIMINAR CONDICIÓN WHERE
         * ------------------------------------------------------------ */
        eliminarCondicion(idx) {
            this.condiciones.splice(idx, 1);
            this.generarSQL();
        },


        /* ------------------------------------------------------------
         * AGREGAR JOIN ENTRE TABLAS
         * ------------------------------------------------------------ */
        agregarJoin() {
            this.joins.push({ tipo: 'INNER', tabla: '', campo1: '', operador: '=', campo2: '' });
        },


        /* ------------------------------------------------------------
         * ELIMINAR JOIN
         * ------------------------------------------------------------ */
        eliminarJoin(idx) {
            this.joins.splice(idx, 1);
            this.generarSQL();
        },


        /* ------------------------------------------------------------
         * GENERAR CONSULTA SQL COMPLETA
         * ------------------------------------------------------------ */
        generarSQL() {
            if (!this.tablaSeleccionada || this.camposSeleccionados.length === 0) {
                this.querySQL = '';
                return;
            }

            // SELECT
            let selectPart = this.camposSeleccionados.map(c => {
                return c.alias ? `${c.name} AS ${c.alias}` : c.name;
            }).join(', ');

            let sql = `SELECT ${selectPart} FROM ${this.tablaSeleccionada}`;

            // JOINs
            this.joins.forEach(j => {
                if (j.tabla && j.campo1 && j.campo2) {
                    sql += ` ${j.tipo} JOIN ${j.tabla} ON ${j.campo1} ${j.operador} ${j.campo2}`;
                }
            });

            // WHERE
            let wherePart = this.condiciones
                .filter(c => c.campo && c.valor)
                .map(c => `${c.campo} ${c.operador} ${c.valor}`)
                .join(' AND ');
            if (wherePart) sql += ' WHERE ' + wherePart;

            // ORDER BY
            if (this.orderBy) sql += ' ORDER BY ' + this.orderBy;

            this.querySQL = sql;
        },


        /* ------------------------------------------------------------
         * TEST: Mostrar todas las tablas con sus campos (modo debug)
         * ------------------------------------------------------------ */
        probarTablasConCampos() {
            axios.post(this.urlBase + 'get_tables_with_fields', {}, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    console.log('[DEBUG] Tablas con campos:', res.data);
                    Swal.fire('Éxito', 'Datos cargados correctamente. Revisa la consola.', 'success');
                })
                .catch(err => {
                    console.error('[ERROR][DEBUG TABLAS]', err);
                    Swal.fire('Error', 'No se pudieron obtener las tablas.', 'error');
                });
        },


        /* ------------------------------------------------------------
         * PROBAR CONSULTA SQL GENERADA
         * ------------------------------------------------------------ */
        probarConsulta() {
            if (!this.querySQL.trim()) {
                Swal.fire('Atención', 'No hay consulta generada.', 'warning');
                return;
            }
            if (!this.querySQL.toUpperCase().startsWith('SELECT')) {
                Swal.fire('Error', 'Solo se permiten consultas SELECT.', 'error');
                return;
            }

            this.loading = true;
            axios.post(this.urlBase + 'test_query', { sql: this.querySQL }, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    this.loading = false;
                    if (res.data.error) Swal.fire('Error', res.data.error, 'error');
                    else this.resultado = res.data.rows;
                })
                .catch(err => {
                    this.loading = false;
                    Swal.fire('Error', 'Error al ejecutar la consulta.', 'error');
                    console.error('[ERROR][PROBAR CONSULTA]', err);
                });
        },


        /* ------------------------------------------------------------
         * GUARDAR REPORTE EN BASE DE DATOS
         * ------------------------------------------------------------ */
        guardarReporte() {
            if (!this.querySQL.trim()) {
                Swal.fire('Atención', 'No hay consulta SQL para guardar.', 'warning');
                return;
            }

            axios.post(this.urlBase + 'save_query', {
                query_name: this.query_name,
                description: this.description,
                department_id: this.departamento_id,
                query_sql: this.querySQL
            }, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => {
                    if (res.data.msg === 'ok') {
                        Swal.fire('Éxito', 'Reporte guardado correctamente.', 'success')
                            .then(() => window.location.href = this.urlBase.replace('ajax/', ''));
                    } else {
                        Swal.fire('Error', res.data.msg || 'No se pudo guardar el reporte.', 'error');
                    }
                })
                .catch(err => {
                    console.error('[ERROR][GUARDAR REPORTE]', err);
                    Swal.fire('Error', 'Error al guardar el reporte.', 'error');
                });
        }
    }
});

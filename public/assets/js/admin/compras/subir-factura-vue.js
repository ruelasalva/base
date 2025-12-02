/* ============================
 * Vue: Subir Factura (Admin)
 * ----------------------------
 * Carga órdenes de compra dinámicamente al seleccionar proveedor.
 * ============================ */
if (document.getElementById("subir-factura-app")) {
    new Vue({
        el: "#subir-factura-app",
        data: {
            provider_id: "",
            purchase_id: "",
            orders: [],
            loadingOrders: false,
            errorOrders: false,
        },
        methods: {
            /** Cargar órdenes del proveedor seleccionado */
            cargarOrdenes() {
                if (!this.provider_id) {
                    this.orders = [];
                    this.purchase_id = "";
                    return;
                }

                this.loadingOrders = true;
                this.errorOrders = false;

                const urlBase = document.getElementById("url-location").dataset.url;

                axios
                    .get(urlBase + "get_orders_by_provider", {
                        params: { provider_id: this.provider_id },
                        headers: { "X-Requested-With": "XMLHttpRequest" },
                    })
                    .then((resp) => {
                        if (resp.data.success) {
                            this.orders = resp.data.orders;
                        } else {
                            this.orders = [];
                            this.errorOrders = true;
                        }
                    })
                    .catch(() => {
                        this.orders = [];
                        this.errorOrders = true;
                    })
                    .finally(() => {
                        this.loadingOrders = false;
                    });
            },
        },
    });
}

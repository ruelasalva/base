<div class="header bg-warning pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <h6 class="h2 text-white py-3">
                <i class="fa-brands fa-mercadolibre"></i> Atributos ML del Producto
            </h6>
        </div>
    </div>
</div>

<div id="mlAttributesApp" class="container-fluid mt--6">

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">
                <?php echo $product->name; ?>
            </h3>
            <span class="text-muted">Código: <?php echo $product->code; ?></span>
        </div>

        <div class="card-body">

            <!-- Categoria -->
            <div class="mb-4">
                <label>Categoría ML</label>
                <input type="text" class="form-control" 
                    v-model="category_id"
                    readonly>
            </div>

            <!-- Botón cargar atributos -->
            <button class="btn btn-primary mb-4" @click="loadAttributes">
                <i class="fa-solid fa-download"></i> Cargar atributos de la categoría
            </button>

            <!-- Mensaje -->
            <div v-if="loading" class="alert alert-info">Cargando atributos...</div>

            <!-- Lista de atributos -->
            <div v-if="attributes.length > 0">
                <h4 class="mb-3">Atributos requeridos y opcionales</h4>

                <div v-for="attr in attributes" class="border p-3 mb-3">

                    <strong>{{ attr.name }}</strong>
                    <span class="badge"
                        :class="attr.tags.includes('required') ? 'badge-danger' : 'badge-secondary'">
                        {{ attr.tags.includes('required') ? 'Requerido' : 'Opcional' }}
                    </span>

                    <!-- Campo dinámico -->
                    <input type="text" class="form-control mt-2"
                        v-model="values[attr.id]"
                        placeholder="Valor...">
                </div>
            </div>

            <!-- Guardar -->
            <button class="btn btn-success mt-4" @click="saveAttributes">
                <i class="fa-solid fa-save"></i> Guardar atributos
            </button>

        </div>
    </div>

</div>

<script src="admin/plataformas/ml/ml_attributes.js"></script>

<script>
window.mlConfig = {
    config_id: "<?php echo $config->id; ?>",
    link_id: "<?php echo $link->id; ?>",
    category_id: "<?php echo $link->ml_category_id; ?>",
};
</script>

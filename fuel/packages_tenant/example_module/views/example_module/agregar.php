<div class="container">
    <h1>Agregar Nuevo Ejemplo</h1>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="name">Nombre:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="description">Descripción:</label>
            <textarea name="description" id="description" class="form-control" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?php echo \Uri::create('example'); ?>" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

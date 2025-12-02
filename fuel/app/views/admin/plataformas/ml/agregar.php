<div class="container-fluid mt-4">
    <?php echo Form::open(['method' => 'POST']); ?>

        <?php 
            $title = 'Agregar Cuenta de Mercado Libre';
            echo render('admin/plataformas/ml/_form', [
                'config' => (object)[],
                'title'  => $title
            ]);
        ?>

    <?php echo Form::close(); ?>
</div>

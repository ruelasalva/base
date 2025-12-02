<div class="container-fluid mt-4">
    <?php echo Form::open(['method' => 'POST']); ?>

        <?php 
            $title = 'Editar Cuenta de Mercado Libre';
            echo render('admin/plataformas/ml/_form', [
                'config' => $config,
                'title'  => $title
            ]);
        ?>

    <?php echo Form::close(); ?>
</div>

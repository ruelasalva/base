<div class="row">
	<div class="col-md-12">
		<div class="jumbotron">
			<h2>¡Bienvenido a la Aplicación Base!</h2>
			<p>Esta es la vista estándar de la aplicación. Utiliza la estructura MVC con un controlador base y un template reutilizable.</p>
			<p><a class="btn btn-primary btn-lg" href="#" role="button">Comenzar</a></p>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<h3><span class="glyphicon glyphicon-folder-open"></span> Estructura MVC</h3>
		<p>Esta aplicación utiliza el patrón Modelo-Vista-Controlador:</p>
		<ul>
			<li><strong>Modelo:</strong> <code>classes/model/</code></li>
			<li><strong>Vista:</strong> <code>views/</code></li>
			<li><strong>Controlador:</strong> <code>classes/controller/</code></li>
		</ul>
	</div>
	<div class="col-md-4">
		<h3><span class="glyphicon glyphicon-cog"></span> Controlador Base</h3>
		<p>Todos los controladores pueden heredar de <code>Controller_Base</code> para utilizar el sistema de templates automático.</p>
		<p><code>APPPATH/classes/controller/base.php</code></p>
	</div>
	<div class="col-md-4">
		<h3><span class="glyphicon glyphicon-file"></span> Template</h3>
		<p>El template principal se encuentra en:</p>
		<p><code>APPPATH/views/template.php</code></p>
		<p>Personalízalo según las necesidades del proyecto.</p>
	</div>
</div>

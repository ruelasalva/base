<?php

class Controller_Baseadmin extends Controller_Template
{
	/**
	 * BEFORE
	 *
	 * OBTIENE Y ESTABLECE EN UNA VARIABLE GLOBAL EL NOMBRE DE USUARIO
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
		parent::before();
	}
}

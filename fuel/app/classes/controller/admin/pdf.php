<?php

use Spipu\Html2Pdf\Html2Pdf;

/**
 * CONTROLADOR ADMIN_PDF
 *
 * @package  app
 * @extends  Controller_Admin
 */
class Controller_Admin_Pdf extends Controller_Admin
{
	/**
	 * BEFORE
	 *
	 * @return Void
	 */
	public function before()
	{
		# REQUERIDA PARA EL TEMPLATING
        parent::before();

		# SI EL USUARIO NO TIENE PERMISOS
		if(!Auth::member(100) && !Auth::member(50))
		{
			# SE ESTABLECE EL MENSAJE DE ERROR
			Session::set_flash('error', 'No tienes los permisos para acceder a esta sección.');

			# SE REDIRECCIONA AL USUARIO
			Response::redirect('admin');
		}
	}


	/**
	 * INDEX
	 *
	 * GENERA UN PDF DE PRUEBA
	 *
	 * @access  public
	 * @return  Void
	 */
	public function action_index()
	{
        # Ruta del XML
        $xmlPath = DOCROOT . 'assets/descargas/cfdis/FA100000_62_timbre.xml';

        # Cargar y parsear el XML
        $xml = simplexml_load_file($xmlPath);
        $namespaces = $xml->getNamespaces(true);
        $xml->registerXPathNamespace('cfdi', $namespaces['cfdi']);
        $xml->registerXPathNamespace('tfd', $namespaces['tfd']);

        # Extraer datos básicos
        $comprobante = $xml->xpath('//cfdi:Comprobante')[0];
        $emisor = $xml->xpath('//cfdi:Emisor')[0];
        $receptor = $xml->xpath('//cfdi:Receptor')[0];
        $concepto = $xml->xpath('//cfdi:Conceptos/cfdi:Concepto')[0];
        $impuesto = $xml->xpath('//cfdi:Impuestos/cfdi:Traslados/cfdi:Traslado')[0];
        $timbre = $xml->xpath('//tfd:TimbreFiscalDigital')[0];

       # Crear el contenido HTML para el PDF
		$content = "
		<style>
			body { font-family: Arial, sans-serif; color: #333; }
			.header { background-color: #D71920; color: white; padding: 10px; }
			.footer { font-size: 10px; text-align: center; color: #333; padding: 10px; border-top: 1px solid #ddd; }
			.section { margin: 10px 0; }
			.table { width: 100%; border-collapse: collapse; margin-top: 10px; }
			.table th { background-color: #D71920; color: white; padding: 5px; }
			.table td { border: 1px solid #ddd; padding: 5px; }
			.text-center { text-align: center; }
			.text-right { text-align: right; }
			.client-info, .emisor-info, .documento-info { width: 32%; display: inline-block; vertical-align: top; }
			.section-header { font-weight: bold; color: #D71920; }
			.description-cell { width: 300px; word-wrap: break-word; white-space: normal; font-size: 10px; }
		</style>

		<page backtop='30mm' backbottom='15mm' backleft='10mm' backright='10mm'>
			<page_header>
				<div class='header'>
					<img src='logo.png' alt='Logo' width='80' style='float: left;' />
					<div style='float: right; text-align: right;'>
						<div>Nombre: {$emisor['Nombre']}</div>
						<div>RFC: {$emisor['Rfc']}</div>
						<div>Régimen Fiscal: {$emisor['RegimenFiscal']}</div>
						<div>Tipo de Comprobante: {$comprobante['TipoDeComprobante']}</div>
						<div>Lugar de Expedición: {$comprobante['LugarExpedicion']}</div>
					</div>
				</div>
			</page_header>

			<page_footer>
				<div class='footer'>
					<p>Distribuidora Sajor S.A. de C.V. - Río Juárez No. 1447, Col. El Rosario, Guadalajara, Jalisco, México, C.P. 44898</p>
					<p>Teléfono: (33) 3942-7070 | Página 1 de 1</p>
				</div>
			</page_footer>

			<div class='section'>
				<div class='client-info'>
					<span class='section-header'>Cliente:</span> C112461
				</div>
				<div class='emisor-info'>
					<span class='section-header'>Emisor</span><br>
					Nombre: {$emisor['Nombre']}<br>
					RFC: {$emisor['Rfc']}
				</div>
				<div class='documento-info'>
					<span class='section-header'>Documento</span><br>
					Factura: {$comprobante['Serie']}-{$comprobante['Folio']}<br>
					Fecha y hora: {$comprobante['Fecha']}<br>
					UUID: {$timbre['UUID']}
				</div>
			</div>

			<div class='section'>
				<table class='table'>
					<tr>
						<th>Cantidad</th>
						<th>Unidad</th>
						<th>Descripción</th>
						<th>Valor Unitario</th>
						<th>Importe</th>
					</tr>
					<tr>
						<td>{$concepto['Cantidad']}</td>
						<td>Paquete</td>
						<td class='description-cell'>{$concepto['Descripcion']}</td>
						<td class='text-right'>{$concepto['ValorUnitario']}</td>
						<td class='text-right'>{$concepto['Importe']}</td>
					</tr>
				</table>
			</div>

			<div class='section'>
				<div style='text-align: right; margin-right: 50px;'>
					<p><strong>Subtotal:</strong> {$comprobante['SubTotal']}</p>
					<p><strong>IVA:</strong> {$impuesto['Importe']}</p>
					<p><strong>Total:</strong> {$comprobante['Total']}</p>
				</div>
			</div>
		</page>";


        # Crear el PDF usando Html2Pdf
        $html2pdf = new Html2Pdf('P', 'A4', 'es', true, 'UTF-8');
        $html2pdf->writeHTML($content);

        # Enviar el archivo PDF como descarga directa al navegador sin guardarlo en el servidor
        $html2pdf->output('invoice_generated.pdf', 'D');


	}


}

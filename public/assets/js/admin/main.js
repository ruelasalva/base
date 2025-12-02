/* ======================================
EXPRESIONES REGULARES
========================================= */
function valid_email(string) {
    return (/^([a-z0-9\+\-\_]+)(\.[a-z0-9\+\-\_]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i).test(string) ? true : false;
}

function alphabetic_spaces(string) {
    return (/^[a-zA-Z\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ\ ]*$/).test(string) ? true : false;
}

function alphanumeric_spaces(string) {
    return (/^[a-zA-Z\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ0-9\ ]*$/).test(string) ? true : false;
}

function alphanumeric(string) {
    return (/^[a-zA-Z\á\é\í\ó\ú\Á\É\Í\Ó\Ú\ñ\Ñ0-9]*$/).test(string) ? true : false;
}

function min_length(string, val) {
    if ((/[^0-9]/).test(val)) {
        return false;
    }

    return (string.length < parseInt(val)) ? false : true;
}

function max_length(string, val) {
    if ((/[^0-9]/).test(val)) {
        return false;
    }

    return (string.length > parseInt(val)) ? false : true;
}

function exact_length(string, val) {
    if ((/[^0-9]/).test(val)) {
        return false;
    }

    return (string.length != parseInt(val)) ? false : true;
}

function numeric(string) {
    return (/^[\-+]?[0-9]*\.?[0-9]+$/).test(string) ? true : false;
}

function integer(string) {
    return (/^[\-+]?[0-9]+$/).test(string) ? true : false;
}

function is_natural(string) {
    return (/^[0-9]+$/).test(string) ? true : false;
}

function is_natural_no_zero(string) {
    if (!(/^[0-9]+$/).test(string)) {
        return false;
    }

    if (parseInt(string) == 0) {
        return false;
    }

    return true;
}

function is_date(string) {
    return (/^([\d]{2})([\/][\d]{2})([\/][\d]{4})$/).test(string) ? true : false;
}

/* ======================================
ALERTIFY
========================================= */
alertify.defaults = {
    notifier: {
        delay: 5,
        position: 'top-right',
        closeButton: true
    }
};


/* ======================================
FILTERS
========================================= */
function filters() {
    var url_current = $('#url-current').data('url');
    var url_temp = '?';
    var option = '';
    var order = $('#order-by').val();

    url_temp += 'orden=' + order + '&';

    window.location = url_current + url_temp.slice(0, -1);
}


/* ======================================
ISEMPTY
========================================= */
function isEmpty(obj) {
    for (var key in obj) {
        if (obj.hasOwnProperty(key))
        return false;
    }

    return true;
}

/* ======================================
VARIABLES GLOBALES
========================================= */
// SE OBTIENE LA URL BASE
const url_location     = document.getElementById('url-location').dataset.url;
const url_base_project = document.getElementById('url').dataset.url;

/*
*	CHECKSTATUS
*
*	VERIFICA EL STATUS DE UNA RESPUESTA
*
*/
const checkStatus = res => {
    if (res.status === 200 && res.status < 300) {
        return res;
    }
    else {
        let error = new Error(res.statusText);
        error.res = res;
        throw error;
    }
};


/*
*	TOJSON
*
*	RETORNA UNA PROMESA RESUELTA CON EL RESUTADO DEL PARSEO DE RESPONSE
*
*/
const toJSON = res => res.json();

/*
*   Class QuoteItem
*   Metodos de acciones del carrito
*/
class QuoteItem
{
	constructor(idProduct = null, quantity = null)
	{
		this.idProduct = idProduct;
		this.quantity  = quantity;
	}

	set setIdProduct(idProduct)
	{
		this.idProduct = idProduct;
	}

	set setQuantity(quantity)
	{
		this.quantity = quantity;
	}

	add()
	{
		fetch(url_location + 'add_product_quote.json', {
			method: 'post',
			headers: {
				Accept: 'application/json',
				'Content-type': 'application/json'
			},
			body: JSON.stringify(this)
		})
		.then(checkStatus)
		.then(toJSON)
		.then(res => {
			switch (res.msg) {
				case 'ok':
					// SE OBTIENE EL PRODUCTO AGREGADO DEL ARREGLO QUOTE_DATA
					const product = res.quote_data.filter(
						product => product.id == res.product_id
					);

                    if(isEmpty(product)) {
						// SE MANDA UNA ALERTA AL USUARIO
						alertify.message(
							`No se puede agregar el producto.`
						);
					}
					else {
                        // SE NOTIFICA QUE EL PRODUCTO HA SIDO AGREGADO
                        const newItem = UIX.createNotifierElement(product[0]);
                        alertify.notify(newItem, 'cart-item', 3);
					}
				break;

				// PRODUCTO NO DISPONIBLE
				case 'product_not_found':
					// SE IMPRIME EL MENSAJE DE ERROR
					alertify.error('No hay piezas disponibles de este producto.');
				break;

				// PETICION INCOMPLETA
				case 'invalid_request':
					// SE IMPRIME EL MENSAJE DE ERROR
					return alertify.error(
						'Algo inesperado ha ocurrido, por favor refresca la página.'
					);
				break;

				default:
					// SE IMPRIME EL MENSAJE DE ERROR
					return alertify.error(
						'Algo inesperado ha ocurrido, por favor refresca la página.'
					);
				break;
			}

			// SE ACTUALIZA EL VALOR EN EL CONTADOR DEL CARRITO
			const numberItemsInCart = document.querySelector('.quote-qty');
			numberItemsInCart.innerHTML = res.total_products_quantity;
		})
		.catch(error => {
			console.log('Solicitud fallida:', error);
		});
	}

	edit()
	{
		fetch(url_location + 'edit_product_quote.json', {
			method: 'post',
			headers: {
				Accept: 'application/json',
				'Content-type': 'application/json'
			},
			body: JSON.stringify(this)
		})
		.then(checkStatus)
		.then(toJSON)
		.then(res => {
			switch (res.msg) {
				// OK
				case 'ok':
					// SE LLAMA A LA FUNCION QUE CONSTRUYE EL CHECKOUT
					UIX.updateCheckout(res);
				break;

				default:
					// SE IMPRIME EL MENSAJE DE ERROR
					alertify.error(
						'Algo inesperado ha ocurrido, por favor refresca la página.'
					);
				break;
			}
		})
		.catch(error => {
			console.log('Solicitud fallida:', error);
		});
	}

	delete()
	{
		fetch(url_location + 'delete_product_quote.json', {
			method: 'post',
			headers: {
				Accept: 'application/json',
				'Content-type': 'application/json'
			},
			body: JSON.stringify(this)
		})
		.then(checkStatus)
		.then(toJSON)
		.then(res => {
			switch (res.msg) {
				// OK
				case 'ok':
					// SE LLAMA A LA FUNCION QUE CONSTRUYE EL CHECKOUT
					UIX.updateCheckout(res);
				break;

				// USUARIO NO VALIDO
				case 'invalid_user':
					// SE IMPRIME EL MENSAJE DE ERROR
					alertify.error('Este usuario no es válido');

					// SE REDIRECCIONA A INICIO DESPUES DE 3SEG
					setTimeout(function() {
						window.location.replace(url_location);
					}, 3000);
				break;

				default:
					// SE IMPRIME EL MENSAJE DE ERROR
					alertify.error(
						'Algo inesperado ha ocurrido, por favor refresca la página.'
					);
				break;
			}
		})
		.catch(error => {
			console.log('Solicitud fallida:', error);
		});
	}
}

/*
*   CLASS UIX
*   METODOS QUE ACTUALIZAN LA INTERFAZ DE USUARIO
*/
class UIX
{
	/*
	*	BOOSTRAPDISPLAYALERT
	*
	*	MUESTRA UN ALERT DE BOOTSTRAP
	*
	*/
	static boostrapDisplayAlert(classNames, alertWrapper, message)
	{
		// SE OBTIENE EL ELEMENTO
		alertWrapper = document.querySelector(alertWrapper);

		// SE LIMPIA EL CAMPO
		alertWrapper.innerHTML = '';

		// SE ESCRIBE EL MENSAJE
		alertWrapper.innerHTML = `<div class="alert alert-${classNames} alert-dismissible fade show" role="alert">${message}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>`;
	}


	/*
	*	ENABLEBUTTON
	*
	*	HABILITA UN BOTÓN
	*
	*/
	static enableButton(elButton, textButton = null)
	{
		elButton.classList.remove('disabled');
		elButton.removeAttribute('disabled');
		elButton.setAttribute('tabindex', 0);
		elButton.setAttribute('aria-disabled', false);

		if(textButton != null)
		{
			elButton.innerHTML = textButton;
		}
	}


	/*
	*	DISABLEBUTTON
	*
	*	PONE UN BOTÓN EN MODO ESPERA
	*
	*/
	static disableButton(elButton, textButton = null)
	{
		elButton.classList.add('disabled');
		elButton.setAttribute('disabled', true);
		elButton.setAttribute('tabindex', -1);
		elButton.setAttribute('aria-disabled', true);

		if(textButton != null)
		{
			elButton.innerHTML = textButton;
		}
	}


	/*
	*	WAITINGBUTTON
	*
	*	PONE UN BOTÓN EN MODO ESPERA
	*
	*/
	static waitingButton(elButton, textButton)
	{
		elButton.classList.add('disabled');
		elButton.setAttribute('disabled', true);
		elButton.setAttribute('tabindex', -1);
		elButton.setAttribute('aria-disabled', true);
		elButton.innerHTML = textButton;
	}


	/*
	*	ENABLEWAITINGSCREEN
	*
	*	HABILITA UN BOTÓN
	*
	*/
	static enableWaitingScreen()
	{
		const waitingResponse = document.getElementById('waiting-response');
		waitingResponse.classList.replace('d-none', 'd-flex');
		document.getElementsByTagName('html')[0].style.overflow = 'hidden';
	}


	/*
	*	DISABLEWAITINGSCREEN
	*
	*	DESHABILITA UN BOTÓN
	*
	*/
	static disableWaitingScreen()
	{
		const waitingResponse = document.getElementById('waiting-response');
		waitingResponse.classList.replace('d-flex', 'd-none');
		document.getElementsByTagName('html')[0].style.overflow = 'auto';
	}


	/*
	*	TOGGLEMEGAMENU
	*
	*	ABRE O CIERRA EL MEGAMENU
	*
	*/
	static toggleMegamenu(e)
	{
		const megamenuEl = document.getElementById('megamenu');

		if(e.keyCode != 27)
		{
			if(megamenuEl.classList.contains('show'))
			{
				megamenuEl.classList.remove('show');
				document.getElementsByTagName('html')[0].style.overflow = 'auto';
			}
			else
			{
				megamenuEl.classList.add('show');
				document.getElementsByTagName('html')[0].style.overflow = 'hidden';
			}
		}
		else
		{
			megamenuEl.classList.remove('show');
			document.getElementsByTagName('html')[0].style.overflow = 'auto';
		}
	}


	/*
	*	CREATENOTIFIERELEMENT
	*
	*	CREA UN DOMELEMENT DE UN ITEM QUE SE AGREGA AL CARRITO
	*  Y QUE SERÁ UTILIZADO PARA LA NOTIFICACIÓN.
	*
	*/
	static createNotifierElement(quoteItem)
	{
		const {
			id,
			slug,
			name,
			image,
			quantity
		} = quoteItem;

		// SE CREA UN ELEMENTO NUEVO
		const newItem = document.createElement('div');
		newItem.classList.add('row', 'horizontal-card', `product-${id}`);
		newItem.innerHTML = `
		<div class="col-12">
		<p class="mb-2 text-left h5">Se agregó el producto:</p>
		<div class="pt-3">
		<div class="row">
		<div class="col-4 pr-0 pr-sm-3">
		<a title="${name}" class="" href="${url_location}producto/${slug}">
		<img alt="${name}" class="img-fluid d-block mx-auto" src="${url_base_project}assets/uploads/thumb_${image}">
		</a>
		</div>
		<div class="col-8">
		<div class="row">
		<div class="col-12 mb-2">
		<h5 class="text-left text-primary mb-2">${name}</h5>
		</div>
		</div>
		</div>
		</div>
		</div>
		</div>`;
		return newItem;
	}


	/*
	*	UPDATECHECKOUT
	*
	*   @PARAM {OBJECT[]} RESPONSE
	*	ACTUALIZA LAS PROPIEDADES DE LOS PRODUCTOS EN EL CARRITO
	*
	*/
	static updateCheckout(response)
	{
		// SE INICIALIZAN LOS ARREGLOS
		let data_to_update = [];

		// SI NO HAY PRODUCTOS EN EL CARRITO
		if(response.total_products_quantity == 0)
		{
			// SE LIMPIA LA TABLA
			UIX.deleteCheckout();

			// SI EL ARREGLO CART UNAVAILABLE TIENE INFORMACION
			if(response.quote_unavailable.length > 0)
			{
				// SE INICIALIZA LA VARIABLE QUE CONTENDRA LA INFORMACION DE LOS PRODUCTOS NO DISPONIBLES
				let unavailable_products = '';

				// SE RECORRE PRODUCTO POR PRODUCTO
				response.quote_unavailable.forEach(quoteItem => {
					// SE CONSTRUYE LA LEYENDA CON LOS PRODUCTOS ELIMINADOS
					unavailable_products += '<p>- ' + quoteItem.name + '</p>';
				});

				// SE ESCRIBE EL MENSAJE CON LOS PRODUCTOS NO DISPONIBLES
				UIX.boostrapDisplayAlert(
					'warning',
					'#general_alert',
					'<h4 class="alert-heading">¡Atención!</h4><p>Los siguientes productos han sido removidos de tu carrito porque ya no están disponibles en la tienda:</p><hr>' +
					unavailable_products
				);

				// SE MUEVE EL VIEWPORT AL TOP
				window.scrollTo(0, 0);
			}
			else
			{
				// SE NOTIFICA QUE SE ELIMINÓ EL PRODUCTO
				alertify.message('Se eliminó el producto del carrito.');
			}
		}
		else
		{
			// SI LA CANTIDAD DEL PRODUCTO ES 0
			if (response.quantity == 0) {
				// SE ELIMINA EL PRODUCTO DEL CARRITO
				$(`.product-${response.product_id}`).hide('normal', function() {
					$(this).remove();
				});

				// SE NOTIFICA QUE SE ELIMINÓ EL PRODUCTO
				alertify.message('Se eliminó el producto del carrito.');
			}

			// SI EL ARREGLO CART DATA EXISTE Y TIENE INFORMACION
			if(response.quote_data.length > 0)
			{
				// SE RECORRE PRODUCTO POR PRODUCTO
				response.quote_data.forEach(quoteItem => {

					// SE ACTUALIZA LA CANTIDAD DEL PRODUCTO EN LOS TOUCHSPIN
					document.querySelector(`.product-${quoteItem.id} .touchspin`).value =
					quoteItem.quantity;
				});
			}

			// SI EL ARREGLO CART DATA EXISTE Y TIENE INFORMACION
			if(response.quote_unavailable.length > 0)
			{
				// SE INICIALIZA LA VARIABLE QUE CONTENDRA LA INFORMACION DE LOS PRODUCTOS NO DISPONIBLES
				let unavailable_products = '';

				// SE RECORRE PRODUCTO POR PRODUCTO
				response.quote_unavailable.forEach(quoteItem => {
					// SE CONSTRUYE LA LEYENDA CON LOS PRODUCTOS ELIMINADOS
					unavailable_products += '<p>- ' + quoteItem.name + '</p>';

					// SE ELIMINA EL PRODUCTO DEL CARRITO
					$(`.product-${quoteItem.id}`).hide('normal', function() {
						$(this).remove();
					});
				});

				// SE ESCRIBE EL MENSAJE CON LOS PRODUCTOS NO DISPONIBLES
				UIX.boostrapDisplayAlert(
					'warning',
					'#general_alert',
					'<h4 class="alert-heading">¡Atención!</h4><p>Los siguientes productos han sido removidos de tu carrito porque ya no están disponibles en la tienda:</p><hr>' +
					unavailable_products
				);

				// SE MUEVE EL VIEWPORT AL TOP
				window.scrollTo(0, 0);
			}
		}

		// SE ALMACENA EN EL ARREGLO DATATOUPDATE EL VALOR EN EL CARRITO
		data_to_update.push({
			field: '.quote-qty',
			value: response.total_products_quantity
		});

		// SE ACTUALIZA LA INFORMACION
		data_to_update.forEach(
			el => (document.querySelector(el.field).innerHTML = el.value)
		);
	}


	/*
	*	DELETECHECKOUT
	*
	*   ELIMINA LOS ELEMENTOS DEL CHECKOUT CUANDO NO HAY PRODUCTOS EN EL CARRITO
	*
	*/
	static deleteCheckout()
	{
		// CHECKOUT TABLE / CART
		if(document.querySelectorAll('.checkout-products').length)
		{
			document.querySelector('.checkout-products').innerHTML =
			'<div class="p-3 border bg-white rounded mb-3"><p class="mb-0">No hay productos en tu cotización.</p></div>';
			$('#checkout_sidebar').hide('normal', function() {
				$(this).remove();
			});
		}
	}
}

$(document).ready(function() {
    class UploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }

        upload() {
            return this.loader.file
            .then(file => new Promise((resolve, reject) => {
                this._initRequest();
                this._initListeners(resolve, reject, file);
                this._sendRequest(file);
            } ) );
        }

        abort() {
            if(this.xhr) {
                this.xhr.abort();
            }
        }

        _initRequest() {
            const xhr = this.xhr = new XMLHttpRequest();

            xhr.open('POST', url + 'ckeditor_image.json', true);
            xhr.responseType = 'json';
        }

        _initListeners(resolve, reject, file) {
            const xhr = this.xhr;
            const loader = this.loader;
            const genericErrorText = `No se puede subir el archivo: ${ file.name }`;

            xhr.addEventListener('error', () => reject(genericErrorText));
            xhr.addEventListener('abort', () => reject());
            xhr.addEventListener('load', () => {
                const response = xhr.response;

                if (!response || response.error) {
                    return reject( response && response.error ? response.error.message : genericErrorText );
                }

                resolve({
                    default: response.url
                });
            });

            if(xhr.upload) {
                xhr.upload.addEventListener('progress', evt => {
                    if(evt.lengthComputable) {
                        loader.uploadTotal = evt.total;
                        loader.uploaded = evt.loaded;
                    }
                });
            }
        }

        _sendRequest(file) {
            const data = new FormData();
            data.append('access_id', access_id);
            data.append('access_token', access_token);
            data.append('file', file);
            this.xhr.send(data);
        }
    }


    function CustomUploadAdapterPlugin( editor ) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new UploadAdapter(loader);
        };
    }

    function notify(placement, align, icon, type, animIn, animOut, messageText) {
        $.notify({
            icon: icon,
            title: ' Aviso importante',
            message: messageText,
            url: ''
        }, {
            element: 'body',
            type: type,
            allow_dismiss: true,
            placement: {
                from: placement,
                align: align
            },
            offset: {
                x: 15,
                y: 15
            },
            spacing: 10,
            z_index: 1080,
            delay: 2500,
            timer: 25000,
            url_target: '_blank',
            mouse_over: false,
            animate: {
                enter: animIn,
                exit: animOut
            },
            template: '<div data-notify="container" class="alert alert-dismissible alert-{0} alert-notify" role="alert">' +
            '<span class="alert-icon" data-notify="icon"></span> ' +
            '<div class="alert-text"</div> ' +
            '<span class="alert-title" data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '</div>' +
            '<button type="button" class="close" data-notify="dismiss" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '</div>'
        });
    }

    if($('.delete-item').length > 0) {
        $('.delete-item').click(function(){
            var link = $(this).attr('href');

            setTimeout(function() {
                swal({
                    title: '¿Estás seguro de eliminar el registro?',
                    text: "No habrá forma de revertir esto.",
                    type: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonClass: 'btn btn-secondary',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if(result.value) {
                        $(location).attr('href', link);
                    }
                })
            }, 200);

            return false;
        });
    }

    if($('.sorted-table').length > 0) {
        var oldIndex;
        var newIndex;

        $('.sorted-table').sortable({
            containerSelector: 'table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '<tr class="placeholder"/>',
            onDragStart: function($item, container, _super) {
                oldIndex = $item.index();
                $item.appendTo($item.parent());
                _super($item, container);
            },
            onDrop: function($item, container, _super) {
                newIndex = $item.index();

                if(newIndex != oldIndex) {
                    $item.closest('table').find('tbody tr').each(function (i, row) {
                        var SendData = {
                            'access_id' : access_id,
                            'access_token' : access_token,
                            'id': $(this).data('item-id'),
                            'order': i+1
                        };

                        row = $(row);
                        row.find('.order-num').html(i+1);

                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: url + 'order_table',
                            data: SendData,
                            success: function(response)
                            {
                                if(response.msg != 'ok')
                                {
                                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                }
                            }
                        });
                    });
                }

                _super($item, container);
            }
        });
    }

    if($('.sorted-table-product-images').length > 0) {
        var oldIndex;
        var newIndex;

        $('.sorted-table-product-images').sortable({
            containerSelector: 'table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '<tr class="placeholder"/>',
            onDragStart: function($item, container, _super) {
                oldIndex = $item.index();
                $item.appendTo($item.parent());
                _super($item, container);
            },
            onDrop: function($item, container, _super) {
                newIndex = $item.index();

                if(newIndex != oldIndex) {
                    $item.closest('table').find('tbody tr').each(function (i, row) {
                        var SendData = {
                            'access_id' : access_id,
                            'access_token' : access_token,
                            'id': $(this).data('item-id'),
                            'order': i+1
                        };

                        row = $(row);
                        row.find('.order-num').html(i+1);

                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: url + 'order_table_product_images',
                            data: SendData,
                            success: function(response)
                            {
                                if(response.msg != 'ok')
                                {
                                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                }
                            }
                        });
                    });
                }

                _super($item, container);
            }
        });
    }

    if($('.toggle-ps').length > 0) {
        $(':checkbox.toggle-ps').bind('change', function() {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id' : access_id,
                'access_token' : access_token,
                'product': $(this).data('product'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'product_status',
                data: SendData,
                success: function(response)
                {
                    // SI LA RESPUESTA ES ERROR
                    if(response.msg != 'ok')
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    if($('.toggle-psi').length > 0) {
        $(':checkbox.toggle-psi').bind('change', function() {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id' : access_id,
                'access_token' : access_token,
                'product': $(this).data('product'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'product_status_index',
                data: SendData,
                success: function(response)
                {
                    // SI LA RESPUESTA ES ERROR
                    if(response.msg != 'ok')
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    if($('#post-intro').length > 0) {
		ClassicEditor
		.create(document.querySelector('#post-intro'), {
	        language: 'es',
            extraPlugins: [ CustomUploadAdapterPlugin ],
            mediaEmbed: {
                previewsInData: true
            }
	    })
		.then( newEditor => {
		    postIntro = newEditor;
		})
		.catch(error => {
			console.error(error);
		});
	}

	if($('#post-content').length > 0) {
		ClassicEditor
		.create(document.querySelector('#post-content'), {
	        language: 'es',
            extraPlugins: [ CustomUploadAdapterPlugin ],
            mediaEmbed: {
                previewsInData: true
            }
	    })
		.then( newEditor => {
		    postContent = newEditor;
		})
		.catch(error => {
			console.error(error);
		});
	}

	$('#add-post').click(function(){
		const introData = postIntro.getData();
		const contentData = postContent.getData();

		$('#intro').val(introData);
		$('#content').val(contentData);
	});

	$('#add-content').click(function(){
		const contentData = postContent.getData();

		$('#content').val(contentData);
	});

    if($('.confirm-transfer').length > 0) {
        $('.confirm-transfer').click(function(){
            var link = $(this).attr('href');

            setTimeout(function() {
                swal({
                    title: '¿Estás seguro de confirmar la transferencia de esta venta?',
                    text: "No habrá forma de revertir esto.",
                    type: 'info',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-primary',
                    confirmButtonText: 'Sí, confirmar',
                    cancelButtonClass: 'btn btn-secondary',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if(result.value) {
                        $(location).attr('href', link);
                    }
                })
            }, 200);

            return false;
        });
    }

    if($('#minimum').length > 0)
    {
        $('#minimum').change(function()
        {
            if($(this).val() == 0)
            {
                $('#total_minimum_div').hide();
            }
            else
            {
                $('#total_minimum_div').show();
            }
        });
    }

    if($('#type_id').length > 0)
    {
        $('#type_id').change(function()
        {
            var SendData = {
                'access_id'    : access_id,
                'access_token' : access_token,
                'type_id'      : $(this).val()
            };

            if($(this).val() != 0)
            {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: url + 'incidentes.json',
                    data: SendData,
                    success: function(response)
                    {
                        if(response.msg == 'ok')
                        {
                            $('#incident_id').html(response.data);
                        }
                        else
                        {
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                        }
                    }
                });
            }
        });
    }

    if ($('#asig_user_id').length > 0) {
        $('#asig_user_id').change(function () {
            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'asig_user_id': $(this).val()
            };

            if ($(this).val() != 0) {
                $.ajax({
                    type: 'post',
                    dataType: 'json',
                    url: url + 'status.json',
                    data: SendData,
                    success: function (response) {
                        if (response.msg == 'ok') {
                            $('#istatus_id').html(response.data);
                        }
                        else {
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                        }
                    }
                });
            }
        });
    }

    //index select cliente y estado
    var initialStatusValue = $('#status_id').val(); // Almacena el valor inicial de status_id

    // Manejar el evento de cambio para el select box status_id
    $('#istatus_id').change(function () {
        if ($('#istatus_id').val() !== initialStatusValue) {
            actualizarURL(); // Actualizar la URL con los valores seleccionados
        }
    });

    // Función para actualizar la URL con los valores seleccionados
    function actualizarURL() {
        var current_url = window.location.search;
        var asig_user   = $('#asig_user_id').val();
        var status      = $('#istatus_id').val();
        var params      = new URLSearchParams(current_url);
        var new_url     = 'index?';

        for(const [key, value] of params)
        {
            new_url += (new_url == 'index?') ? key + '=' + value : '&' + key + '=' + value;
        }

        new_url += (new_url == 'index?') ? 'asig_user=' + asig_user : '&asig_user=' + asig_user;

        new_url += '&status=' + status;

        // Redirigir a la nueva URL
        window.location.href = new_url;
    }



    if($('.ticket-modal').length > 0) {
        $('.ticket-modal').click(function(){
            var link   = $(this).attr('href');
            var ticket = $(this).data('ticket');

            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'ticket': ticket
            };

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'get_ticket_opts',
                data: SendData,
                success: function (response) {
                    if(response.msg == 'ok')
                    {
                        Swal.fire({
                            title: 'Cerrar ticket',
                            html: `
                            <div class="col-12">
                               <div class="form-group">
                                  <textarea id="solution" class="form-control" placeholder="Solución" rows="7" name="solution"></textarea>
                               </div>
                            </div>
                            <div class="col-12">
                               <div class="form-group">
                                  <select id="asig_user" class="form-control">
                                     ${response.opts}
                                  </select>
                               </div>
                            </div>
                            `,
                            inputAttributes: {
                                autocapitalize: "off"
                            },
                            showCancelButton: true,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary'
                            },
                            confirmButtonText: 'Guardar',
                            cancelButtonText: 'Cancelar',
                            showLoaderOnConfirm: true,
                            preConfirm: function() {
                                let solution = $('#solution').val();
                                let asig_user   = $('#asig_user').val();

                                var SecondSendData = {
                                    'access_id'    : access_id,
                                    'access_token' : access_token,
                                    'ticket'       : ticket,
                                    'solution'  : solution,
                                    'asig_user'    : asig_user
                                };

                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'close_ticket',
                                    data: SecondSendData,
                                    success: function (response) {
                                        if(response.msg == 'ok') {
                                            $(location).attr('href', link);
                                        }
                                        else {
                                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                        }
                                    }
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        })/*.then((result) => {
                            if(result.isConfirmed) {
                                Swal.fire({
                                    title: `${result.value.login}'s avatar`,
                                    imageUrl: result.value.avatar_url,
                                });
                            }
                        });*/
                    }
                    else {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });

            return false;
        });
    }


    if ($('.task-modal').length > 0) {
        $('.task-modal').click(function () {
            var link = $(this).attr('href');
            var task = $(this).data('task');

            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'task': task
            };

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'get_task_opts',
                data: SendData,
                success: function (response) {
                    if(response.msg == 'ok')
                    {
                        Swal.fire({
                            title: 'Cerrar tarea',
                            html: `
                            <div class="col-12">
                               <div class="form-group">
                                  <textarea id="comments" class="form-control" placeholder="Comentarios" rows="7" name="comments"></textarea>
                               </div>
                            </div>
                            <div class="col-12">
                               <div class="form-group">
                                  <select id="employee_id" class="form-control">
                                     ${response.opts}
                                  </select><br>
                                  <input type="date" id="finish_at" class="form-control" placeholder="Fecha de cierre">
                               </div>
                            </div>
                            `,
                            inputAttributes: {
                                autocapitalize: "off"
                            },
                            showCancelButton: true,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary'
                            },
                            confirmButtonText: 'Guardar',
                            cancelButtonText: 'Cancelar',
                            showLoaderOnConfirm: true,
                            preConfirm: function () {
                                let comments    = $('#comments').val();
                                let employee_id = $('#employee_id').val();
                                let finish_at   = $('#finish_at').val();

                                var SecondSendData = {
                                    'access_id': access_id,
                                    'access_token': access_token,
                                    'task': task,
                                    'comments': comments,
                                    'employee_id': employee_id,
                                    'finish_at': finish_at
                                };

                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'close_task',
                                    data: SecondSendData,
                                    success: function (response) {
                                        if (response.msg == 'ok') {
                                            $(location).attr('href', link);
                                        }
                                        else {
                                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                        }
                                    }
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        })/*.then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: `${result.value.login}'s avatar`,
                                    imageUrl: result.value.avatar_url
                                });
                            }
                        });*/
                    }
                    else {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });

            return false;
        });
    }

    if ($('.ticket-asig').length > 0) {
        $('.ticket-asig').click(function () {
            var link = $(this).attr('href');
            var ticket = $(this).data('ticket');

            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'ticket': ticket
            };

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'get_ticket_asig',
                data: SendData,
                success: function (response) {
                    if (response.msg == 'ok') {
                        Swal.fire({
                            title: 'Asignar o cambiar usario de soporte',
                            html: `
                            <div class="col-12">
                               <div class="form-group">
                                  <select id="asig_user" class="form-control">
                                     ${response.opts}
                                  </select>
                               </div>
                            </div>
                            `,
                            inputAttributes: {
                                autocapitalize: "off"
                            },
                            showCancelButton: true,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary'
                            },
                            confirmButtonText: 'Guardar',
                            cancelButtonText: 'Cancelar',
                            showLoaderOnConfirm: true,
                            preConfirm: function () {
                                let asig_user = $('#asig_user').val();

                                var SecondSendData = {
                                    'access_id': access_id,
                                    'access_token': access_token,
                                    'ticket': ticket,
                                    'asig_user': asig_user
                                };

                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'asig_ticket',
                                    data: SecondSendData,
                                    success: function (response) {
                                        if (response.msg == 'ok') {
                                            $(location).attr('href', link);
                                        }
                                        else {
                                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                        }
                                    }
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        })/*.then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: `${result.value.login}'s avatar`,
                                    imageUrl: result.value.avatar_url
                                });
                            }
                        });*/
                    }
                    else {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });

            return false;
        });
    }

    if ($('.ticket-clos').length > 0) {
        $('.ticket-clos').click(function () {
            var link = $(this).attr('href');
            var ticket = $(this).data('ticket');

            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'ticket': ticket
            };

            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'get_ticket_clos',
                data: SendData,
                success: function (response) {
                    if (response.msg == 'ok') {
                        Swal.fire({
                            title: 'Finalizar ticket',
                            html: `
                            <div class="col-12">
                               <div class="form-group">
                                  <textarea id="solution" class="form-control" placeholder="Solución" rows="7" name="solution"></textarea>
                               </div>
                            </div>
                            <div class="col-12">
                               <div class="form-group">
                                  <select id="asig_user" class="form-control">
                                     ${response.opts}
                                  </select>
                               </div>
                            </div>
                            `,
                            inputAttributes: {
                                autocapitalize: "off"
                            },
                            showCancelButton: true,
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary'
                            },
                            confirmButtonText: 'Guardar',
                            cancelButtonText: 'Cancelar',
                            showLoaderOnConfirm: true,
                            preConfirm: function () {
                                let solution = $('#solution').val();
                                let asig_user = $('#asig_user').val();

                                var SecondSendData = {
                                    'access_id': access_id,
                                    'access_token': access_token,
                                    'ticket': ticket,
                                    'solution': solution,
                                    'asig_user': asig_user
                                };

                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'ticket_clos',
                                    data: SecondSendData,
                                    success: function (response) {
                                        if (response.msg == 'ok') {
                                            $(location).attr('href', link);
                                        }
                                        else {
                                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                        }
                                    }
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        })/*.then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: `${result.value.login}'s avatar`,
                                    imageUrl: result.value.avatar_url
                                });
                            }
                        });*/
                    }
                    else {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });

            return false;
        });
    }

    if($('#price_per').length > 0)
    {
        $('#price_per').change(function()
        {
            var price_per = $(this).val();

            switch(price_per)
            {
                case '':
                    $('#amount_div').hide();
                break;

                case 'u':
                    $('#amount_div').hide();
                break;

                case 'm':
                    $('#amount_div').show();
                break;
            }
        });
    }

    if($('.sorted-table-banners').length > 0) {
        var oldIndex;
        var newIndex;

        $('.sorted-table-banners').sortable({
            containerSelector: 'table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '<tr class="placeholder"/>',
            onDragStart: function($item, container, _super) {
                oldIndex = $item.index();
                $item.appendTo($item.parent());
                _super($item, container);
            },
            onDrop: function($item, container, _super) {
                newIndex = $item.index();

                if(newIndex != oldIndex) {
                    $item.closest('table').find('tbody tr').each(function (i, row) {
                        var SendData = {
                            'access_id' : access_id,
                            'access_token' : access_token,
                            'id': $(this).data('item-id'),
                            'order': i+1
                        };

                        row = $(row);
                        row.find('.order-num').html(i+1);

                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: url + 'order_table_banners',
                            data: SendData,
                            success: function(response)
                            {
                                if(response.msg != 'ok')
                                {
                                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                }
                            }
                        });
                    });
                }

                _super($item, container);
            }
        });
    }

    if($('.sorted-table-banners-laterales').length > 0) {
        var oldIndex;
        var newIndex;

        $('.sorted-table-banners-laterales').sortable({
            containerSelector: 'table',
            itemPath: '> tbody',
            itemSelector: 'tr',
            placeholder: '<tr class="placeholder"/>',
            onDragStart: function($item, container, _super) {
                oldIndex = $item.index();
                $item.appendTo($item.parent());
                _super($item, container);
            },
            onDrop: function($item, container, _super) {
                newIndex = $item.index();

                if(newIndex != oldIndex) {
                    $item.closest('table').find('tbody tr').each(function (i, row) {
                        var SendData = {
                            'access_id' : access_id,
                            'access_token' : access_token,
                            'id': $(this).data('item-id'),
                            'order': i+1
                        };

                        row = $(row);
                        row.find('.order-num').html(i+1);

                        $.ajax({
                            type: 'post',
                            dataType: 'json',
                            url: url + 'order_table_banners_laterales',
                            data: SendData,
                            success: function(response)
                            {
                                if(response.msg != 'ok')
                                {
                                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                                }
                            }
                        });
                    });
                }

                _super($item, container);
            }
        });
    }

    if($('.toggle-bs').length > 0) {
        $(':checkbox.toggle-bs').bind('change', function() {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id' : access_id,
                'access_token' : access_token,
                'banner': $(this).data('banner'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'banner_status',
                data: SendData,
                success: function(response)
                {
                    // SI LA RESPUESTA ES ERROR
                    if(response.msg != 'ok')
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    if ($('.toggle-b').length > 0) {
        $(':checkbox.toggle-b').bind('change', function () {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'banne': $(this).data('banne'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'banne_status',
                data: SendData,
                success: function (response) {
                    // SI LA RESPUESTA ES ERROR
                    if (response.msg != 'ok') {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    //ESTE ES PARA SLIDER PRODUCTOS
    if ($('.toggle-sl').length > 0) {
        $(':checkbox.toggle-sl').bind('change', function () {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'slide': $(this).data('slide'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'slide_status',
                data: SendData,
                success: function (response) {
                    // SI LA RESPUESTA ES ERROR
                    if (response.msg != 'ok') {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    //ESTE ES PARA MARCAS
    if ($('.toggle-bn').length > 0) {
        $(':checkbox.toggle-bn').bind('change', function () {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'brand': $(this).data('brand'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'brand_status',
                data: SendData,
                success: function (response) {
                    // SI LA RESPUESTA ES ERROR
                    if (response.msg != 'ok') {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    // --- TOGGLE SOON ---
    if ($('.toggle-soon').length > 0) {
        $(':checkbox.toggle-soon').on('change', function () {
            var checked = this.checked;
            var pid = $(this).data('product');
            var SendData = {
                access_id: access_id,
                access_token: access_token,
                product: pid,
                value: checked ? 1 : 0
            };

            $.post(url + 'product_soon', SendData, function (r) {
                if (r.msg != 'ok') {
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', r.msg);
                    return;
                }
                if (checked) {
                    // DESACTIVA LOS OTROS DOS
                    var $new = $(':checkbox.toggle-new[data-product="' + pid + '"]');
                    var $out = $(':checkbox.toggle-out[data-product="' + pid + '"]');
                    if ($new.prop('checked')) $new.prop('checked', false);
                    if ($out.prop('checked')) $out.prop('checked', false);
                }
            }, 'json');
        });
    }

    // --- TOGGLE NEW ---
    if ($('.toggle-new').length > 0) {
        $(':checkbox.toggle-new').on('change', function () {
            var checked = this.checked;
            var pid = $(this).data('product');
            var SendData = {
                access_id: access_id,
                access_token: access_token,
                product: pid,
                value: checked ? 1 : 0
            };

            $.post(url + 'product_new', SendData, function (r) {
                if (r.msg != 'ok') {
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', r.msg);
                    return;
                }
                if (checked) {
                    var $soon = $(':checkbox.toggle-soon[data-product="' + pid + '"]');
                    var $out = $(':checkbox.toggle-out[data-product="' + pid + '"]');
                    if ($soon.prop('checked')) $soon.prop('checked', false);
                    if ($out.prop('checked')) $out.prop('checked', false);
                }
            }, 'json');
        });
    }

    // --- TOGGLE OUT ---
    if ($('.toggle-out').length > 0) {
        $(':checkbox.toggle-out').on('change', function () {
            var checked = this.checked;
            var pid = $(this).data('product');
            var SendData = {
                access_id: access_id,
                access_token: access_token,
                product: pid,
                value: checked ? 1 : 0
            };

            $.post(url + 'product_out', SendData, function (r) {
                if (r.msg != 'ok') {
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', r.msg);
                    return;
                }
                if (checked) {
                    var $soon = $(':checkbox.toggle-soon[data-product="' + pid + '"]');
                    var $new = $(':checkbox.toggle-new[data-product="' + pid + '"]');
                    if ($soon.prop('checked')) $soon.prop('checked', false);
                    if ($new.prop('checked')) $new.prop('checked', false);
                }
            }, 'json');
        });
    }


    //ESTE ES PARA CATEGORIAS
    if ($('.toggle-ct').length > 0) {
        $(':checkbox.toggle-ct').bind('change', function () {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'category': $(this).data('category'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'category_status',
                data: SendData,
                success: function (response) {
                    // SI LA RESPUESTA ES ERROR
                    if (response.msg != 'ok') {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }


    //ESTE ES PARA SUBCATEGORIAS
    if ($('.toggle-sc').length > 0) {
        $(':checkbox.toggle-sc').bind('change', function () {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id': access_id,
                'access_token': access_token,
                'category': $(this).data('category'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'subcategory_status',
                data: SendData,
                success: function (response) {
                    // SI LA RESPUESTA ES ERROR
                    if (response.msg != 'ok') {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    if ($('.rating-ticket').length > 0) {
        $('.rating-ticket').click(function () {
            var link = $(this).attr('href');

            setTimeout(function () {
                // CAMBIO CLAVE: Usar Swal.fire() en lugar de swal()
                Swal.fire({
                    title: '¿Cómo calificarías la solución que se le dio el ticket?',
                    text: "Almacenaremos tu respuesta por aspectos de calidad",
                    icon: 'info', // CAMBIO: 'type' se cambió a 'icon' en SweetAlert2
                    showCancelButton: true,
                    buttonsStyling: false,
                    // CAMBIO: Usar customClass en lugar de confirmButtonClass y cancelButtonClass
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    confirmButtonText: 'Buena',
                    cancelButtonText: 'Mala'
                }).then((result) => {
                    if (result.isConfirmed) // CAMBIO: Acceder a result.isConfirmed para el botón de confirmación
                    {
                        $(location).attr('href', link + 1);
                    }
                    else {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            $(location).attr('href', link + 2);
                        }
                    }
                })
            }, 200); // El setTimeout probablemente no es necesario, pero lo mantengo si tienes una razón específica para él.

            return false;
        });
    }

    if ($('#activities-table-body').length > 0) {
        console.log('El script se está ejecutando y encontró el elemento #activities-table-body.');

        // Guardar valores iniciales de los select
        $('#activity-form select').each(function () {
            $(this).data('default', $(this).val());
        });

        $('#add-activity').click(function (e) {
            e.preventDefault();

            // SE INICIALIZAN LAS VARIABLES
            var error  = 0;
            var fields = '';
            var actnum = $(this).data('actnum');
            var edit   = $(this).data('edit');

            // SI FECHA ESTA VACIO
            if($('#global_date').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Fecha</strong>, ';
            }

            // SI CLIENTE ESTA VACIO
            if($('#customer').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Cliente</strong>, ';
            }

            // SI RAZON SOCIAL ESTA VACIO
            if($('#company').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Razón Social</strong>, ';
            }

            // SI TOTAL ESTA VACIO
            if($('#total').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Monto Total Sin IVA</strong>, ';
            }

            // SI MEDIO ESTA VACIO
            if($('#contact_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Medio</strong>, ';
            }

            // SI HORA ESTA VACIO
            if($('#hour').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Hora</strong>, ';
            }

            // SI FACTURA ESTA VACIO
            if($('#invoice').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Factura</strong>, ';
            }

            // SI FORANEO ESTA VACIO
            if($('#foreing').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Foráneo</strong>, ';
            }

            // SI DURACION DE LA LLAMADA ESTA VACIO
            if($('#time_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Duración de Llamada</strong>, ';
            }

            // SI ENTRANTE/SALIENTE ESTA VACIO
            if($('#type_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Entrante/Saliente</strong>, ';
            }

            // SI SEGUIMIENTO ESTA VACIO
            if($('#status_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Iniciación, Seguimiento o Venta</strong>, ';
            }

            // SI PRODUCTO ESTA VACIO
            if($('#category_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Producto de Interés</strong>, ';
            }

            // SI HAY ERRORES
            if(error != 0)
            {
                // SE GENERA EL MENSAJE DE ERROR
                var msg = 'Encontramos algunos errores en el formulario.<br>Por favor llena ';
                fields = fields.substr(0, (fields.length -2)) + '.';

                // SI HAY UN SOLO ERROR
                if(error == 1)
                {
                    // SE CONCATENA EL TEXTO
                    msg += 'el campo ' + fields;
                }
                else
                {
                    // SE CONCATENA EL TEXTO
                    msg += 'los campos ' + fields;
                }

                // SE MUESTRA EL MENSAJE DE ERROR
                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', msg);

                return false;
            }

            // SE ESTABLECE LA INFORMACION
            var SendData = {
                'access_id'    : access_id,
                'access_token' : access_token,
                'act_num'      : actnum,
                'global_date'  : $('#global_date').val(),
                'customer'     : $('#customer').val(),
                'company'      : $('#company').val(),
                'total'        : $('#total').val(),
                'contact_id'   : $('#contact_id').val(),
                'hour'         : $('#hour').val(),
                'invoice'      : $('#invoice').val(),
                'foreing'      : $('#foreing').val(),
                'time_id'      : $('#time_id').val(),
                'type_id'      : $('#type_id').val(),
                'status_id'    : $('#status_id').val(),
                'category_id'  : $('#category_id').val(),
                'comments'     : $('#comments').val()
            };

            // ENVIAR DATOS AL SERVIDOR
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'add_activity',
                data: SendData,
                success: function(response) {
                    if(response.msg === 'ok')
                    {
                        // AGREGAR LA ACTIVIDAD A LA TABLA
                        const activity = response.data;

                        if(edit == 0)
                        {
                            const newRow = `
                            <tr>
                                <td>${activity.customer}</td>
                                <td>${activity.invoice}</td>
                                <td>${activity.company}</td>
                                <td>${activity.foreing}</td>
                                <td>${activity.contact}</td>
                                <td>${activity.hour}</td>
                                <td>${activity.time}</td>
                                <td>${activity.type}</td>
                                <td>${activity.status}</td>
                                <td>${activity.category}</td>
                                <td>${activity.comments}</td>
                                <td>${activity.total}</td>
                            </tr>`;
                            $('#activities-table-body').append(newRow);
                        }
                        else
                        {
                            const newRow = `
                            <tr>
                                <td>${activity.customer}</td>
                                <td>${activity.invoice}</td>
                                <td>${activity.company}</td>
                                <td>${activity.foreing}</td>
                                <td>${activity.contact}</td>
                                <td>${activity.hour}</td>
                                <td>${activity.time}</td>
                                <td>${activity.type}</td>
                                <td>${activity.status}</td>
                                <td>${activity.category}</td>
                                <td>${activity.comments}</td>
                                <td>${activity.total}</td>
                                <td><a title="Editar" class="activity-edit" data-activity-id="${activity.id}" href="#">Editar</a></td>
                            </tr>`;
                            $('#activities-table-body').append(newRow);
                        }

                        // LIMPIAR EL FORMULARIO
                        $('#activity-form').trigger('reset');
                        $('#activity-form select').each(function () {
                            $(this).val($(this).data('default')).trigger('change'); // RESTAURAR SELECTS
                        });

                        notify('top', 'center', 'ni ni-bell-55', 'success', 'animated bounceIn', 'animated bounceOut', 'Actividad agregada correctamente.');
                    }
                    else
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al agregar la actividad.');
                }
            });
        });

        $(document).on('click', '.activity-edit', function() {
            // SE INICIALIZAN LAS VARIABLES
            var activity_id = $(this).data('activity-id');

            // SE ESTABLECE LA INFORMACION
            var SendData = {
                'access_id'    : access_id,
                'access_token' : access_token,
                'activity_id'  : activity_id
            };

            // ENVIAR DATOS AL SERVIDOR
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'load_activity',
                data: SendData,
                success: function(response) {
                    if(response.msg === 'ok')
                    {
                        // AGREGAR LA ACTIVIDAD A LA TABLA
                        const activity = response.data;

                        $('#customer').val(activity.customer);
                        $('#company').val(activity.company);
                        $('#total').val(activity.total);
                        $('#contact_id').val(activity.contact_id);
                        $('#contact_id').trigger('change');
                        $('#hour').val(activity.hour);
                        $('#invoice').val(activity.invoice);
                        $('#invoice').trigger('change');
                        $('#foreing').val(activity.foreing);
                        $('#foreing').trigger('change');
                        $('#time_id').val(activity.time_id);
                        $('#time_id').trigger('change');
                        $('#type_id').val(activity.type_id);
                        $('#type_id').trigger('change');
                        $('#status_id').val(activity.status_id);
                        $('#status_id').trigger('change');
                        $('#category_id').val(activity.category_id);
                        $('#category_id').trigger('change');
                        $('#comments').val(activity.comments);

                        $('#add-activity').hide();
                        $('#edit-activity').show();
                        $('#edit-activity').data('activity-id', activity.id);
                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                    }
                    else
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al cargar la actividad.');
                }
            });

            return false;
        });


        $('#edit-activity').click(function (e) {
            e.preventDefault();

            // SE INICIALIZAN LAS VARIABLES
            var error       = 0;
            var fields      = '';
            var activity_id = $(this).data('activity-id');

            // SI CLIENTE ESTA VACIO
            if($('#customer').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Cliente</strong>, ';
            }

            // SI RAZON SOCIAL ESTA VACIO
            if($('#company').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Razón Social</strong>, ';
            }

            // SI TOTAL ESTA VACIO
            if($('#total').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Monto Total Sin IVA</strong>, ';
            }

            // SI MEDIO ESTA VACIO
            if($('#contact_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Medio</strong>, ';
            }

            // SI HORA ESTA VACIO
            if($('#hour').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Hora</strong>, ';
            }

            // SI FACTURA ESTA VACIO
            if($('#invoice').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Factura</strong>, ';
            }

            // SI FORANEO ESTA VACIO
            if($('#foreing').val() == '')
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Foráneo</strong>, ';
            }

            // SI DURACION DE LA LLAMADA ESTA VACIO
            if($('#time_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Duración de Llamada</strong>, ';
            }

            // SI ENTRANTE/SALIENTE ESTA VACIO
            if($('#type_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Entrante/Saliente</strong>, ';
            }

            // SI SEGUIMIENTO ESTA VACIO
            if($('#status_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Iniciación, Seguimiento o Venta</strong>, ';
            }

            // SI PRODUCTO ESTA VACIO
            if($('#category_id').val() == 0)
            {
                // SE ALMACENAN LOS ERRORES
                error++;
                fields += '<strong>Producto de Interés</strong>, ';
            }

            // SI HAY ERRORES
            if(error != 0)
            {
                // SE GENERA EL MENSAJE DE ERROR
                var msg = 'Encontramos algunos errores en el formulario.<br>Por favor llena ';
                fields = fields.substr(0, (fields.length -2)) + '.';

                // SI HAY UN SOLO ERROR
                if(error == 1)
                {
                    // SE CONCATENA EL TEXTO
                    msg += 'el campo ' + fields;
                }
                else
                {
                    // SE CONCATENA EL TEXTO
                    msg += 'los campos ' + fields;
                }

                // SE MUESTRA EL MENSAJE DE ERROR
                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', msg);

                return false;
            }

            // SE ESTABLECE LA INFORMACION
            var SendData = {
                'access_id'    : access_id,
                'access_token' : access_token,
                'activity_id'  : activity_id,
                'customer'     : $('#customer').val(),
                'company'      : $('#company').val(),
                'total'        : $('#total').val(),
                'contact_id'   : $('#contact_id').val(),
                'hour'         : $('#hour').val(),
                'invoice'      : $('#invoice').val(),
                'foreing'      : $('#foreing').val(),
                'time_id'      : $('#time_id').val(),
                'type_id'      : $('#type_id').val(),
                'status_id'    : $('#status_id').val(),
                'category_id'  : $('#category_id').val(),
                'comments'     : $('#comments').val()
            };

            // ENVIAR DATOS AL SERVIDOR
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'edit_activity',
                data: SendData,
                success: function(response) {
                    if(response.msg === 'ok')
                    {
                        window.location.replace(response.url);
                    }
                    else
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al editar la actividad.');
                }
            });
        });

        // BOTON PARA FINALIZAR ACTIVIDADES
        $('#finalize-activities').click(function (e) {
            e.preventDefault();

            // SE ESTABLECE LA INFORMACION
            var SendData = {
                'access_id'    : access_id,
                'access_token' : access_token,
                'act_num'      : $(this).data('actnum')
            };

            // ENVIAR DATOS AL SERVIDOR
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'finalize_activities',
                data: SendData,
                success: function(response) {
                    if(response.msg === 'ok')
                    {
                        window.location.replace(response.url);
                    }
                    else
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la solicitud:', error);
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al finalizar las actividades.');
                }
            });
        });
    }
    else
    {
        //console.error('No se encontró el elemento #activities-table-body en el DOM.');
    }


    if($('.toggle-hac').length > 0) {
        $(':checkbox.toggle-hac').bind('change', function() {
            // SE OBTIENEN LOS DATOS
            var SendData = {
                'access_id' : access_id,
                'access_token' : access_token,
                'activity': $(this).data('activity'),
                'value': (this.checked) ? 1 : 0
            };

            // SE REALIZA EL AJAX
            $.ajax({
                type: 'post',
                dataType: 'json',
                url: url + 'activity_completed',
                data: SendData,
                success: function(response)
                {
                    // SI LA RESPUESTA ES ERROR
                    if(response.msg != 'ok')
                    {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.msg);
                    }
                }
            });
        });
    }

    //este es el js para la venta al cliente, genera en el sale de la tabla
    // Verificar si el formulario de ventas existe antes de ejecutar el código
    if ($('#product-list').length > 0) {
        console.log('Script ejecutándose, elemento #product-list encontrado.');

        var clienteSeleccionado = false; // Variable para saber si se seleccionó un cliente

        // Deshabilitar campos al inicio
        $('#product_id, #quantity, #agregar-producto, #address_id').prop('disabled', true);
        $('#selected_address').val(''); // Limpia el domicilio al cargar

        // Función para formatear números con separadores de miles
        function formatNumber(number) {
            return number.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Función para actualizar totales desglosando el IVA
        function actualizarTotales() {
            var subtotal = 0;
            var totalPiezas = 0;
            var ivaTotal = 0;

            $('#product-list tr').each(function () {
                var cantidad = parseInt($(this).find('td:nth-child(2)').text().replace(/,/g, '')) || 0;
                var totalProductoConIVA = parseFloat($(this).find('td:nth-child(4)').text().replace('$', '').replace(/,/g, '')) || 0;

                // Desglosar el IVA correctamente
                var totalProductoSinIVA = totalProductoConIVA / 1.16;
                var ivaProducto = totalProductoConIVA - totalProductoSinIVA;

                subtotal += totalProductoSinIVA;
                ivaTotal += ivaProducto;
                totalPiezas += cantidad;
            });

            var total = subtotal + ivaTotal;

            // Mostrar valores corregidos en la vista
            $('#total-piezas').text(formatNumber(totalPiezas));
            $('#subtotal').text('$' + formatNumber(subtotal));
            $('#iva').text('$' + formatNumber(ivaTotal));
            $('#total').text('$' + formatNumber(total));
        }


        // INICIALIZA SELECT2 PARA EL SELECTOR DE CLIENTES
        // SE REALIA AJAX
        if ($('#customer_id').length > 0) {
            $('#customer_id').select2({
                placeholder: 'Busca y selecciona un cliente',
                allowClear: true,
                ajax: {
                    url: url + 'search_customers',
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            'access_id': access_id,
                            'access_token': access_token,
                            'term': params.term
                        };
                    },
                    processResults: function (response) {
                        console.log("Respuesta AJAX:", response); // Depuración en consola

                        // Verificamos si la respuesta tiene datos y está en el formato correcto
                        if (response.msg !== 'ok' || !Array.isArray(response.data)) {
                            console.error("Error: La respuesta de AJAX no es válida.", response);
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'No se encuentran clientes con esos datos.');
                            return { results: [] };
                        }


                        // Convertir los datos en el formato esperado por Select2
                        return {
                            results: response.data.map(function (customer) {
                                return {
                                    id: customer.id,
                                    text: `${customer.name} ${customer.last_name} - SAP: ${customer.sap_code} - CORREO: ${customer.email} - USUARIO: ${customer.username}`
                                };
                            })
                        };
                    },
                    error: function (xhr, status, error) {
                        console.error("Error AJAX:", status, error);
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error en la comunicación con el servidor.');
                    },
                    cache: true
                },
                minimumInputLength: 1
            });
        }


        // EVENTO AL SELECCIONAR UN CLIENTE
        if ($('#customer_id').length > 0) {
            $('#customer_id').on('select2:select', function (e) {
                // Validar si ya hay un cliente seleccionado
                if (clienteSeleccionado) {
                    notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'El cliente ya está seleccionado. Reinicia el formulario para cambiarlo.');
                    return;
                }

                var customerId = $(this).val();

                if (customerId && customerId !== '0') {
                    var requestData = {
                        'access_id': access_id,
                        'access_token': access_token,
                        'customer_id': customerId
                    };

                    // Solicitar domicilios y productos al servidor
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: url + 'get_customer_addresses',
                        data: requestData,
                        success: function (response) {
                            console.log("Respuesta AJAX (Domicilios):", response);

                            // Verificar respuesta válida
                            if (response.msg === 'ok' && Array.isArray(response.addresses)) {
                                var addressSelect = $('#address_id');
                                addressSelect.empty().append('<option value="0">Selecciona un domicilio</option>');

                                response.addresses.forEach(function (address) {
                                    var option = $('<option>')
                                        .val(address.id)
                                        .text(address.full_address);

                                    if (address.default) {
                                        option.prop('selected', true);
                                        $('#selected_address').val(address.full_address); // Mostrar domicilio por defecto
                                    }

                                    addressSelect.append(option);
                                });

                                notify('top', 'center', 'ni ni-bell-55', 'success', 'animated bounceIn', 'animated bounceOut', 'Domicilios cargados correctamente.');

                                // Cargar productos
                                cargarProductos(customerId);

                                // Habilitar campos
                                $('#address_id').prop('disabled', false);
                            } else {
                                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'Error al cargar los domicilios.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error AJAX (Domicilios):", error);
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al solicitar los domicilios. Intenta nuevamente.');
                        }
                    });
                } else {
                    notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Selecciona un cliente válido.');
                }
            });
        }


        // CARGAR PRODUCTOS RELACIONADOS AL CLIENTE
        // SE REALIZA AJAX
        function cargarProductos(customerId) {
            if (!customerId || customerId === '0') {
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Selecciona un cliente válido.');
                return;
            }

            var requestData = {
                'access_id': access_id,
                'access_token': access_token,
                'customer_id': customerId
            };

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: url + 'get_customer_prices',
                data: requestData,
                success: function (response) {
                    console.log("Respuesta AJAX (Productos):", response);

                    if (response.msg === 'ok' && Array.isArray(response.products)) {
                        var productSelect = $('#product_id');
                        productSelect.empty().append('<option value="0">Selecciona un producto</option>');

                        response.products.forEach(function (product) {
                            var option = $('<option>')
                                .val(product.id)
                                .text(`${product.name} - Disponible: ${formatNumber(product.available)} - Precio: $${formatNumber(product.price)}`)
                                .data('price', parseFloat(product.price));

                            productSelect.append(option);
                        });

                        notify('top', 'center', 'ni ni-bell-55', 'success', 'animated bounceIn', 'animated bounceOut', 'Productos cargados correctamente.');

                        // Habilitar campos de productos y deshabilitar selección de cliente
                        $('#product_id, #quantity, #agregar-producto').prop('disabled', false);
                        $('#customer_id').prop('disabled', true);
                        clienteSeleccionado = true;
                    } else {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'No se encontraron productos para este cliente.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error AJAX (Productos):", error);
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al solicitar los productos. Intenta nuevamente.');
                }
            });
        }


        // EVENTO PARA ACTUALIZAR EL PRECIO EN EL CAMPO DE PRECIO
        if ($('#customer_id').length > 0) {
            // Evento para actualizar el precio en el campo de precio
            $('#product_id').on('change', function () {
                var selectedPrice = $(this).find('option:selected').data('price') || 0;
                console.log('Precio seleccionado:', selectedPrice);
                $('#product-price').text(`$${formatNumber(selectedPrice)}`);

                // Habilitar botón de agregar producto solo si hay precio válido
                $('#agregar-producto').prop('disabled', selectedPrice <= 0);
            });

            // Evento para actualizar el domicilio seleccionado
            $('#address_id').on('change', function () {
                var selectedAddressText = $(this).find('option:selected').text();
                $('#selected_address').val(selectedAddressText || '');
            });

            // ACTIVAR BÚSQUEDA DINÁMICA DE PRODUCTOS CON SELECT2
            if ($('#product_id').length > 0) {
                $('#product_id').select2({
                    placeholder: 'Escribe para buscar un producto',
                    allowClear: true,
                    minimumInputLength: 1,
                    ajax: {
                        url: url + 'search_products',
                        type: 'POST',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                'access_id': access_id,
                                'access_token': access_token,
                                'customer_id': $('#customer_id').val(),
                                'term': params.term
                            };
                        },
                        processResults: function (response) {
                            console.log("📨 Respuesta AJAX (Productos):", response);

                            if (response.msg !== 'ok' || !Array.isArray(response.products)) {
                                console.error("Error: Respuesta inválida.", response);
                                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error en la búsqueda de productos.');
                                return { results: [] };
                            }

                            if (response.products.length === 0) {
                                notify('top', 'center', 'ni ni-bell-55', 'info', 'animated bounceIn', 'animated bounceOut', 'No se encontraron productos en la base de datos.');
                                return { results: [] };
                            }

                            return {
                                results: response.products.map(function (product) {
                                    return {
                                        id: product.id,
                                        text: `${product.name} - Disponible: ${formatNumber(product.available)} - Precio: $${formatNumber(product.price)}`,
                                        price: parseFloat(product.price) || 0,
                                        available: parseInt(product.available) || 0
                                    };
                                })
                            };
                        },
                        error: function (xhr, status, error) {
                            console.error("Error AJAX:", status, error);
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error en la comunicación con el servidor.');
                        },
                        cache: true
                    },
                    minimumInputLength: 2
                });
            }


            // EVENTO PARA ACTUALIZAR PRECIO Y DISPONIBLES AL SELECCIONAR UN PRODUCTO
            $('#product_id').on('select2:select', function (e) {
                var selectedData = e.params.data; // Obtener datos del producto seleccionado
                var selectedPrice = selectedData.price || 0;
                var selectedAvailable = selectedData.available || 0;

                console.log('Producto seleccionado:', selectedData.text);
                console.log('Precio extraído:', selectedPrice);
                console.log('Disponibles:', selectedAvailable);

                // Actualizar el precio en la vista
                $('#product-price').text(`$${formatNumber(selectedPrice)}`);

                // Verificar disponibilidad
                if (selectedAvailable <= 0) {
                    notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Este producto no tiene stock disponible.');
                    $('#agregar-producto').prop('disabled', true);
                } else {
                    $('#agregar-producto').prop('disabled', false);
                }
            });



            // Evento para agregar productos a la tabla
            $('#agregar-producto').on('click', function () {
                var product_id = $('#product_id').val();
                var product_name = $('#product_id option:selected').text().trim();
                var quantity = parseInt($('#quantity').val()) || 0;
                var customer_id = $('#customer_id').val();
                var type_id = $('#customer_id').find(':selected').data('type-id'); // Obtener el type_id del cliente

                console.log(' Validando producto:', { product_id, product_name, quantity });

                // Validar que no se agregue "Selecciona un producto"
                if (!product_id || product_id === '0' || product_name.toLowerCase().includes('selecciona')) {
                    notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Debes seleccionar un producto válido.');
                    return;
                }

                if (quantity <= 0) {
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'La cantidad debe ser mayor a 0.');
                    return;
                }

                //  ENVIAR DATOS AL SERVIDOR PARA OBTENER EL PRECIO CORRECTO
                var requestData = {
                    'access_id': access_id,
                    'access_token': access_token,
                    'customer_id': customer_id,
                    'type_id': type_id, // Enviar type_id
                    'product_id': product_id,
                    'quantity': quantity
                };

                console.log(" Enviando datos AJAX:", requestData);

                $.ajax({
                    type: 'POST',
                    url: url + 'add_product.json',
                    data: requestData,
                    dataType: 'json',
                    success: function (response) {
                        console.log("Respuesta AJAX:", response);

                        if (response.msg === 'ok' && response.price) {
                            var price = parseFloat(response.price) || 0;
                            var total = quantity * price;

                            if (price <= 0) {
                                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'El producto no tiene un precio válido.');
                                return;
                            }

                            // ELIMINAR FILAS NO VÁLIDAS (Ejemplo: "Selecciona un producto")
                            $('#product-list tr').each(function () {
                                var rowText = $(this).find('td:first').text().trim().toLowerCase();
                                if (rowText.includes('selecciona un producto')) {
                                    console.log("🗑 Eliminando fila con 'Selecciona un producto'.");
                                    $(this).remove();
                                }
                            });

                            // AGREGAR PRODUCTO A LA TABLA O ACTUALIZAR SI YA EXISTE
                            var existingRow = $('#product-list tr[data-product-id="' + product_id + '"]');
                            if (existingRow.length) {
                                var existingQuantity = parseInt(existingRow.find('.quantity').text());
                                var newQuantity = existingQuantity + quantity;
                                var newTotal = price * newQuantity;

                                existingRow.find('.quantity').text(newQuantity);
                                existingRow.find('.total').text('$' + formatNumber(newTotal));
                            } else {
                                var row = `
                        <tr data-product-id="${product_id}">
                            <td>${product_name}</td>
                            <td class="quantity text-right">${quantity}</td>
                            <td class="text-right price">$${formatNumber(price)}</td>
                            <td class="total text-right">$${formatNumber(total)}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm edit-product">
                                    <i class="ni ni-ruler-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm remove-product">
                                    <i class="ni ni-fat-remove"></i>
                                </button>
                            </td>
                        </tr>`;

                                $('#product-list').append(row);
                            }

                            actualizarTotales();

                            console.log('Producto agregado:', { product_id, product_name, quantity, price, total });

                            // Reiniciar selección de producto
                            $('#product_id').val('0').trigger('change');
                            $('#quantity').val('1');
                            $('#product-price').text('$0.00');
                            $('#agregar-producto').prop('disabled', true);
                        } else {
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'Error al agregar el producto.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error en la solicitud AJAX:", { xhr, status, error });
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error en la comunicación con el servidor.');
                    }
                });
            });

            //Evento para editar SOLO la cantidad de productos en la tabla
            $(document).on('click', '.edit-product', function () {
                var row = $(this).closest('tr');
                var currentQuantity = row.find('.quantity').text().trim();

                // Reemplazar cantidad por input editable
                row.find('.quantity').html(`<input type="number" class="form-control input-quantity" value="${currentQuantity}" min="1">`);

                // Cambiar icono de edición a confirmación
                $(this).removeClass('btn-info edit-product').addClass('btn-success save-product').html('<i class="ni ni-check-bold"></i>');
            });

            // EDITAR SOLO LA CANTIDAD DEL PRODUCTO
            $(document).on('click', '.edit-product', function () {
                var row = $(this).closest('tr');
                var currentQuantity = row.find('.quantity').text().trim();

                // Reemplazar cantidad por input editable
                row.find('.quantity').html(`<input type="number" class="form-control input-quantity" value="${currentQuantity}" min="1">`);

                // Cambiar icono de edición a confirmación
                $(this).removeClass('btn-info edit-product').addClass('btn-success save-product').html('<i class="ni ni-check-bold"></i>');
            });

            // GUARDAR EDICIÓN DE CANTIDAD Y RE-CALCULAR TOTAL CORRECTAMENTE
            $(document).on('click', '.save-product', function () {
                var row = $(this).closest('tr');

                // Obtiene la cantidad ingresada en el input
                var newQuantity = parseInt(row.find('.input-quantity').val()) || 1;

                // Obtiene el precio unitario correctamente desde la celda de precio
                var price = parseFloat(row.find('.price').text().replace('$', '').replace(/,/g, '').trim()) || 0;

                // Verifica que la cantidad sea válida
                if (newQuantity <= 0) {
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'La cantidad debe ser mayor a 0.');
                    return;
                }

                // Calcula el nuevo total correctamente
                var newTotal = newQuantity * price;

                console.log(`✏ Editando producto: Cantidad ${newQuantity}, Precio Unitario: ${price}, Nuevo Total: ${newTotal}`);

                // Restaurar valores en la tabla
                row.find('.quantity').text(newQuantity); // Cantidad corregida
                row.find('.total').text('$' + formatNumber(newTotal)); // Total corregido (multiplicado correctamente)

                // Restaurar botón a edición
                $(this).removeClass('btn-success save-product').addClass('btn-info edit-product').html('<i class="ni ni-ruler-pencil"></i>');

                // Actualizar totales generales después de la edición
                actualizarTotales();
            });

            //Evento para eliminar productos con delegación de eventos
            $(document).on('click', '.remove-product', function () {
                $(this).closest('tr').remove();
                actualizarTotales();
                console.log('Producto eliminado');
            });

            // Evento para reiniciar todo el formulario
            $('#reiniciar').on('click', function () {
                if (confirm('¿Estás seguro de que deseas reiniciar el formulario? Esto eliminará todos los datos agregados.')) {
                    $('#customer_id').val(null).trigger('change').prop('disabled', false);
                    $('#product_id').val('0').prop('disabled', true);
                    $('#quantity').val('1').prop('disabled', true);
                    $('#agregar-producto').prop('disabled', true);
                    $('#product-list').empty();
                    $('#subtotal, #total, #iva, #total-piezas').text('$0.00');
                    $('#product-price').text('$0.00');
                    $('#address_id').empty().append('<option value="0">Selecciona un domicilio</option>');
                    $('#selected_address').val('');
                    clienteSeleccionado = false;
                }
            });
        }


        if ($('.toggle-fsi').length > 0) {
            //  Evento para manejar el cambio en el toggle de factura
            $('.toggle-fsi').on('change', function () {
                var facturaSeleccionada = $(this).is(':checked');
                var customerId = $('#customer_id').val();

                if (facturaSeleccionada) {
                    if (customerId && customerId !== '0') {
                        console.log('Solicitud de datos de facturación para el cliente:', customerId);

                        $.ajax({
                            type: 'POST',
                            url: url + 'get_customer_invoice_data',
                            data: {
                                'access_id': access_id,
                                'access_token': access_token,
                                'customer_id': customerId
                            },
                            dataType: 'json',
                            success: function (response) {
                                console.log('Respuesta AJAX:', response);

                                if (response.msg === 'ok' && Array.isArray(response.invoice_data)) {
                                    var invoiceSelect = $('#invoice-select');
                                    var invoiceData = $('#invoice-data');

                                    invoiceSelect.empty().append('<option value="0">Selecciona una opción</option>');

                                    // Poblar el select con las opciones de facturación
                                    response.invoice_data.forEach(function (data) {
                                        var option = $('<option>')
                                            .val(data.id)
                                            .text(data.formatted);

                                        if (data.default) {
                                            option.prop('selected', true);
                                            invoiceData.val(data.formatted);
                                        }

                                        invoiceSelect.append(option);
                                    });

                                    $('#invoice-container').show();
                                } else {
                                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'Error al cargar los datos de facturación.');
                                    $('.toggle-fsi').prop('checked', false);
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('Error en la solicitud AJAX:', error);
                                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error en la comunicación con el servidor.');
                            }
                        });
                    } else {
                        notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Selecciona un cliente antes de activar la facturación.');
                        $('.toggle-fsi').prop('checked', false);
                    }
                } else {
                    $('#invoice-select').empty();
                    $('#invoice-data').val('').prop('readonly', true);
                    $('#invoice-container').hide();
                }
            });

            // Evento para actualizar el textarea al seleccionar una opción del select
            $('#invoice-select').on('change', function () {
                var selectedText = $(this).find(':selected').text();
                $('#invoice-data').val(selectedText);
            });
        }


        // Finalizar pedido con AJAX
        if ($('#finalizar-pedido').length > 0) {
            $('#finalizar-pedido').on('click', function () {
                console.log("Finalización de pedido iniciada.");

                // OBTENER DATOS DEL FORMULARIO
                var customerId = $('#customer_id').val();
                var paymentId = $('#payment_id').val();
                var addressId = $('#address_id').val();
                var taxDatumId = $('#invoice-select').val() || null; // Permitir que sea opcional
                var products = [];
                var error = 0;
                var fields = '';

                // RECORRER LA TABLA DE PRODUCTOS Y EXTRAER DATOS
                $('#product-list tr').each(function () {
                    var productId = $(this).data('product-id');
                    var quantity = parseInt($(this).find('td:nth-child(2)').text().trim()) || 0;
                    var price = parseFloat($(this).find('td:nth-child(3)').text().replace('$', '').replace(/,/g, '').trim()) || 0;

                    console.log(`Producto ID: ${productId}, Cantidad: ${quantity}, Precio: ${price}`);

                    // VALIDAR PRODUCTOS ANTES DE AGREGARLOS
                    if (productId && quantity > 0 && price > 0) {
                        products.push({
                            id: productId,
                            quantity: quantity,
                            price: price
                        });
                    }
                });

                console.log("Productos a enviar:", products);

                // **VALIDACIONES CONSOLIDADAS**
                if (!customerId || customerId === '0') {
                    error++;
                    fields += '<strong>Cliente</strong>, ';
                }

                if (!addressId || addressId === '0') {
                    error++;
                    fields += '<strong>Domicilio</strong>, ';
                }

                if (paymentId != 2) { // Verificar que sea "Transferencia"
                    error++;
                    fields += '<strong>Método de Pago (Debe ser Transferencia)</strong>, ';
                }

                if (products.length === 0) {
                    error++;
                    fields += '<strong>Productos</strong>, ';
                }

                // SI HAY ERRORES, SE MUESTRA UN SOLO MENSAJE
                if (error !== 0) {
                    var msg = 'Encontramos algunos errores en el formulario.<br>Por favor completa ';
                    fields = fields.substr(0, (fields.length - 2)) + '.'; // Elimina la última coma

                    msg += (error === 1) ? 'el campo ' + fields : 'los campos ' + fields;

                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', msg);
                    return;
                }

                //  **MENSAJE DE CONFIRMACIÓN CON NOTIFY()**
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut',
                    '¿Estás seguro de finalizar esta venta? <br>' +
                    '<button id="confirm-finalizar" class="btn btn-success btn-sm m-2">Sí, finalizar</button> ' +
                    '<button id="cancel-finalizar" class="btn btn-danger btn-sm m-2">Cancelar</button>'
                );

                // EVENTO PARA DETECTAR CUANDO EL USUARIO CONFIRME LA VENTA
                $(document).off('click', '#confirm-finalizar').one('click', '#confirm-finalizar', function () {
                    console.log("Enviando datos AJAX...");

                    var requestData = {
                        'access_id': access_id,
                        'access_token': access_token,
                        'customer_id': customerId,
                        'payment_id': paymentId,
                        'address_id': addressId,
                        'tax_datum': taxDatumId,
                        'products': products
                    };

                    console.log("📡 Datos enviados:", requestData);

                    $.ajax({
                        type: 'POST',
                        url: url + 'finalizar_transferencia',
                        data: requestData,
                        dataType: 'json',
                        success: function (response) {
                            console.log("Respuesta AJAX:", response);

                            if (response.msg === 'ok') {
                                notify('top', 'center', 'ni ni-check-bold', 'success', 'animated bounceIn', 'animated bounceOut', 'Venta registrada correctamente como pendiente para transferencia.');

                                setTimeout(() => {
                                    if (response.data.redirect === 'reload') {
                                        window.location.reload(); // Recargar la misma página
                                    } else {
                                        window.location.href = response.data.redirect; // Redirigir si hay otra URL
                                    }
                                }, 1500);
                            } else {
                                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'Error al procesar la solicitud.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(" Error en la solicitud AJAX:", { xhr, status, error });
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al finalizar el pedido. Intenta nuevamente.');
                        }
                    });
                });

                // EVENTO PARA CANCELAR
                $(document).off('click', '#cancel-finalizar').one('click', '#cancel-finalizar', function () {
                    notify('top', 'center', 'ni ni-bell-55', 'info', 'animated bounceIn', 'animated bounceOut', 'Finalización cancelada.');
                });
            });
        }



    } //fin de if ventas para el cliente web


    // EVENTOS SOCIOS - FILTRO
    $(document).ready(function () {
        $('#filtro_socios').on('change', function () {
            var filtro = $(this).val();

            // Validar que no sea opción vacía
            if (filtro === 'none' || filtro === '') return;

            // Opcional: mostrar loader
            $('.table-responsive tbody.list').html('<tr><td colspan="13">Cargando datos...</td></tr>');

            // Desactivar el select mientras se procesa
            $(this).prop('disabled', true);

            $.ajax({
                url: url + 'filtro_socios',
                method: 'POST',
                dataType: 'json',
                data: {
                    filter: filtro
                },
                success: function (response) {
                    if (response.msg === 'ok') {
                        $('.table-responsive tbody.list').html(response.html);
                    } else {
                        $('.table-responsive tbody.list').html('<tr><td colspan="13">' + response.html + '</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    $('.table-responsive tbody.list').html('<tr><td colspan="13">Ocurrió un error al procesar la solicitud: ' + error + '</td></tr>');
                },
                complete: function () {
                    // Reactivar el select después de la llamada
                    $('#filtro_socios').prop('disabled', false);
                }
            });
        });
    });

    ///////EMPIEZO MODULO DE COTIZACIONES DE SOCIOS DE NEGOCIOS SOLO SOCIOS DADOS DE ALTA AGREGA COTIZACIONES PARA EL SOCIO

    // Verificar si el formulario de ventas existe antes de ejecutar el código
    if ($('#productlist').length > 0) {
        console.log('Script ejecutándose, elemento #productlist encontrado.');

        var partnerSeleccionado = false; // Variable para saber si se seleccionó un cliente

        // Deshabilitar campos al inicio
        // Estado inicial: solo socio activo, todo lo demás deshabilitado
        $('#product_id').prop('disabled', true);
        $('#quantity').prop('disabled', true);
        $('#agregar-producto-socio').prop('disabled', true);
        $('#address_id').prop('disabled', true);
        $('#partner_contact_id').prop('disabled', true);
        $('#reference').prop('disabled', true);
        $('#valid_date').prop('disabled', true);
        $('#comments').prop('disabled', true);
        $('#payment_id').prop('disabled', true);
        $('#finalizar-cotizacion').prop('disabled', true);
        $('#reiniciarcot').prop('disabled', true);
        $('#agregar-productos-marca').prop('disabled', true);
        $('#seller_asig_id').prop('disabled', true);
        $('#agregar-productos-marca').val(null).trigger('change');
        $('#agregar-productos-rango').prop('disabled', true);
        $('#codigo-inicio').val('');
        $('#codigo-fin').val('');

        $('#selected_address').val('');
        $('#partner_id').prop('disabled', false); // Seleccionar socio activo

        $('#selected_address').val(''); // Limpia el domicilio al cargar
        $('#payment_id').val('3');

        $('#partner_id').val(null).trigger('change');
        $('#partner_contact_id').val(null).trigger('change');
        $('#product_id').val(null).trigger('change');
        $('#address_id').val('0');
        //$('#paymet_id').val('3');

        // EVENTO PARA ACTUALIZAR PRECIO Y DISPONIBLES AL SELECCIONAR UN PRODUCTO borrrar luego
        // Función utilitaria para dar formato bonito a los precios
        function formatNumber(number) {
            // Log para ver el valor que llega a la función
            console.log('[FormatNumber] Formateando número:', number);
            return Number(number).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }



        // Función para actualizar los totales generales de la cotización
        function actualizarTotalesCotizacion() {
            console.log('[Totales] Ejecutando actualizarTotalesCotizacion');
            var subtotal = 0;
            var totalPiezas = 0;
            var ivaTotal = 0;

            $('#productlist tr').each(function (i) {
                // Si hay un input dentro de .quantity, usa el valor del input
                var cantidadCell = $(this).find('.quantity');
                var cantidadInput = cantidadCell.find('input');
                var cantidad = 0;
                if (cantidadInput.length > 0) {
                    cantidad = parseInt(cantidadInput.val()) || 0;
                } else {
                    cantidad = parseInt(cantidadCell.text()) || 0;
                }

                // Igual para el total, pero normalmente siempre es texto plano
                var totalCell = $(this).find('.total');
                var totalProductoConIVA = parseFloat(totalCell.text().replace('$', '').replace(/,/g, '')) || 0;
                // O bien, mejor usar el atributo data-total si lo tienes
                if (totalCell.data('total')) {
                    totalProductoConIVA = parseFloat(totalCell.data('total')) || 0;
                }

                console.log(`[Totales] Fila ${i} | cantidad: ${cantidad} | totalProductoConIVA: ${totalProductoConIVA}`);

                var totalProductoSinIVA = totalProductoConIVA ;
                var ivaProducto = totalProductoConIVA * 0.16;

                subtotal += totalProductoSinIVA;
                ivaTotal += ivaProducto;
                totalPiezas += cantidad;
            });

            var total = subtotal + ivaTotal;

            $('#total-piezas').text(formatNumber(totalPiezas));
            $('#subtotal').text('$' + formatNumber(subtotal));
            $('#iva').text('$' + formatNumber(ivaTotal));
            $('#total').text('$' + formatNumber(total));

            console.log(`[Totales] Subtotal: ${subtotal}, IVA: ${ivaTotal}, Total: ${total}, Piezas: ${totalPiezas}`);
        }


        // EVENTO AL SELECCIONAR UN CLIENTE
        if ($('#partner_id').length > 0) {
            $('#partner_id').on('select2:select', function (e) {
                if (partnerSeleccionado) {
                    $.notifyClose();
                    notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'El cliente ya está seleccionado. Reinicia el formulario para cambiarlo.');
                    return;
                }

                var partnerId = $(this).val();
                if (partnerId && partnerId !== '0') {
                    $('#partner_contact_id, #reference, #valid_date, #reiniciarcot').prop('disabled', false);
                    $('#product_id, #quantity, #agregar-producto-socio, #comments, #payment_id, #finalizar-cotizacion, #address_id, #payment_id , #agregar-productos-marca, #agregar-productos-rango ').prop('disabled', true);

                    var requestData = {
                        'access_id': access_id,
                        'access_token': access_token,
                        'partner_id': partnerId
                    };

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: url + 'get_partner_addresses',
                        data: requestData,
                        success: function (response) {
                            console.log("Respuesta AJAX (Domicilios):", response);
                            $('#address_id').data('has-addresses', response.has_addresses || false);


                            if (response.msg === 'ok' && Array.isArray(response.addresses)) {
                                var addressSelect = $('#address_id');
                                addressSelect.empty().append('<option value="0">Selecciona un domicilio</option>');

                                response.addresses.forEach(function (address) {
                                    var option = $('<option>')
                                        .val(address.id)
                                        .text(address.full_address);

                                    if (address.default) {
                                        option.prop('selected', true);
                                        $('#selected_address').val(address.full_address);
                                    }

                                    addressSelect.append(option);
                                });

                                $.notifyClose();
                                notify('top', 'center', 'ni ni-bell-55', 'success', 'animated bounceIn', 'animated bounceOut', 'Domicilios cargados correctamente.');

                                cargarProductosPartner(partnerId);


                            } else {
                                $.notifyClose();
                                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'Socio sin datos adicionales necesarios para la captura, solicitar al socio o darlos de alta.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error AJAX (Domicilios):", error);
                            $.notifyClose();
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al solicitar los domicilios. Intenta nuevamente.');
                        }
                    });
                } else {
                    $.notifyClose();
                    notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Selecciona un cliente válido.');
                }
            });
        }

        // Función de validación de los 3 campos clave
        function habilitarCamposFinales() {
            const contacto = $('#partner_contact_id').val();
            const referencia = $('#reference').val().trim();
            const fechaValidez = $('#valid_date').val().trim();

            const completos = contacto && referencia !== '' && fechaValidez !== '';
            if (completos) {
                $('#product_id, #quantity, #agregar-producto-socio, #comments, #payment_id, #finalizar-cotizacion, #address_id, #seller_asig_id, #agregar-productos-marca').prop('disabled', false);
                $('#partner_id, #partner_contact_id, #reference, #valid_date').prop('disabled', true);
                console.log('✔️ Datos completos. Avance tras agregar contacto nuevo.');
            }

        }

        $('#partner_contact_id, #reference, #valid_date').on('change keyup', habilitarCamposFinales);


        // INICIALIZA SELECT2 PARA EL SELECTOR DE SOCIOS
        // SE REALIA AJAX
        if ($('#partner_id').length > 0) {
            $('#partner_id').select2({
                placeholder: 'Busca y selecciona un socio',
                allowClear: true,
                ajax: {
                    url: url + 'search_partners',
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            'access_id': access_id,
                            'access_token': access_token,
                            'term': params.term
                        };
                    },
                    processResults: function (response) {
                        console.log("Respuesta AJAX:", response); // Depuración en consola

                        // Verificamos si la respuesta tiene datos y está en el formato correcto
                        if (response.msg !== 'ok' || !Array.isArray(response.data)) {
                            console.error("Error: La respuesta de AJAX no es válida.", response);
                            $.notifyClose();
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'No se encuentran clientes con esos datos.');
                            return { results: [] };
                        }


                        // Convertir los datos en el formato esperado por Select2
                        return {
                            results: response.data.map(function (partner) {
                                return {
                                    id: partner.id,
                                    text: `${partner.name} - SAP: ${partner.code_sap} `
                                };
                            })
                        };
                    },
                    error: function (xhr, status, error) {
                        console.error("Error AJAX:", status, error);
                        $.notifyClose();
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error en la comunicación con el servidor.');
                    },
                    cache: true
                },
                minimumInputLength: 1
            });
        }

        // INICIALIZA SELECT2 PARA EL SELECTOR DE CONTACTOS SOCIOS
        if ($('#partner_id').length > 0) {
            console.log('[Select2] Iniciando carga del select de contactos para el socio seleccionado.');

            $('#partner_contact_id').select2({
                placeholder: 'Contactos del Socio',
                allowClear: true,
                ajax: {
                    url: url + 'search_partners_contacts',
                    method: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function () {
                        console.log('[Select2] Ejecutando función de datos para envío...');

                        if (typeof access_id === 'undefined' || typeof access_token === 'undefined') {
                            $.notifyClose();
                            console.error("[Select2] Error: Variables de autenticación no definidas (access_id o access_token).");
                            return {};
                        }

                        const partnerId = $('#partner_id').val();

                        if (!partnerId) {
                            console.warn("[Select2] Advertencia: No se ha seleccionado un socio (partner_id está vacío).");
                            return {};
                        }

                        const payload = {
                            access_id: access_id,
                            access_token: access_token,
                            partner_id: partnerId,
                            term: ''
                        };

                        console.log('[Select2] Payload generado para AJAX:', payload);
                        return payload;
                    },
                    processResults: function (response) {
                        console.log('[Select2] Respuesta AJAX recibida:', response);

                        if (response.msg !== 'ok') {
                            console.warn('[Select2] Respuesta no exitosa. Mensaje:', response.msg);
                            return { results: [] };
                        }

                        if (!Array.isArray(response.data)) {
                            console.error('[Select2] Error: La respuesta no contiene un array válido en "data".');
                            return { results: [] };
                        }

                        const formattedResults = response.data.map(function (contact) {
                            return {
                                id: contact.id,
                                text: `${contact.name} ${contact.last_name || ''}`.trim()
                            };
                        });

                        if (formattedResults.length === 0) {
                            $('#nuevo-contacto').removeClass('d-none').prop('disabled', false); // Mostrar botón
                        } else {
                            $('#nuevo-contacto').addClass('d-none'); // Ocultarlo si hay contactos
                        }


                        console.log('[Select2] Resultados transformados:', formattedResults);
                        return { results: formattedResults };
                    },
                    error: function (xhr, status, error) {
                        console.error('[Select2] Error en la solicitud AJAX:', status, error);
                        $.notifyClose();
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error en la comunicación con el servidor.');
                    },
                    cache: true
                }
            });

            console.log('[Select2] Inicialización del componente completada.');
        }

        // Detectar cuando se abre el Select2 de contactos
        $('#partner_contact_id').on('select2:open', function () {
            setTimeout(() => {
                const noResults = $('.select2-results__message').length > 0;

                if (noResults) {
                    $('#nuevo-contacto-container').removeClass('d-none'); // Mostrar contenedor
                    $('#nuevo-contacto').prop('disabled', false);
                } else {
                    $('#nuevo-contacto-container').addClass('d-none'); // Ocultar si hay opciones
                }
            }, 150); // Tiempo suficiente para que se rendericen los resultados
        });

        if ($('.agregar-contacto-modal').length > 0) {
            $(document).on('click', '.agregar-contacto-modal', function (e) {
                e.preventDefault();
                const partnerId = $('#partner_id').val();

                console.log('🟢 Click en modal de nuevo contacto | Partner ID:', partnerId);

                if (!partnerId || partnerId === '0') {
                    notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Debes seleccionar un socio antes.');
                    return;
                }

                console.log('🧩 Mostrando Swal...');

                Swal.fire({
                    title: 'Agregar nuevo contacto',
                    html: `
                <input id="nuevo_nombre" class="form-control mb-2" placeholder="Nombre">
                <input id="nuevo_apellido" class="form-control" placeholder="Apellido">
            `,
                    confirmButtonText: 'Guardar',

                    preConfirm: () => {
                        const nombre = $('#nuevo_nombre').val().trim();
                        const apellido = $('#nuevo_apellido').val().trim();

                        if (!nombre || !apellido) {
                            trigger('change')
                            Swal.showValidationMessage('Debes ingresar nombre y apellido');
                            return false;
                        }


                        const datos = {
                            access_id: access_id,
                            access_token: access_token,
                            partner_id: partnerId,
                            name: nombre,
                            last_name: apellido
                        };

                        console.log('📤 Enviando contacto desde preConfirm:', datos);

                        // ⬇️ Devuelve la promesa del AJAX a Swal
                        return $.ajax({
                            type: 'POST',
                            url: url + 'add_partner_contact',
                            dataType: 'json',
                            data: datos,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        }).then(response => {
                            if (response.msg === 'ok') {
                                return response.data.contact;
                            } else {
                                Swal.showValidationMessage(response.message || 'Error al guardar el contacto.');
                                return false;
                            }
                        }).catch(err => {
                            Swal.showValidationMessage('Error al guardar el contacto. Intenta nuevamente.');
                            console.error('❌ Error AJAX:', err);
                            return false;
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then(result => {
                    if (result.isConfirmed && result.value) {
                        const contacto = result.value;
                        const texto = `${contacto.name} ${contacto.last_name}`;
                        const option = new Option(texto, contacto.id, true, true);
                        $('#partner_contact_id').append(option).trigger('change');
                        $('#partner_contact_id').prop('disabled', true);
                        $('#nuevo-contacto-container').addClass('d-none');
                        habilitarCamposFinales();

                        // 🔧 Lanzar manualmente evento para que los campos se activen
                        $('#partner_contact_id').trigger('change');
                        notify('top', 'center', 'ni ni-check-bold', 'info', 'animated bounceIn', 'animated bounceOut', 'Contacto agregado, ya puedes continuar.');

                    } else {
                        console.log('❎ Cancelado o no se agregó contacto');
                    }
                });
            });
        }



        // CARGAR PRODUCTOS RELACIONADOS AL SOCIO
        // SE REALIZA AJAX
        function cargarProductosPartner(partnerId) {
            if (!partnerId || partnerId === '0') {
                $.notifyClose();
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Selecciona un socio válido.');
                return;
            }

            var requestData = {
                'access_id': access_id,
                'access_token': access_token,
                'partner_id': partnerId
            };

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: url + 'get_partner_prices',
                data: requestData,
                success: function (response) {
                    console.log("Respuesta AJAX (Productos):", response);

                    if (response.msg === 'ok' && Array.isArray(response.products)) {
                        var productSelect = $('#product_id');
                        productSelect.empty().append('<option value="0">Selecciona un producto</option>');

                        response.products.forEach(function (product) {
                            var option = $('<option>')
                                .val(product.id)
                                .text(`${product.name} - Disponible: ${formatNumber(product.available)} - Precio: $${formatNumber(product.price)}`) ///este es el que se lleva al productslist el que pone los datos en el pedido
                                .data('price', parseFloat(product.price))
                                .data('available', parseInt(product.available))
                                .data('code', product.code);

                            productSelect.append(option);
                        });

                        $.notifyClose();
                        notify('top', 'center', 'ni ni-bell-55', 'success', 'animated bounceIn', 'animated bounceOut', 'Productos cargados correctamente.');

                        // Habilitar campos de productos y deshabilitar selección de cliente
                        $('#product_id, #quantity, #agregar-producto-socio, #payment_id, #comments').prop('disabled', false);
                        $('#partner_id').prop('disabled', true);
                        $('#codigo-inicio, #codigo-fin').on('input', function () {
                            if ($('#codigo-inicio').val() !== '' && $('#codigo-fin').val() !== '') {
                                $('#agregar-productos-rango').prop('disabled', false);
                            } else {
                                $('#agregar-productos-rango').prop('disabled', true);
                            }
                        });

                        partnerSeleccionado = true;
                    } else {
                        $.notifyClose();
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'No se encontraron productos para este cliente.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Error AJAX (Productos):", error);
                    $.notifyClose();
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al solicitar los productos. Intenta nuevamente.');
                }
            });
        }




        // Evento cuando seleccionas un producto
        $('#product_id').on('select2:select', function (e) {
            var data = e.params.data;
            $('#product_code_hidden').val(data.code || '');
            $('#product_name_hidden').val(data.name || data.text || '');

            // Si ya está en la tabla, deshabilita
            if ($('#productlist tr[data-product-id="' + data.id + '"]').length) {
                let precioExistente = $('#productlist tr[data-product-id="' + data.id + '"] .price').text().replace('$', '').replace(/,/g, '');
                $('#product-price-socio').val(Number(precioExistente).toFixed(2)).prop('readonly', true);
                $('#agregar-producto-socio').prop('disabled', true);
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Este producto ya existe en la cotización.');
            } else {
                // Si tiene precio en BD, lo sugiere pero editable
                $('#product-price-socio').val(Number(data.price || 0).toFixed(2)).prop('readonly', false);
                $('#agregar-producto-socio').prop('disabled', true); // Hasta que se capture cantidad y precio > 0
            }
        });

        ///imagen
        // Al seleccionar un producto con Select2
        $('#product_id').on('select2:select', function (e) {
            var data = e.params.data;

            // Muestra la imagen si existe la URL
            if (data.image_url) {
                console.log('[Preview] Mostrando previsualización:', data.image_url, data);
                $('#img-preview-producto').attr('src', data.image_url);
            } else {
                $('#img-preview-producto').attr('src', '/assets/uploads/thumb_no_image.png');
            }
            $('#nombre-preview-producto').text(data.name || '');
            $('#codigo-preview-producto').text(data.code ? 'Código: ' + data.code : '');
            $('#preview-producto-container').show();
            console.log('[Preview] Mostrando previsualización:', data.image_url);
        });

        // Ocultar preview al limpiar selección
        $('#product_id').on('select2:clear', function () {
            $('#preview-producto-container').hide();
        });

        // También ocultar al agregar producto
        $('#agregar-producto-socio').on('click', function () {
            $('#preview-producto-container').hide();
        });
        ////image

        // Limpiar al limpiar producto
        $('#product_id').on('select2:clear', function () {
            $('#product_code_hidden').val('');
            $('#product_name_hidden').val('');
            $('#product-price-socio').val('0.00').prop('readonly', false);
            $('#agregar-producto-socio').prop('disabled', true);
        });

        // Habilitar botón sólo si hay producto, cantidad y precio válidos
        $('#quantity, #product-price-socio').on('input', function () {
            var precio = parseFloat($('#product-price-socio').val()) || 0;
            var cantidad = parseInt($('#quantity').val()) || 0;
            var prodSel = $('#product_id').val();
            if ($('#productlist tr[data-product-id="' + prodSel + '"]').length) {
                $('#agregar-producto-socio').prop('disabled', true);
            } else {
                $('#agregar-producto-socio').prop('disabled', (!prodSel || cantidad <= 0 || precio <= 0));
            }
        });

        // Evento para agregar producto a la tabla de cotización
        $('#agregar-producto-socio').on('click', function () {
            console.log('[Agregar Producto] Click en botón agregar producto');

            var product_id = $('#product_id').val();
            var product_code = $('#product_code_hidden').val();
            var product_name = $('#product_name_hidden').val();
            var price = parseFloat($('#product-price-socio').val()) || 0;
            var quantity = parseInt($('#quantity').val()) || 0;

            // Evita duplicados
            if ($('#productlist tr[data-product-id="' + product_id + '"]').length) {
                console.warn('[Agregar Producto] El producto ya existe en la cotización');
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Este producto ya existe en la cotización.');
                return;
            }

            // Construir la fila usando atributos data-* para los valores numéricos REALES
            var total = price * quantity;
            var row = `
                <tr data-product-id="${product_id}">
                        <td>${product_code}</td>
                        <td>${product_name}</td>
                        <td class="quantity text-right">${quantity}</td>
                        <td class="price text-right" data-price="${price}">$${formatNumber(price)}</td>
                        <td class="total text-right" data-total="${total}">$${formatNumber(total)}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm edit-product"><i class="ni ni-ruler-pencil"></i></button>
                            <button type="button" class="btn btn-danger btn-sm remove-product"><i class="ni ni-fat-remove"></i></button>
                        </td>
                    </tr>
                `;
            console.log('[Agregar Producto] Agregando fila:', row);


            $('#productlist').append(row);
            setTimeout(actualizarTotalesCotizacion, 30);

            // Log del total de filas después de agregar
            console.log('[Agregar Producto] Total filas:', $('#productlist tr').length);

            // ¡Asegúrate de refrescar los data-* en el DOM!
            $('#productlist tr:last .total').data('total', total);
            $('#productlist tr:last .price').data('price', price);

            // Recalcula los totales después de agregar producto
            actualizarTotalesCotizacion();

            // Limpiar SOLO los campos de captura de producto
            $('#product_id').val(null).trigger('change.select2');
            $('#quantity').val('1');
            $('#product-price-socio').val('0.00').prop('readonly', false);
            $('#agregar-producto-socio').prop('disabled', true);
        });



        // Evento para actualizar el domicilio seleccionado socio
        $('#address_id').on('change', function () {
            var selectedAddressText = $(this).find('option:selected').text();
            $('#selected_address').val(selectedAddressText || '');
        });
        //este ya quedo

        // Evento para poner cantidad y precio editables (botón lápiz)
        $(document).on('click', '.edit-product', function () {
            var row = $(this).closest('tr');
            var currentQuantity = row.find('.quantity').text().trim();
            var currentPrice = row.find('.price').data('price') || 0;
            // Muestra inputs para cantidad y precio
            row.find('.quantity').html(`<input type="number" class="form-control input-quantity" value="${currentQuantity}" min="1" style="max-width:70px;">`);
            row.find('.price').html(`<input type="number" class="form-control input-price" value="${currentPrice}" min="0.01" step="0.01" style="max-width:100px;">`);

            // Cambia icono a check
            $(this).removeClass('btn-info edit-product').addClass('btn-success save-product').html('<i class="ni ni-check-bold"></i>');
        });


        // Evento para guardar la edición de cantidad y precio
        $(document).on('click', '.save-product', function () {
            var row = $(this).closest('tr');
            var newQuantity = parseInt(row.find('.input-quantity').val()) || 1;
            var newPrice = parseFloat(row.find('.input-price').val()) || 0;

            if (newQuantity <= 0) {
                $.notifyClose();
                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'La cantidad debe ser mayor a 0.');
                return;
            }
            if (newPrice <= 0) {
                $.notifyClose();
                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'El precio debe ser mayor a 0.');
                return;
            }

            var newTotal = newQuantity * newPrice;

            // Actualiza valores visuales y atributos data-*
            row.find('.quantity').text(newQuantity);
            row.find('.price').text('$' + formatNumber(newPrice)).data('price', newPrice);
            row.find('.total').text('$' + formatNumber(newTotal)).data('total', newTotal);

            // Regresa botón a modo edición
            $(this).removeClass('btn-success save-product').addClass('btn-info edit-product').html('<i class="ni ni-ruler-pencil"></i>');

            // Recalcula totales generales después de la edición
            actualizarTotalesCotizacion();
        });




        // Evento para eliminar productos de la tabla
        $(document).on('click', '.remove-product', function () {
            var row = $(this).closest('tr');
            var productId = row.data('product-id');
            console.log(`[Eliminar Producto] Eliminando producto con ID: ${productId}`);
            row.remove();

            // Recalcula totales generales después de eliminar
            actualizarTotalesCotizacion();
        });


        /////por marca
        $('#agregar-productos-marca').on('click', function () {
            var partnerId = $('#partner_id').val();
            var filtro = 'Deli'; // Puedes hacer esto dinámico después

            // Evitar doble click si ya está en proceso
            $(this).prop('disabled', true);

            var requestData = {
                access_id: access_id,
                access_token: access_token,
                partner_id: partnerId,
                filtro: filtro
            };

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: url + 'get_partner_products_by_brand',
                data: requestData,
                success: function (response) {
                    if (response.msg === 'ok' && Array.isArray(response.products)) {
                        response.products.forEach(function (product) {
                            // Evita duplicados
                            if ($('#productlist tr[data-product-id="' + product.id + '"]').length) return;

                            var row = `
                        <tr data-product-id="${product.id}">
                            <td>${product.code}</td>
                            <td>${product.name}</td>
                            <td class="quantity text-right">1</td>
                            <td class="price text-right" data-price="${product.price}">$${formatNumber(product.price)}</td>
                            <td class="total text-right" data-total="${product.price}">$${formatNumber(product.price)}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm edit-product"><i class="ni ni-ruler-pencil"></i></button>
                                <button type="button" class="btn btn-danger btn-sm remove-product"><i class="ni ni-fat-remove"></i></button>
                            </td>
                        </tr>
                    `;
                            $('#productlist').append(row);
                        });

                        actualizarTotalesCotizacion();
                    } else {
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'No se encontraron productos de la marca.');
                    }
                },
                complete: function () {
                    $('#agregar-productos-marca').prop('disabled', false);
                },
                error: function () {
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al obtener productos de la marca.');
                    $('#agregar-productos-marca').prop('disabled', false);
                }
            });
        });

        ////agregar por rango
        $('#agregar-productos-rango').on('click', function () {
            var partnerId = $('#partner_id').val();
            var inicio = $('#codigo-inicio').val().trim();
            var fin = $('#codigo-fin').val().trim();

            // 1. Validaciones
            if (!partnerId || partnerId === '0') {
                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Debes seleccionar un socio primero.');
                return;
            }
            if (!inicio || !fin || isNaN(inicio) || isNaN(fin)) {
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Debes ingresar ambos códigos de inicio y fin válidos.');
                return;
            }
            if (parseInt(inicio) > parseInt(fin)) {
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'El código de inicio debe ser menor o igual al de fin.');
                return;
            }

            var requestData = {
                'access_id': access_id,
                'access_token': access_token,
                'partner_id': partnerId,
                'codigo_inicio': inicio,
                'codigo_fin': fin
            };

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: url + 'get_partner_products_by_code_range',
                data: requestData,
                success: function (response) {
                    if (response.msg === 'ok' && Array.isArray(response.products) && response.products.length > 0) {
                        let productosAgregados = 0;
                        response.products.forEach(function (product) {
                            // Evitar duplicados en la tabla (por ID de producto)
                            if ($('#productlist tr[data-product-id="' + product.id + '"]').length === 0) {
                                var row = `
                            <tr data-product-id="${product.id}">
                                <td>${product.code}</td>
                                <td>${product.name}</td>
                                <td class="quantity text-right">1</td>
                                <td class="price text-right" data-price="${product.price}">$${formatNumber(product.price)}</td>
                                <td class="total text-right" data-total="${product.price}">$${formatNumber(product.price)}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm edit-product"><i class="ni ni-ruler-pencil"></i></button>
                                    <button type="button" class="btn btn-danger btn-sm remove-product"><i class="ni ni-fat-remove"></i></button>
                                </td>
                            </tr>
                        `;
                                $('#productlist').append(row);
                                productosAgregados++;
                            }
                        });
                        actualizarTotalesCotizacion();

                        if (productosAgregados > 0) {
                            notify('top', 'center', 'ni ni-check-bold', 'success', 'animated bounceIn', 'animated bounceOut', productosAgregados + ' productos agregados correctamente.');
                        } else {
                            notify('top', 'center', 'ni ni-bell-55', 'info', 'animated bounceIn', 'animated bounceOut', 'No se agregaron productos nuevos (ya estaban en la tabla).');
                        }
                    } else {
                        notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'No se encontraron productos para ese rango de códigos.');
                    }
                },
                error: function (xhr, status, error) {
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error al buscar productos por rango. Intenta nuevamente.');
                }
            });
        });







        //finalizar cotizacion
        // Finalizar cotizacion con AJAX
        if ($('#finalizar-cotizacion').length > 0) {
            $('#finalizar-cotizacion').on('click', function () {
                // Si no hay domicilios cargados, forzar que address_id sea 0
                if ($('#address_id').data('has-addresses') === false) {
                    $('#address_id').val('0');
                }

                console.log("Finalización de cotizacion iniciada.");

                // OBTENER DATOS DEL FORMULARIO
                var partnerId = $('#partner_id').val();
                var paymentId = $('#payment_id').val();
                var addressId = $('#address_id').val();
                var products = [];
                var error = 0;
                var fields = '';
                var contactId = $('#partner_contact_id').val();
                var sellerId = $('#seller_asig_id').val();
                var reference = $('#reference').val();
                var validDate = $('#valid_date').val();
                var comments = $('#comments').val();

                // NUEVO: Para productos sin precio
                var productosSinPrecio = [];

                // RECORRER LA TABLA DE PRODUCTOS Y EXTRAER DATOS
                $('#productlist tr').each(function () {
                    var productId = $(this).data('product-id');
                    var productName = $(this).find('td:nth-child(2)').text().trim(); // 2da columna = nombre
                    var quantity = parseInt($(this).find('td:nth-child(3)').text().trim()) || 0;
                    var price = parseFloat($(this).find('td:nth-child(4)').text().replace('$', '').replace(/,/g, '').trim());

                    // Validar productos antes de agregarlos
                    if (productId && quantity > 0 && price > 0) {
                        products.push({
                            id: productId,
                            quantity: quantity,
                            price: price
                        });
                    } else if (productId && (price === 0 || isNaN(price))) {
                        productosSinPrecio.push(productName + ' (ID: ' + productId + ')');
                    }
                });

                console.log("Productos a enviar:", products);

                // VALIDACIÓN: Si hay productos sin precio, mostrar error y NO continuar
                if (productosSinPrecio.length > 0) {
                    let lista = '<ul style="text-align:left;">';
                    productosSinPrecio.forEach(function (nombre) {
                        lista += `<li>${nombre}</li>`;
                    });
                    lista += '</ul>';

                    $.notifyClose();
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut',
                        'No puedes finalizar la cotización porque los siguientes productos no tienen precio asignado:' +
                        lista +
                        'Por favor, edita el precio de estos productos antes de continuar.'
                    );
                    return; // Detener aquí
                }

                // **VALIDACIONES CONSOLIDADAS**
                if (!partnerId || partnerId === '0') {
                    error++;
                    fields += '<strong>Cliente</strong>, ';
                }

                if (products.length === 0) {
                    $('#productlist').addClass('border border-danger');
                } else {
                    $('#productlist').removeClass('border border-danger');
                }

                // SI HAY ERRORES, SE MUESTRA UN SOLO MENSAJE
                if (error !== 0) {
                    var msg = 'Encontramos algunos errores en el formulario.<br>Por favor completa ';
                    fields = fields.substr(0, (fields.length - 2)) + '.'; // Elimina la última coma

                    msg += (error === 1) ? 'el campo ' + fields : 'los campos ' + fields;

                    $.notifyClose();
                    notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', msg);
                    return;
                }

                // MENSAJE DE CONFIRMACIÓN CON NOTIFY()
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut',
                    '¿Estás seguro de finalizar la cotizacion? <br>' +
                    '<button id="confirm-finalizar" class="btn btn-success btn-sm m-2">Sí, finalizar</button> ' +
                    '<button id="cancel-finalizar" class="btn btn-danger btn-sm m-2">Cancelar</button>'
                );

                // EVENTO PARA DETECTAR CUANDO EL USUARIO CONFIRME LA VENTA
                $(document).off('click', '#confirm-finalizar').one('click', '#confirm-finalizar', function () {
                    console.log("Enviando datos AJAX... aqui se envia al AJAX finalizar cotizacion");

                    var requestData = {
                        'access_id': access_id,
                        'access_token': access_token,
                        'partner_id': partnerId,
                        'payment_id': paymentId,
                        'address_id': addressId,
                        'partner_contact_id': contactId,
                        'seller_asig_id': sellerId,
                        'reference': reference,
                        'valid_date': validDate,
                        'comments': comments,
                        'products': JSON.stringify(products)
                    };

                    console.log("📡 Datos enviados de json a ajax:", requestData);

                    $.ajax({
                        type: 'POST',
                        url: url + 'finalizar_cotizacion',
                        data: requestData,
                        dataType: 'json',
                        success: function (response) {
                            console.log("Respuesta AJAX controlador:", response);

                            if (response.msg === 'ok') {
                                $.notifyClose();
                                notify('top', 'center', 'ni ni-check-bold', 'success', 'animated bounceIn', 'animated bounceOut', 'Cotización registrada correctamente.');

                                // Deshabilitar campos después de finalizar la cotización
                                $('#partner_contact_id').prop('disabled', true);
                                $('#reference').prop('disabled', true);
                                $('#valid_date').prop('disabled', true);
                                $('#comments').prop('disabled', true);
                                $('#payment_id').prop('disabled', true);
                                $('#address_id').prop('disabled', true);
                                $('#seller_asig_id').prop('disabled', true);
                                $('#agregar-productos-marca').prop('disabled', true);
                                $('#finalizar-cotizacion').prop('disabled', true);

                                // ✅ Habilitar el botón de reinicio correctamente
                                $('#reiniciarcot').prop('disabled', false);

                                // Deshabilitar los botones de editar/eliminar productos
                                $('.edit-product, .save-product, .remove-product').prop('disabled', true);

                                // Hacer comentarios de solo lectura
                                $('#comments').prop('readonly', true);

                                // ACTIVAR el botón de imprimir
                                $('#imprimir')
                                    .removeClass('d-none')                 // mostrar el botón
                                    .data('quote', response.data.quote_id) // actualizar el ID
                                    .off('click')                          // eliminar cualquier click anterior
                                    .on('click', function () {
                                        const id = $(this).data('quote');
                                        if (id) {
                                            window.open(base_url + 'admin/cotizaciones/imprimir/' + id, '_blank');
                                        }
                                    });

                                // ABRIR AUTOMÁTICAMENTE LA VISTA DE IMPRESIÓN
                                setTimeout(() => {
                                    window.open(url + 'imprimir/' + response.data.quote_id, '_blank');
                                }, 1000);
                            }
                            else {
                                $.notifyClose();
                                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'Error al procesar la cotización.');
                            }
                        }
                    });
                });

                // EVENTO PARA CANCELAR
                $(document).off('click', '#cancel-finalizar').one('click', '#cancel-finalizar', function () {
                    $.notifyClose();
                    notify('top', 'center', 'ni ni-bell-55', 'info', 'animated bounceIn', 'animated bounceOut', 'Finalización cancelada.');
                });
            });
        }







        // Evento para reiniciar todo el formulario
        $('#reiniciarcot').on('click', function () {
            console.log('Click detectado en #reiniciarcot');
            $.notifyClose();

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esto reiniciará el formulario y eliminará todos los datos agregados.",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, reiniciar',
                cancelButtonText: 'Cancelar'
                    }).then((result) => {
                if (result.value) {

                    // 1. Deshabilitar todos los campos (reset general)
                    $('#product_id, #quantity, #agregar-producto-socio, #address_id, #partner_contact_id, #reference, #valid_date, #comments, #payment_id, #finalizar-cotizacion, #seller_asig_id, #agregar-productos-marca').prop('disabled', true);

                    // 2. Habilitar solo el selector de socio
                    $('#partner_id').prop('disabled', false);

                    // 3. Resetear valores de selects y campos
                    $('#partner_id').val(null).trigger('change');
                    $('#img-preview-producto').attr('src', '/assets/uploads/thumb_no_image.png');
                    $('#nombre-preview-producto').text('');
                    $('#codigo-preview-producto').text('');
                    $('#preview-producto-container').hide();
                    $('#partner_contact_id').val(null).trigger('change');
                    $('#seller_asig_id').prop('disabled', true);
                    $('#agregar-productos-marca').val(null).trigger('change');
                    $('#agregar-productos-rango').prop('disabled', true);
                    $('#codigo-inicio').val('');
                    $('#codigo-fin').val('');
                    $('#product_id').val(null).trigger('change');
                    $('#address_id').val('0').empty().append('<option value="0">Selecciona un domicilio</option>');


                    $('#reference, #valid_date, #comments').val('');
                    $('#quantity').val('1');
                    $('#product-price-socio').val('0');
                    $('#payment_id').val('3');
                    $('#selected_address').val('');

                    // 4. Limpiar tabla de productos y totales
                    $('#productlist').empty().removeClass('border border-danger');
                    $('#subtotal, #total, #iva, #total-piezas, #product-price-socio').text('$0.00');

                    actualizarTotalesCotizacion();


                    // 5. Reiniciar variables de estado global
                    partnerSeleccionado = false;

                    // 6. Restablecer botón de imprimir (si fue mostrado)
                    $('#imprimir').addClass('d-none').data('quote', '');

                    // 7. Habilitar nuevamente el botón de reinicio
                    $('#reiniciarcot').prop('disabled', false);

                    // 8. Quitar modo readonly del campo de comentarios si fue activado
                    $('#comments').prop('readonly', false);

                    notify('top', 'center', 'ni ni-check-bold', 'info', 'animated bounceIn', 'animated bounceOut', 'Formulario reiniciado correctamente.');
                }
            });
        });






    }///ESTE ES EL FIN DE COTIZACIONES TODO QUE VAYA DENTRO DE LO DE COTIZACION AQUI

 ///cancelar cotizacion en index

    if ($('.cancelar-cotizacion').length > 0) {
        $('.cancelar-cotizacion').click(function (e) {
            e.preventDefault();
            var link = $(this).attr('href');

            setTimeout(function () {
                swal({
                    title: '¿Estás seguro de cancelar la cotización?',
                    text: "No podrás revertir esta acción.",
                    type: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonClass: 'btn btn-danger',
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonClass: 'btn btn-secondary',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.value) {
                        window.location.href = link;
                    }
                })
            }, 200);

            return false;
        });
    }







 ///se cierra la cancelacion


    ///////// INICIA MODULO DE EDICION DE COTIZACIONES //////////

    // Ejecuta solo si existen los elementos clave (tabla y form de edición)

    $(document).ready(function () {

        // Utilidad para formatear a precio
        function formatNumber(number) {
            return Number(number).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Recalcula totales
        function actualizarTotales() {
            var subtotal = 0, ivaTotal = 0, totalPiezas = 0;
            $('#tabla-productos tbody tr').each(function () {
                var cantidad = parseInt($(this).find('.cantidad-text').text()) || 0;
                var precio = parseFloat($(this).find('.precio-unitario').text().replace('$', '').replace(/,/g, '')) || 0;
                var totalProd = cantidad * precio;
                subtotal += totalProd || 0;
                ivaTotal += totalProd - (totalProd / 1.16);
                totalPiezas += cantidad;
                $(this).find('.total-producto').text('$' + formatNumber(totalProd));
            });
            $('#subtotal').text('$' + formatNumber(subtotal));
            $('#iva').text('$' + formatNumber(ivaTotal));
            $('#total').text('$' + formatNumber(subtotal + ivaTotal));
            $('#total-piezas').text(formatNumber(totalPiezas));
        }

        // Inicializa SELECT2 de productos (si existe)
        if ($('#product_id').length > 0) {
            $('#product_id').select2({
                placeholder: 'Buscar producto...',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: url + 'search_products_socios',
                    type: 'POST',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            'access_id': access_id,
                            'access_token': access_token,
                            'partner_id': $('#partner_id').val(),
                            'term': params.term
                        };
                    },
                    processResults: function (response) {
                        if (response.msg !== 'ok' || !Array.isArray(response.products)) {
                            return { results: [] };
                        }
                        return {
                            results: response.products.map(function (product) {
                                return {
                                    id: product.id,
                                    text: `${product.name} (${product.code})`,
                                    price: parseFloat(product.price) || 0,
                                    code: product.code,
                                    name: product.name,
                                    image_url: product.image_url /////esto de la imagen en agregar cotizacion
                                };
                            })
                        };
                    }
                }
            });

            // Al seleccionar producto del select2...
            $('#product_id').on('select2:select', function (e) {
                var data = e.params.data;
                $('#product_code_hidden').val(data.code || '');
                $('#product_name_hidden').val(data.name || data.text || '');

                // Si el producto ya existe en la tabla, solo mostrar precio, deshabilitar botón agregar
                if ($('#tabla-productos tr[data-producto-id="' + data.id + '"]').length) {
                    let precioExistente = $('#tabla-productos tr[data-producto-id="' + data.id + '"] .precio-unitario').text().replace('$', '').replace(/,/g, '');
                    $('#product-price-socio').val(Number(precioExistente).toFixed(2)).prop('readonly', true);
                    $('#agregar-producto-socio').prop('disabled', true);
                    notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Este producto ya existe en la cotización.');
                } else {
                    // Si el producto tiene precio, es editable para cotizar diferente; si no, obligatorio capturar
                    $('#product-price-socio').val(Number(data.price || 0).toFixed(2)).prop('readonly', false);
                    $('#agregar-producto-socio').prop('disabled', (parseFloat($('#product-price-socio').val()) <= 0));
                }
            });

            // Al limpiar el select, limpia campos y habilita precio editable
            $('#product_id').on('select2:clear', function () {
                $('#product_code_hidden').val('');
                $('#product_name_hidden').val('');
                $('#product-price-socio').val('0.00').prop('readonly', false);
                $('#agregar-producto-socio').prop('disabled', true);
            });

            // Si el usuario edita manualmente el precio
            $('#product-price-socio').on('input', function () {
                var precio = parseFloat($(this).val()) || 0;
                var prodSel = $('#product_id').val();
                // Habilita botón solo si precio > 0 y el producto NO está en la tabla
                if ($('#tabla-productos tr[data-producto-id="' + prodSel + '"]').length) {
                    $('#agregar-producto-socio').prop('disabled', true);
                } else {
                    $('#agregar-producto-socio').prop('disabled', (!prodSel || precio <= 0));
                }
            });

            // Deshabilita botón al inicio (siempre)
            $('#agregar-producto-socio').prop('disabled', true);
        }

        // ========== AGREGAR PRODUCTO ==========
        $('#agregar-producto-socio').on('click', function () {
            var product_id = $('#product_id').val();
            var product_name = $('#product_name_hidden').val();
            var quantity = parseInt($('#quantity').val()) || 0;
            var price = parseFloat($('#product-price-socio').val()) || 0;

           /* if (!product_id || !product_name || quantity <= 0 || price <= 0) {
                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Selecciona producto, cantidad y precio válidos.');
                return;
            }*/

            // Checa duplicados (no debería ocurrir, solo seguridad)
            if ($('#tabla-productos tr[data-producto-id="' + product_id + '"]').length) {
                notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut', 'Este producto ya existe en la cotización.');
                return;
            }

            // Agrega nueva fila
            var row = `
        <tr data-producto-id="${product_id}" data-nuevo="1">
            <td>${product_name}</td>
            <td>
                <span class="cantidad-text">${quantity}</span>
                <input type="number" class="form-control cantidad-input d-none" value="${quantity}" min="1">
            </td>
            <td class="precio-unitario">$${Number(price).toFixed(2)}</td>
            <td class="total-producto">$${Number(quantity * price).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-info btn-sm btn-editar-cantidad"><i class="ni ni-ruler-pencil"></i></button>
                <button type="button" class="btn btn-danger btn-sm btn-eliminar-producto"><i class="ni ni-fat-remove"></i></button>
            </td>
        </tr>
        `;
            $('#tabla-productos tbody').append(row);

            actualizarTotales();

            // Limpia campos de agregar producto
            $('#product_id').val(null).trigger('change');
            $('#quantity').val(1);
            $('#product-price-socio').val('0.00').prop('readonly', false);
            $('#agregar-producto-socio').prop('disabled', true);
        });

        // ========== EDITAR SOLO CANTIDAD EN LA TABLA (o podrías permitir editar precio aquí también) ==========
        // (Aquí puedes ampliar si quieres permitir editar precio también con otro input)
        $(document).on('click', '.btn-editar-cantidad', function () {
            var row = $(this).closest('tr');
            var cantidadActual = row.find('.cantidad-text').text();
            row.find('.cantidad-text').addClass('d-none');
            row.find('.cantidad-input').removeClass('d-none').val(cantidadActual).focus();
            $(this).removeClass('btn-info btn-editar-cantidad').addClass('btn-success btn-guardar-cantidad').html('<i class="ni ni-check-bold"></i>');
        });

        $(document).on('click', '.btn-guardar-cantidad', function () {
            var row = $(this).closest('tr');
            var inputCantidad = row.find('.cantidad-input');
            var nuevaCantidad = parseInt(inputCantidad.val()) || 1;
            if (nuevaCantidad <= 0) {
                notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Cantidad inválida.');
                return;
            }
            row.find('.cantidad-text').text(nuevaCantidad).removeClass('d-none');
            inputCantidad.addClass('d-none');

            $(this).removeClass('btn-success btn-guardar-cantidad').addClass('btn-info btn-editar-cantidad').html('<i class="ni ni-ruler-pencil"></i>');

            actualizarTotales();
        });

        // ========== ELIMINAR PRODUCTO ==========
        $(document).on('click', '.btn-eliminar-producto', function () {
            var row = $(this).closest('tr');
            row.remove();
            actualizarTotales();
        });

        // ========== AL INICIO: ACTUALIZA TOTALES ==========
        actualizarTotales();


        // ========== GUARDAR: ACTUALIZA TOTALES ==========
        // Array global para eliminados
        window.productosEliminados = window.productosEliminados || [];

        // Cuando das click en guardar cambios
        $('#guardar-cambios-cotizacion').on('click', function (e) {
            e.preventDefault();

            // CABECERA
            var seller_asig_id = $('#seller_asig_id').val();
            var reference = $('#reference').val();
            var valid_date = $('#valid_date').val();
            var comments = $('#comments').val();

            console.log('[Editar Cotización] Guardar cambios: click detectado');
            console.log('Quote ID:', $('#quote_id').val());

            // Arrays a enviar
            var productosNuevos = [];
            var productosEditados = [];
            var productosEliminados = window.productosEliminados || [];

            // Recolecta productos de la tabla
            $('#tabla-productos tbody tr').each(function () {
                var $tr = $(this);
                var prodId = $tr.data('producto-id');
                var esNuevo = $tr.data('nuevo') == 1;

                var cantidad = parseInt($tr.find('.cantidad-text').text()) || 0;
                var precio = parseFloat($tr.find('.precio-unitario').text().replace('$', '').replace(/,/g, '')) || 0;

                // Para productos originales, compara con valores originales (usa data-original-cantidad y data-original-precio)
                if (!esNuevo) {
                    var originalCantidad = parseInt($tr.data('original-cantidad')) || 0;
                    var originalPrecio = parseFloat($tr.data('original-precio')) || 0;
                    if (cantidad !== originalCantidad || precio !== originalPrecio) {
                        console.log(`[Editar Cotización] Producto editado: ID=${prodId} Cantidad: ${originalCantidad}→${cantidad} Precio: ${originalPrecio}→${precio}`);
                        productosEditados.push({ id: prodId, cantidad: cantidad, precio: precio });
                    }
                } else {
                    console.log(`[Editar Cotización] Producto nuevo: ID=${prodId} Cantidad: ${cantidad} Precio: ${precio}`);
                    productosNuevos.push({ id: prodId, cantidad: cantidad, precio: precio });
                }
            });

            console.log('[Editar Cotización] Productos nuevos:', productosNuevos);
            console.log('[Editar Cotización] Productos editados:', productosEditados);
            console.log('[Editar Cotización] Productos eliminados:', productosEliminados);

            var requestData = {
                access_id: access_id,
                access_token: access_token,
                quote_id: $('#quote_id').val(),
                productos_nuevos: JSON.stringify(productosNuevos),
                productos_editados: JSON.stringify(productosEditados),
                productos_eliminados: JSON.stringify(productosEliminados),
                seller_asig_id: seller_asig_id,
                reference: reference,
                valid_date: valid_date,
                comments: comments
            };

            // Validar si hay cambios antes de mostrar confirmación
            if (productosNuevos.length === 0 && productosEditados.length === 0 && productosEliminados.length === 0) {
                notify('top', 'center', 'ni ni-bell-55', 'info', 'animated bounceIn', 'animated bounceOut', 'No hay cambios que guardar en la cotización.');
                return;
            }


            // Usar NOTIFY en vez de SweetAlert
            notify('top', 'center', 'ni ni-bell-55', 'warning', 'animated bounceIn', 'animated bounceOut',
                '¿Deseas guardar los cambios en la cotización? <br>' +
                '<button id="confirm-editar-cot" class="btn btn-success btn-sm m-2">Sí, guardar</button> ' +
                '<button id="cancel-editar-cot" class="btn btn-danger btn-sm m-2">Cancelar</button>'
            );

            $(document).off('click', '#confirm-editar-cot').one('click', '#confirm-editar-cot', function () {
                console.log("[Editar Cotización] Enviando AJAX...", requestData);

                $.ajax({
                    type: 'POST',
                    url: url + 'editar_guardar_cotizacion',
                    data: requestData,
                    dataType: 'json',
                    success: function (response) {
                        console.log("[Editar Cotización] Respuesta del backend:", response);

                        $.notifyClose();
                        if (response.msg === 'ok') {
                            notify('top', 'center', 'ni ni-check-bold', 'success', 'animated bounceIn', 'animated bounceOut', 'Cotización editada y guardada correctamente.');
                            // Redirecciona, recarga, etc.
                        } else {
                            notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', response.message || 'Ocurrió un error al guardar.');
                        }
                    },
                    error: function (xhr, status, error) {
                        $.notifyClose();
                        notify('top', 'center', 'ni ni-bell-55', 'danger', 'animated bounceIn', 'animated bounceOut', 'Error en la comunicación con el servidor.');
                    }
                });
            });

            $(document).off('click', '#cancel-editar-cot').one('click', '#cancel-editar-cot', function () {
                $.notifyClose();
                notify('top', 'center', 'ni ni-bell-55', 'info', 'animated bounceIn', 'animated bounceOut', 'Edición cancelada.');
            });
        });


        // Evento para eliminar producto
        $(document).on('click', '.btn-eliminar-producto', function () {
            var prodId = $(this).closest('tr').data('producto-id');
            if (!$(this).closest('tr').data('nuevo')) {
                window.productosEliminados = window.productosEliminados || [];
                window.productosEliminados.push(prodId);
            }
            $(this).closest('tr').remove();
            actualizarTotales();
        });


    });


    // ========= FIN MODULO DE EDICION DE COTIZACIONES =========

///modulo para editar socios en el admin esto para que sea mas aguil y facilite el trabajo

    $(document).on('click', '.btn-edit-generales', function (e) {
        e.preventDefault();
        var partner_id = $(this).data('id');

        var SendData = {
            'access_id': access_id,
            'access_token': access_token,
            'partner_id': partner_id
        };

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_generales_opts',
            data: SendData,
            success: function (response) {
                if (response.msg == 'ok') {
                    Swal.fire({
                        title: 'Editar datos generales',
                        width: 800,
                        html: `
                    <form id="form-editar-generales">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>Código SAP</label>
                                <input class="form-control" value="${response.code_sap}" readonly>
                                <small class="form-text text-muted">El código no puede ser editado.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Razón Social</label>
                                <input id="name" class="form-control" value="${response.name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>RFC</label>
                                <input id="rfc" class="form-control" value="${response.rfc || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input id="email" class="form-control" value="${response.email || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Usuario Web</label>
                                <select id="customer_id" class="form-control"></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Vendedor</label>
                                <select id="employee_id" class="form-control"></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Lista de precios</label>
                                <select id="type_id" class="form-control"></select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Bloqueado</label>
                                <select id="banned" class="form-control">
                                    <option value="0" ${parseInt(response.banned) == 0 ? 'selected' : ''}>No</option>
                                    <option value="1" ${parseInt(response.banned) == 1 ? 'selected' : ''}>Sí</option>
                                </select>
                            </div>
                        </div>
                    </form>
                `,
                        didOpen: function () {
                            $('#customer_id').html(response.customer_opts_html);
                            $('#employee_id').html(response.employee_opts_html);
                            $('#type_id').html(response.type_opts_html);
                            $('#customer_id').val(String(response.customer_id || ''));
                            $('#employee_id').val(String(response.employee_id || ''));
                            $('#type_id').val(String(response.type_id || ''));
                            $('#customer_id').trigger('change');
                            $('#employee_id').trigger('change');
                            $('#type_id').trigger('change');
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false,
                        preConfirm: function () {
                            return new Promise(function (resolve, reject) {
                                var datos = {
                                    access_id: access_id,
                                    access_token: access_token,
                                    partner_id: partner_id,
                                    name: $('#name').val(),
                                    rfc: $('#rfc').val(),
                                    email: $('#email').val(),
                                    customer_id: $('#customer_id').val(),
                                    employee_id: $('#employee_id').val(),
                                    type_id: $('#type_id').val(),
                                    banned: $('#banned').val()
                                };
                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'save_generales',
                                    data: datos,
                                    success: function (resp) {
                                        console.log('[DEBUG RESP SAVE_GENERALES]:', resp);
                                        resolve(resp); // Esto es CLAVE
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: function () { return !Swal.isLoading(); }
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            // El usuario canceló, NO mostrar nada, ni error, ni info.
                            return;
                        }
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'Los datos fueron actualizados.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value && result.value.errors && result.value.errors.length > 0) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value && result.value.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones en los datos, por lo que no se guardaron.', 'info');
                        } else if (result.value && result.value.msg) {
                            Swal.fire('Error', result.value.msg, 'error');
                        } else {
                            // Este es realmente un error inesperado, pero sólo si no fue cancelación.
                            Swal.fire('Error inesperado', 'Revisa los datos y vuelve a intentarlo.', 'error');
                        }
                    });

                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            }
        });
    });


    // AGREGAR
    $(document).on('click', '#btn-agregar-fiscal', function (e) {
        e.preventDefault();
        var partner_id = $(this).data('partner-id');
        abrirModalFiscal(partner_id, false);
    });

    // EDITAR
    $(document).on('click', '.btn-edit-fiscal', function (e) {
        e.preventDefault();
        var partner_id = $(this).data('partner-id');
        abrirModalFiscal(partner_id, true);
    });

    function abrirModalFiscal(partner_id, editing) {
        var SendData = {
            'access_id': access_id,
            'access_token': access_token,
            'partner_id': partner_id
        };
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_fiscal_opts',
            data: SendData,
            success: function (response) {
                if (response.msg === 'ok') {
                    Swal.fire({
                        title: (response.editing ? 'Editar' : 'Agregar') + ' datos fiscales',
                        width: 800,
                        html: `
                        <form id="form-fiscal">
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>Razón Social</label>
                                    <input id="business_name" class="form-control" value="${response.business_name || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>RFC</label>
                                    <input id="rfc" class="form-control" value="${response.rfc || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Calle</label>
                                    <input id="street" class="form-control" value="${response.street || ''}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Número Ext.</label>
                                    <input id="number" class="form-control" value="${response.number || ''}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Número Int.</label>
                                    <input id="internal_number" class="form-control" value="${response.internal_number || ''}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Colonia</label>
                                    <input id="colony" class="form-control" value="${response.colony || ''}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Ciudad</label>
                                    <input id="city" class="form-control" value="${response.city || ''}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Municipio</label>
                                    <input id="municipality" class="form-control" value="${response.municipality || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Estado</label>
                                    <select id="state_id" class="form-control"></select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label>Código Postal</label>
                                    <input id="zipcode" class="form-control" value="${response.zipcode || ''}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Régimen Fiscal SAT</label>
                                    <select id="sat_tax_regime_id" class="form-control"></select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Forma de Pago</label>
                                    <select id="payment_method_id" class="form-control"></select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Uso de CFDI</label>
                                    <select id="cfdi_id" class="form-control"></select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label>Email facturación</label>
                                    <input id="email" class="form-control" value="${response.email || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Constancia de situación fiscal (PDF)</label>
                                    <input type="file" id="csf" class="form-control">
                                    ${(response.csf_link ? `<a href="${response.csf_link}" target="_blank" class="btn btn-sm btn-neutral mt-2">Ver constancia cargada</a>` : '')}
                                </div>
                            </div>
                        </form>
                    `,
                        didOpen: function () {
                            $('#state_id').html(response.states_opts_html);
                            $('#sat_tax_regime_id').html(response.sat_tax_regime_opts_html);
                            $('#payment_method_id').html(response.payment_method_opts_html);
                            $('#cfdi_id').html(response.cfdi_opts_html);

                            $('#state_id').val(String(response.state_id || ''));
                            $('#sat_tax_regime_id').val(String(response.sat_tax_regime_id || ''));
                            $('#payment_method_id').val(String(response.payment_method_id || ''));
                            $('#cfdi_id').val(String(response.cfdi_id || ''));
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false,
                        preConfirm: function () {
                            return new Promise(function (resolve) {
                                // FormData para soporte de archivo
                                var formData = new FormData();
                                formData.append('access_id', access_id);
                                formData.append('access_token', access_token);
                                formData.append('partner_id', partner_id);
                                formData.append('business_name', $('#business_name').val());
                                formData.append('rfc', $('#rfc').val());
                                formData.append('street', $('#street').val());
                                formData.append('number', $('#number').val());
                                formData.append('internal_number', $('#internal_number').val());
                                formData.append('colony', $('#colony').val());
                                formData.append('city', $('#city').val());
                                formData.append('municipality', $('#municipality').val());
                                formData.append('state_id', $('#state_id').val());
                                formData.append('zipcode', $('#zipcode').val());
                                formData.append('sat_tax_regime_id', $('#sat_tax_regime_id').val());
                                formData.append('payment_method_id', $('#payment_method_id').val());
                                formData.append('cfdi_id', $('#cfdi_id').val());
                                formData.append('email', $('#email').val());
                                var fileInput = document.getElementById('csf');
                                if (fileInput && fileInput.files.length > 0) {
                                    formData.append('csf', fileInput.files[0]);
                                }
                                $.ajax({
                                    type: 'post',
                                    url: url + 'save_fiscal',
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                    dataType: 'json',
                                    success: function (resp) {
                                        resolve(resp);
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: function () { return !Swal.isLoading(); }
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            // El usuario canceló, NO mostrar nada, ni error, ni info.
                            return;
                        }
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'Los datos fueron actualizados.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value && result.value.errors && result.value.errors.length > 0) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value && result.value.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones en los datos, por lo que no se guardaron.', 'info');
                        } else if (result.value && result.value.msg) {
                            Swal.fire('Error', result.value.msg, 'error');
                        } else {
                            // Este es realmente un error inesperado, pero sólo si no fue cancelación.
                            Swal.fire('Error inesperado', 'Revisa los datos y vuelve a intentarlo.', 'error');
                        }
                    });

                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo obtener la información: ' + error, 'error');
            }
        });
    }



    // Botón AGREGAR contacto
    $(document).on('click', '#btn-agregar-contacto', function (e) {
        e.preventDefault();
        abrirModalContacto(0, $(this).data('partner-id')); // id 0 = nuevo
    });

    // Botón EDITAR contacto
    $(document).on('click', '.btn-edit-contacto', function (e) {
        e.preventDefault();
        abrirModalContacto($(this).data('id'), $(this).data('partner-id'));
    });

    function abrirModalContacto(id, partner_id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_contacto_opts',
            data: {
                access_id: access_id,
                access_token: access_token,
                contact_id: id,
                partner_id: partner_id
            },
            success: function (response) {
                if (response.msg === 'ok') {
                    Swal.fire({
                        title: (id == 0 ? 'Agregar' : 'Editar') + ' contacto',
                        width: 700,
                        html: `
                        <form id="form-contacto">
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>ID Contacto</label>
                                    <input id="idcontact" class="form-control" value="${response.idcontact || ''}" ${id != 0 ? 'readonly' : ''}>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Nombre</label>
                                    <input id="name" class="form-control" value="${response.name || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Apellido</label>
                                    <input id="last_name" class="form-control" value="${response.last_name || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Teléfono</label>
                                    <input id="phone" class="form-control" value="${response.phone || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Celular</label>
                                    <input id="cel" class="form-control" value="${response.cel || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Email</label>
                                    <input id="email" class="form-control" value="${response.email || ''}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Departamento</label>
                                    <input id="departments" class="form-control" value="${response.departments || ''}">
                                </div>
                            </div>
                        </form>
                    `,
                        didOpen: function () {
                            // Nada especial por ahora, podrías poner focus si gustas
                            $('#name').focus();
                        },
                        showCancelButton: true,

                        confirmButtonText: 'Guardar',

                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false,
                        preConfirm: function () {
                            return new Promise(function (resolve, reject) {
                                var datos = {
                                    access_id: access_id,
                                    access_token: access_token,
                                    contact_id: id,
                                    partner_id: partner_id,
                                    idcontact: $('#idcontact').val(),
                                    name: $('#name').val(),
                                    last_name: $('#last_name').val(),
                                    phone: $('#phone').val(),
                                    cel: $('#cel').val(),
                                    email: $('#email').val(),
                                    departments: $('#departments').val()
                                };
                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'save_contacto',
                                    data: datos,
                                    success: function (resp) {
                                        resolve(resp);
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: function () { return !Swal.isLoading(); }
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            // El usuario canceló, NO mostrar nada, ni error, ni info.
                            return;
                        }
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'Los datos fueron actualizados.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value && result.value.errors && result.value.errors.length > 0) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value && result.value.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones en los datos, por lo que no se guardaron.', 'info');
                        } else if (result.value && result.value.msg) {
                            Swal.fire('Error', result.value.msg, 'error');
                        } else {
                            // Este es realmente un error inesperado, pero sólo si no fue cancelación.
                            Swal.fire('Error inesperado', 'Revisa los datos y vuelve a intentarlo.', 'error');
                        }
                    });

                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            }
        });
    }


    // AGREGAR domicilio
    $(document).on('click', '#btn-agregar-entrega', function (e) {
        e.preventDefault();
        var partner_id = $(this).data('partner-id');
        abrirModalEntrega(0, partner_id); // 0 para agregar nuevo
    });

    // EDITAR domicilio
    $(document).on('click', '.btn-edit-entrega', function (e) {
        e.preventDefault();
        var entrega_id = $(this).data('id');
        var partner_id = $(this).data('partner-id');
        abrirModalEntrega(entrega_id, partner_id);
    });

    function abrirModalEntrega(id, partner_id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_entrega_opts',
            data: {
                access_id: access_id,
                access_token: access_token,
                entrega_id: id,
                partner_id: partner_id
            },
            success: function (response) {
                if (response.msg === 'ok') {
                    Swal.fire({
                        title: (id == 0 ? 'Agregar' : 'Editar') + ' domicilio de entrega',
                        width: 800,
                        html: `
                    <form id="form-entrega">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>ID de Dirección</label>
                                <input id="iddelivery" class="form-control" value="${response.iddelivery || ''}" ${id != 0 ? 'readonly' : ''}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Calle</label>
                                <input id="street" class="form-control" value="${response.street || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Número Ext.</label>
                                <input id="number" class="form-control" value="${response.number || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Número Int.</label>
                                <input id="internal_number" class="form-control" value="${response.internal_number || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Colonia</label>
                                <input id="colony" class="form-control" value="${response.colony || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Ciudad</label>
                                <input id="city" class="form-control" value="${response.city || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Municipio</label>
                                <input id="municipality" class="form-control" value="${response.municipality || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Estado</label>
                                <select id="state" class="form-control"></select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Código postal</label>
                                <input id="zipcode" class="form-control" value="${response.zipcode || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Horario de recepción</label>
                                <input id="reception_hours" class="form-control" value="${response.reception_hours || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Datos adicionales</label>
                                <input id="delivery_notes" class="form-control" value="${response.delivery_notes || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nombre de contacto</label>
                                <input id="name" class="form-control" value="${response.name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Apellido de contacto</label>
                                <input id="last_name" class="form-control" value="${response.last_name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Teléfono de contacto</label>
                                <input id="phone" class="form-control" value="${response.phone || ''}">
                            </div>
                        </div>
                    </form>
                `,
                        didOpen: function () {
                            $('#state').html(response.state_opts_html);
                            $('#state').val(response.state || '').trigger('change');
                        },
                        showCancelButton: true,

                        confirmButtonText: 'Guardar',

                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        customClass: {
                            confirmButton: 'btn btn-success',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false,
                        preConfirm: function () {
                            return new Promise(function (resolve, reject) {
                                var datos = {
                                    access_id: access_id,
                                    access_token: access_token,
                                    entrega_id: id,
                                    partner_id: partner_id,
                                    iddelivery: $('#iddelivery').val(),
                                    street: $('#street').val(),
                                    number: $('#number').val(),
                                    internal_number: $('#internal_number').val(),
                                    colony: $('#colony').val(),
                                    city: $('#city').val(),
                                    municipality: $('#municipality').val(),
                                    state: $('#state').val(),
                                    zipcode: $('#zipcode').val(),
                                    reception_hours: $('#reception_hours').val(),
                                    delivery_notes: $('#delivery_notes').val(),
                                    name: $('#name').val(),
                                    last_name: $('#last_name').val(),
                                    phone: $('#phone').val()
                                };
                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'save_entrega',
                                    data: datos,
                                    success: function (resp) {
                                        resolve(resp);
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: function () { return !Swal.isLoading(); }
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) {
                            // El usuario canceló, NO mostrar nada, ni error, ni info.
                            return;
                        }
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'Los datos fueron actualizados.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value && result.value.errors && result.value.errors.length > 0) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value && result.value.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones en los datos, por lo que no se guardaron.', 'info');
                        } else if (result.value && result.value.msg) {
                            Swal.fire('Error', result.value.msg, 'error');
                        } else {
                            // Este es realmente un error inesperado, pero sólo si no fue cancelación.
                            Swal.fire('Error inesperado', 'Revisa los datos y vuelve a intentarlo.', 'error');
                        }
                    });

                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            }
        });
    }



    // ===================================================================================================
    // MODULO EDICION DE PROVEEDORES
    // ===================================================================================================
    /// MODAL: EDITAR DATOS GENERALES DEL PROVEEDOR

    $(document).on('click', '.btn-edit-generales-provider', function (e) {
        e.preventDefault();
        var provider_id = $(this).data('id');

        var SendData = {
            'access_id': access_id,
            'access_token': access_token,
            'provider_id': provider_id
        };

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_generales_opts_provider',
            data: SendData,
            success: function (response) {
                if (response.msg == 'ok') {
                    Swal.fire({
                        title: 'Editar datos generales del proveedor',
                        width: 800,
                        html: `
                    <form id="form-editar-generales-provider">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>Código SAP</label>
                                <input class="form-control" value="${response.code_sap}" readonly>
                                <small class="form-text text-muted">El código no puede ser editado.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Razón Social</label>
                                <input id="name" class="form-control" value="${response.name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>RFC</label>
                                <input id="rfc" class="form-control" value="${response.rfc || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input id="email" class="form-control" value="${response.email || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tipo de proveedor</label>
                                <select id="provider_type" class="form-control">
                                    <option value="0" ${parseInt(response.provider_type) === 0 ? 'selected' : ''}>Servicio</option>
                                    <option value="1" ${parseInt(response.provider_type) === 1 ? 'selected' : ''}>Mercancía</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Procedencia</label>
                                <select id="origin" class="form-control">
                                    <option value="0" ${parseInt(response.origin) === 0 ? 'selected' : ''}>Nacional</option>
                                    <option value="1" ${parseInt(response.origin) === 1 ? 'selected' : ''}>Extranjero</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Departamento principal</label>
                                <select id="employees_department_id" class="form-control">
                                    ${response.departments_opts_html || ''}
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Términos de Pago</label>
                                <select id="payment_terms_id" class="form-control">
                                    ${response.terms_opts_html}
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Bloqueado</label>
                                <select id="banned" class="form-control">
                                    <option value="0" ${parseInt(response.banned) == 0 ? 'selected' : ''}>No</option>
                                    <option value="1" ${parseInt(response.banned) == 1 ? 'selected' : ''}>Sí</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    `,
                        showCancelButton: true,
                        confirmButtonClass: 'btn btn-success',
                        confirmButtonText: 'Guardar',
                        cancelButtonClass: 'btn btn-secondary',
                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        preConfirm: function () {
                            return new Promise(function (resolve) {
                                var datos = {
                                    access_id: access_id,
                                    access_token: access_token,
                                    provider_id: provider_id,
                                    name: $('#name').val(),
                                    rfc: $('#rfc').val(),
                                    email: $('#email').val(),
                                    payment_terms_id: $('#payment_terms_id').val(),
                                    provider_type: $('#provider_type').val(),
                                    origin: $('#origin').val(),
                                    employees_department_id: $('#employees_department_id').val(),
                                    banned: $('#banned').val()
                                };
                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'save_generales_provider',
                                    data: datos,
                                    success: function (resp) {
                                        resolve(resp);
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) return;

                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'Los datos fueron actualizados.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value && result.value.errors) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value && result.value.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones.', 'info');
                        } else {
                            Swal.fire('Error inesperado', 'Ocurrió un problema.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            }
        });
    });


    /// MODAL: AGREGAR DATOS FISCALES DEL PROVEEDOR (Solo si no existen)
    $(document).on('click', '.btn-add-fiscal-provider', function (e) {
        e.preventDefault();
        console.log('[AGREGAR FISCAL] Click detectado');
        var provider_id = $(this).data('provider-id');
        console.log('[AGREGAR FISCAL] Provider ID:', provider_id);
        abrirModalFiscalProvider(provider_id);

    });


    /// MODAL: EDITAR DATOS FISCALES DEL PROVEEDOR
    $(document).on('click', '.btn-edit-fiscal-provider', function (e) {
        e.preventDefault();
        var provider_id = $(this).data('provider-id');
        abrirModalFiscalProvider(provider_id);
    });

    function abrirModalFiscalProvider(provider_id) {
        var SendData = {
            access_id: access_id,
            access_token: access_token,
            provider_id: provider_id
        };

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_fiscal_opts_provider',
            data: SendData,
            success: function (response) {
                // SI SE ENVIARON DATOS DEL CSF, SOBRESCRIBIMOS LOS VALORES
               /* if (datosCSF) {
                    console.log('[CSF] Sobrescribiendo campos con datos del CSF');
                    response.business_name = datosCSF.business_name || response.business_name;
                    response.rfc = datosCSF.rfc || response.rfc;
                    response.street = datosCSF.street || response.street;
                    response.number = datosCSF.number || response.number;
                    response.internal_number = datosCSF.internal_number || response.internal_number;
                    response.colony = datosCSF.colony || response.colony;
                    response.city = datosCSF.city || response.city;
                    response.municipality = datosCSF.municipality || response.municipality;
                    response.zipcode = datosCSF.zipcode || response.zipcode;
                    response.state_id = datosCSF.state_id || response.state_id;
                    response.sat_tax_regime_id = datosCSF.sat_tax_regime_id || response.sat_tax_regime_id;
                    response.payment_method_id = datosCSF.payment_method_id || response.payment_method_id;
                    response.cfdi_id = datosCSF.cfdi_id || response.cfdi_id;
                    response.email = datosCSF.email || response.email;
                }*/
                console.log('[FISCAL] Respuesta del servidor:', response);
                if (response.msg === 'ok') {
                    Swal.fire({
                        title: 'Editar datos fiscales',
                        width: 800,
                        html: `
                    <form id="form-fiscal-provider">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>Razón Social</label>
                                <input id="business_name" class="form-control" value="${response.business_name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>RFC</label>
                                <input id="rfc" class="form-control" value="${response.rfc || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Calle</label>
                                <input id="street" class="form-control" value="${response.street || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Número Ext.</label>
                                <input id="number" class="form-control" value="${response.number || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Número Int.</label>
                                <input id="internal_number" class="form-control" value="${response.internal_number || ''}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Colonia</label>
                                <input id="colony" class="form-control" value="${response.colony || ''}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Ciudad</label>
                                <input id="city" class="form-control" value="${response.city || ''}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Municipio</label>
                                <input id="municipality" class="form-control" value="${response.municipality || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Estado</label>
                                <select id="state_id" class="form-control"></select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Código Postal</label>
                                <input id="zipcode" class="form-control" value="${response.zipcode || ''}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Régimen Fiscal SAT</label>
                                <select id="sat_tax_regime_id" class="form-control"></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Forma de Pago</label>
                                <select id="payment_method_id" class="form-control"></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Uso de CFDI</label>
                                <select id="cfdi_id" class="form-control"></select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Email</label>
                                <input id="email" class="form-control" value="${response.email || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Constancia de situación fiscal (PDF)</label>
                                <input type="file" id="csf" class="form-control">
                                ${(response.csf_link ? `<a href="${response.csf_link}" target="_blank" class="btn btn-sm btn-neutral mt-2">Ver constancia cargada</a>` : '')}
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Opinion de Cumplimiento (PDF)</label>
                                <input type="file" id="opc" class="form-control">
                                ${(response.opc_link ? `<a href="${response.opc_link}" target="_blank" class="btn btn-sm btn-neutral mt-2">Ver opinion de cumplimiento</a>` : '')}
                            </div>
                        </div>
                    </form>
                    `,
                        onOpen: function () {
                            $('#state_id').html(response.states_opts_html);
                            $('#sat_tax_regime_id').html(response.sat_tax_regime_opts_html);
                            $('#payment_method_id').html(response.payment_method_opts_html);
                            $('#cfdi_id').html(response.cfdi_opts_html);

                            $('#state_id').val(String(response.state_id || ''));
                            $('#sat_tax_regime_id').val(String(response.sat_tax_regime_id || ''));
                            $('#payment_method_id').val(String(response.payment_method_id || ''));
                            $('#cfdi_id').val(String(response.cfdi_id || ''));
                        },
                        showCancelButton: true,
                        confirmButtonClass: 'btn btn-success',
                        confirmButtonText: 'Guardar',
                        cancelButtonClass: 'btn btn-secondary',
                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        preConfirm: function () {
                            return new Promise(function (resolve) {
                                var formData = new FormData();
                                formData.append('access_id', access_id);
                                formData.append('access_token', access_token);
                                formData.append('provider_id', provider_id);
                                formData.append('business_name', $('#business_name').val());
                                formData.append('rfc', $('#rfc').val());
                                formData.append('street', $('#street').val());
                                formData.append('number', $('#number').val());
                                formData.append('internal_number', $('#internal_number').val());
                                formData.append('colony', $('#colony').val());
                                formData.append('city', $('#city').val());
                                formData.append('municipality', $('#municipality').val());
                                formData.append('state_id', $('#state_id').val());
                                formData.append('zipcode', $('#zipcode').val());
                                formData.append('sat_tax_regime_id', $('#sat_tax_regime_id').val());
                                formData.append('payment_method_id', $('#payment_method_id').val());
                                formData.append('cfdi_id', $('#cfdi_id').val());
                                formData.append('email', $('#email').val());
                                var fileInput = document.getElementById('csf');
                                if (fileInput && fileInput.files.length > 0) {
                                    formData.append('csf', fileInput.files[0]);
                                }
                                var fileInput = document.getElementById('opc');
                                if (fileInput && fileInput.files.length > 0) {
                                    formData.append('opc', fileInput.files[0]);
                                }
                                $.ajax({
                                    type: 'post',
                                    url: url + 'save_fiscal_provider',
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                    dataType: 'json',
                                    success: function (resp) {
                                        resolve(resp);
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: function () { return !Swal.isLoading(); }
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) return;
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'Los datos fueron actualizados.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value?.errors?.length > 0) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value?.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones.', 'info');
                        } else {
                            Swal.fire('Error', result.value?.msg || 'Revisa los datos e intenta nuevamente.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo obtener la información: ' + error, 'error');
            }
        });
    }

    function abrirModalFiscalDesdeCSF(provider_id, data_csf = null) {
        console.log('[CSF] Abriendo modal con datos parseados del CSF', data_csf);

        // Simulamos una respuesta "response" similar al AJAX real
        var fakeResponse = {
            msg: 'ok',
            business_name: data_csf.business_name || '',
            rfc: data_csf.rfc || '',
            street: data_csf.street || '',
            number: data_csf.number || '',
            internal_number: data_csf.internal_number || '',
            colony: data_csf.colony || '',
            city: data_csf.city || '',
            municipality: data_csf.municipality || '',
            zipcode: data_csf.zipcode || '',
            state_id: data_csf.state_id || '',
            sat_tax_regime_id: data_csf.sat_tax_regime_id || '',
            payment_method_id: data_csf.payment_method_id || '',
            cfdi_id: data_csf.cfdi_id || '',
            email: data_csf.email || '',
            csf_link: '',
            states_opts_html: '',
            sat_tax_regime_opts_html: '',
            payment_method_opts_html: '',
            cfdi_opts_html: ''
        };

        // Cargamos los catálogos primero desde el backend real
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_fiscal_opts_provider',
            data: {
                access_id: access_id,
                access_token: access_token,
                provider_id: provider_id
            },
            success: function (catalogos) {
                // Mezclamos los catálogos con los datos parseados
                fakeResponse.states_opts_html = catalogos.states_opts_html;
                fakeResponse.sat_tax_regime_opts_html = catalogos.sat_tax_regime_opts_html;
                fakeResponse.payment_method_opts_html = catalogos.payment_method_opts_html;
                fakeResponse.cfdi_opts_html = catalogos.cfdi_opts_html;

                // Inyectamos manualmente el "response" para que funcione igual
                // Sobrescribimos temporalmente la función original
                var originalAjax = $.ajax;

                $.ajax = function (options) {
                    if (options.url === url + 'get_fiscal_opts_provider') {
                        options.success(fakeResponse);
                        return;
                    }
                    return originalAjax(options);
                };

                // Llamamos la función normal de abrir modal fiscal
                abrirModalFiscalProvider(provider_id);

                // Restauramos la función AJAX original
                $.ajax = originalAjax;
            },
            error: function () {
                Swal.fire('Error', 'No se pudieron cargar los catálogos fiscales.', 'error');
            }
        });
    }



    $(document).on('click', '#btn-cargar-csf-provider', function (e) {
        e.preventDefault();

        var provider_id = $(this).data('provider-id');
        console.log('[CSF] Click en botón cargar CSF - provider_id:', provider_id);

        // Si el modal no existe, lo agregamos dinámicamente al body
        if (!$('#modal-cargar-csf-provider').length) {
            console.log('[CSF] Modal no existe, se construye dinámicamente.');
            $('body').append(`
        <div class="modal fade" id="modal-cargar-csf-provider" tabindex="-1" role="dialog" aria-labelledby="modalLabelCSFProvider" aria-hidden="true">
          <div class="modal-dialog modal-md" role="document">
            <form id="form-cargar-csf-provider" enctype="multipart/form-data">
              <div class="modal-content">
                <div class="modal-header bg-info text-white">
                  <h5 class="modal-title" id="modalLabelCSFProvider">Importar datos fiscales desde CSF</h5>
                  <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <input type="hidden" name="provider_id" id="provider_id_csf" value="">
                  <div class="form-group">
                    <label for="csf_file">Seleccionar archivo PDF</label>
                    <input type="file" class="form-control" id="csf_file" name="csf" accept="application/pdf" required>
                    <small class="form-text text-muted">Solo se aceptan archivos PDF.</small>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-info">Procesar CSF</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
              </div>
            </form>
          </div>
        </div>`);
        }

        $('#provider_id_csf').val(provider_id);
        $('#modal-cargar-csf-provider').modal('show');
        console.log('[CSF] Modal mostrado con provider_id:', provider_id);
    });

    // Envío del formulario CSF
    $(document).on('submit', '#form-cargar-csf-provider', function (e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('access_id', access_id);
        formData.append('access_token', access_token);

        console.log('[CSF] Enviando formulario para parsear CSF');

        Swal.fire({
            title: 'Importando CSF...',
            text: 'Procesando el archivo PDF',
            allowOutsideClick: false,
            onBeforeOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: url + 'parse_csf_provider',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                Swal.close();
                console.log('[CSF] Respuesta recibida del backend:', response);

                if (response.msg === 'ok') {
                    console.log('[CSF] Datos parseados correctamente:', response.data);
                    Swal.fire('Datos cargados', 'Se importaron los datos del CSF.', 'success');

                    // Abrir el modal con los datos parseados
                    abrirModalFiscalDesdeCSF(response.provider_id, response.data);

                    $('#modal-cargar-csf-provider').modal('hide');
                    console.log('[CSF] Modal cerrado después de cargar.');
                } else {
                    console.warn('[CSF] Error en la respuesta:', response.msg);
                    Swal.fire('Error', response.msg, 'error');
                }
            },
            error: function (xhr) {
                Swal.close();
                console.error('[CSF] Error de comunicación con el backend', xhr.responseText);
                Swal.fire('Error', 'Hubo un problema al procesar el CSF.', 'error');
            }
        });
    });

    /// MODAL: AGREGAR CONTACTO DEL PROVEEDOR
    $(document).on('click', '.btn-add-contacto-provider', function (e) {
        e.preventDefault();
        var provider_id = $(this).data('provider-id');
        abrirModalContactoProvider(0, provider_id);
    });

    /// MODAL: EDITAR CONTACTO DEL PROVEEDOR
    $(document).on('click', '.btn-edit-contacto-provider', function (e) {
        e.preventDefault();
        var contact_id = $(this).data('id');
        var provider_id = $(this).data('provider-id');
        abrirModalContactoProvider(contact_id, provider_id);
    });

    function abrirModalContactoProvider(contact_id, provider_id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_contacto_opts_provider',
            data: {
                access_id: access_id,
                access_token: access_token,
                contact_id: contact_id,
                provider_id: provider_id
            },
            success: function (response) {
                if (response.msg === 'ok') {
                    Swal.fire({
                        title: (contact_id == 0 ? 'Agregar' : 'Editar') + ' contacto',
                        width: 700,
                        html: `
                    <form id="form-contacto">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>ID Contacto</label>
                                <input id="idcontact" class="form-control" value="${response.idcontact || ''}" ${contact_id != 0 ? 'readonly' : ''}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nombre</label>
                                <input id="name" class="form-control" value="${response.name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Apellido</label>
                                <input id="last_name" class="form-control" value="${response.last_name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Teléfono</label>
                                <input id="phone" class="form-control" value="${response.phone || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Celular</label>
                                <input id="cel" class="form-control" value="${response.cel || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input id="email" class="form-control" value="${response.email || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Departamento</label>
                                <input id="departments" class="form-control" value="${response.departments || ''}">
                            </div>
                        </div>
                    </form>
                    `,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        preConfirm: function () {
                            return new Promise(function (resolve) {
                                var datos = {
                                    access_id: access_id,
                                    access_token: access_token,
                                    contact_id: contact_id,
                                    provider_id: provider_id,
                                    idcontact: $('#idcontact').val(),
                                    name: $('#name').val(),
                                    last_name: $('#last_name').val(),
                                    phone: $('#phone').val(),
                                    cel: $('#cel').val(),
                                    email: $('#email').val(),
                                    departments: $('#departments').val()
                                };
                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'save_contacto_provider',
                                    data: datos,
                                    success: function (resp) {
                                        resolve(resp);
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: function () { return !Swal.isLoading(); }
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) return;
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'Los datos fueron actualizados.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value?.errors?.length > 0) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value?.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones.', 'info');
                        } else {
                            Swal.fire('Error', result.value?.msg || 'Revisa los datos e intenta nuevamente.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            }
        });
    }


    /// MODAL: AGREGAR DOMICILIO DE ENTREGA DEL PROVEEDOR
    $(document).on('click', '.btn-add-entrega-provider', function (e) {
        e.preventDefault();
        var provider_id = $(this).data('provider-id');
        abrirModalEntregaProvider(0, provider_id);
    });

    /// MODAL: EDITAR DOMICILIO DE ENTREGA DEL PROVEEDOR
    $(document).on('click', '.btn-edit-entrega-provider', function (e) {
        e.preventDefault();
        var entrega_id = $(this).data('id');
        var provider_id = $(this).data('provider-id');
        abrirModalEntregaProvider(entrega_id, provider_id);
    });

    function abrirModalEntregaProvider(id, provider_id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_entrega_opts_provider',
            data: {
                access_id: access_id,
                access_token: access_token,
                entrega_id: id,
                provider_id: provider_id
            },
            success: function (response) {
                if (response.msg === 'ok') {
                    Swal.fire({
                        title: (id == 0 ? 'Agregar' : 'Editar') + ' domicilio de entrega',
                        width: 800,
                        html: `
                    <form id="form-entrega-provider">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>ID de Dirección</label>
                                <input id="iddelivery" class="form-control" value="${response.iddelivery || ''}" ${id != 0 ? 'readonly' : ''}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Calle</label>
                                <input id="street" class="form-control" value="${response.street || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Número Ext.</label>
                                <input id="number" class="form-control" value="${response.number || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Número Int.</label>
                                <input id="internal_number" class="form-control" value="${response.internal_number || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Colonia</label>
                                <input id="colony" class="form-control" value="${response.colony || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Ciudad</label>
                                <input id="city" class="form-control" value="${response.city || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Municipio</label>
                                <input id="municipality" class="form-control" value="${response.municipality || ''}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Estado</label>
                                <select id="state" class="form-control"></select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Código postal</label>
                                <input id="zipcode" class="form-control" value="${response.zipcode || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Horario de recepción</label>
                                <input id="reception_hours" class="form-control" value="${response.reception_hours || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Datos adicionales</label>
                                <input id="delivery_notes" class="form-control" value="${response.delivery_notes || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nombre de contacto</label>
                                <input id="name" class="form-control" value="${response.name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Apellido de contacto</label>
                                <input id="last_name" class="form-control" value="${response.last_name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Teléfono de contacto</label>
                                <input id="phone" class="form-control" value="${response.phone || ''}">
                            </div>
                        </div>
                    </form>
                    `,
                        onOpen: function () {
                            $('#state').html(response.state_opts_html);
                            $('#state').val(response.state || '').trigger('change');
                        },
                        showCancelButton: true,
                        confirmButtonClass: 'btn btn-success',
                        confirmButtonText: 'Guardar',
                        cancelButtonClass: 'btn btn-secondary',
                        cancelButtonText: 'Cancelar',
                        focusConfirm: false,
                        preConfirm: function () {
                            return new Promise(function (resolve) {
                                var datos = {
                                    access_id: access_id,
                                    access_token: access_token,
                                    entrega_id: id,
                                    provider_id: provider_id,
                                    iddelivery: $('#iddelivery').val(),
                                    street: $('#street').val(),
                                    number: $('#number').val(),
                                    internal_number: $('#internal_number').val(),
                                    colony: $('#colony').val(),
                                    city: $('#city').val(),
                                    municipality: $('#municipality').val(),
                                    state: $('#state').val(),
                                    zipcode: $('#zipcode').val(),
                                    reception_hours: $('#reception_hours').val(),
                                    delivery_notes: $('#delivery_notes').val(),
                                    name: $('#name').val(),
                                    last_name: $('#last_name').val(),
                                    phone: $('#phone').val()
                                };
                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'save_entrega_provider',
                                    data: datos,
                                    success: function (resp) {
                                        resolve(resp);
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: function () { return !Swal.isLoading(); }
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) return;
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'Los datos fueron actualizados.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value?.errors?.length > 0) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value?.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones.', 'info');
                        } else {
                            Swal.fire('Error', result.value?.msg || 'Revisa los datos e intenta nuevamente.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            },
            error: function (xhr, status, error) {
                Swal.fire('Error', 'No se pudo obtener la información: ' + error, 'error');
            }
        });
    }


    // ABRIR MODAL: AGREGAR CUENTA BANCARIA
    $(document).on('click', '.btn-add-banco-provider', function (e) {
        e.preventDefault();
        var provider_id = $(this).data('provider-id');
        abrirModalBancoProvider(0, provider_id);
    });

    // ABRIR MODAL: EDITAR CUENTA BANCARIA
    $(document).on('click', '.btn-edit-banco-provider', function (e) {
        e.preventDefault();
        var bank_id = $(this).data('id');
        var provider_id = $(this).data('provider-id');
        abrirModalBancoProvider(bank_id, provider_id);
    });


    function abrirModalBancoProvider(bank_id, provider_id) {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_banco_opts_provider',
            data: {
                access_id: access_id,
                access_token: access_token,
                bank_id: bank_id,
                provider_id: provider_id
            },
            success: function (response) {
                if (response.msg === 'ok') {
                    Swal.fire({
                        title: (bank_id == 0 ? 'Agregar' : 'Editar') + ' cuenta bancaria',
                        width: 700,
                        html: `
                    <form id="form-banco-provider">
                        <div class="form-row">
                            <div class="col-md-6 mb-3">
                                <label>Banco</label>
                                <select id="bank_id" class="form-control">${response.bank_opts_html || ''}</select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Moneda</label>
                                <select id="currency_id" class="form-control">${response.currency_opts_html || ''}</select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Cuenta</label>
                                <input id="account_number" class="form-control" value="${response.account_number || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>CLABE</label>
                                <input id="clabe" class="form-control" value="${response.clabe || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Beneficiario</label>
                                <input id="name" class="form-control" value="${response.name || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input id="email" class="form-control" value="${response.email || ''}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Teléfono</label>
                                <input id="phone" class="form-control" value="${response.phone || ''}">
                            </div>

                            <div class="col-md-12 mb-3" style="display:none;">
                                <label><strong>Días de pago</strong></label>
                                <div class="form-group" style="background: #f7f7f9; border-radius: 7px; padding: 10px; border: 1px solid #e6e9ed;">
                                    ${['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'].map((day, i) => `
                                        <label class="checkbox-inline mr-2">
                                            <input type="checkbox" class="pay_day" value="${day}" ${response.pay_days && response.pay_days.split(',').includes(day) ? 'checked' : ''}> ${['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'][i]}
                                        </label>
                                    `).join('')}
                                </div>
                                <small class="text-muted">Selecciona uno o más días en los que se realiza el pago.</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>¿Predeterminada?</label>
                                <select id="default" class="form-control">
                                    <option value="0" ${response.default == 0 ? 'selected' : ''}>No</option>
                                    <option value="1" ${response.default == 1 ? 'selected' : ''}>Sí</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Carátula bancaria (PDF, JPG, PNG, BMP)</label>
                                <input type="file" id="bank_cover" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.bmp">
                            </div>


                        </div>
                    </form>
                    `,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        preConfirm: function () {
                            return new Promise(function (resolve) {
                                var formData = new FormData();
                                formData.append('access_id', access_id);
                                formData.append('access_token', access_token);
                                formData.append('bank_id', bank_id);
                                formData.append('provider_id', provider_id);
                                formData.append('bank_id_val', $('#bank_id').val());
                                formData.append('currency_id', $('#currency_id').val());
                                formData.append('account_number', $('#account_number').val());
                                formData.append('clabe', $('#clabe').val());
                                formData.append('name', $('#name').val());
                                formData.append('email', $('#email').val());
                                formData.append('phone', $('#phone').val());
                                formData.append('pay_days', $('#swal2-content .pay_day:checked').map(function () {
                                    return $(this).val();
                                }).get().join(','));
                                formData.append('default', $('#default').val());

                                // 👇 NUEVO: archivo carátula bancaria
                                var bankCover = $('#bank_cover')[0].files[0];
                                if (bankCover) {
                                    formData.append('bank_cover', bankCover);
                                }

                                $.ajax({
                                    type: 'post',
                                    dataType: 'json',
                                    url: url + 'save_banco_provider',
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                    success: function (resp) {
                                        resolve(resp);
                                    },
                                    error: function (xhr, status, error) {
                                        resolve({ msg: 'error', errors: ['Error inesperado: ' + error] });
                                    }
                                });
                            });
                        },
                        allowOutsideClick: function () { return !Swal.isLoading(); }
                    }).then(function (result) {
                        if (result.dismiss === Swal.DismissReason.cancel) return;
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('¡Guardado!', 'La cuenta bancaria fue actualizada.', 'success').then(function () {
                                location.reload();
                            });
                        } else if (result.value?.errors?.length > 0) {
                            Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                        } else if (result.value?.msg === 'no_changes') {
                            Swal.fire('Sin cambios detectados', 'No se realizaron modificaciones.', 'info');
                        } else {
                            Swal.fire('Error', result.value?.msg || 'Revisa los datos e intenta nuevamente.', 'error');
                        }
                    });
                    // Set selected values si aplica
                    $('#bank_id').val(response.bank_id || '');
                    $('#currency_id').val(response.currency_id || '');
                } else {
                    Swal.fire('Error', response.msg, 'error');
                }
            }
        });
    }

    $(document).on('click', '.btn-add-depto-provider', function () {
        const provider_id = $(this).data('id');

        $.ajax({
            type: 'post',
            dataType: 'json',
            url: url + 'get_departments_opts',
            data: { access_id, access_token, provider_id },
            success: function (response) {
                if (response.msg === 'ok') {
                    Swal.fire({
                        title: 'Agregar departamento',
                        html: `
                        <form id="form-add-depto">
                        <div class="form-group">
                            <label>Departamento</label>
                            <select id="employees_department_id" class="form-control">
                            ${response.departments_opts_html}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>¿Principal?</label>
                            <select id="main" class="form-control">
                            <option value="0">No</option>
                            <option value="1">Sí</option>
                            </select>
                        </div>
                        </form>`,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        preConfirm: () => {
                            const payload = new FormData();
                            payload.append('access_id', access_id);
                            payload.append('access_token', access_token);
                            payload.append('provider_id', provider_id);
                            payload.append('employees_department_id', $('#employees_department_id').val());
                            payload.append('main', $('#main').val());

                            return fetch(url + 'save_department_provider', {
                                method: 'POST',
                                body: payload
                            })
                                .then(async (response) => {
                                    const text = await response.text();
                                    console.log('[RAW RESPONSE]', text);
                                    try {
                                        return JSON.parse(text);
                                    } catch (e) {
                                        return { msg: 'error', errors: ['Respuesta no JSON: ' + text] };
                                    }
                                })
                                .catch(error => {
                                    return { msg: 'error', errors: ['Error inesperado: ' + error.message] };
                                });
                        }


                    }).then((result) => {
                        console.log('[DEBUG][SAVE_DEPT]', result);
                        if (result.value && result.value.msg === 'ok') {
                            Swal.fire('Guardado', 'El departamento fue agregado.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', result.value?.msg || 'Error al guardar', 'error');
                        }
                    });

                }
            }
        });
    });

    // CAMBIAR DEPARTAMENTO PRINCIPAL
    $(document).on('click', '.btn-set-main-depto', function () {
        const id = $(this).data('id');
        $.post(url + 'set_main_department_provider', {
            access_id, access_token, id
        }, function (response) {
            if (response.msg === 'ok') {
                Swal.fire('Actualizado', 'Departamento principal actualizado.', 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error', response.msg || 'No se pudo actualizar.', 'error');
            }
        }, 'json');
    });

    // ELIMINAR DEPARTAMENTO
    $(document).on('click', '.btn-delete-depto', function () {
        const id = $(this).data('id');
        Swal.fire({
            title: '¿Eliminar departamento?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(result => {
            if (result.isConfirmed) {
                $.post(url + 'delete_department_provider', {
                    access_id, access_token, id
                }, function (response) {
                    if (response.msg === 'ok') {
                        Swal.fire('Eliminado', 'Departamento eliminado.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', response.msg || 'No se pudo eliminar.', 'error');
                    }
                }, 'json');
            }
        });
    });

    // ===============================
    // CONTRATOS – NUEVO
    // ===============================
    $(document).on('click', '.btn-add-contrato-provider', function (e) {
        e.preventDefault();
        var provider_id = $(this).data('provider-id');
        var user_id = $(this).data('user-id');

        $.post(url + 'get_contrato_opts_provider', {
            access_id, access_token, provider_id, contract_id: 0
        }, function (r) {
            if (r.msg !== 'ok') { Swal.fire('Error', r.msg || 'No disponible', 'error'); return; }

            Swal.fire({
                title: 'Nuevo contrato',
                width: 800,
                html: `
        <form id="form-contrato-provider">
          <div class="form-row">
            <div class="col-md-6 mb-3">
              <label>Título</label>
              <input id="title" class="form-control" value="">
            </div>
            <div class="col-md-3 mb-3">
              <label>Folio</label>
              <input id="code" class="form-control" value="">
            </div>
            <div class="col-md-3 mb-3">
              <label>Estatus</label>
              <select id="status" class="form-control">${r.status_opts_html}</select>
            </div>
            <div class="col-md-6 mb-3">
              <label>Categoría</label>
              <select id="category" class="form-control">${r.category_opts_html}</select>
            </div>
            <div class="col-md-3 mb-3">
              <label>Inicio</label>
              <input id="start_date" type="date" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
              <label>Fin</label>
              <input id="end_date" type="date" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
              <label>Descripción</label>
              <textarea id="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-12 mb-3">
              <label>Archivo (PDF)</label>
              <input type="file" id="contract_file" class="form-control" accept="application/pdf">
            </div>
          </div>
        </form>
      `,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        var fd = new FormData();
                        fd.append('access_id', access_id);
                        fd.append('access_token', access_token);
                        fd.append('provider_id', provider_id);
                        fd.append('contract_id', 0);
                        fd.append('title', $('#title').val());
                        fd.append('code', $('#code').val());
                        fd.append('category', $('#category').val());
                        fd.append('status', $('#status').val());
                        fd.append('start_date', $('#start_date').val());
                        fd.append('end_date', $('#end_date').val());
                        fd.append('description', $('#description').val());
                        var f = $('#contract_file')[0].files[0];
                        if (f) fd.append('contract_file', f);

                        $.ajax({
                            type: 'post', url: url + 'save_contrato_provider',
                            data: fd, contentType: false, processData: false, dataType: 'json',
                            success: function (resp) { resolve(resp); },
                            error: function (xhr) { resolve({ msg: 'error', errors: ['Error inesperado'] }); }
                        });
                    });
                }
            }).then(function (result) {
                if (result.dismiss === Swal.DismissReason.cancel) return;
                if (result.value && result.value.msg === 'ok') {
                    Swal.fire('Guardado', 'Contrato registrado.', 'success').then(() => location.reload());
                } else if (result.value?.errors?.length) {
                    Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                } else {
                    Swal.fire('Error', result.value?.msg || 'No se pudo guardar', 'error');
                }
            });

        }, 'json');
    });

    // ===============================
    // CONTRATOS – EDITAR
    // ===============================
    $(document).on('click', '.btn-edit-contrato-provider', function (e) {
        e.preventDefault();
        var provider_id = $(this).data('provider-id');
        var contract_id = $(this).data('id');

        $.post(url + 'get_contrato_opts_provider', {
            access_id, access_token, provider_id, contract_id
        }, function (r) {
            if (r.msg !== 'ok') { Swal.fire('Error', r.msg || 'No disponible', 'error'); return; }

            Swal.fire({
                title: 'Editar contrato',
                width: 800,
                html: `
        <form id="form-contrato-provider">
          <div class="form-row">
            <div class="col-md-6 mb-3">
              <label>Título</label>
              <input id="title" class="form-control" value="${r.title || ''}">
            </div>
            <div class="col-md-3 mb-3">
              <label>Folio</label>
              <input id="code" class="form-control" value="${r.code || ''}">
            </div>
            <div class="col-md-3 mb-3">
              <label>Estatus</label>
              <select id="status" class="form-control">${r.status_opts_html}</select>
            </div>
            <div class="col-md-6 mb-3">
              <label>Categoría</label>
              <select id="category" class="form-control">${r.category_opts_html}</select>
            </div>
            <div class="col-md-3 mb-3">
              <label>Inicio</label>
              <input id="start_date" type="date" class="form-control" value="${r.start_date || ''}">
            </div>
            <div class="col-md-3 mb-3">
              <label>Fin</label>
              <input id="end_date" type="date" class="form-control" value="${r.end_date || ''}">
            </div>
            <div class="col-md-12 mb-3">
              <label>Descripción</label>
              <textarea id="description" class="form-control" rows="3">${r.description || ''}</textarea>
            </div>
            <div class="col-md-12 mb-1">
              <label>Reemplazar archivo (PDF)</label>
              <input type="file" id="contract_file" class="form-control" accept="application/pdf">
            </div>
            ${r.pdf_link ? `<div class="col-md-12 mb-2"><a href="${r.pdf_link}" target="_blank" class="btn btn-sm btn-neutral mt-2">Ver PDF actual</a></div>` : ''}
          </div>
        </form>
      `,
                didOpen: function () {
                    $('#status').val(String(r.status || 0));
                    $('#category').val(String(r.category || ''));
                },
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        var fd = new FormData();
                        fd.append('access_id', access_id);
                        fd.append('access_token', access_token);
                        fd.append('provider_id', provider_id);
                        fd.append('contract_id', contract_id);
                        fd.append('title', $('#title').val());
                        fd.append('code', $('#code').val());
                        fd.append('category', $('#category').val());
                        fd.append('status', $('#status').val());
                        fd.append('start_date', $('#start_date').val());
                        fd.append('end_date', $('#end_date').val());
                        fd.append('description', $('#description').val());
                        var f = $('#contract_file')[0].files[0];
                        if (f) fd.append('contract_file', f);

                        $.ajax({
                            type: 'post', url: url + 'save_contrato_provider',
                            data: fd, contentType: false, processData: false, dataType: 'json',
                            success: function (resp) { resolve(resp); },
                            error: function (xhr) { resolve({ msg: 'error', errors: ['Error inesperado'] }); }
                        });
                    });
                }
            }).then(function (result) {
                if (result.dismiss === Swal.DismissReason.cancel) return;
                if (result.value && result.value.msg === 'ok') {
                    Swal.fire('Guardado', 'Contrato actualizado.', 'success').then(() => location.reload());
                } else if (result.value?.errors?.length) {
                    Swal.fire('Datos no guardados', result.value.errors.join('<br>'), 'warning');
                } else {
                    Swal.fire('Error', result.value?.msg || 'No se pudo guardar', 'error');
                }
            });

        }, 'json');
    });

    // ===============================
    // CONTRATOS – ELIMINAR
    // ===============================
    $(document).on('click', '.btn-delete-contrato-provider', function () {
        var contract_id = $(this).data('id');
        var provider_id = $('#tab-generales-proveedor').length ? $('.btn-edit-generales-provider').data('id') : 0;

        Swal.fire({
            title: '¿Eliminar contrato?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then(function (res) {
            if (!res.isConfirmed) return;

            $.post(url + 'delete_contrato_provider', {
                access_id, access_token, contract_id, provider_id
            }, function (r) {
                if (r.msg === 'ok') {
                    Swal.fire('Eliminado', 'Contrato eliminado.', 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error', r.msg || 'No se pudo eliminar', 'error');
                }
            }, 'json');
        });
    });




////////////////////editor paginas templa

    // EDITOR VISUAL DE PLANTILLAS - LÓGICA JQUERY/AJAX
    // DOCUMENTACIÓN EN MAYÚSCULAS EN BLOQUES PRINCIPALES

    // MAIN.JS - BLOQUE EXCLUSIVO PARA EL EDITOR DE PLANTILLAS

    $(function () {
        // VARIABLE GLOBAL url (debe estar definida antes de este script)
        // window.plantilla_id_editor DEBE estar definida DESDE PHP

        if ($('#gjs').length > 0 && typeof window.plantilla_id_editor !== 'undefined') {
            var plantilla_id = window.plantilla_id_editor;

            // CARGA LA PLANTILLA DESDE EL SERVIDOR
            $.ajax({
                type: 'GET',
                url: url + 'load/' + plantilla_id,
                dataType: 'json',
                success: function (data) {
                    // INICIALIZA EL EDITOR GRAPESJS
                    var editor = grapesjs.init({
                        container: '#gjs',
                        height: '80vh',
                        fromElement: false,
                        plugins: [
                            'grapesjs-preset-webpage',
                            'gjs-blocks-basic',
                            'grapesjs-navbar',
                            'grapesjs-tabs',
                            'grapesjs-plugin-forms',
                            'grapesjs-custom-code',
                            'grapesjs-blocks-bootstrap4',
                            'grapesjs-touch'
                        ],
                        pluginsOpts: {
                            'grapesjs-preset-webpage': {},
                            'gjs-blocks-basic': {},
                            'grapesjs-navbar': {},
                            'grapesjs-tabs': {},
                            'grapesjs-plugin-forms': {},
                            'grapesjs-custom-code': {},
                            'grapesjs-blocks-bootstrap4': {},
                            'grapesjs-touch': {}
                        },
                        storageManager: false,
                        components: data.components ? JSON.parse(data.components) : '',
                        style: data.styles ? JSON.parse(data.styles) : '',
                    });

                    // GUARDAR PLANTILLA AL PRESIONAR BOTÓN
                    $('#guardar-template').on('click', function () {
                        // 1. Renderiza el HTML+CSS en un div oculto (fuera del iframe)
                        var $previewDiv = $('<div id="miniatura-render" style="width:400px;height:300px;position:absolute;left:-9999px;top:-9999px;z-index:-1;background:#fff;"></div>');
                        $('body').append($previewDiv);
                        $previewDiv.html(editor.getHtml());
                        $('<style>' + editor.getCss() + '</style>').appendTo($previewDiv);

                        // 2. Captura la miniatura real con html2canvas
                        html2canvas($previewDiv[0], { backgroundColor: "#fff" }).then(function (canvas) {
                            var miniatura = canvas.toDataURL("image/png");
                            // Limpia el div oculto
                            $previewDiv.remove();

                            // 3. Prepara payload para guardar
                            var payload = {
                                html: editor.getHtml(),
                                css: editor.getCss(),
                                components: JSON.stringify(editor.getComponents()),
                                styles: JSON.stringify(editor.getStyle()),
                                preview: miniatura // Aquí va la base64 lista
                            };

                            // 4. AJAX para guardar en el servidor
                            $.ajax({
                                type: 'POST',
                                url: url + 'save/' + plantilla_id,
                                data: JSON.stringify(payload),
                                contentType: 'application/json',
                                dataType: 'json',
                                success: function (resp) {
                                    if (resp.success) {
                                        Swal.fire('Guardado', 'Los cambios fueron guardados correctamente.', 'success');
                                    } else {
                                        Swal.fire('Error', resp.msg || 'No se pudo guardar la plantilla.', 'error');
                                    }
                                },
                                error: function (xhr) {
                                    var msg = 'Ocurrió un error al guardar.';
                                    if (xhr && xhr.responseJSON && xhr.responseJSON.msg) msg = xhr.responseJSON.msg;
                                    Swal.fire('Error', msg, 'error');
                                }
                            });
                        });
                    });
                },
                error: function (xhr) {
                    var msg = 'No se pudo cargar la plantilla.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                    Swal.fire('Error', msg, 'error');
                }
            });
        }
        // EL RESTO DE TU JS PUEDE IR AQUÍ
    });

    /*==========================================
    AGREGAR AL CARRITO DE COTIZACION
    ==========================================*/
    $(document).on('click', '.add-product-quote' , function(e) {
        var $this      = $(this);
        var product_id = $(this).data('product');
        let quantity   = 0;

        switch($this.data('type'))
        {
            case 'single':
            quantity = 1;
            break;

            case 'multiple':
            quantity = $('#quote-qty-' + product_id).val();
            break;

            default:
            return alertify.error(
                'Algo inesperado ha ocurrido, por favor refresca la página.'
            );
            break;
        }

        const quote = new QuoteItem($this.data('product'), quantity);
        quote.add();
    });


    /*==========================================
    EDITAR CARRITO DE COTIZACION
    ==========================================*/
    if(document.querySelectorAll('.edit-product-quote').length)
    {
        const quote = new QuoteItem();

        $('.edit-product-quote').change(e => {
            const inputEl = e.target;
            quote.setIdProduct = inputEl.dataset.product;
            quote.setQuantity = inputEl.value;
            quote.edit();
        });
    }


    /*==========================================
    ELIMINAR PRODUCTO CARRITO DE COTIZACION
    ==========================================*/
    if(document.querySelectorAll('.delete-product-quote').length)
    {
        const deleteBtns = document.querySelectorAll('.delete-product-quote');
        deleteBtns.forEach(btn =>
            btn.addEventListener('click', e => {
                const quote = new QuoteItem(e.target.dataset.product);
                quote.delete();
            })
        );
    }
});

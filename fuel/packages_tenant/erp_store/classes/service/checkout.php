<?php
/**
 * ERP Store Module - Checkout Service
 *
 * Checkout process service.
 *
 * @package    ERP_Store
 * @version    1.0.0
 * @author     ERP Development Team
 * @license    MIT License
 */

namespace ERP_Store;

/**
 * Checkout Service
 *
 * Handles checkout operations.
 */
class Service_Checkout
{
	/**
	 * Process checkout
	 *
	 * @param array $shipping_info Shipping information
	 * @param string $payment_method Payment method
	 * @return Model_Order|false
	 */
	public static function process($shipping_info, $payment_method)
	{
		$cart_items = Service_CartManager::get_items();

		if (empty($cart_items))
		{
			return false;
		}

		// Calculate totals
		$subtotal = Service_CartManager::get_total();
		$shipping = static::calculate_shipping($shipping_info);
		$tax = static::calculate_tax($subtotal);
		$total = $subtotal + $shipping + $tax;

		// Create order
		$order = Model_Order::forge(array(
			'order_number' => Model_Order::generate_order_number(),
			'user_id' => \Session::get('user_id'),
			'status' => 'pending',
			'subtotal' => $subtotal,
			'shipping' => $shipping,
			'tax' => $tax,
			'discount' => 0,
			'total' => $total,
			'payment_method' => $payment_method,
			'payment_status' => 'pending',
			'shipping_name' => $shipping_info['name'],
			'shipping_address' => $shipping_info['address'],
			'shipping_city' => $shipping_info['city'],
			'shipping_state' => $shipping_info['state'],
			'shipping_postal_code' => $shipping_info['postal_code'],
			'shipping_country' => $shipping_info['country'],
			'shipping_phone' => $shipping_info['phone'],
		));

		if ( ! $order->save())
		{
			return false;
		}

		// Clear cart
		Service_CartManager::clear();

		return $order;
	}

	/**
	 * Calculate shipping cost
	 *
	 * @param array $shipping_info Shipping information
	 * @return float
	 */
	public static function calculate_shipping($shipping_info)
	{
		// Basic shipping calculation
		// Can be extended to support multiple shipping methods
		return 5.00;
	}

	/**
	 * Calculate tax
	 *
	 * @param float $subtotal Subtotal
	 * @return float
	 */
	public static function calculate_tax($subtotal)
	{
		// Basic tax calculation (16% IVA)
		$tax_rate = 0.16;

		return round($subtotal * $tax_rate, 2);
	}

	/**
	 * Get payment methods
	 *
	 * @return array
	 */
	public static function get_payment_methods()
	{
		return array(
			'card' => array(
				'name' => 'Tarjeta de Crédito/Débito',
				'description' => 'Pago seguro con tarjeta',
				'icon' => 'credit-card',
			),
			'paypal' => array(
				'name' => 'PayPal',
				'description' => 'Pago rápido con PayPal',
				'icon' => 'paypal',
			),
			'transfer' => array(
				'name' => 'Transferencia Bancaria',
				'description' => 'Pago por transferencia',
				'icon' => 'bank',
			),
			'cash' => array(
				'name' => 'Pago en Efectivo',
				'description' => 'Pago al recibir',
				'icon' => 'money',
			),
		);
	}
}

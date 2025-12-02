<?php

/**
 * Helper para construir el payload oficial de Mercado Libre
 * a partir de un producto del ERP + su configuración ML.
 */
class Service_Ml_Helper
{
    /**
     * Construye el payload para ML usando overrides del producto.
     *
     * @param Model_Product                 $product
     * @param Model_Plataforma_Ml_Product   $link
     * @param Model_Plataforma_Ml_Configuration $config
     * @return array
     */
public static function build_item_payload($product, $link, $config)
{
    $payload = [];

    /*
    |--------------------------------------------------------------------------
    | 1. TÍTULO
    |--------------------------------------------------------------------------
    */
    $title = trim($link->ml_title_override ?: $product->name);
    $payload['title'] = Str::truncate($title, 60, '');


    /*
    |--------------------------------------------------------------------------
    | 2. CATEGORÍA ML
    |--------------------------------------------------------------------------
    */
    $payload['category_id'] = $link->ml_category_id;


    /*
    |--------------------------------------------------------------------------
    | 3. DESCRIPCIÓN
    |--------------------------------------------------------------------------
    */
    $payload['description'] = self::build_description($product, $link);


    /*
    |--------------------------------------------------------------------------
    | 4. IMÁGENES
    |--------------------------------------------------------------------------
    */
    $payload['pictures'] = self::build_pictures($product);


    /*
    |--------------------------------------------------------------------------
    | 5. PRECIO
    |--------------------------------------------------------------------------
    */
    if (!empty($link->ml_price_override) && floatval($link->ml_price_override) > 0) {
        $payload['price'] = floatval($link->ml_price_override);
    } else {
        $payload['price'] = floatval($product->price ? $product->price->price : 0);
    }


    /*
    |--------------------------------------------------------------------------
    | 6. STOCK
    |--------------------------------------------------------------------------
    */
    if (!empty($link->ml_stock_override)) {
        $payload['available_quantity'] = (int)$link->ml_stock_override;
    } else {
        $payload['available_quantity'] = max((int) $product->available, 0);
    }


    /*
    |--------------------------------------------------------------------------
    | 7. LISTING TYPE + BASE
    |--------------------------------------------------------------------------
    */
    $payload['listing_type_id'] = $link->ml_listing_type_override ?: 'gold_special';
    $payload['condition']       = 'new';
    $payload['currency_id']     = 'MXN';
    $payload['buying_mode']     = 'buy_it_now';


    /*
    |--------------------------------------------------------------------------
    | 8. STATUS
    |--------------------------------------------------------------------------
    */
    if (!empty($link->ml_status_override)) {
        $payload['status'] = $link->ml_status_override;
    }


    /*
    |--------------------------------------------------------------------------
    | 9. ATRIBUTOS ML (BRAND, MODEL + dinámicos + catálogo)
    |--------------------------------------------------------------------------
    */
    $attributes = [];

    // Brand
    $attributes[] = [
        'id'         => 'BRAND',
        'value_name' => $product->brand ? $product->brand->name : 'Genérico',
    ];

    // Model
    $attributes[] = [
        'id'         => 'MODEL',
        'value_name' => $product->code ?: '-',
    ];


    /*
    |--------------------------------------------------------------------------
    | 9.2 ATRIBUTOS DINÁMICOS (CORREGIDO)
    |--------------------------------------------------------------------------
    */
    $saved = Model_Plataforma_Ml_Product_Attribute::query()
        ->related('category_attribute')
        ->where('ml_product_id', $product->id) // <<< CORREGIDO!
        ->get();

    foreach ($saved as $pa)
    {
        if (!$pa->category_attribute) continue;

        $attr = $pa->category_attribute;

        $row = [
            'id' => $attr->ml_attribute_id
        ];

        /*
        |---------------------------------------------------------------
        | Soporte catálogos ML
        |---------------------------------------------------------------
        */
        if ($attr->is_catalog_required && $pa->ml_value_id) {
            // Valor de catálogo oficial
            $row['value_id']   = $pa->ml_value_id;
        }

        // Siempre mandar value_name (ML lo permite incluso con catálogo)
        if (!empty($pa->value_name)) {
            $row['value_name'] = $pa->value_name;
        }

        $attributes[] = $row;
    }

    $payload['attributes'] = $attributes;


    return $payload;
}


    // ============================================================
    // AUXILIAR: Construye descripción final
    // ============================================================
    private static function build_description($product, $link)
    {
        // 1. Si existe plantilla, cargarla (más adelante se hará con DB)
        if (!empty($link->ml_description_template_id)) {
            // Lógica temporal:
            $template = "Descripción del producto:\n{{DESCRIPTION}}\n\nMarca: {{BRAND}}\nCódigo: {{CODE}}\n";

            $desc = str_replace('{{DESCRIPTION}}', strip_tags($product->description), $template);
            $desc = str_replace('{{BRAND}}', $product->brand ? $product->brand->name : 'N/A', $desc);
            $desc = str_replace('{{CODE}}', $product->code, $desc);

            return $desc;
        }

        // 2. Si no hay plantilla → descripción directa
        return strip_tags($product->description ?: '');
    }



    // ============================================================
    // AUXILIAR: Galería de imágenes ML
    // ============================================================
    private static function build_pictures($product)
    {
        $pictures = [];

        // Imagen principal
        if (!empty($product->image)) {
            $pictures[] = [
                'source' => Uri::base(false) . 'uploads/products/' . $product->image
            ];
        }

        // Galería adicional
        if (!empty($product->galleries)) {
            foreach ($product->galleries as $g) {
                if (!empty($g->image)) {
                    $pictures[] = [
                        'source' => Uri::base(false) . 'uploads/products/gallery/' . $g->image
                    ];
                }
            }
        }

        return $pictures;
    }


    public static function log($config, $resource, $resource_id, $operation, $status, $message)
{
    Model_Plataforma_Ml_Log::forge([
        'configuration_id' => $config->id,
        'resource'         => $resource,
        'resource_id'      => $resource_id,
        'operation'        => $operation,
        'status'           => $status,
        'message'          => $message,
    ])->save();
}

public static function error($config, $product_id, $ml_item_id, $code, $message, $origin)
{
    Model_Plataforma_Ml_Error::forge([
        'configuration_id' => $config->id,
        'product_id'       => $product_id,
        'ml_item_id'       => $ml_item_id,
        'error_code'       => $code,
        'error_message'    => $message,
        'origin'           => $origin,
    ])->save();
}


}

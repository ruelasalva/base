<?php
class Helper_OC
{
    /**
     * GENERA EL SIGUIENTE FOLIO DE OC EN FORMATO OC000001, OC000002...
     */
    public static function next_code()
    {
        $last = Model_Providers_Order::query()
            ->order_by('id', 'desc')
            ->limit(1)
            ->get_one();
        $next = 1;
        if ($last && !empty($last->code_order)) {
            // Si tu code_order es tipo OC000123
            $num = intval(preg_replace('/\D/', '', $last->code_order));
            $next = $num + 1;
        }
        return 'OC' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }
}

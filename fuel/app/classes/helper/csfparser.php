<?php

use Smalot\PdfParser\Parser;
use Smalot\PdfParser\Config;

class Helper_CsfParser
{
    /**
     * PARSEA EL CSF Y RETORNA LOS DATOS ENCONTRADOS
     *
     * @param string $pdf_path Ruta absoluta al archivo PDF
     * @return array Datos extraídos
     */
    public static function parse($pdf_path)
    {
        require_once COREPATH . '/../vendor/smalot/pdfparser/Parser.php';
        require_once COREPATH . '/../vendor/smalot/pdfparser/Config.php';

        $config = new \Smalot\PdfParser\Config();
        $config->setFontSpaceLimit(10);

        $parser = new \Smalot\PdfParser\Parser([], $config);
        $pdf = $parser->parseFile($pdf_path);
        $text = $pdf->getText();


        // NORMALIZA CAMPOS SIN ESPACIOS
        $text = self::normalize_fields($text);




        // LOG DE TEXTO PARSEADO
        \Log::info('[CSF PARSER] TEXTO EXTRAÍDO DEL PDF: ' . mb_substr($text, 0, 1500));

        // RETORNAMOS DATOS PARSEADOS
        return [
            'rfc'             => self::extract_rfc($text),
            'business_name'   => self::extract_business_name($text),
            'street'          => self::extract_street($text),
            'number'          => self::extract_number($text),
            'internal_number' => '', // NO SE INCLUYE EN CSF
            'colony'          => self::extract_colony($text),
            'zipcode'         => self::extract_zipcode($text),
            'city'            => self::extract_city($text),
            'municipality'    => self::extract_municipality($text),
            'state'           => self::extract_state($text),
            'regimen'         => self::extract_regimen($text),
        ];
    }

    /**
     * NORMALIZA CAMPOS SIN ESPACIOS PARA MEJOR LECTURA
     */
    private static function normalize_fields($text)
    {
        $replacements = [
            '/NombredelaColonia:/'                                 => "Nombre de la Colonia:",
            '/NombredeVialidad:/'                                  => "Nombre de Vialidad:",
            '/NúmeroExterior:/'                                    => "Número Exterior:",
            '/CódigoPostal:/'                                      => "Código Postal:",
            '/Denominación\/RazónSocial:/'                         => "Denominación/Razón Social:",
            '/NombredelMunicipiooDemarcaciónTerritorial:/'         => "Nombre del Municipio o Demarcación Territorial:",
            '/NombredelaEntidadFederativa:/'                       => "Nombre de la Entidad Federativa:",
            '/NombredelaLocalidad:/'                               => "Nombre de la Localidad:",
        ];

        return preg_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * EXTRAE RFC DEL TEXTO
     */
    private static function extract_rfc($text)
    {
        preg_match('/[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}/', $text, $matches);
        \Log::info('[CSF PARSER] RFC: ' . ($matches[0] ?? 'NO ENCONTRADO'));
        return $matches[0] ?? '';
    }

    /**
     * EXTRAE RAZÓN SOCIAL
     */
    private static function extract_business_name($text)
{
    // INTENTA PRIMERO CON EL BLOQUE SUPERIOR (más confiable)
    if (preg_match('/([A-ZÑ& ]+)\s+Nombre, denominación o razón\s+social/i', $text, $matches)) {
        \Log::info('[CSF PARSER] BUSINESS NAME (SUPERIOR): ' . trim($matches[1]));
        return trim($matches[1]);
    }

    // SI FALLA, USA EL BLOQUE INFERIOR (el tradicional)
    if (preg_match('/Denominaci[oó]n\/Raz[oó]n Social:\s*(.+)/i', $text, $matches)) {
        \Log::info('[CSF PARSER] BUSINESS NAME (INFERIOR): ' . trim($matches[1]));
        return trim($matches[1]);
    }

    \Log::info('[CSF PARSER] BUSINESS NAME: NO ENCONTRADO');
    return '';
}


    /**
     * EXTRAE CALLE
     */
    private static function extract_street($text)
    {
        preg_match('/Nombre de Vialidad:\s*(.+)/i', $text, $matches);
        \Log::info('[CSF PARSER] CALLE: ' . ($matches[1] ?? 'NO ENCONTRADO'));
        return trim($matches[1] ?? '');
    }

    /**
     * EXTRAE NÚMERO EXTERIOR
     */
    private static function extract_number($text)
    {
        preg_match('/Número Exterior:\s*(.+)/i', $text, $matches);
        \Log::info('[CSF PARSER] NÚMERO EXT: ' . ($matches[1] ?? 'NO ENCONTRADO'));
        return trim($matches[1] ?? '');
    }

    /**
     * EXTRAE COLONIA
     */
    private static function extract_colony($text)
    {
        preg_match('/Nombre de la Colonia:\s*(.+)/i', $text, $matches);
        \Log::info('[CSF PARSER] COLONIA: ' . ($matches[1] ?? 'NO ENCONTRADO'));
        return trim($matches[1] ?? '');
    }

    /**
     * EXTRAE CÓDIGO POSTAL
     */
    private static function extract_zipcode($text)
    {
        preg_match('/Código Postal:\s*(\d{5})/i', $text, $matches);
        \Log::info('[CSF PARSER] CÓDIGO POSTAL: ' . ($matches[1] ?? 'NO ENCONTRADO'));
        return $matches[1] ?? '';
    }

    /**
     * EXTRAE MUNICIPIO COMO CIUDAD
     */
    private static function extract_city($text)
    {
        return self::extract_municipality($text);
    }

    /**
     * EXTRAE MUNICIPIO
     */
    private static function extract_municipality($text)
    {
        preg_match('/Nombre del Municipio o Demarcación Territorial:\s*(.+)/i', $text, $matches);
        \Log::info('[CSF PARSER] MUNICIPIO: ' . ($matches[1] ?? 'NO ENCONTRADO'));
        return trim($matches[1] ?? '');
    }

    /**
     * EXTRAE ESTADO
     */
    private static function extract_state($text)
    {
        preg_match('/Nombre de la Entidad Federativa:\s*(.+)/i', $text, $matches);
        \Log::info('[CSF PARSER] ESTADO: ' . ($matches[1] ?? 'NO ENCONTRADO'));
        return trim($matches[1] ?? '');
    }

    /**
     * EXTRAE RÉGIMEN FISCAL (EJEMPLO BÁSICO)
     */
    private static function extract_regimen($text)
    {
        preg_match('/(Régimen General de Ley Personas Morales)/', $text, $matches);
        \Log::info('[CSF PARSER] RÉGIMEN: ' . ($matches[1] ?? 'NO ENCONTRADO'));
        return $matches[1] ?? '';
    }
}

<?php

/*
 * Catálogo base de fallas y mantenimientos.
 *
 * Intervalos recomendados = valores de referencia comunes (manuales de fabricante,
 * guías de talleres y fabricantes de repuestos) para un carro de gasolina en uso
 * normal. "Lo que ocurra primero" entre km y meses. null = sin intervalo por ese
 * criterio (se cambia según desgaste/falla). Al registrarse, cada usuario recibe
 * una copia con el intervalo seteado igual al recomendado y puede ajustarlo.
 *
 * [nombre, categoría, km recomendado, meses recomendado, nota]
 */

return [
    // Motor
    ['Cambio de aceite y filtro de aceite', 'Motor', 5000, 6, 'Aceite mineral/semisintético. Sintético: hasta 10.000 km / 12 meses.'],
    ['Cambio de bujías', 'Motor', 30000, 24, 'Bujías convencionales. Iridio/platino: 80.000-100.000 km.'],
    ['Cambio de cables de bujías', 'Motor', 60000, 48, null],
    ['Cambio de correa de tiempo (distribución)', 'Motor', 80000, 60, 'Rango típico 60.000-100.000 km; verificar manual. Si es cadena, solo revisión.'],
    ['Cambio de correa de accesorios (alternador)', 'Motor', 60000, 48, null],
    ['Cambio de bomba de agua', 'Motor', 100000, 60, 'Se recomienda junto con la correa de tiempo.'],
    ['Limpieza de inyectores', 'Motor', 30000, 24, null],
    ['Cambio de empacadura de tapa de válvulas', 'Motor', 100000, 60, 'O antes si hay fugas de aceite.'],
    ['Cambio de empacaduras del motor (culata/cárter)', 'Motor', null, null, 'Según falla: fugas, recalentamiento o consumo de refrigerante.'],
    ['Cambio de sensor de oxígeno', 'Motor', 100000, null, null],
    ['Cambio de soportes (bases) de motor', 'Motor', 100000, null, 'Según desgaste/vibraciones.'],

    // Filtros
    ['Cambio de filtro de aire del motor', 'Filtros', 15000, 12, 'Más frecuente en zonas con mucho polvo.'],
    ['Cambio de filtro de aire de cabina (A/C)', 'Filtros', 15000, 12, null],
    ['Cambio de filtro de combustible', 'Filtros', 40000, 24, null],

    // Enfriamiento
    ['Cambio de refrigerante', 'Enfriamiento', 40000, 24, 'Refrigerante de larga duración: hasta 100.000 km / 60 meses.'],
    ['Cambio de termostato', 'Enfriamiento', 100000, 60, null],
    ['Cambio de mangueras del radiador', 'Enfriamiento', 80000, 48, null],

    // Frenos
    ['Cambio de líquido de frenos', 'Frenos', 40000, 24, null],
    ['Cambio de pastillas de freno delanteras', 'Frenos', 40000, 24, 'Rango típico 30.000-60.000 km según manejo.'],
    ['Cambio de pastillas/bandas de freno traseras', 'Frenos', 60000, 36, null],
    ['Cambio/rectificación de discos de freno', 'Frenos', 80000, null, 'Rectificar o cambiar según espesor mínimo.'],

    // Cauchos
    ['Rotación de cauchos', 'Cauchos', 10000, 6, null],
    ['Alineación y balanceo', 'Cauchos', 10000, 12, null],
    ['Cambio de cauchos', 'Cauchos', 50000, 60, 'O cuando la banda llegue a 1,6 mm. No más de 6 años de antigüedad.'],

    // Transmisión
    ['Cambio de aceite de caja automática (ATF)', 'Transmisión', 60000, 48, null],
    ['Cambio de aceite de caja sincrónica', 'Transmisión', 60000, 48, null],
    ['Cambio de aceite del diferencial', 'Transmisión', 60000, 48, null],
    ['Cambio de kit de embrague (clutch)', 'Transmisión', 100000, null, 'Según desgaste y forma de manejo.'],

    // Suspensión y dirección
    ['Cambio de líquido de dirección hidráulica', 'Suspensión y dirección', 60000, 48, null],
    ['Cambio de amortiguadores', 'Suspensión y dirección', 80000, 60, null],
    ['Cambio de bujes y gomas de suspensión', 'Suspensión y dirección', 80000, null, null],
    ['Revisión de tren delantero (rótulas, terminales)', 'Suspensión y dirección', 40000, 24, null],

    // Eléctrico
    ['Cambio de batería', 'Eléctrico', null, 48, 'Vida típica 3-5 años.'],
    ['Cambio de plumillas limpiaparabrisas', 'Eléctrico', null, 12, null],
    ['Cambio de bombillos', 'Eléctrico', null, null, 'Según falla.'],

    // Aire acondicionado
    ['Recarga de gas del aire acondicionado', 'Aire acondicionado', null, 24, null],

    // General
    ['Revisión general / inspección', 'General', 10000, 12, null],
    ['Reparación no programada / Otro', 'General', null, null, 'Para fallas que no están en el catálogo.'],
];

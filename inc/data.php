<?php
// Contenido del sitio. Fuente: Brief_Contenidos_Sitio_Valores_Real_Estate.docx + brochures de cada proyecto.

const NAV = [
    'inicio'     => ['Inicio', 'index.php'],
    'proyectos'  => ['Proyectos', 'proyectos.php'],
    'investor'   => ['Investor Pass', 'investor-pass.php'],
    'nosotros'   => ['Nosotros', 'nosotros.php'],
    'noticias'   => ['Noticias', 'noticias.php'],
    'trabaja'    => ['Trabaja con nosotros', 'trabaja-con-nosotros.php'],
    'contacto'   => ['Contacto', 'contacto.php'],
];

// Número institucional (conectado al CRM con chatbot). Todos los botones de WhatsApp van acá.
const WHATSAPP = '595994442828';

const CONTACT = [
    ['Dirección', 'Avda. Santa Teresa 1827 casi Aviadores del Chaco, Paseo La Galería, Torre 3, Piso 10, Oficina 2 · Asunción'],
    ['Teléfono', '+595 21 600 450'],
    ['WhatsApp', '+595 994 442 828'],
    ['Email', 'valores@valores.com.py'],
    ['Horario', 'Lunes a viernes, 8:00 a 17:00'],
];
const OFFICE_LL = [-25.2844, -57.5639];

const SOCIAL = [
    ['Instagram', '#'],
    ['Facebook', '#'],
    ['LinkedIn', '#'],
];

const ADVISORS = [
    ['name' => 'Gissela Laconich', 'email' => 'gisselalaconich@valoresrealestate.com.py', 'ig' => 'gisselalaconich.realestate'],
    ['name' => 'Sol Noguera', 'email' => 'solnoguera@valoresrealestate.com.py', 'ig' => 'solnoguera.m'],
    ['name' => 'Paola Paats', 'email' => 'Paolapaats@valoresrealestate.com.py', 'ig' => ''],
    ['name' => 'Nahiara Engelwart', 'email' => 'nahiaraengelwart@valores.com.py', 'ig' => '', 'hidden' => true],
];

const PROJECTS = [
    'sol-city' => [
        'num' => 1, 'name' => 'Sol City', 'fullName' => 'Sol City', 'type' => 'Edificio residencial',
        'loc' => 'Las Lomas, Asunción', 'll' => [-25.2862, -57.5705], 'kind' => 'units',
        'img' => 'sol/fachada',
        'brochure' => 'assets/brochures/sol-city.pdf',
        'tagline' => 'Un desarrollo urbano contemporáneo, bien conectado con la ciudad.',
        'short' => 'Residencias, espacios comunes y áreas verdes en una de las zonas con mayor proyección de Asunción.',
        'p1' => 'Sol City es un desarrollo urbano contemporáneo, ubicado estratégicamente en una de las zonas con mayor proyección de crecimiento, pensado para quienes buscan un entorno moderno, funcional y bien conectado. El proyecto integra diseño, planificación y calidad constructiva, con una propuesta que combina residencias, espacios comunes y áreas verdes, generando un entorno equilibrado para la vida diaria.',
        'p2' => 'Cuenta con infraestructura completa, accesos controlados y espacios pensados para acompañar un estilo de vida actual: áreas de uso común, espacios verdes, servicios cercanos y una ubicación que facilita la conexión con los principales puntos de la ciudad.',
        'delivery' => 'Diciembre 2026',
        'ig' => 'solcityasuncion.py',
        'facts' => [['Dic. 2026', 'Fecha de entrega'], ['Las Lomas', 'Asunción'], ['Domótica', 'En cada unidad'], ['Acceso', 'Controlado']],
        'gallery' => ['sol/fachada', 'sol/rooftop', 'sol/recepcion', 'sol/quincho', 'sol/gimnasio', 'sol/living', 'sol/balcon', 'sol/dormitorio', 'sol/cocina'],
        'tipos' => [
            ['name' => '2 dormitorios · Tipo A', 'imgs' => ['sol/planta-2d-a', 'sol/axo-2d-a'],
             'specs' => [['Dormitorios', '2 (ambos en suite)'], ['Baños', '2 + social'], ['Superficie', '88 m² propios'], ['Ambientes', 'Estar-comedor, cocina integrada con lavadero, balcón con parrilla']]],
            ['name' => '2 dormitorios · Tipo B', 'imgs' => ['sol/planta-2d-b', 'sol/axo-2d-b'],
             'specs' => [['Dormitorios', '2 (ambos en suite)'], ['Baños', '2 + social'], ['Superficie', '88 m² propios'], ['Ambientes', 'Estar-comedor, cocina integrada con lavadero, balcón con parrilla']]],
            ['name' => '3 dormitorios', 'imgs' => ['sol/planta-3d', 'sol/axo-3d'],
             'specs' => [['Dormitorios', '3 (uno en suite)'], ['Baños', 'Suite + familiar + social'], ['Superficie', '112 m² propios'], ['Ambientes', 'Estar-comedor, cocina integrada con lavadero, balcón con parrilla']]],
        ],
    ],
    'paraqvaria' => [
        'num' => 2, 'name' => 'Paraqvaria', 'fullName' => 'Paraqvaria', 'type' => 'Barrio cerrado · Lotes',
        'loc' => 'Cnel. José Félix Bogado, Itapúa', 'll' => [-27.0170, -56.2540], 'kind' => 'lots',
        'img' => 'paraqvaria/000',
        'brochure' => 'assets/brochures/paraqvaria.pdf',
        'masterplan' => 'paraqvaria/masterplan',
        'lots' => ['range' => [1000, 2600], 'perRow' => 10, 'water' => 'LAGO YACYRETÁ', 'center' => 'CLUB HOUSE'],
        'tagline' => 'Naturaleza, lujo y confort a orillas del Lago Yacyretá.',
        'short' => '650.000 m² con más de 1.600 metros de costa sobre el Lago Yacyretá.',
        'p1' => 'Un concepto único e innovador, ubicado estratégicamente en la ciudad de Coronel José Félix Bogado, Itapúa, con características que conjugan naturaleza, lujo y confort en un solo lugar.',
        'p2' => 'Desarrollado en un predio de 650.000 m², en medio de un entorno singular, con más de 1.600 metros de costa sobre el Lago Yacyretá. El proyecto cuenta con seguridad las 24 horas, pista de aviación, hangares, club house, canchas multideportivas, área náutica, playa, isla natural, laguna, piscina, quinchos, entre otros amenities.',
        'ig' => 'paraqvaria_aeromarina',
        'video' => 'paraqvaria',
        'facts' => [['650.000 m²', 'Superficie del predio'], ['+1.600 m', 'De costa sobre el lago'], ['24 h', 'Seguridad'], ['Pista', 'De aviación y hangares']],
        'gallery' => ['paraqvaria/000', 'paraqvaria/001', 'paraqvaria/002', 'paraqvaria/016', 'paraqvaria/018', 'paraqvaria/012', 'paraqvaria/014', 'paraqvaria/008', 'paraqvaria/011'],
    ],
    'la-ribera' => [
        'num' => 3, 'name' => 'La Ribera', 'fullName' => 'La Ribera', 'type' => 'Desarrollo urbano · Lotes',
        'loc' => 'Nueva Asunción, Chaco', 'll' => [-25.2310, -57.6560], 'kind' => 'lots',
        'img' => 'ribera/003',
        'brochure' => 'assets/brochures/la-ribera.pdf',
        'masterplan' => 'ribera/masterplan',
        'lots' => ['range' => [360, 620], 'perRow' => 14, 'water' => 'RÍO PARAGUAY', 'center' => 'POLO COMERCIAL · SUM'],
        'tagline' => 'Un nuevo horizonte, cerca de todo.',
        'short' => '217 lotes residenciales y 6 de alta densidad del otro lado del río.',
        'p1' => 'La Ribera es un desarrollo urbano que propone un nuevo horizonte en la Nueva Asunción, integrando ciudad, naturaleza y planificación en una ubicación estratégica del otro lado del río.',
        'p2' => 'Cuenta con un masterplan de 217 lotes residenciales y 6 lotes de alta densidad, integrando ciudad y entorno natural, con amenities como playas de arena, lagos, piscina, SUM y polo comercial. Un nuevo horizonte, cerca de todo: La Ribera redefine la relación entre cercanía urbana y espacio, consolidándose como una nueva centralidad en expansión.',
        'ig' => 'lariberaasu',
        'amenities' => [
            'Helipuerto', 'Náutica', 'Lagos interactivos', 'Playas de arena',
            'Circuito de caminatas', 'Ciclovías', 'Sala de wellness y yoga', 'Áreas verdes',
            'Zona de tratamientos corporales', 'Quincho climatizado con parrilla', 'Zona deportiva', 'Polo gastronómico y seating',
            'Generadores para el 100% de las residencias', 'Estacionamiento', 'Sala audiovisual', 'Sector de entrenamiento',
        ],
        'facts' => [['217', 'Lotes residenciales'], ['6', 'Lotes de alta densidad'], ['Playas', 'De arena y lagos'], ['Polo', 'Comercial']],
        'gallery' => ['ribera/003', 'ribera/006', 'ribera/004', 'ribera/007', 'ribera/008', 'ribera/010', 'ribera/011', 'ribera/012', 'ribera/014'],
    ],
    'cumbres' => [
        'num' => 4, 'name' => 'Cumbres', 'fullName' => 'Cumbres de San Bernardino', 'type' => 'Barrio cerrado · Lotes',
        'loc' => 'San Bernardino, Cordillera', 'll' => [-25.3095, -57.2960], 'kind' => 'lots',
        'img' => 'cumbres/053',
        'brochure' => 'assets/brochures/cumbres.pdf',
        'masterplan' => 'cumbres/masterplan',
        'lots' => ['range' => [800, 1500], 'perRow' => 4, 'water' => 'LAGO YPACARAÍ', 'center' => 'ÁREA VERDE'],
        'tagline' => 'Vistas abiertas e ininterrumpidas al Lago Ypacaraí.',
        'short' => 'Barrio cerrado con masterplan aterrazado, a pasos del centro y del Club Náutico.',
        'p1' => 'Cumbres de San Bernardino es un desarrollo residencial exclusivo, ubicado estratégicamente en una de las zonas más privilegiadas de la ciudad, a pasos del centro y del Club Náutico, con vistas abiertas e ininterrumpidas al Lago Ypacaraí.',
        'p2' => 'El proyecto se desarrolla sobre un terreno cuidadosamente planificado, con un masterplan aterrazado que garantiza visuales privilegiadas desde cada lote y una integración armónica con el entorno natural. Cumbres combina paisaje, diseño y tranquilidad en un barrio cerrado concebido para disfrutar durante todo el año, proponiendo una nueva forma de habitar San Bernardino: más calma, más naturaleza y un entorno diseñado para disfrutar hoy y proyectarse hacia el futuro.',
        'ig' => 'cumbres_sanber',
        'video' => 'cumbres',
        'sports' => [
            'title' => 'Canchas de pádel',
            'text' => 'Cumbres cuenta con una zona deportiva con canchas de pádel, con clases de lunes a viernes.',
            'phones' => ['0981 419 177', '0986 466 646'],
        ],
        'facts' => [['12 ha', 'Superficie del condominio'], ['+60', 'Lotes en 12 manzanas'], ['Vista al lago', 'Desde cada lote'], ['Club Náutico', 'A pasos']],
        'gallery' => ['cumbres/053', 'cumbres/044', 'cumbres/052', 'cumbres/051', 'cumbres/008', 'cumbres/045', 'cumbres/057', 'cumbres/js03'],
    ],
];

const COMPARE = [
    ['Ubicación', null],
    ['Tipo', ['Departamentos de 2 y 3 dormitorios', 'Lotes en barrio cerrado', 'Lotes residenciales y de alta densidad', 'Lotes en barrio cerrado']],
    ['Superficie', ['88 a 112 m² propios', '650.000 m² de predio', '217 + 6 lotes', '12 ha · +60 lotes']],
    ['Amenities', ['Rooftop con piscina, gimnasio, co-working, quincho', 'Pista de aviación, club house, área náutica, playa, piscina', 'Helipuerto, náutica, lagos, playas de arena, wellness, polo gastronómico', 'Club house, canchas de pádel, vistas al lago, áreas verdes']],
    ['Entorno', ['Urbano', 'Lago Yacyretá', 'Río Paraguay', 'Lago Ypacaraí']],
];

const NEWS = [
    ['pid' => 'sol-city', 'tag' => 'Sol City', 'source' => 'InfoNegocios', 'img' => 'sol/fachada', 'title' => 'Sol City: un edificio con departamentos que incorporan la domótica en Las Lomas (US$ 4,2 millones de inversión)', 'url' => 'https://infonegocios.com.py/infomicasa/sol-city-un-edificio-con-departamentos-que-incorporan-la-domotica-en-las-lomas-us-4-2-millones-de-inversion'],
    ['pid' => 'la-ribera', 'tag' => 'La Ribera', 'source' => 'El Inmobiliario', 'img' => 'ribera/003', 'title' => 'Descubre La Ribera: el nuevo horizonte en la Nueva Asunción', 'url' => 'https://www.elinmobiliario.com.py/post/descubre-la-ribera-el-nuevo-horizonte-en-la-nueva-asunci%C3%B3n'],
    ['pid' => 'cumbres', 'tag' => 'Cumbres', 'source' => 'Forbes Paraguay', 'img' => 'cumbres/053', 'title' => 'Cumbres San Bernardino, una joya del real estate con vista plena al lago Ypacaraí', 'url' => 'https://www.forbes.com.py/negocios/cumbres-san-bernardino-una-joya-real-estate-vista-plena-lago-ypacarai-n85731'],
    ['pid' => 'cumbres', 'tag' => 'Cumbres', 'source' => 'InfoNegocios', 'img' => 'cumbres/044', 'title' => 'Cumbres de San Bernardino, el barrio cerrado con vista al lago Ypacaraí, supera el 50% de ventas', 'url' => 'https://infonegocios.com.py/infomicasa/cumbres-de-san-bernardino-el-barrio-cerrado-con-vista-al-lago-ypacarai-supera-el-50-de-ventas'],
    ['pid' => 'cumbres', 'tag' => 'Cumbres', 'source' => 'Proyecta', 'img' => 'cumbres/052', 'title' => 'Cumbres de San Bernardino: un desarrollo que transforma el paisaje en experiencia residencial', 'url' => 'https://proyecta.com.py/cumbres-de-san-bernardino-un-desarrollo-que-transforma-el-paisaje-en-experiencia-residencial/'],
    ['pid' => 'la-ribera', 'tag' => 'La Ribera', 'source' => 'Forbes Paraguay', 'img' => 'ribera/006', 'title' => 'Desarrolladores inmobiliarios paraguayos ahora mueven sus inversiones hacia el interior', 'url' => 'https://www.forbes.com.py/macroeconomia/desarrolladores-inmobiliarios-paraguayos-ahora-mueven-sus-inversiones-interior-n61425'],
    ['pid' => 'mercado', 'tag' => 'Mercado', 'source' => 'Forbes Paraguay', 'img' => 'sol/zona', 'title' => 'Volver a vivir en el centro: la apuesta inmobiliaria que busca devolverle población a Asunción', 'url' => 'https://www.forbes.com.py/negocios/volver-vivir-centro-apuesta-inmobiliaria-busca-devolverle-poblacion-asuncion-n96700'],
];
const NEWS_TAGS = ['Todas', 'Sol City', 'La Ribera', 'Cumbres', 'Mercado'];

const INVESTOR_STEPS = [
    ['01', 'Asesoramiento', 'Analizamos tu perfil y objetivos para definir la modalidad adecuada.'],
    ['02', 'Elección del proyecto', 'Seleccionás lotes o unidades habilitadas dentro de nuestros desarrollos.'],
    ['03', 'Inversión y documentación', 'Estructuramos la operación y reunimos la documentación requerida.'],
    ['04', 'Residencia', 'Con la constancia de inversionista se tramita la residencia permanente.'],
];

// Misma plana directiva que Valores Casa de Bolsa (www.valores.com.py/nosotros#directiva).
const BOARD = [
    [
        'name' => 'Diego Christian Borja Terán',
        'role' => 'Presidente y accionista mayoritario',
        'photo' => 'directorio/presidente-diego-borja',
        'bio' => [
            'Presidente y accionista mayoritario de Valores Casa de Bolsa. Más de 30 años liderando operaciones bursátiles y estructuraciones fiduciarias en el mercado paraguayo.',
            'Es Doctor en Jurisprudencia por la Pontificia Universidad Católica del Ecuador, cuenta con una Especialización Superior en Derecho Financiero y Bursátil por la Universidad Andina, un MBA por la Universidad Americana de Paraguay y estudios en Negociación por la Universidad de Delaware.',
            'Reside en Paraguay desde 2005 y cuenta con una trayectoria vinculada al sector financiero y al desarrollo del mercado de capitales. A lo largo de su carrera, ha participado en la implementación de estructuras jurídicas y financieras, incluyendo el desarrollo de fideicomisos, y ha impulsado iniciativas orientadas a la internacionalización del mercado de capitales paraguayo, la atracción de inversión y la inclusión financiera a través de la tecnología.',
        ],
    ],
    [
        'name' => 'Gustavo Mathias Angulo Turitich',
        'role' => 'Vicepresidente',
        'photo' => 'directorio/vicepresidente-mathias-angulo',
        'bio' => [
            'Mathias Angulo es un destacado profesional con más de 10 años de experiencia en finanzas corporativas y mercado de capitales, especializado en el desarrollo de alternativas de financiamiento y estructuración de operaciones financieras.',
            'A lo largo de su trayectoria, ha liderado proyectos vinculados a la emisión de bonos y acciones en el mercado local, estructuración y reestructuración de productos financieros, cotización y venta de empresas, negocios fiduciarios, real estate y derecho bancario.',
            'Actualmente, como Vicepresidente de Valores Casa de Bolsa, aporta su experiencia y visión al desarrollo de soluciones financieras innovadoras y al crecimiento del mercado de capitales paraguayo.',
        ],
    ],
    [
        'name' => 'Yanina Monges Chávez',
        'role' => 'Directora Titular',
        'photo' => 'directorio/directora-titular-yanina-monges',
        'bio' => [
            'Directora titular especializada en gestión operativa, optimización de procesos e implementación de sistemas internos de información y proyectos de tecnología. Lidera iniciativas estratégicas orientadas a la eficiencia, innovación y transformación operativa de la organización.',
        ],
    ],
];

// Imágenes que son renders (llevan la leyenda "Imagen ilustrativa (render)"). Planos, masterplans y fotos aéreas quedan fuera.
const RENDERS = ['sol/*', 'ribera/*', 'cumbres/008', 'cumbres/044', 'cumbres/051', 'cumbres/052', 'paraqvaria/002', 'general/investor'];
const NOT_RENDERS = ['*/masterplan', 'sol/planta-*', 'sol/zona'];

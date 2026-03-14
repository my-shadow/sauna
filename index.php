<?php
$file = 'data.json';
$data = json_decode(file_exists($file) ? file_get_contents($file) : '{"settings":{},"bookings":[]}', true);
$settings = $data['settings'] ?? [];

function e($text) { return htmlspecialchars($text ?? ''); }

$business_name = e($settings['business_name']) ?: 'Водопад';
$business_desc = e($settings['business_desc']) ?: 'Чотири сауни, хамам, чанн, СПА та масаж — повне відновлення тіла й душі за один сеанс.';
$promo_text    = e($settings['promo_text']) ?: '🔥 АКЦІЯ: Безкоштовний трансфер при бронюванні!';
$form_title    = e($settings['form_title']) ?: 'Забронюйте сеанс';
$footer_text   = e($settings['footer_text']) ?: 'Готель-сауна Водопад — місце де відновлюється тіло і душа.';

$price_3h = e($settings['price_3h']) ?: '4500';
$price_4h = e($settings['price_4h']) ?: '5500';
$price_5h = e($settings['price_5h']) ?: '6250';
$price_6h = e($settings['price_6h']) ?: '6750';
$price_7h = e($settings['price_7h']) ?: '7250';
$price_8h = e($settings['price_8h']) ?: '7750';

$phone        = e($settings['phone']) ?: '096 001 6 001';
$address      = e($settings['address']) ?: 'Дачний масив «Видрові доли», готель-сауна Водопад';
$analytics_id = e($settings['analytics_id']) ?: '';

$phone_clean = preg_replace('/[^\d+]/', '', $phone);
if (substr($phone_clean, 0, 1) === '0') $phone_clean = '+38' . $phone_clean;

$photos = glob('sauna-photo/*.jpg');
usort($photos, function($a, $b) {
    preg_match('/photo_(\d+)/', $a, $ma);
    preg_match('/photo_(\d+)/', $b, $mb);
    return ($ma[1] ?? 0) - ($mb[1] ?? 0);
});
?>
<!DOCTYPE html>
<html lang="uk" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($settings['meta_title']) ?: $business_name . ' — Готель-Сауна у Хмельницькому' ?></title>
    <meta name="description" content="<?= e($settings['meta_desc']) ?: $business_desc ?>"/>
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1"/>
    <?php
        $meta_title = e($settings['meta_title']) ?: $business_name . ' — Готель-Сауна у Хмельницькому';
        $meta_desc  = e($settings['meta_desc'])  ?: $business_desc;
        $og_image   = e($settings['og_image'])   ?: '';
        $site_url   = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? '') . '/';
    ?>
    <!-- Open Graph -->
    <meta property="og:locale" content="uk_UA"/>
    <meta property="og:type" content="website"/>
    <meta property="og:site_name" content="<?= $business_name ?>"/>
    <meta property="og:url" content="<?= $site_url ?>"/>
    <meta property="og:title" content="<?= $meta_title ?>"/>
    <meta property="og:description" content="<?= $meta_desc ?>"/>
    <?php if($og_image): ?>
    <meta property="og:image" content="<?= $og_image ?>"/>
    <meta property="og:image:secure_url" content="<?= $og_image ?>"/>
    <meta property="og:image:width" content="1200"/>
    <meta property="og:image:height" content="630"/>
    <meta property="og:image:alt" content="<?= $business_name ?>"/>
    <meta property="og:image:type" content="image/jpeg"/>
    <?php endif; ?>
    <!-- Twitter / X Card -->
    <meta name="twitter:card" content="<?= $og_image ? 'summary_large_image' : 'summary' ?>"/>
    <meta name="twitter:title" content="<?= $meta_title ?>"/>
    <meta name="twitter:description" content="<?= $meta_desc ?>"/>
    <?php if($og_image): ?>
    <meta name="twitter:image" content="<?= $og_image ?>"/>
    <?php endif; ?>

    <?php if(!empty($analytics_id)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $analytics_id ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date()); gtag('config', '<?= $analytics_id ?>');
    </script>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&family=Playfair+Display:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'play':     ['Play', 'sans-serif'],
                        'playfair': ['"Playfair Display"', 'serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* ── Base ─────────────────────────────────────────────── */
        * { font-family: 'Play', sans-serif; }

        /* ── Scroll-triggered animations (NOT on hero) ────────── */
        .anim { opacity: 0; transition: opacity .75s ease, transform .75s ease; }
        .anim-up    { transform: translateY(40px); }
        .anim-left  { transform: translateX(-50px); }
        .anim-right { transform: translateX(50px); }
        .anim-scale { transform: scale(0.94); }
        .anim.visible { opacity: 1; transform: none; }
        .d1 { transition-delay: .10s; }
        .d2 { transition-delay: .20s; }
        .d3 { transition-delay: .30s; }
        .d4 { transition-delay: .40s; }
        .d5 { transition-delay: .50s; }
        .d6 { transition-delay: .60s; }

        /* ── Nav ──────────────────────────────────────────────── */
        #nav { transition: background .35s, border-color .35s; }
        #nav.solid { background: rgba(3,7,18,.97) !important; border-bottom-color: rgba(20,184,166,.2) !important; }

        /* ── Ticker ───────────────────────────────────────────── */
        @keyframes ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .ticker-inner { display: inline-block; white-space: nowrap; animation: ticker 30s linear infinite; }

        /* ── Gallery running line ─────────────────────────────── */
        @keyframes gallery-l { from { transform: translateX(0); }    to { transform: translateX(-50%); } }
        @keyframes gallery-r { from { transform: translateX(-50%); } to { transform: translateX(0); }    }
        .gallery-track   { display: flex; width: max-content; gap: 12px; }
        .gallery-track-l { animation: gallery-l 55s linear infinite; }
        .gallery-track-r { animation: gallery-r 55s linear infinite; }
        .gallery-strip:hover .gallery-track { animation-play-state: paused; }
        .gallery-item { flex-shrink: 0; overflow: hidden; cursor: pointer; }
        .gallery-item img { display: block; -webkit-user-drag: none; user-select: none; pointer-events: none; transition: transform .5s ease; }
        .gallery-item:hover img { transform: scale(1.06); }

        /* ── Lightbox ─────────────────────────────────────────── */
        #lb { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.95); z-index: 9999; }
        #lb.open { display: flex; align-items: center; justify-content: center; }
        #lb-img { max-height: 90vh; max-width: 90vw; object-fit: contain; border-radius: 4px; }
        .lb-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,.1); border: none; color: #fff; font-size: 1.4rem; padding: .65rem 1.1rem; cursor: pointer; border-radius: 4px; transition: background .2s; }
        .lb-btn:hover { background: rgba(255,255,255,.25); }
        #lb-prev { left: 1rem; }
        #lb-next { right: 1rem; }
        #lb-close { position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,.1); border: none; color: #fff; width: 2.5rem; height: 2.5rem; border-radius: 50%; font-size: 1rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .2s; }
        #lb-close:hover { background: rgba(220,38,38,.5); }
        #lb-counter { position: absolute; bottom: 1.25rem; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,.45); font-size: 1rem; letter-spacing: .1em; }

        /* ── Step connector ───────────────────────────────────── */
        .step-track { position: relative; }
        .step-track::before {
            content: '';
            position: absolute;
            top: 1.75rem;
            left: calc(50% + 2.5rem);
            width: calc(100% - 5rem);
            height: 1px;
            background: linear-gradient(to right, #0d9488 0%, #0d9488 50%, transparent 100%);
        }

        /* ── Price table row hover ────────────────────────────── */
        .price-row { transition: background .2s; }
        .price-row:hover { background: rgba(20,184,166,.06); }

        /* ── Zone photo overlay ───────────────────────────────── */
        .zone-img { transition: transform .6s ease; }
        .zone-wrap:hover .zone-img { transform: scale(1.04); }

        /* ── Alert banner ─────────────────────────────────────── */
        .alert-banner { transition: opacity .4s; }
    </style>
</head>
<body class="bg-gray-950 text-gray-200">

    <!-- ═══════════════════════════════════ SUCCESS ALERT ══ -->
    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert-banner fixed top-24 left-1/2 -translate-x-1/2 z-[200] flex items-center gap-3 bg-teal-700 text-white px-6 py-3.5 shadow-2xl w-11/12 max-w-md" style="border-left: 4px solid #99f6e4">
        <i class="fa-solid fa-circle-check text-teal-200 text-lg flex-shrink-0"></i>
        <span class="text-sm font-bold">Заявку прийнято! Ми зателефонуємо вам найближчим часом.</span>
        <button onclick="this.closest('.alert-banner').style.opacity=0;setTimeout(()=>this.closest('.alert-banner').remove(),400)" class="ml-auto text-teal-200 hover:text-white pl-4">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <?php endif; ?>

    <!-- ═══════════════════════════════════════════ NAV ══ -->
    <nav id="nav" class="fixed w-full z-50 border-b border-transparent">
        <div class="max-w-screen-xl mx-auto px-6 h-[72px] flex items-center justify-between gap-6">

            <!-- Logo -->
            <a href="#" class="flex items-center gap-3 flex-shrink-0">
                <span class="text-2xl leading-none select-none">💧</span>
                <div class="leading-none">
                    <span class="block text-base font-bold text-teal-300 uppercase tracking-[.18em]">ВОДОПАД</span>
                    <span class="block text-base text-gray-600 uppercase tracking-[.28em] mt-0.5">Готель · Сауна</span>
                </div>
            </a>

            <!-- Links -->
            <div class="hidden lg:flex items-center gap-7">
                <?php foreach ([['#experience','Послуги'],['#pricing','Ціни'],['#gallery','Галерея'],['#contacts','Контакти']] as [$href,$label]): ?>
                <a href="<?= $href ?>" class="text-base font-bold text-gray-500 hover:text-teal-300 uppercase tracking-[.15em] transition nav-link"><?= $label ?></a>
                <?php endforeach; ?>
            </div>

            <!-- Right -->
            <div class="flex items-center gap-4 flex-shrink-0">
                <a href="tel:<?= $phone_clean ?>" class="hidden md:flex items-center gap-2 text-gray-400 hover:text-white transition text-sm font-bold tracking-tight">
                    <i class="fa-solid fa-phone text-teal-500 text-xs"></i><?= $phone ?>
                </a>
                <a href="#booking" class="bg-amber-500 hover:bg-amber-400 text-gray-950 text-base font-bold uppercase tracking-[.15em] px-5 py-2.5 transition flex-shrink-0">
                    Забронювати
                </a>
            </div>

        </div>
    </nav>

    <!-- ═══════════════════════════════════════ HERO ══════
         STATIC — zero animations here
    ════════════════════════════════════════════════════ -->
    <header class="relative min-h-screen flex flex-col lg:flex-row overflow-hidden">

        <!-- Left: text panel -->
        <div class="relative z-10 w-full lg:w-[46%] bg-gray-950 flex flex-col justify-center
                    px-8 md:px-14 xl:px-20 pt-28 pb-16 lg:pt-0 lg:pb-0 flex-shrink-0">

            <!-- Eyebrow -->
            <div class="flex items-center gap-3 mb-10">
                <div class="w-10 h-px bg-teal-600"></div>
                <span class="text-base font-bold text-teal-500 uppercase tracking-[.25em]">Хмельницький · Відрові Доли</span>
            </div>

            <!-- Headline -->
            <h1 class="font-playfair leading-none text-white mb-8" style="font-size: clamp(5rem, 12vw, 9rem); line-height: .9;">
                Водо<span class="text-teal-300">пад</span>
            </h1>

            <!-- Desc -->
            <p class="text-gray-400 text-base leading-relaxed mb-10 max-w-sm">
                <?= $business_desc ?>
            </p>

            <!-- CTA row -->
            <div class="flex flex-wrap gap-4 mb-12">
                <a href="#booking" class="bg-amber-500 hover:bg-amber-400 text-gray-950 px-8 py-4 text-sm font-bold uppercase tracking-[.12em] transition">
                    Забронювати сеанс
                </a>
                <a href="#experience" class="border border-gray-700 hover:border-teal-700 text-gray-400 hover:text-teal-300 px-8 py-4 text-sm font-bold uppercase tracking-[.12em] transition">
                    Дізнатися більше
                </a>
            </div>

            <!-- Phone -->
            <div class="flex items-center gap-3">
                <div class="w-8 h-px bg-gray-800"></div>
                <a href="tel:<?= $phone_clean ?>" class="text-sm text-gray-500 font-bold hover:text-white transition tracking-widest">
                    <?= $phone ?>
                </a>
            </div>

            <!-- Bottom decoration -->
            <div class="hidden lg:block absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-teal-800/40 to-transparent"></div>
        </div>

        <!-- Right: photo -->
        <div class="relative flex-1 min-h-[55vw] lg:min-h-screen overflow-hidden">
            <img src="sauna-photo/photo_1_2026-03-14_13-29-47.jpg"
                 alt="Готель-сауна Водопад"
                 class="absolute inset-0 w-full h-full object-cover">
            <!-- Overlap gradient from left -->
            <div class="absolute inset-0 bg-gradient-to-r from-gray-950 via-gray-950/5 to-transparent pointer-events-none"></div>
            <!-- Mobile bottom gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-gray-950/60 to-transparent lg:hidden pointer-events-none"></div>

            <!-- Floating stat card -->
            <div class="absolute bottom-8 right-8 hidden md:block bg-gray-950/85 backdrop-blur-sm border border-gray-800/80 p-6 min-w-[170px]">
                <div class="font-playfair text-5xl text-amber-400 leading-none mb-1">4</div>
                <div class="text-base text-gray-500 uppercase tracking-[.2em] mb-5">типи саун</div>
                <div class="w-full h-px bg-gray-800 mb-5"></div>
                <div class="font-playfair text-5xl text-amber-400 leading-none mb-1">20<span class="text-3xl">+</span></div>
                <div class="text-base text-gray-500 uppercase tracking-[.2em]">років досвіду</div>
            </div>
        </div>

        <!-- Vertical "scroll" hint (desktop) -->
        <div class="hidden lg:flex absolute bottom-8 left-[46%] -translate-x-1/2 flex-col items-center gap-2 z-20">
            <div class="w-px h-12 bg-gradient-to-b from-transparent to-teal-700"></div>
            <span class="text-base text-gray-600 uppercase tracking-[.3em] rotate-0">scroll</span>
        </div>

    </header>

    <!-- ═══════════════════════════════════════ TICKER ══ -->
    <div class="bg-amber-500 border-y border-amber-600 py-3 overflow-hidden" aria-hidden="true">
        <div class="ticker-inner text-gray-950 font-bold uppercase text-xs tracking-[.18em]">
            <?php for($i=0;$i<6;$i++): ?>
            <?= $promo_text ?> &nbsp;&nbsp;&nbsp;✦&nbsp;&nbsp;&nbsp;
            <?php endfor; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════ STATS ══ -->
    <section class="py-20 bg-gray-900 border-b border-gray-800">
        <div class="max-w-screen-xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-y-10 gap-x-6 text-center">
                <?php
                $stats = [
                    ['4',    'Типи саун'],
                    ['256',  'Форсунок у СПА'],
                    ['8',    'Місць у СПА'],
                    ['12',   'Місць відпочинку'],
                    ['20+',  'Років досвіду'],
                ];
                foreach ($stats as $i => [$num, $label]):
                ?>
                <div class="anim anim-up d<?= $i+1 ?>">
                    <div class="font-playfair text-5xl md:text-6xl text-teal-300 leading-none mb-3"><?= $num ?></div>
                    <div class="w-6 h-px bg-teal-700 mx-auto mb-3"></div>
                    <div class="text-base text-gray-500 uppercase tracking-[.18em]"><?= $label ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════ EXPERIENCE ZONES ══ -->
    <section id="experience" class="py-28 bg-gray-950 border-b border-gray-900">
        <div class="max-w-screen-xl mx-auto px-6">

            <!-- Section header -->
            <div class="mb-20">
                <div class="flex items-center gap-4 mb-5 anim anim-up">
                    <div class="w-10 h-px bg-teal-600"></div>
                    <span class="text-base font-bold text-teal-500 uppercase tracking-[.25em]">Що вас чекає</span>
                </div>
                <h2 class="font-playfair text-5xl md:text-6xl text-white anim anim-up d1">
                    Зони відпочинку
                </h2>
            </div>

            <!-- Zone 1 ─ Жар ─────────────────────────── -->
            <div class="grid lg:grid-cols-2 gap-12 xl:gap-20 items-center mb-28">
                <div class="anim anim-left order-2 lg:order-1">
                    <div class="font-playfair text-[8rem] leading-none text-gray-800/60 mb-2 select-none">01</div>
                    <h3 class="font-playfair text-4xl text-white mb-5">Зона Жару</h3>
                    <p class="text-gray-400 leading-relaxed mb-8">
                        Чотири різних сауни для будь-якого настрою та потреби тіла.
                        Від класичної дров'яної парної до м'якої інфрачервоної —
                        кожен знайде свій ідеальний жар.
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <?php foreach ([
                            ['Волога парна', 'Традиційна на дровах із природним живим жаром'],
                            ['Хамам', 'З ароматичними оліями — очищення тіла й розуму'],
                            ['Соляна кімната', 'Галотерапія для здоров\'я дихальних шляхів'],
                            ['Інфрачервона', 'М\'яке глибоке прогрівання на клітинному рівні'],
                        ] as [$name, $desc]): ?>
                        <div class="border-l-2 border-teal-700 pl-4 py-1">
                            <div class="text-sm font-bold text-white mb-1"><?= $name ?></div>
                            <div class="text-xs text-gray-500 leading-snug"><?= $desc ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="zone-wrap overflow-hidden anim anim-right order-1 lg:order-2">
                    <img src="sauna-photo/photo_3_2026-03-14_13-29-47.jpg"
                         alt="Сауна Водопад — Зона жару"
                         class="zone-img w-full h-[420px] object-cover">
                </div>
            </div>

            <!-- Zone 2 ─ Вода ────────────────────────── -->
            <div class="grid lg:grid-cols-2 gap-12 xl:gap-20 items-center mb-28">
                <div class="zone-wrap overflow-hidden anim anim-left">
                    <img src="sauna-photo/photo_7_2026-03-14_13-29-47.jpg"
                         alt="Сауна Водопад — Водні процедури"
                         class="zone-img w-full h-[420px] object-cover">
                </div>
                <div class="anim anim-right">
                    <div class="font-playfair text-[8rem] leading-none text-gray-800/60 mb-2 select-none">02</div>
                    <h3 class="font-playfair text-4xl text-white mb-5">Водні Процедури</h3>
                    <p class="text-gray-400 leading-relaxed mb-8">
                        Контрастні процедури — найефективніший спосіб загартувати організм
                        і розбудити кровообіг. Від теплого гідромасажу до бадьорого
                        крижаного занурення — все в одному комплексі.
                    </p>
                    <div class="grid grid-cols-2 gap-4">
                        <?php foreach ([
                            ['Теплий басейн', 'Шийний масаж, гейзер і протитечія для відновлення'],
                            ['Чанн', 'З гілочками ялинки, сосни та можевельника'],
                            ['Крижаний басейн', 'Загартування після парних процедур'],
                            ['Душ Шарко & Відро', 'Контрастний душ і водоспад для тонусу'],
                        ] as [$name, $desc]): ?>
                        <div class="border-l-2 border-teal-700 pl-4 py-1">
                            <div class="text-sm font-bold text-white mb-1"><?= $name ?></div>
                            <div class="text-xs text-gray-500 leading-snug"><?= $desc ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Zone 3 ─ СПА ─────────────────────────── -->
            <div class="grid lg:grid-cols-2 gap-12 xl:gap-20 items-center mb-28">
                <div class="anim anim-left order-2 lg:order-1">
                    <div class="font-playfair text-[8rem] leading-none text-gray-800/60 mb-2 select-none">03</div>
                    <h3 class="font-playfair text-4xl text-white mb-5">СПА & Масаж</h3>
                    <p class="text-gray-400 leading-relaxed mb-8">
                        Гідромасажний СПА-басейн на 8 осіб з 256 форсунками та гейзером
                        і масаж від майстра з понад 20-річним досвідом — кожному
                        гостю індивідуально 20–30 хвилин. Не процедура, а ритуал.
                    </p>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start gap-4 bg-gray-900/60 border border-gray-800 p-5">
                            <i class="fa-solid fa-spa text-teal-400 text-xl flex-shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-sm font-bold text-white mb-1">СПА-басейн на 8 осіб</div>
                                <div class="text-xs text-gray-500">256 форсунок і підводний гейзер для повноцінного гідромасажу</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-4 bg-gray-900/60 border border-gray-800 p-5">
                            <i class="fa-solid fa-hand-holding-heart text-teal-400 text-xl flex-shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-sm font-bold text-white mb-1">Індивідуальний масаж</div>
                                <div class="text-xs text-gray-500">20–30 хв кожному гостю, майстер із 20+ роками практики</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="zone-wrap overflow-hidden anim anim-right order-1 lg:order-2">
                    <img src="sauna-photo/photo_12_2026-03-14_13-29-47.jpg"
                         alt="СПА Водопад"
                         class="zone-img w-full h-[420px] object-cover">
                </div>
            </div>

            <!-- Zone 4 ─ Відпочинок ────────────────────── -->
            <div class="grid lg:grid-cols-2 gap-12 xl:gap-20 items-center">
                <div class="zone-wrap overflow-hidden anim anim-left">
                    <img src="sauna-photo/photo_20_2026-03-14_13-29-47.jpg"
                         alt="Кімната відпочинку Водопад"
                         class="zone-img w-full h-[420px] object-cover">
                </div>
                <div class="anim anim-right">
                    <div class="font-playfair text-[8rem] leading-none text-gray-800/60 mb-2 select-none">04</div>
                    <h3 class="font-playfair text-4xl text-white mb-5">Зона Релаксації</h3>
                    <p class="text-gray-400 leading-relaxed mb-8">
                        Затишна кімната відпочинку на 12 осіб з живим каміном та
                        декоративним акваріумом — ідеальне місце щоб відновити
                        сили між процедурами, поспілкуватись і насолодитись тишею.
                    </p>
                    <div class="grid grid-cols-3 gap-3">
                        <?php foreach ([
                            ['fa-fire','Камін'],
                            ['fa-fish','Акваріум'],
                            ['fa-users','До 12 осіб'],
                        ] as [$icon, $label]): ?>
                        <div class="text-center bg-gray-900/60 border border-gray-800 p-4">
                            <i class="fa-solid <?= $icon ?> text-teal-400 text-xl mb-2 block"></i>
                            <div class="text-xs text-gray-400 font-bold"><?= $label ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ══════════════════════════════════════ WELLNESS ══ -->
    <section id="wellness" class="py-28 bg-gray-900 border-b border-gray-800">
        <div class="max-w-screen-xl mx-auto px-6">

            <div class="grid lg:grid-cols-2 gap-16 xl:gap-24 items-start">

                <!-- Left: header + quote -->
                <div class="lg:sticky lg:top-28">
                    <div class="flex items-center gap-4 mb-5 anim anim-up">
                        <div class="w-10 h-px bg-teal-600"></div>
                        <span class="text-base font-bold text-teal-500 uppercase tracking-[.25em]">Для вашого здоров'я</span>
                    </div>
                    <h2 class="font-playfair text-5xl md:text-6xl text-white mb-8 anim anim-up d1">
                        Що дає<br><em>сауна</em>
                    </h2>
                    <p class="text-gray-400 leading-relaxed mb-10 anim anim-up d2">
                        Регулярні відвідування лазні й сауни — це не просто приємне дозвілля,
                        а повноцінна оздоровча практика, що має наукове підтвердження.
                    </p>
                    <blockquote class="border-l-2 border-amber-500 pl-6 py-2 anim anim-up d3">
                        <p class="font-playfair text-xl text-amber-300/80 italic leading-snug">
                            "Дайте мені жар і я вилікую будь-яку хворобу."
                        </p>
                        <footer class="text-xs text-gray-600 mt-3 uppercase tracking-widest">— Парацельс</footer>
                    </blockquote>
                </div>

                <!-- Right: benefit cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <?php
                    $benefits = [
                        ['fa-heart-pulse',      'Кровообіг',      'Судини розширюються, кров активно насичується киснем, нормалізується тиск.'],
                        ['fa-lungs',            'Дихання',        'Соляна кімната очищує дихальні шляхи, полегшує алергічні прояви.'],
                        ['fa-person-running',   'М\'язи',          'Тепло знімає м\'язову напругу, прискорює відновлення після фізичних навантажень.'],
                        ['fa-brain',            'Стрес',          'Виробляються ендорфіни; контрастні процедури «скидають» нервову напругу.'],
                        ['fa-moon',             'Сон',            'Глибока релаксація нервової системи забезпечує міцний і відновлюючий сон.'],
                        ['fa-droplet',          'Детокс',         'Через активне потовиділення організм виводить токсини та важкі метали.'],
                    ];
                    foreach ($benefits as $i => [$icon, $title, $text]): ?>
                    <div class="flex gap-4 bg-gray-800/50 border border-gray-700/60 p-5 anim anim-up d<?= ($i%3)+1 ?>">
                        <div class="w-10 h-10 rounded-full bg-teal-900/60 border border-teal-700/40 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid <?= $icon ?> text-teal-400 text-sm"></i>
                        </div>
                        <div>
                            <div class="font-bold text-white text-sm mb-1"><?= $title ?></div>
                            <div class="text-xs text-gray-500 leading-relaxed"><?= $text ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- ════════════════════════════════ HOW IT WORKS ══ -->
    <section id="howto" class="py-28 bg-gray-950 border-b border-gray-900 overflow-hidden">
        <div class="max-w-screen-xl mx-auto px-6">

            <div class="text-center mb-16">
                <div class="flex items-center justify-center gap-4 mb-5 anim anim-up">
                    <div class="w-10 h-px bg-teal-600"></div>
                    <span class="text-base font-bold text-teal-500 uppercase tracking-[.25em]">Просто і зручно</span>
                    <div class="w-10 h-px bg-teal-600"></div>
                </div>
                <h2 class="font-playfair text-5xl md:text-6xl text-white anim anim-up d1">Як це працює</h2>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 relative">
                <!-- connector line desktop -->
                <div class="hidden lg:block absolute top-[1.75rem] left-[calc(12.5%+1.75rem)] right-[calc(12.5%+1.75rem)] h-px bg-gradient-to-r from-teal-800 via-teal-700 to-transparent pointer-events-none"></div>

                <?php
                $steps = [
                    ['1', 'fa-calendar-check', 'Забронюйте',    'Залиште заявку на сайті або зателефонуйте — оберемо зручний час разом.'],
                    ['2', 'fa-location-dot',   'Приїжджайте',   'Вас зустрінуть, проведуть екскурсію комплексом і допоможуть облаштуватись.'],
                    ['3', 'fa-water-ladder',   'Насолоджуйтесь','Вільно переходьте між зонами у своєму темпі, без розкладу й поспіху.'],
                    ['4', 'fa-star',           'Відновіться',   'Виходьте оновленим, сповненим сил — тіло і розум скажуть дякую.'],
                ];
                foreach ($steps as $i => [$num, $icon, $title, $text]): ?>
                <div class="text-center relative anim anim-up d<?= $i+1 ?>">
                    <div class="w-14 h-14 rounded-full bg-teal-900 border-2 border-teal-600 flex items-center justify-center mx-auto mb-5 relative z-10">
                        <i class="fa-solid <?= $icon ?> text-teal-300 text-lg"></i>
                    </div>
                    <div class="font-playfair text-xs text-teal-700 mb-2 tracking-widest"><?= str_pad($num,2,'0',STR_PAD_LEFT) ?></div>
                    <h4 class="font-bold text-white text-sm mb-2 uppercase tracking-[.08em]"><?= $title ?></h4>
                    <p class="text-xs text-gray-500 leading-relaxed"><?= $text ?></p>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- ═══════════════════════════════════════ PRICING ══ -->
    <section id="pricing" class="py-28 bg-gray-900 border-b border-gray-800">
        <div class="max-w-screen-xl mx-auto px-6">

            <div class="grid lg:grid-cols-[1fr_1.4fr] gap-16 xl:gap-24 items-start">

                <!-- Left: header -->
                <div class="lg:sticky lg:top-28">
                    <div class="flex items-center gap-4 mb-5 anim anim-up">
                        <div class="w-10 h-px bg-amber-500"></div>
                        <span class="text-base font-bold text-amber-500 uppercase tracking-[.25em]">Прозоро і чесно</span>
                    </div>
                    <h2 class="font-playfair text-5xl md:text-6xl text-white mb-8 anim anim-up d1">Ціни</h2>
                    <p class="text-gray-400 leading-relaxed mb-8 anim anim-up d2">
                        Оренда всього комплексу для вашої компанії.
                        У вартість включено всі зони без обмежень,
                        плюс індивідуальний масаж кожному гостю.
                    </p>
                    <div class="bg-gray-800/50 border border-gray-700/60 p-6 anim anim-up d3">
                        <div class="flex items-start gap-3 mb-4">
                            <i class="fa-solid fa-circle-check text-teal-500 mt-0.5"></i>
                            <span class="text-sm text-gray-300">Всі 4 сауни без обмежень</span>
                        </div>
                        <div class="flex items-start gap-3 mb-4">
                            <i class="fa-solid fa-circle-check text-teal-500 mt-0.5"></i>
                            <span class="text-sm text-gray-300">СПА, басейни, душі, чанн</span>
                        </div>
                        <div class="flex items-start gap-3 mb-4">
                            <i class="fa-solid fa-circle-check text-teal-500 mt-0.5"></i>
                            <span class="text-sm text-gray-300">Масаж 20–30 хв кожному гостю</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-check text-teal-500 mt-0.5"></i>
                            <span class="text-sm text-gray-300">Кімната відпочинку з каміном</span>
                        </div>
                    </div>
                </div>

                <!-- Right: price list -->
                <div class="anim anim-right">
                    <?php
                    $prices = [
                        ['3', $price_3h],
                        ['4', $price_4h],
                        ['5', $price_5h],
                        ['6', $price_6h],
                        ['7', $price_7h],
                        ['8', $price_8h],
                    ];
                    foreach ($prices as $i => [$hours, $price]):
                        $popular = ($i === 2);
                    ?>
                    <div class="price-row flex items-center gap-6 px-6 py-5 border-b border-gray-800/80 <?= $popular ? 'bg-teal-900/20 border-l-2 border-l-teal-600' : '' ?>">
                        <div class="flex-shrink-0 w-12 text-right">
                            <span class="font-playfair text-4xl <?= $popular ? 'text-teal-300' : 'text-gray-600' ?>"><?= $hours ?></span>
                        </div>
                        <div class="text-xs text-gray-600 uppercase tracking-[.15em] flex-shrink-0 w-14">
                            <?= $hours == 1 ? 'година' : ($hours < 5 ? 'години' : 'годин') ?>
                        </div>
                        <div class="flex-1 h-px bg-gray-800/60 mx-2 hidden sm:block"></div>
                        <?php if($popular): ?>
                        <div class="flex-shrink-0 mr-2">
                            <span class="bg-teal-700 text-teal-200 text-base font-bold uppercase tracking-[.1em] px-2.5 py-1">Популярний</span>
                        </div>
                        <?php endif; ?>
                        <div class="flex-shrink-0 text-right">
                            <span class="font-playfair text-3xl <?= $popular ? 'text-amber-400' : 'text-white' ?>"><?= number_format((int)$price, 0, '.', '&thinsp;') ?></span>
                            <span class="text-xs text-gray-600 ml-1">грн</span>
                        </div>
                        <a href="#booking"
                           data-pick-hours="<?= $hours ?>"
                           class="pick-plan flex-shrink-0 <?= $popular ? 'bg-amber-500 hover:bg-amber-400 text-gray-950' : 'border border-gray-700 hover:border-teal-700 text-gray-400 hover:text-teal-300' ?> text-base font-bold uppercase tracking-[.1em] px-4 py-2 transition">
                            Обрати
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════ GALLERY ══ -->
    <section id="gallery" class="py-28 bg-gray-950 border-b border-gray-900">
        <div class="max-w-screen-xl mx-auto px-6 mb-10">
            <div class="flex items-end justify-between">
                <div>
                    <div class="flex items-center gap-4 mb-5 anim anim-up">
                        <div class="w-10 h-px bg-teal-600"></div>
                        <span class="text-base font-bold text-teal-500 uppercase tracking-[.25em]">Наш комплекс</span>
                    </div>
                    <h2 class="font-playfair text-5xl md:text-6xl text-white anim anim-up d1">Галерея</h2>
                </div>
                <span class="text-base text-gray-600 uppercase tracking-[.18em] hidden md:flex items-center gap-2 mb-2 anim anim-up">
                    <i class="fa-solid fa-hand-pointer"></i>Клікайте для перегляду
                </span>
            </div>
        </div>

        <!-- Row 1 → left -->
        <div class="gallery-strip overflow-hidden mb-3">
            <div class="gallery-track gallery-track-l">
                <?php foreach ([$photos, $photos] as $set): foreach ($set as $i => $photo): ?>
                <div class="gallery-item" data-index="<?= $i ?>">
                    <img src="<?= e($photo) ?>" alt="Фото <?= $i+1 ?>" class="h-[260px] w-auto">
                </div>
                <?php endforeach; endforeach; ?>
            </div>
        </div>

        <!-- Row 2 → right -->
        <div class="gallery-strip overflow-hidden">
            <div class="gallery-track gallery-track-r">
                <?php
                $photos_rev = array_reverse($photos);
                foreach ([$photos_rev, $photos_rev] as $set): foreach ($set as $i => $photo):
                    $orig_idx = array_search($photo, $photos);
                ?>
                <div class="gallery-item" data-index="<?= $orig_idx ?>">
                    <img src="<?= e($photo) ?>" alt="Фото <?= $orig_idx+1 ?>" class="h-[260px] w-auto">
                </div>
                <?php endforeach; endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ════════════════════════════════════ LIGHTBOX ══ -->
    <div id="lb" role="dialog" aria-modal="true">
        <button id="lb-close" class="lb-close" aria-label="Закрити"><i class="fa-solid fa-xmark"></i></button>
        <button class="lb-btn" id="lb-prev" aria-label="Попереднє"><i class="fa-solid fa-chevron-left"></i></button>
        <img id="lb-img" src="" alt="Фото">
        <button class="lb-btn" id="lb-next" aria-label="Наступне"><i class="fa-solid fa-chevron-right"></i></button>
        <div id="lb-counter"></div>
    </div>

    <!-- ══════════════════════════════════════ BOOKING ══ -->
    <section id="booking" class="relative py-28 bg-gray-900 border-b border-gray-800 overflow-hidden">
        <!-- Decorative bg text -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none overflow-hidden" aria-hidden="true">
            <span class="font-playfair text-[18vw] text-gray-800/20 whitespace-nowrap leading-none">Водопад</span>
        </div>

        <div class="relative max-w-screen-xl mx-auto px-6">
            <div class="max-w-2xl mx-auto text-center">
                <div class="flex items-center justify-center gap-4 mb-5 anim anim-up">
                    <div class="w-10 h-px bg-amber-500"></div>
                    <span class="text-base font-bold text-amber-500 uppercase tracking-[.25em]">Ми вас чекаємо</span>
                    <div class="w-10 h-px bg-amber-500"></div>
                </div>
                <h2 class="font-playfair text-5xl md:text-6xl text-white mb-5 anim anim-up d1"><?= $form_title ?></h2>
                <p class="text-gray-400 mb-12 anim anim-up d2">Залиште заявку — ми зателефонуємо і підберемо зручний час для вашої компанії.</p>

                <form action="submit.php" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4 anim anim-up d3">
                    <!-- Name -->
                    <input type="text" name="guest_name" required placeholder="Ваше ім'я"
                           class="bg-gray-800 border border-gray-700 text-white placeholder-gray-600 px-5 py-4 outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition">
                    <!-- Phone -->
                    <input type="tel" id="phone-input" name="guest_phone" required placeholder="+38 (0__) ___-__-__"
                           class="bg-gray-800 border border-gray-700 text-white placeholder-gray-600 px-5 py-4 outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition">
                    <!-- Hours select -->
                    <div class="relative">
                        <select id="hours-select" name="guest_hours" required
                                class="w-full appearance-none bg-gray-800 border border-gray-700 text-white px-5 py-4 outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition cursor-pointer">
                            <option value="" disabled selected>Кількість годин</option>
                            <?php foreach ([
                                ['3', $price_3h],
                                ['4', $price_4h],
                                ['5', $price_5h],
                                ['6', $price_6h],
                                ['7', $price_7h],
                                ['8', $price_8h],
                            ] as [$h, $p]): ?>
                            <option value="<?= $h ?>"><?= $h ?> год — <?= number_format((int)$p, 0, '.', ' ') ?> грн</option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                    </div>
                    <!-- Booking date -->
                    <input type="date" name="guest_date"
                           min="<?= date('Y-m-d') ?>"
                           class="bg-gray-800 border border-gray-700 text-white px-5 py-4 outline-none focus:border-teal-600 focus:ring-1 focus:ring-teal-600 transition [color-scheme:dark]">
                    <!-- Submit — full width -->
                    <div class="sm:col-span-2">
                        <button type="submit"
                                class="w-full bg-amber-500 hover:bg-amber-400 text-gray-950 py-4 font-bold uppercase tracking-[.12em] transition">
                            Надіслати заявку
                        </button>
                    </div>
                </form>
                <p class="text-base mt-5 text-gray-600 uppercase tracking-[.15em] anim anim-up d4">
                    Передзвонимо протягом 5 хвилин
                </p>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════ CONTACTS ══ -->
    <section id="contacts" class="py-28 bg-gray-950">
        <div class="max-w-screen-xl mx-auto px-6">

            <div class="grid lg:grid-cols-2 gap-16 xl:gap-24">

                <!-- Info -->
                <div>
                    <div class="flex items-center gap-4 mb-5 anim anim-up">
                        <div class="w-10 h-px bg-teal-600"></div>
                        <span class="text-base font-bold text-teal-500 uppercase tracking-[.25em]">Як нас знайти</span>
                    </div>
                    <h2 class="font-playfair text-5xl text-white mb-12 anim anim-up d1">Контакти</h2>

                    <div class="space-y-8">
                        <div class="flex items-start gap-5 anim anim-up d2">
                            <div class="w-10 h-10 bg-teal-900/50 border border-teal-700/40 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-map-pin text-teal-400 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-base font-bold text-gray-600 uppercase tracking-[.2em] mb-2">Адреса</div>
                                <p class="text-gray-200 leading-snug"><?= $address ?></p>
                            </div>
                        </div>

                        <div class="flex items-start gap-5 anim anim-up d3">
                            <div class="w-10 h-10 bg-teal-900/50 border border-teal-700/40 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-phone text-teal-400 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-base font-bold text-gray-600 uppercase tracking-[.2em] mb-2">Телефон</div>
                                <a href="tel:<?= $phone_clean ?>" class="font-bold text-2xl text-white hover:text-teal-300 transition tracking-tight">
                                    <?= $phone ?>
                                </a>
                                <div class="flex items-center gap-4 mt-4">
                                    <a href="viber://chat?number=<?= $phone_clean ?>" class="text-purple-400 hover:text-purple-300 text-2xl transition" title="Viber">
                                        <i class="fa-brands fa-viber"></i>
                                    </a>
                                    <a href="https://wa.me/<?= $phone_clean ?>" class="text-green-400 hover:text-green-300 text-2xl transition" title="WhatsApp">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    <a href="https://t.me/+<?= $phone_clean ?>" class="text-blue-400 hover:text-blue-300 text-2xl transition" title="Telegram">
                                        <i class="fa-brands fa-telegram"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-5 anim anim-up d4">
                            <div class="w-10 h-10 bg-teal-900/50 border border-teal-700/40 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-clock text-teal-400 text-sm"></i>
                            </div>
                            <div>
                                <div class="text-base font-bold text-gray-600 uppercase tracking-[.2em] mb-2">Графік роботи</div>
                                <p class="text-gray-200 font-bold">Цілодобово, без вихідних</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map -->
                <div class="anim anim-right">
                    <div class="relative h-full min-h-[380px] border border-gray-800 overflow-hidden">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2604.0!2d26.95!3d49.42!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDnCsDI1JzEyLjAiTiAyNsKwNTcnMDAuMCJF!5e0!3m2!1suk!2sua!4v1700000000000!5m2!1suk!2sua"
                            width="100%" height="100%"
                            style="border:0; position:absolute; inset:0; filter:invert(90%) hue-rotate(180deg) saturate(0.8);"
                            allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════ FOOTER ══ -->
    <footer class="bg-gray-950 border-t border-gray-900 py-16">
        <div class="max-w-screen-xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-8 mb-12">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <span class="text-3xl">💧</span>
                    <div>
                        <div class="text-lg font-bold text-teal-300 uppercase tracking-[.18em]">ВОДОПАД</div>
                        <div class="text-base text-gray-600 uppercase tracking-[.3em]">Готель · Сауна</div>
                    </div>
                </div>

                <!-- Nav links -->
                <div class="flex flex-wrap gap-x-8 gap-y-3">
                    <?php foreach ([['#experience','Послуги'],['#pricing','Ціни'],['#gallery','Галерея'],['#booking','Бронювання'],['#contacts','Контакти']] as [$href,$label]): ?>
                    <a href="<?= $href ?>" class="text-base font-bold text-gray-600 hover:text-teal-400 uppercase tracking-[.15em] transition"><?= $label ?></a>
                    <?php endforeach; ?>
                </div>

                <!-- Phone -->
                <a href="tel:<?= $phone_clean ?>" class="text-gray-400 hover:text-white font-bold transition tracking-tight hidden md:block">
                    <?= $phone ?>
                </a>
            </div>

            <div class="h-px bg-gray-900 mb-8"></div>

            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-xs text-gray-600 italic"><?= $footer_text ?></p>
                <p class="text-base text-gray-700 uppercase tracking-[.2em]">&copy; <?= date('Y') ?> Всі права захищені</p>
            </div>
        </div>
    </footer>

    <!-- ══════════════════════════════════════ SCRIPTS ══ -->
    <script src="https://unpkg.com/imask"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {

        /* ── Phone mask ──────────────────────────────────── */
        const phoneInput = document.getElementById('phone-input');
        if (phoneInput) IMask(phoneInput, { mask: '+{38} (000) 000-00-00' });

        /* ── Smooth scroll ──────────────────────────────── */
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                const target = document.querySelector(a.getAttribute('href'));
                if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
            });
        });

        /* ── Pricing → pre-select hours in form ─────────── */
        const hoursSelect = document.getElementById('hours-select');
        document.querySelectorAll('.pick-plan').forEach(btn => {
            btn.addEventListener('click', () => {
                const h = btn.dataset.pickHours;
                if (hoursSelect) hoursSelect.value = h;
            });
        });

        /* ── Nav: transparent → solid on scroll ─────────── */
        const nav = document.getElementById('nav');
        const onScroll = () => nav.classList.toggle('solid', window.scrollY > 60);
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        /* ── Active nav link on scroll ───────────────────── */
        const navLinks = document.querySelectorAll('.nav-link');
        const sections = [...navLinks]
            .map(a => document.querySelector(a.getAttribute('href')))
            .filter(Boolean);

        const setActive = id => {
            navLinks.forEach(a => {
                const isActive = a.getAttribute('href') === '#' + id;
                a.classList.toggle('text-teal-300', isActive);
                a.classList.toggle('text-gray-500', !isActive);
            });
        };

        const sectionObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) setActive(entry.target.id);
            });
        }, { rootMargin: '-40% 0px -55% 0px', threshold: 0 });

        sections.forEach(s => sectionObserver.observe(s));

        /* ── Intersection Observer for fade animations ───── */
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.anim').forEach(el => observer.observe(el));


        /* ── Lightbox ────────────────────────────────────── */
        const photos = <?= json_encode(array_values($photos)) ?>;
        const lb = document.getElementById('lb');
        const lbImg = document.getElementById('lb-img');
        const lbCounter = document.getElementById('lb-counter');
        let current = 0;

        const open  = idx => {
            current = idx;
            lbImg.src = photos[current];
            lbCounter.textContent = (current + 1) + ' / ' + photos.length;
            lb.classList.add('open');
            document.body.style.overflow = 'hidden';
        };
        const close = () => { lb.classList.remove('open'); document.body.style.overflow = ''; };
        const prev  = () => { current = (current - 1 + photos.length) % photos.length; open(current); };
        const next  = () => { current = (current + 1) % photos.length; open(current); };

        document.querySelectorAll('.gallery-item').forEach(item => {
            item.addEventListener('click', () => open(+item.dataset.index));
        });

        document.getElementById('lb-close').addEventListener('click', close);
        document.getElementById('lb-prev').addEventListener('click', prev);
        document.getElementById('lb-next').addEventListener('click', next);
        lb.addEventListener('click', e => { if (e.target === lb || e.target === lbImg) {} }); // click on backdrop
        lb.addEventListener('click', e => { if (e.target === lb) close(); });
        document.addEventListener('keydown', e => {
            if (!lb.classList.contains('open')) return;
            if (e.key === 'Escape') close();
            if (e.key === 'ArrowLeft') prev();
            if (e.key === 'ArrowRight') next();
        });

        /* ── Touch swipe in lightbox ─────────────────────── */
        let lbTouchX = 0;
        lbImg.addEventListener('touchstart', e => { lbTouchX = e.touches[0].clientX; }, { passive: true });
        lbImg.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - lbTouchX;
            if (dx > 50) prev();
            else if (dx < -50) next();
        });

    });
    </script>
</body>
</html>

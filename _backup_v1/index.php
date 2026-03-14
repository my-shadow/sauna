<?php
$file = 'data.json';
$data = json_decode(file_exists($file) ? file_get_contents($file) : '{"settings":{},"bookings":[]}', true);
$settings = $data['settings'] ?? [];

function e($text) { return htmlspecialchars($text ?? ''); }

$business_name  = e($settings['business_name']) ?: 'Водопад';
$business_desc  = e($settings['business_desc']) ?: 'Справжній відпочинок для тіла і душі. Чотири сауни, СПА на 8 осіб, теплий басейн, масаж та кімната відпочинку з каміном.';
$promo_text     = e($settings['promo_text']) ?: '🔥 АКЦІЯ: Безкоштовний трансфер при бронюванні! 🔥';
$form_title     = e($settings['form_title']) ?: 'Забронюйте сеанс';
$footer_text    = e($settings['footer_text']) ?: 'Готель-сауна Водопад — місце де відновлюється тіло і душа.';

$price_3h = e($settings['price_3h']) ?: '4500';
$price_4h = e($settings['price_4h']) ?: '5500';
$price_5h = e($settings['price_5h']) ?: '6250';
$price_6h = e($settings['price_6h']) ?: '6750';
$price_7h = e($settings['price_7h']) ?: '7250';
$price_8h = e($settings['price_8h']) ?: '7750';

$phone   = e($settings['phone']) ?: '096 001 6 001';
$address = e($settings['address']) ?: 'Дачний масив «Видрові доли», готель-сауна Водопад';
$analytics_id = e($settings['analytics_id']) ?: '';

$phone_clean = preg_replace('/[^\d+]/', '', $phone);
if (substr($phone_clean, 0, 1) === '0') $phone_clean = '+38' . $phone_clean;

// Збираємо всі фото з папки sauna-photo
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
    <title><?= $business_name ?> — Готель-Сауна у Хмельницькому</title>

    <meta name="description" content="<?= $business_desc ?>"/>
    <meta name="robots" content="index, follow"/>
    <meta property="og:locale" content="uk_UA" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= $business_name ?> — Готель-Сауна у Хмельницькому" />
    <meta property="og:description" content="<?= $business_desc ?>" />
    <meta property="og:site_name" content="<?= $business_name ?>" />

    <?php if(!empty($analytics_id)): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $analytics_id ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?= $analytics_id ?>');
    </script>
    <?php endif; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');
        body { font-family: 'Montserrat', sans-serif; }
        .hero-bg {
            background: linear-gradient(rgba(0,30,40,0.65), rgba(0,20,30,0.75)),
                        url('sauna-photo/photo_1_2026-03-14_13-29-47.jpg');
            background-size: cover;
            background-position: center;
        }
        .promo-pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 22s linear infinite;
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .photo-carousel { cursor: grab; }
        .photo-carousel.active { cursor: grabbing; scroll-snap-type: none; }
        .photo-carousel img { user-select: none; -webkit-user-drag: none; pointer-events: none; }

        /* Lightbox */
        #lightbox {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.92);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        #lightbox.open { display: flex; }
        #lightbox img {
            max-height: 90vh;
            max-width: 90vw;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 0 60px rgba(0,0,0,0.8);
        }
        #lightbox .lb-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            font-size: 2rem;
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.2s;
        }
        #lightbox .lb-btn:hover { background: rgba(255,255,255,0.3); }
        #lightbox .lb-prev { left: 1rem; }
        #lightbox .lb-next { right: 1rem; }
        #lightbox .lb-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            font-size: 1.5rem;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        #lightbox .lb-close:hover { background: rgba(255,100,100,0.5); }
        #lightbox .lb-counter {
            position: absolute;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            font-family: 'Montserrat', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100">

    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="fixed top-24 left-1/2 transform -translate-x-1/2 z-[100] bg-green-500 text-white px-6 py-3 rounded shadow-2xl font-bold flex items-center space-x-2 w-11/12 max-w-md">
        <i class="fa-solid fa-check-circle"></i>
        <span class="text-sm md:text-base">Ваша заявка успішно відправлена! Ми скоро зателефонуємо.</span>
        <button onclick="this.parentElement.style.display='none'" class="ml-auto text-white hover:text-gray-200"><i class="fa-solid fa-times"></i></button>
    </div>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 bg-gray-900/95 backdrop-blur-md shadow-lg border-b border-teal-800/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-3xl">💧</span>
                    <div class="flex flex-col">
                        <span class="text-xl md:text-2xl font-bold text-teal-300 leading-none tracking-tight">ВОДОПАД</span>
                        <span class="text-[9px] md:text-[10px] uppercase tracking-widest text-gray-400">Готель-Сауна</span>
                    </div>
                </div>
                <div class="hidden lg:flex space-x-8 text-sm font-semibold uppercase tracking-wider text-gray-300">
                    <a href="#amenities" class="hover:text-teal-300 transition">Послуги</a>
                    <a href="#pricing" class="hover:text-teal-300 transition">Ціни</a>
                    <a href="#gallery" class="hover:text-teal-300 transition">Галерея</a>
                    <a href="#contacts" class="hover:text-teal-300 transition">Контакти</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="tel:<?= $phone_clean ?>" class="hidden md:block font-bold text-teal-300 tracking-tighter text-lg"><?= $phone ?></a>
                    <a href="#booking" class="bg-amber-500 text-gray-900 px-4 md:px-6 py-2 rounded-full font-bold hover:bg-amber-400 transition shadow-lg uppercase text-[10px] md:text-xs promo-pulse">Забронювати</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <header class="relative h-screen flex items-center justify-center text-center text-white hero-bg">
        <div class="max-w-4xl px-4">
            <div class="bg-teal-600/70 backdrop-blur-sm inline-block px-6 py-2 rounded mb-6 uppercase tracking-widest text-sm font-bold shadow-lg">Хмельницький</div>
            <h1 class="text-5xl md:text-7xl font-bold mb-6 tracking-tight text-white">
                Готель-Сауна<br><span class="text-teal-300"><?= $business_name ?></span>
            </h1>
            <p class="text-lg md:text-2xl mb-10 font-light text-gray-200"><?= $business_desc ?></p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#booking" class="bg-amber-500 text-gray-900 px-10 py-4 rounded-lg font-bold hover:bg-amber-400 transition shadow-xl">Забронювати зараз</a>
                <a href="#amenities" class="border-2 border-teal-400 text-teal-300 px-10 py-4 rounded-lg font-bold hover:bg-teal-400/10 transition">Дізнатися більше</a>
            </div>
        </div>
    </header>

    <!-- Promo Banner -->
    <section class="bg-amber-500 py-3 md:py-4 overflow-hidden relative border-y border-amber-600 shadow-sm">
        <div class="whitespace-nowrap flex">
            <div class="animate-marquee">
                <span class="font-extrabold uppercase italic text-gray-900 text-[11px] md:text-lg tracking-tight">
                    <?= $promo_text ?> &nbsp;&nbsp;&nbsp;&nbsp; <?= $promo_text ?> &nbsp;&nbsp;&nbsp;&nbsp;
                </span>
                <span class="font-extrabold uppercase italic text-gray-900 text-[11px] md:text-lg tracking-tight">
                    <?= $promo_text ?> &nbsp;&nbsp;&nbsp;&nbsp; <?= $promo_text ?> &nbsp;&nbsp;&nbsp;&nbsp;
                </span>
            </div>
        </div>
    </section>

    <!-- Amenities -->
    <section id="amenities" class="py-20 bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-left mb-14 border-l-4 border-teal-500 pl-6">
                <h2 class="text-4xl font-bold text-teal-300 mb-4 tracking-tighter italic uppercase">Наші послуги</h2>
                <p class="text-gray-400 text-sm font-medium">Комплекс оздоровчих процедур для повноцінного відпочинку</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8 text-center">
                <!-- Парна -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-fire-flame-curved text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Волога парна</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">На дровах — справжня лазня з природним жаром</p>
                </div>
                <!-- Хамам -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-droplet text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Хамам</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">З ароматичними оліями для глибокого розслаблення</p>
                </div>
                <!-- Соляна кімната -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-wind text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Соляна кімната</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">Галотерапія для здоров'я дихальних шляхів</p>
                </div>
                <!-- Інфрачервона сауна -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-sun text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Інфрачервона сауна</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">М'яке прогрівання на клітинному рівні</p>
                </div>
                <!-- Чанн -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-bucket text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Чанн</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">З гілочками ялинки, сосни та можевельника</p>
                </div>
                <!-- СПА -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-spa text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">СПА на 8 осіб</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">256 форсунок та гейзер для гідромасажу</p>
                </div>
                <!-- Теплий басейн -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-water-ladder text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Теплий басейн</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">Шийний масаж, гейзер і протитечія</p>
                </div>
                <!-- Крижаний басейн -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-snowflake text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Крижаний басейн</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">Загартування після парних процедур</p>
                </div>
                <!-- Душ Шарко -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-shower text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Душ Шарко</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">Терапевтичний душ для тонусу м'язів</p>
                </div>
                <!-- Відро-Водопад -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-faucet-drip text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Відро-Водопад</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">Різкий холодний обливання для бадьорості</p>
                </div>
                <!-- Масаж -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-hand-holding-heart text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Масаж</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">Індивідуально 20-30 хв, майстер з 20-річним досвідом</p>
                </div>
                <!-- Кімната відпочинку -->
                <div class="p-5 md:p-6 bg-gray-700/50 rounded-2xl border border-gray-600 hover:border-teal-500 transition">
                    <div class="w-14 h-14 bg-teal-900/60 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-couch text-2xl text-teal-400"></i>
                    </div>
                    <h4 class="font-bold text-sm md:text-base mb-2 text-teal-200 leading-tight">Кімната відпочинку</h4>
                    <p class="text-gray-400 text-[10px] md:text-xs">На 12 осіб з каміном та акваріумом</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-20 bg-gray-900 border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-left mb-14 border-l-4 border-amber-500 pl-6">
                <h2 class="text-4xl font-bold text-amber-400 mb-4 tracking-tighter italic uppercase">Ціни</h2>
                <p class="text-gray-400 text-sm font-medium">Оренда всього комплексу — ціна за компанію</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php
                $prices = [
                    ['hours' => '3', 'price' => $price_3h],
                    ['hours' => '4', 'price' => $price_4h],
                    ['hours' => '5', 'price' => $price_5h],
                    ['hours' => '6', 'price' => $price_6h],
                    ['hours' => '7', 'price' => $price_7h],
                    ['hours' => '8', 'price' => $price_8h],
                ];
                foreach ($prices as $i => $p):
                    $featured = ($i === 2);
                ?>
                <div class="<?= $featured ? 'bg-teal-700 border-2 border-teal-400 shadow-2xl' : 'bg-gray-800 border border-gray-700' ?> rounded-2xl p-6 text-center hover:border-teal-500 transition relative">
                    <?php if($featured): ?>
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-amber-400 text-gray-900 text-[10px] font-black uppercase px-3 py-1 rounded-full">Популярний</div>
                    <?php endif; ?>
                    <div class="text-4xl md:text-5xl font-black text-teal-300 mb-1"><?= $p['hours'] ?></div>
                    <div class="text-xs text-gray-400 uppercase tracking-wider mb-4">год</div>
                    <div class="text-2xl font-bold <?= $featured ? 'text-white' : 'text-amber-400' ?>"><?= number_format((int)$p['price'], 0, '.', ' ') ?></div>
                    <div class="text-xs text-gray-400 mt-1">грн</div>
                    <a href="#booking" class="mt-4 block text-center <?= $featured ? 'bg-amber-400 text-gray-900 hover:bg-amber-300' : 'bg-teal-700/50 text-teal-300 hover:bg-teal-700' ?> px-4 py-2 rounded-lg text-xs font-bold transition">Забронювати</a>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-10 bg-gray-800 border border-teal-700/40 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center gap-4">
                <i class="fa-solid fa-circle-info text-teal-400 text-2xl flex-shrink-0"></i>
                <p class="text-gray-300 text-sm leading-relaxed">
                    <span class="font-bold text-teal-300">До ціни включено:</span> користування всіма саунами, СПА, басейнами, душами та кімнатою відпочинку з каміном. Масаж кожному гостю індивідуально 20-30 хвилин від майстра з 20-річним досвідом.
                </p>
            </div>
        </div>
    </section>

    <!-- Photo Gallery -->
    <section id="gallery" class="py-20 bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-10">
                <div class="border-l-4 border-teal-500 pl-6">
                    <h2 class="text-4xl font-bold text-teal-300 tracking-tighter italic uppercase">Галерея</h2>
                </div>
                <span class="text-[10px] sm:text-xs font-bold text-gray-500 uppercase tracking-widest bg-gray-700 px-3 py-1.5 rounded-full">
                    <i class="fa-solid fa-arrows-left-right mr-2"></i>Гортайте вбік
                </span>
            </div>

            <div id="photo-carousel" class="photo-carousel flex overflow-x-auto space-x-4 pb-6 snap-x snap-mandatory hide-scrollbar">
                <?php foreach ($photos as $i => $photo): ?>
                <div class="snap-center shrink-0 w-[75vw] md:w-[380px] rounded-2xl overflow-hidden shadow-lg border border-gray-600 cursor-pointer gallery-item"
                     data-index="<?= $i ?>">
                    <img src="<?= e($photo) ?>" alt="Фото <?= $i+1 ?>" class="w-full h-[260px] md:h-[280px] object-cover hover:scale-105 transition duration-500" style="pointer-events: none;">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div id="lightbox">
        <button class="lb-close" id="lb-close"><i class="fa-solid fa-xmark"></i></button>
        <button class="lb-btn lb-prev" id="lb-prev"><i class="fa-solid fa-chevron-left"></i></button>
        <img id="lb-img" src="" alt="Фото">
        <button class="lb-btn lb-next" id="lb-next"><i class="fa-solid fa-chevron-right"></i></button>
        <div class="lb-counter" id="lb-counter"></div>
    </div>

    <!-- Booking Form -->
    <section id="booking" class="py-24 bg-gray-900 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-teal-900/30 rounded-full blur-3xl -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-amber-900/20 rounded-full blur-3xl -ml-48 -mb-48"></div>
        <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
            <h2 class="text-4xl font-bold mb-4 italic tracking-tighter text-amber-400 uppercase underline decoration-teal-500 underline-offset-8"><?= $form_title ?></h2>
            <p class="text-xl mb-12 text-gray-400 font-light">Залиште номер — ми передзвонимо і підберемо зручний час</p>

            <form action="submit.php" method="POST" class="grid sm:grid-cols-2 gap-4 max-w-2xl mx-auto">
                <input type="text" name="guest_name" required placeholder="Ваше ім'я"
                    class="bg-gray-800 border border-gray-600 text-white placeholder-gray-500 p-4 rounded-lg outline-none focus:ring-2 focus:ring-teal-500 transition font-medium">
                <input type="tel" id="phone-input" name="guest_phone" required placeholder="+38 (0__) ___-__-__"
                    class="bg-gray-800 border border-gray-600 text-white placeholder-gray-500 p-4 rounded-lg outline-none focus:ring-2 focus:ring-teal-500 transition font-medium">
                <div class="sm:col-span-2">
                    <button type="submit" class="w-full bg-amber-500 text-gray-900 py-5 rounded-lg font-bold text-xl uppercase hover:bg-amber-400 transition shadow-2xl tracking-widest italic">
                        Надіслати заявку
                    </button>
                    <p class="text-xs mt-4 text-gray-500 italic uppercase tracking-tighter">Ми перетелефонуємо вам протягом 5 хвилин</p>
                </div>
            </form>
        </div>
    </section>

    <!-- Contacts -->
    <section id="contacts" class="py-20 bg-gray-800 border-t border-gray-700">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-16">
            <div>
                <h2 class="text-4xl font-bold text-teal-300 mb-8 italic border-b-2 border-amber-500 inline-block uppercase tracking-tight">Контакти</h2>
                <div class="space-y-8">
                    <div class="flex items-start space-x-4">
                        <i class="fa-solid fa-map-location-dot text-teal-400 text-2xl mt-1"></i>
                        <div>
                            <p class="font-bold italic text-sm uppercase tracking-tight text-gray-400 mb-1">Адреса:</p>
                            <p class="text-gray-200 leading-tight text-lg font-light"><?= $address ?></p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <i class="fa-solid fa-phone-volume text-teal-400 text-2xl mt-1"></i>
                        <div>
                            <p class="font-bold italic text-sm uppercase tracking-tight text-gray-400 mb-2">Телефон:</p>
                            <a href="tel:<?= $phone_clean ?>" class="text-teal-300 font-bold text-3xl tracking-tighter leading-none"><?= $phone ?></a>
                            <div class="flex items-center space-x-6 pt-4">
                                <a href="viber://chat?number=<?= $phone_clean ?>" class="text-purple-400 text-3xl hover:scale-110 transition-transform" title="Viber">
                                    <i class="fa-brands fa-viber"></i>
                                </a>
                                <a href="https://wa.me/<?= $phone_clean ?>" class="text-green-400 text-3xl hover:scale-110 transition-transform" title="WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                                <a href="https://t.me/+<?= $phone_clean ?>" class="text-blue-400 text-3xl hover:scale-110 transition-transform" title="Telegram">
                                    <i class="fa-brands fa-telegram"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <i class="fa-solid fa-clock text-teal-400 text-2xl mt-1"></i>
                        <div>
                            <p class="font-bold italic text-sm uppercase tracking-tight text-gray-400 mb-1">Графік роботи:</p>
                            <p class="text-gray-200 text-lg font-semibold">Цілодобово, без вихідних</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-700 rounded-3xl overflow-hidden min-h-[350px] h-full relative border border-gray-600 shadow-2xl">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2604.0!2d26.95!3d49.42!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDnCsDI1JzEyLjAiTiAyNsKwNTcnMDAuMCJF!5e0!3m2!1suk!2sua!4v1700000000000!5m2!1suk!2sua"
                    width="100%"
                    height="100%"
                    style="border:0; position:absolute; top:0; left:0; filter: invert(90%) hue-rotate(180deg);"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-500 py-12 text-center border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <span class="text-2xl">💧</span>
                <span class="text-lg font-bold text-teal-400 uppercase tracking-widest"><?= $business_name ?></span>
            </div>
            <p class="text-xs italic leading-relaxed max-w-md mx-auto opacity-60 mb-2"><?= $footer_text ?></p>
            <p class="text-[10px] mt-6 uppercase tracking-widest opacity-30 font-bold">&copy; <?= date('Y') ?> Всі права захищені.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/imask"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Phone mask
        var phoneInput = document.getElementById('phone-input');
        if (phoneInput) {
            IMask(phoneInput, { mask: '+{38} (000) 000-00-00' });
        }

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Drag-to-scroll carousel
        const slider = document.getElementById('photo-carousel');
        let isDown = false, startX, scrollLeft;
        if (slider) {
            slider.addEventListener('mousedown', (e) => {
                isDown = true;
                slider.classList.add('active');
                startX = e.pageX - slider.offsetLeft;
                scrollLeft = slider.scrollLeft;
            });
            slider.addEventListener('mouseleave', () => { isDown = false; slider.classList.remove('active'); });
            slider.addEventListener('mouseup', () => { isDown = false; slider.classList.remove('active'); });
            slider.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offsetLeft;
                slider.scrollLeft = scrollLeft - (x - startX) * 2;
            });
        }

        // Lightbox
        const photos = <?= json_encode(array_values($photos)) ?>;
        const lightbox = document.getElementById('lightbox');
        const lbImg = document.getElementById('lb-img');
        const lbCounter = document.getElementById('lb-counter');
        let currentIdx = 0;

        function openLightbox(idx) {
            currentIdx = idx;
            lbImg.src = photos[currentIdx];
            lbCounter.textContent = (currentIdx + 1) + ' / ' + photos.length;
            lightbox.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeLightbox() {
            lightbox.classList.remove('open');
            document.body.style.overflow = '';
        }
        function prevPhoto() {
            currentIdx = (currentIdx - 1 + photos.length) % photos.length;
            lbImg.src = photos[currentIdx];
            lbCounter.textContent = (currentIdx + 1) + ' / ' + photos.length;
        }
        function nextPhoto() {
            currentIdx = (currentIdx + 1) % photos.length;
            lbImg.src = photos[currentIdx];
            lbCounter.textContent = (currentIdx + 1) + ' / ' + photos.length;
        }

        document.querySelectorAll('.gallery-item').forEach(item => {
            item.addEventListener('click', function () {
                openLightbox(parseInt(this.dataset.index));
            });
        });

        document.getElementById('lb-close').addEventListener('click', closeLightbox);
        document.getElementById('lb-prev').addEventListener('click', prevPhoto);
        document.getElementById('lb-next').addEventListener('click', nextPhoto);

        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });

        document.addEventListener('keydown', function (e) {
            if (!lightbox.classList.contains('open')) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') prevPhoto();
            if (e.key === 'ArrowRight') nextPhoto();
        });
    });
    </script>
</body>
</html>

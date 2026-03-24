<?php
$file = 'data.json';
$data = json_decode(file_exists($file) ? file_get_contents($file) : '{"settings":{},"bookings":[]}', true);
$settings = $data['settings'] ?? [];

function e($text) { return htmlspecialchars($text ?? ''); }

$business_name = e($settings['business_name']) ?: 'Водопад';
$phone         = e($settings['phone']) ?: '096 001 6 001';
$phone_clean   = preg_replace('/[^\d+]/', '', $phone);
if (substr($phone_clean, 0, 1) === '0') $phone_clean = '+38' . $phone_clean;
?>
<!DOCTYPE html>
<html lang="uk" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Політика приватності — <?= $business_name ?></title>
    <meta name="robots" content="noindex, nofollow"/>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'play': ['Play', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>* { font-family: 'Play', sans-serif; }</style>
</head>
<body class="bg-gray-950 text-gray-300 antialiased">

    <!-- Nav -->
    <nav class="bg-gray-950/95 border-b border-gray-800">
        <div class="max-w-screen-md mx-auto px-6 h-[72px] flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <span class="text-2xl leading-none select-none">💧</span>
                <span class="text-base font-bold text-teal-300 uppercase tracking-[.18em]"><?= $business_name ?></span>
            </a>
            <a href="/" class="text-sm text-gray-500 hover:text-teal-400 transition">&larr; На головну</a>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-screen-md mx-auto px-6 py-16">
        <h1 class="font-play text-4xl md:text-5xl text-white mb-10">Політика приватності</h1>

        <p class="mb-6 text-sm text-gray-500">Дата оновлення: <?= date('d.m.Y') ?></p>

        <div class="space-y-10 text-gray-400 leading-relaxed">

            <section>
                <h2 class="text-xl text-white font-bold mb-3">1. Загальні положення</h2>
                <p>Ця Політика приватності описує, як готель-сауна «<?= $business_name ?>» (далі — «ми», «нас») збирає, використовує та захищає персональні дані, які ви надаєте через наш веб-сайт.</p>
                <p class="mt-2">Використовуючи наш сайт, ви погоджуєтесь з умовами цієї Політики.</p>
            </section>

            <section>
                <h2 class="text-xl text-white font-bold mb-3">2. Які дані ми збираємо</h2>
                <p>При оформленні бронювання ми можемо збирати такі дані:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Ваше ім'я</li>
                    <li>Номер телефону</li>
                    <li>Бажана дата та час відвідування</li>
                    <li>Кількість гостей</li>
                    <li>Додаткові побажання, залишені у формі бронювання</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl text-white font-bold mb-3">3. Мета збору даних</h2>
                <p>Ваші персональні дані використовуються виключно для:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Обробки та підтвердження бронювань</li>
                    <li>Зв'язку з вами щодо деталей візиту</li>
                    <li>Покращення якості наших послуг</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl text-white font-bold mb-3">4. Зберігання та захист даних</h2>
                <p>Ми зберігаємо ваші дані на захищеному сервері та вживаємо розумних заходів для їх захисту від несанкціонованого доступу, зміни чи знищення.</p>
                <p class="mt-2">Дані бронювань зберігаються протягом періоду, необхідного для надання послуг, після чого можуть бути видалені.</p>
            </section>

            <section>
                <h2 class="text-xl text-white font-bold mb-3">5. Передача даних третім особам</h2>
                <p>Ми не продаємо, не обмінюємо та не передаємо ваші персональні дані третім особам, окрім випадків, передбачених законодавством України.</p>
                <p class="mt-2">Сповіщення про нові бронювання можуть надсилатися через месенджер Telegram для оперативної обробки заявок.</p>
            </section>

            <section>
                <h2 class="text-xl text-white font-bold mb-3">6. Аналітика</h2>
                <p>Ми можемо використовувати Google Analytics для збору анонімної статистики відвідувань сайту. Ці дані не дозволяють ідентифікувати вас особисто та використовуються лише для аналізу відвідуваності.</p>
            </section>

            <section>
                <h2 class="text-xl text-white font-bold mb-3">7. Ваші права</h2>
                <p>Відповідно до законодавства України про захист персональних даних, ви маєте право:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li>Знати про збір та обробку ваших персональних даних</li>
                    <li>Отримати доступ до своїх персональних даних</li>
                    <li>Вимагати виправлення неточних даних</li>
                    <li>Вимагати видалення ваших даних</li>
                    <li>Відкликати згоду на обробку персональних даних</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl text-white font-bold mb-3">8. Контакти</h2>
                <p>Якщо у вас є питання щодо цієї Політики приватності або ви хочете скористатися своїми правами, зв'яжіться з нами:</p>
                <p class="mt-2">
                    <a href="tel:<?= $phone_clean ?>" class="text-teal-400 hover:text-teal-300 transition"><?= $phone ?></a>
                </p>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-900 py-8">
        <div class="max-w-screen-md mx-auto px-6 text-center text-sm text-gray-700">
            &copy; <?= date('Y') ?> <?= $business_name ?> — Всі права захищені
        </div>
    </footer>

</body>
</html>

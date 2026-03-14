<?php
session_start();
$file = 'data.json';

// Завантаження даних
$data = json_decode(file_exists($file) ? file_get_contents($file) : '{"settings":{},"bookings":[]}', true);
if (!isset($data['bookings'])) $data['bookings'] = [];
if (!isset($data['settings'])) $data['settings'] = [];

$admin_password = $data['settings']['admin_password'] ?? 'sauna';

// Логіка входу/виходу
if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = "Невірний пароль!";
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true):
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід в Адмінку — Водопад</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 h-screen flex items-center justify-center p-4">
    <div class="bg-gray-800 border border-gray-700 p-8 rounded-xl shadow-2xl w-full max-w-sm">
        <div class="text-center mb-6">
            <span class="text-4xl">💧</span>
            <h2 class="text-2xl font-bold mt-2 text-teal-300">Водопад</h2>
            <p class="text-gray-500 text-sm">Панель керування</p>
        </div>
        <?php if(isset($error)) echo "<p class='text-red-400 mb-4 font-bold text-center text-sm'>$error</p>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Введіть пароль" required
                class="w-full p-3 bg-gray-700 border border-gray-600 text-white rounded-lg mb-4 outline-none focus:ring-2 focus:ring-teal-500 transition">
            <button type="submit" class="w-full bg-teal-600 text-white py-3 rounded-lg font-bold hover:bg-teal-500 transition">Увійти</button>
        </form>
    </div>
</body>
</html>
<?php
exit;
endif;

// Перезавантажуємо дані після входу (можливо вже змінилися)
$data = json_decode(file_exists($file) ? file_get_contents($file) : '{"settings":{},"bookings":[]}', true);
if (!isset($data['bookings'])) $data['bookings'] = [];
if (!isset($data['settings'])) $data['settings'] = [];

$tab = $_GET['tab'] ?? 'bookings';

// ЕКСПОРТ CSV
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=bookings_vodopad.csv');
    echo "\xEF\xBB\xBF";
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Дата заявки', 'Клієнт', 'Телефон', 'Дата бронювання', 'Годин', 'Статус', 'Нотатка']);
    foreach ($data['bookings'] as $row) {
        $status_text = ($row['status'] ?? 'new') == 'new' ? 'Нова' : 'Оброблена';
        $bd = !empty($row['booking_date']) ? date('d.m.Y', strtotime($row['booking_date'])) : '';
        fputcsv($output, [
            $row['date'],
            $row['name'],
            $row['phone'],
            $bd,
            $row['hours'] ?? '',
            $status_text,
            $row['note'] ?? ''
        ]);
    }
    fclose($output);
    exit;
}

// ОБРОБКА ДІЙ ІЗ ЗАЯВКАМИ
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($_GET['action'] === 'delete') {
        $data['bookings'] = array_values(array_filter($data['bookings'], function($b) use ($id) {
            return $b['id'] != $id;
        }));
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: admin.php?tab=bookings&msg=deleted");
        exit;
    }

    if ($_GET['action'] === 'toggle') {
        foreach ($data['bookings'] as &$b) {
            if ($b['id'] == $id) {
                $b['status'] = ($b['status'] ?? 'new') === 'new' ? 'processed' : 'new';
                break;
            }
        }
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: admin.php?tab=bookings");
        exit;
    }
}

// ЗБЕРЕЖЕННЯ НОТАТКИ
if (isset($_POST['save_note'])) {
    $id = $_POST['booking_id'];
    $note = $_POST['note'];
    foreach ($data['bookings'] as &$b) {
        if ($b['id'] == $id) {
            $b['note'] = trim($note);
            break;
        }
    }
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: admin.php?tab=bookings");
    exit;
}

// ЗБЕРЕЖЕННЯ КОНТЕНТУ
if (isset($_POST['save_content'])) {
    $fields = ['meta_title', 'meta_desc', 'og_image', 'business_desc', 'promo_text', 'price_3h', 'price_4h', 'price_5h', 'price_6h', 'price_7h', 'price_8h', 'form_title', 'footer_text'];
    foreach ($fields as $f) {
        $data['settings'][$f] = $_POST[$f] ?? '';
    }
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $success_msg = "Контент успішно збережено!";
}

// ЗБЕРЕЖЕННЯ НАЛАШТУВАНЬ
if (isset($_POST['save_settings'])) {
    $fields = ['business_name', 'phone', 'address', 'analytics_id', 'telegram_token', 'telegram_chat_id'];
    foreach ($fields as $f) {
        $data['settings'][$f] = $_POST[$f] ?? '';
    }
    // Зміна пароля
    $new_pass = trim($_POST['admin_password'] ?? '');
    if (!empty($new_pass)) {
        $data['settings']['admin_password'] = $new_pass;
    }
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $success_msg = "Налаштування успішно збережено!";
}

function val($key) {
    global $data;
    return htmlspecialchars($data['settings'][$key] ?? '');
}

$total_bookings    = count($data['bookings']);
$new_bookings      = count(array_filter($data['bookings'], fn($b) => ($b['status'] ?? 'new') === 'new'));
$processed_bookings = $total_bookings - $new_bookings;
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмінка | Водопад</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-950 text-gray-200">

    <nav class="bg-gray-900 border-b border-gray-800 shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="text-xl">💧</span>
                <h1 class="text-lg font-bold text-teal-300">Водопад — Панель керування</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="index.php" target="_blank" class="text-gray-400 hover:text-teal-300 text-sm font-bold transition"><i class="fa-solid fa-arrow-up-right-from-square mr-1"></i>Сайт</a>
                <a href="?logout=1" class="bg-red-700 hover:bg-red-600 px-4 py-2 rounded font-bold text-sm transition">Вийти</a>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-6">

        <?php if(isset($success_msg)) echo "<div class='bg-teal-900/50 border-l-4 border-teal-500 text-teal-300 p-4 mb-6 rounded'><b>Чудово!</b> $success_msg</div>"; ?>
        <?php if(isset($_GET['msg']) && $_GET['msg']=='deleted') echo "<div class='bg-yellow-900/50 border-l-4 border-yellow-500 text-yellow-300 p-4 mb-6 rounded'>Заявку видалено.</div>"; ?>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-4 mb-6 border-b border-gray-700">
            <a href="?tab=bookings" class="pb-3 px-2 font-bold transition-colors <?= $tab == 'bookings' ? 'text-teal-400 border-b-2 border-teal-400' : 'text-gray-500 hover:text-teal-300' ?>">
                <i class="fa-solid fa-bell mr-1"></i> Бронювання
                <?php if($new_bookings > 0): ?><span class="bg-red-600 text-white text-[10px] px-2 py-0.5 rounded-full ml-1"><?= $new_bookings ?></span><?php endif; ?>
            </a>
            <a href="?tab=content" class="pb-3 px-2 font-bold transition-colors <?= $tab == 'content' ? 'text-teal-400 border-b-2 border-teal-400' : 'text-gray-500 hover:text-teal-300' ?>">
                <i class="fa-solid fa-pen-to-square mr-1"></i> Контент
            </a>
            <a href="?tab=settings" class="pb-3 px-2 font-bold transition-colors <?= $tab == 'settings' ? 'text-teal-400 border-b-2 border-teal-400' : 'text-gray-500 hover:text-teal-300' ?>">
                <i class="fa-solid fa-gear mr-1"></i> Налаштування
            </a>
        </div>

        <!-- TAB: BOOKINGS -->
        <?php if ($tab == 'bookings'): ?>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-800 p-4 rounded-xl border-l-4 border-blue-500 flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Всього заявок</div>
                    <div class="text-2xl font-black text-white"><?= $total_bookings ?></div>
                </div>
                <i class="fa-solid fa-layer-group text-3xl text-blue-900"></i>
            </div>
            <div class="bg-gray-800 p-4 rounded-xl border-l-4 border-green-500 flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Нових (в роботі)</div>
                    <div class="text-2xl font-black text-green-400"><?= $new_bookings ?></div>
                </div>
                <i class="fa-solid fa-bell text-3xl text-green-900"></i>
            </div>
            <div class="bg-gray-800 p-4 rounded-xl border-l-4 border-gray-500 flex justify-between items-center">
                <div>
                    <div class="text-gray-500 text-xs font-bold uppercase tracking-wider">Оброблених</div>
                    <div class="text-2xl font-black text-gray-400"><?= $processed_bookings ?></div>
                </div>
                <i class="fa-solid fa-check-double text-3xl text-gray-700"></i>
            </div>
        </div>

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-teal-300">Список бронювань</h2>
            <a href="?action=export" class="bg-green-700 text-white px-4 py-2 rounded text-sm font-bold hover:bg-green-600 transition flex items-center shadow">
                <i class="fa-solid fa-file-excel mr-2"></i>Завантажити (Excel)
            </a>
        </div>

        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-700 text-gray-400 text-sm">
                            <th class="p-4 border-b border-gray-600 w-36">Дата заявки</th>
                            <th class="p-4 border-b border-gray-600">Клієнт / Телефон</th>
                            <th class="p-4 border-b border-gray-600 w-40">Бронювання</th>
                            <th class="p-4 border-b border-gray-600">Нотатка</th>
                            <th class="p-4 border-b border-gray-600 text-center w-32">Статус</th>
                            <th class="p-4 border-b border-gray-600 text-right w-40">Дії</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($data['bookings'])): ?>
                            <?php foreach($data['bookings'] as $row):
                                $status = $row['status'] ?? 'new';
                                $isNew = ($status === 'new');
                            ?>
                            <tr class="border-b border-gray-700 hover:bg-gray-700/30 transition <?= $isNew ? '' : 'opacity-60' ?>">
                                <td class="p-4 text-xs text-gray-500 align-top whitespace-nowrap"><?= $row['date'] ?></td>
                                <td class="p-4 align-top">
                                    <div class="font-bold text-white text-lg mb-1"><?= htmlspecialchars($row['name']) ?></div>
                                    <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="text-teal-400 font-bold hover:underline inline-block bg-teal-900/30 px-2 py-1 rounded text-sm">
                                        <i class="fa-solid fa-phone mr-1 text-xs"></i><?= htmlspecialchars($row['phone']) ?>
                                    </a>
                                </td>
                                <td class="p-4 align-top text-sm">
                                    <?php if (!empty($row['booking_date'])): ?>
                                    <div class="text-white font-bold mb-1">
                                        <i class="fa-regular fa-calendar text-teal-500 mr-1"></i>
                                        <?= date('d.m.Y', strtotime($row['booking_date'])) ?>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($row['hours'])): ?>
                                    <div class="text-amber-400 font-bold">
                                        <i class="fa-regular fa-clock mr-1"></i><?= $row['hours'] ?> год
                                    </div>
                                    <?php endif; ?>
                                    <?php if (empty($row['booking_date']) && empty($row['hours'])): ?>
                                    <span class="text-gray-600">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 align-top">
                                    <form method="POST" class="flex gap-2">
                                        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                        <input type="text" name="note" value="<?= htmlspecialchars($row['note'] ?? '') ?>" placeholder="Додати коментар..."
                                            class="text-sm p-2 bg-gray-700 border border-gray-600 text-white rounded w-full focus:ring-2 focus:ring-teal-500 outline-none transition placeholder-gray-500">
                                        <button type="submit" name="save_note" class="bg-teal-800 text-teal-300 px-3 rounded hover:bg-teal-700 transition" title="Зберегти">
                                            <i class="fa-solid fa-save"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="p-4 text-center align-top pt-5">
                                    <?php if($isNew): ?>
                                        <span class="bg-green-900/50 text-green-400 px-3 py-1 rounded-full text-xs font-bold border border-green-700">Нова</span>
                                    <?php else: ?>
                                        <span class="bg-gray-700 text-gray-400 px-3 py-1 rounded-full text-xs font-bold border border-gray-600">Оброблена</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-right align-top pt-4">
                                    <div class="flex justify-end space-x-2">
                                        <a href="?action=toggle&id=<?= $row['id'] ?>&tab=bookings"
                                           class="<?= $isNew ? 'bg-green-700 text-white hover:bg-green-600' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' ?> px-3 py-2 rounded text-xs font-bold transition shadow-sm"
                                           title="<?= $isNew ? 'Відмітити як оброблену' : 'Повернути в Нові' ?>">
                                            <i class="fa-solid <?= $isNew ? 'fa-check' : 'fa-rotate-left' ?>"></i>
                                        </a>
                                        <a href="?action=delete&id=<?= $row['id'] ?>&tab=bookings"
                                           onclick="return confirm('Точно видалити цю заявку?')"
                                           class="bg-red-900/50 text-red-400 hover:bg-red-700 hover:text-white px-3 py-2 rounded text-xs font-bold transition shadow-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-16 text-center text-gray-600">
                                    <i class="fa-solid fa-inbox text-5xl mb-4 block opacity-20"></i>
                                    <span class="text-lg">Заявок поки немає.</span>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif; ?>

        <!-- TAB: CONTENT -->
        <?php if ($tab == 'content'): ?>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
            <form method="POST" class="space-y-6 max-w-4xl">

                <div class="space-y-4 bg-gray-700/30 p-4 rounded-lg border border-gray-700">
                    <h3 class="font-bold text-gray-300 border-b border-gray-600 pb-2">
                        <i class="fa-solid fa-magnifying-glass mr-2 text-purple-400"></i>SEO / Meta
                    </h3>

                    <!-- Fields -->
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
                            Meta Title <span class="text-gray-600 normal-case font-normal">(тег &lt;title&gt;)</span>
                            <span id="title-count" class="ml-2 text-gray-600 font-normal normal-case"></span>
                        </label>
                        <input id="f-meta-title" type="text" name="meta_title" value="<?= val('meta_title') ?>"
                               placeholder="Водопад — Готель-Сауна у Хмельницькому"
                               maxlength="70"
                               class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">
                            Meta Description <span class="text-gray-600 normal-case font-normal">(до 160 символів)</span>
                            <span id="desc-count" class="ml-2 text-gray-600 font-normal normal-case"></span>
                        </label>
                        <textarea id="f-meta-desc" name="meta_desc" rows="2" maxlength="160"
                                  placeholder="Чотири сауни, СПА, масаж і кімната відпочинку з каміном..."
                                  class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-purple-500"><?= val('meta_desc') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">OG Image URL <span class="text-gray-600 normal-case font-normal">(1200×630 px)</span></label>
                        <input id="f-og-image" type="url" name="og_image" value="<?= val('og_image') ?>"
                               placeholder="https://yoursite.com/sauna-photo/photo_1_....jpg"
                               class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Previews -->
                    <div class="pt-2 space-y-5">

                        <!-- Google SERP preview -->
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Попередній перегляд — Google</p>
                            <div class="bg-white rounded-lg p-4 text-left max-w-xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center text-[10px]">💧</div>
                                    <div>
                                        <div class="text-xs text-gray-600 leading-none"><?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'yoursite.com') ?></div>
                                    </div>
                                </div>
                                <div id="prev-google-title" class="text-[#1a0dab] text-xl leading-snug mb-1 font-normal hover:underline cursor-pointer" style="font-family:arial,sans-serif;">
                                    <?= val('meta_title') ?: 'Водопад — Готель-Сауна у Хмельницькому' ?>
                                </div>
                                <div id="prev-google-desc" class="text-[#4d5156] text-sm leading-snug" style="font-family:arial,sans-serif;">
                                    <?= val('meta_desc') ?: 'Чотири сауни, хамам, чанн, СПА та масаж — повне відновлення тіла й душі за один сеанс.' ?>
                                </div>
                            </div>
                        </div>

                        <!-- Social / OG card preview -->
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase mb-2 tracking-wider">Попередній перегляд — Соцмережі (Facebook / Telegram)</p>
                            <div class="border border-gray-300 rounded overflow-hidden max-w-xl bg-white">
                                <div id="prev-og-img-wrap" class="<?= val('og_image') ? '' : 'hidden' ?> bg-gray-100">
                                    <img id="prev-og-img" src="<?= val('og_image') ?>" alt="OG image" class="w-full h-52 object-cover">
                                </div>
                                <div id="prev-og-noimg" class="<?= val('og_image') ? 'hidden' : '' ?> bg-gray-100 h-52 flex items-center justify-center">
                                    <span class="text-gray-400 text-sm">Немає зображення</span>
                                </div>
                                <div class="p-3 border-t border-gray-200">
                                    <div class="text-xs text-gray-400 uppercase tracking-wider mb-1"><?= htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'yoursite.com') ?></div>
                                    <div id="prev-og-title" class="font-bold text-gray-900 text-sm leading-snug mb-1">
                                        <?= val('meta_title') ?: 'Водопад — Готель-Сауна у Хмельницькому' ?>
                                    </div>
                                    <div id="prev-og-desc" class="text-gray-500 text-xs leading-snug line-clamp-2">
                                        <?= val('meta_desc') ?: 'Чотири сауни, хамам, чанн, СПА та масаж — повне відновлення тіла й душі за один сеанс.' ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!-- /previews -->
                </div>

                <script>
                (function() {
                    const titleInput = document.getElementById('f-meta-title');
                    const descInput  = document.getElementById('f-meta-desc');
                    const imgInput   = document.getElementById('f-og-image');

                    const titleCount = document.getElementById('title-count');
                    const descCount  = document.getElementById('desc-count');

                    const gTitle = document.getElementById('prev-google-title');
                    const gDesc  = document.getElementById('prev-google-desc');
                    const ogTitle   = document.getElementById('prev-og-title');
                    const ogDesc    = document.getElementById('prev-og-desc');
                    const ogImgWrap = document.getElementById('prev-og-img-wrap');
                    const ogNoImg   = document.getElementById('prev-og-noimg');
                    const ogImg     = document.getElementById('prev-og-img');

                    const FALLBACK_TITLE = 'Водопад — Готель-Сауна у Хмельницькому';
                    const FALLBACK_DESC  = 'Чотири сауни, хамам, чанн, СПА та масаж — повне відновлення тіла й душі за один сеанс.';

                    function countColor(len, max) {
                        if (len === 0) return 'text-gray-600';
                        if (len <= max * 0.75) return 'text-green-500';
                        if (len <= max) return 'text-yellow-500';
                        return 'text-red-500';
                    }

                    function update() {
                        const t = titleInput.value.trim() || FALLBACK_TITLE;
                        const d = descInput.value.trim()  || FALLBACK_DESC;

                        // Counters
                        const tl = titleInput.value.length;
                        const dl = descInput.value.length;
                        titleCount.textContent = tl ? tl + '/70' : '';
                        titleCount.className = 'ml-2 font-normal normal-case text-xs ' + countColor(tl, 70);
                        descCount.textContent = dl ? dl + '/160' : '';
                        descCount.className  = 'ml-2 font-normal normal-case text-xs ' + countColor(dl, 160);

                        // Google preview
                        gTitle.textContent = t;
                        gDesc.textContent  = d;

                        // OG preview
                        ogTitle.textContent = t;
                        ogDesc.textContent  = d;

                        // OG image
                        const url = imgInput.value.trim();
                        if (url) {
                            ogImg.src = url;
                            ogImgWrap.classList.remove('hidden');
                            ogNoImg.classList.add('hidden');
                        } else {
                            ogImgWrap.classList.add('hidden');
                            ogNoImg.classList.remove('hidden');
                        }
                    }

                    titleInput.addEventListener('input', update);
                    descInput.addEventListener('input', update);
                    imgInput.addEventListener('input', update);
                    update();
                })();
                </script>

                <div class="space-y-4 bg-gray-700/30 p-4 rounded-lg border border-gray-700">
                    <h3 class="font-bold text-gray-300 border-b border-gray-600 pb-2">
                        <i class="fa-solid fa-heading mr-2 text-teal-400"></i>Головний екран
                    </h3>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Опис (під назвою)</label>
                        <textarea name="business_desc" rows="2" class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500"><?= val('business_desc') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Текст жовтої стрічки (Акція)</label>
                        <input type="text" name="promo_text" value="<?= val('promo_text') ?>" class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="space-y-4 bg-gray-700/30 p-4 rounded-lg border border-gray-700">
                    <h3 class="font-bold text-gray-300 border-b border-gray-600 pb-2">
                        <i class="fa-solid fa-tag mr-2 text-amber-400"></i>Ціни (грн)
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php
                        $price_labels = ['price_3h'=>'3 години', 'price_4h'=>'4 години', 'price_5h'=>'5 годин', 'price_6h'=>'6 годин', 'price_7h'=>'7 годин', 'price_8h'=>'8 годин'];
                        foreach ($price_labels as $key => $label):
                        ?>
                        <div>
                            <label class="block text-xs font-bold text-amber-500 uppercase mb-1"><?= $label ?></label>
                            <input type="text" name="<?= $key ?>" value="<?= val($key) ?>"
                                class="w-full p-2 bg-gray-700 border border-amber-700/50 text-white rounded text-sm font-bold outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-y-4 bg-gray-700/30 p-4 rounded-lg border border-gray-700">
                    <h3 class="font-bold text-gray-300 border-b border-gray-600 pb-2">
                        <i class="fa-solid fa-align-left mr-2 text-teal-400"></i>Форма та Footer
                    </h3>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Заголовок форми бронювання</label>
                        <input type="text" name="form_title" value="<?= val('form_title') ?>" class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Текст у підвалі (Footer)</label>
                        <textarea name="footer_text" rows="2" class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500"><?= val('footer_text') ?></textarea>
                    </div>
                </div>

                <button type="submit" name="save_content" class="bg-teal-600 text-white px-8 py-3 rounded font-bold hover:bg-teal-500 transition shadow-lg w-full md:w-auto">
                    <i class="fa-solid fa-save mr-2"></i>Зберегти контент
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- TAB: SETTINGS -->
        <?php if ($tab == 'settings'): ?>
        <div class="bg-gray-800 p-6 rounded-xl border border-gray-700">
            <form method="POST" class="space-y-6 max-w-3xl">

                <div class="space-y-4 bg-gray-700/30 p-4 rounded-lg border border-gray-700">
                    <h3 class="font-bold text-gray-300 border-b border-gray-600 pb-2">
                        <i class="fa-solid fa-address-card mr-2 text-teal-400"></i>Контактні дані
                    </h3>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Назва закладу</label>
                        <input type="text" name="business_name" value="<?= val('business_name') ?>" class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Телефон</label>
                        <input type="text" name="phone" value="<?= val('phone') ?>" class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Адреса</label>
                        <input type="text" name="address" value="<?= val('address') ?>" class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="space-y-4 bg-gray-700/30 p-4 rounded-lg border border-gray-700">
                    <h3 class="font-bold text-gray-300 border-b border-gray-600 pb-2">
                        <i class="fa-brands fa-telegram mr-2 text-blue-400"></i>Інтеграція Telegram
                    </h3>
                    <p class="text-xs text-gray-500">Заповніть ці поля, щоб отримувати нові заявки миттєво у Telegram.</p>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Bot Token</label>
                        <input type="text" name="telegram_token" value="<?= val('telegram_token') ?>" placeholder="123456789:ABCdef..."
                            class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Chat ID</label>
                        <input type="text" name="telegram_chat_id" value="<?= val('telegram_chat_id') ?>" placeholder="-1001234567"
                            class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="space-y-4 bg-gray-700/30 p-4 rounded-lg border border-gray-700">
                    <h3 class="font-bold text-gray-300 border-b border-gray-600 pb-2">
                        <i class="fa-solid fa-chart-line mr-2 text-teal-400"></i>Аналітика
                    </h3>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Google Analytics ID</label>
                        <input type="text" name="analytics_id" value="<?= val('analytics_id') ?>" placeholder="G-XXXXXXXXXX"
                            class="w-full p-2 bg-gray-700 border border-gray-600 text-white rounded text-sm outline-none focus:ring-2 focus:ring-teal-500">
                    </div>
                </div>

                <div class="space-y-4 bg-gray-700/30 p-4 rounded-lg border border-red-900/30">
                    <h3 class="font-bold text-gray-300 border-b border-gray-600 pb-2">
                        <i class="fa-solid fa-lock mr-2 text-red-400"></i>Безпека
                    </h3>
                    <div>
                        <label class="block text-xs font-bold text-red-400 uppercase mb-1">Новий пароль адмінки (залиште пустим, щоб не змінювати)</label>
                        <input type="password" name="admin_password" placeholder="••••••••"
                            class="w-full p-2 bg-gray-700 border border-red-800/50 text-white rounded text-sm outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                </div>

                <button type="submit" name="save_settings" class="bg-teal-600 text-white px-8 py-3 rounded font-bold hover:bg-teal-500 transition shadow-lg w-full md:w-auto">
                    <i class="fa-solid fa-save mr-2"></i>Зберегти налаштування
                </button>
            </form>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>

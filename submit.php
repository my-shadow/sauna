<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Kyiv');

$file = 'data.json';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST['guest_name']) ? htmlspecialchars(strip_tags(trim($_POST['guest_name']))) : '';
    $phoneRaw = isset($_POST['guest_phone']) ? trim($_POST['guest_phone']) : '';

    $digitsOnly = preg_replace('/[^\d]/', '', $phoneRaw);

    if (empty($name) || empty($phoneRaw)) {
        die("<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>
                <h2>Помилка: Заповніть всі поля!</h2>
                <button onclick='history.back()' style='padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 5px; cursor: pointer;'>Повернутися назад</button>
             </div>");
    }

    if (strlen($digitsOnly) < 12) {
        die("<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>
                <h2>Помилка: Неповний або некоректний номер телефону!</h2>
                <p>Перевірте правильність введеного номеру. Ви ввели замало цифр.</p>
                <button onclick='history.back()' style='padding: 10px 20px; background: #dc2626; color: white; border: none; border-radius: 5px; cursor: pointer;'>Повернутися назад</button>
             </div>");
    }

    $json_data = file_exists($file) ? file_get_contents($file) : '{}';
    $data = json_decode($json_data, true);

    if (!is_array($data)) $data = [];
    if (!isset($data['bookings']) || !is_array($data['bookings'])) $data['bookings'] = [];
    if (!isset($data['settings']) || !is_array($data['settings'])) $data['settings'] = [];

    $hours = isset($_POST['guest_hours']) ? (int)$_POST['guest_hours'] : 0;
    $booking_date = isset($_POST['guest_date']) ? preg_replace('/[^\d\-]/', '', $_POST['guest_date']) : '';

    $new_booking = [
        'id'           => time(),
        'name'         => $name,
        'phone'        => $phoneRaw,
        'hours'        => $hours ?: null,
        'booking_date' => $booking_date ?: null,
        'date'         => date('d.m.Y H:i:s'),
        'status'       => 'new'
    ];

    array_unshift($data['bookings'], $new_booking);

    $saved = file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Відправка в Telegram
    $telegram_token = $data['settings']['telegram_token'] ?? '';
    $telegram_chat_id = $data['settings']['telegram_chat_id'] ?? '';

    if (!empty($telegram_token) && !empty($telegram_chat_id)) {
        $business_name = $data['settings']['business_name'] ?? 'Водопад';

        $msg = "🔔 <b>Нова заявка з сайту! ($business_name)</b>\n\n";
        $msg .= "👤 <b>Клієнт:</b> " . $name . "\n";
        $msg .= "📱 <b>Телефон:</b> <a href='tel:+" . $digitsOnly . "'>" . $phoneRaw . "</a>\n";
        if ($hours)        $msg .= "⏱ <b>Годин:</b> " . $hours . " год\n";
        if ($booking_date) $msg .= "📅 <b>Дата:</b> " . date('d.m.Y', strtotime($booking_date)) . "\n";
        $msg .= "⏰ <b>Заявка:</b> " . $new_booking['date'] . "\n\n";
        $msg .= "👉 <a href='https://" . $_SERVER['HTTP_HOST'] . "/admin.php'>Перейти в адмінку</a>";

        $url = "https://api.telegram.org/bot" . $telegram_token . "/sendMessage";

        $post_fields = [
            'chat_id' => $telegram_chat_id,
            'text' => $msg,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    }

    if ($saved !== false) {
        header("Location: index.php?success=1#booking");
        exit;
    } else {
        die("<div style='font-family: sans-serif; text-align: center; margin-top: 50px;'>
                <h2>Помилка сервера!</h2>
                <p>Не вдалося зберегти заявку. Можливо, на хостингу немає прав на запис файлу data.json.</p>
             </div>");
    }

} else {
    header("Location: index.php");
    exit;
}
?>

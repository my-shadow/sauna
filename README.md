# 💧 Водопад — Готель-Сауна

Landing page + admin panel for **Sauna Vodopad**, Khmelnytsky.

---

## Stack

- **PHP** — no framework, pure PHP
- **Tailwind CSS** — via CDN
- **Google Fonts** — Play 400/700 (body), Playfair Display 400 (headings)
- **Font Awesome 6** — icons
- **IMask.js** — phone input masking
- **Flat-file storage** — `data.json` (no database)
- **Telegram Bot API** — booking notifications

---

## File Structure

```
sauna/
├── index.php          # Landing page
├── admin.php          # Admin panel
├── submit.php         # Booking form handler
├── data.json          # All settings + bookings storage
├── sauna-photo/       # 26 JPEGs (photo_1 … photo_29)
└── _backup_v1/        # Previous version of index.php
```

---

## Landing Page Sections

| Section | Anchor | Description |
|---|---|---|
| Hero | — | Split-screen: text left, full-bleed photo right. Static, no animation |
| Ticker | — | Amber auto-scrolling promo banner |
| Stats | — | 5 key numbers (4 saunas, 256 jets, 8 SPA seats…) |
| Experience Zones | `#experience` | 4 alternating zones: Жар / Вода / СПА / Релаксація |
| Health Benefits | `#wellness` | 6 benefit cards + Paracelsus quote |
| How it Works | `#howto` | 4-step process with connector line |
| Pricing | `#pricing` | Table 3h–8h, click "Обрати" pre-selects hours in form |
| Gallery | `#gallery` | Two infinite auto-scroll rows (CSS animation, pause on hover) |
| Booking Form | `#booking` | Name, Phone (masked), Hours select, Date picker |
| Contacts | `#contacts` | Address, phone, messengers, Google Maps embed |
| Footer | — | Links, copyright |

**Animations:** scroll-triggered fade-in via `IntersectionObserver` (`.anim` class). Hero is always static.
**Active nav:** `IntersectionObserver` highlights the current section link.
**Lightbox:** vanilla JS, ESC / arrow keys / touch swipe supported.

---

## Admin Panel

**URL:** `/admin.php`
**Default password:** `sauna` (change in Settings tab)

### Tab: Бронювання
- Stats dashboard (total / new / processed)
- Table with: submission date, client name & phone, **booking date**, **hours**, admin note, status badge
- Toggle status (New ↔ Processed), Delete
- Export CSV (includes all fields)

### Tab: Контент
- **SEO / Meta** — meta title, meta description (with char counter), OG image URL; live Google SERP preview + social card preview
- Main description, promo ticker text
- Prices for 3h / 4h / 5h / 6h / 7h / 8h
- Booking form title, footer text

### Tab: Налаштування
- Business name, phone, address
- Telegram Bot Token + Chat ID
- Google Analytics ID
- Admin password change

---

## Booking Form Fields

| Field | Name | Required |
|---|---|---|
| Ім'я | `guest_name` | ✅ |
| Телефон | `guest_phone` | ✅ |
| Кількість годин | `guest_hours` | — |
| Дата бронювання | `guest_date` | — |

Submitted to `submit.php` → saved to `data.json` → Telegram notification sent (if configured).

---

## Telegram Notifications

1. Go to **Admin → Налаштування**
2. Create a bot via [@BotFather](https://t.me/BotFather), copy the token
3. Get your Chat ID (e.g. via [@userinfobot](https://t.me/userinfobot))
4. Save both fields — notifications fire on every new booking

Message format:
```
🔔 Нова заявка з сайту! (Водопад)

👤 Клієнт: Іван
📱 Телефон: +38 (096) 001-60-01
⏱ Годин: 5 год
📅 Дата: 20.03.2026
⏰ Заявка: 15.03.2026 14:32:00

👉 Перейти в адмінку
```

---

## SEO Meta Tags Output

When meta fields are filled in admin, the page outputs:

- `<title>`, `<meta name="description">`
- `og:locale`, `og:type`, `og:site_name`, `og:url`, `og:title`, `og:description`
- `og:image` + `og:image:secure_url`, `og:image:width/height/alt/type`
- `twitter:card` (auto `summary_large_image` when image set), `twitter:title/description/image`

---

## data.json Structure

```json
{
  "settings": {
    "business_name": "Водопад",
    "phone": "096 001 6 001",
    "address": "Дачний масив «Видрові доли», готель-сауна Водопад",
    "business_desc": "...",
    "promo_text": "...",
    "price_3h": "4500",
    "price_4h": "5500",
    "price_5h": "6250",
    "price_6h": "6750",
    "price_7h": "7250",
    "price_8h": "7750",
    "form_title": "Забронюйте сеанс",
    "footer_text": "...",
    "meta_title": "...",
    "meta_desc": "...",
    "og_image": "",
    "telegram_token": "",
    "telegram_chat_id": "",
    "analytics_id": "",
    "admin_password": "sauna"
  },
  "bookings": [
    {
      "id": 1710000000,
      "name": "Іван",
      "phone": "+38 (096) 001-60-01",
      "hours": 5,
      "booking_date": "2026-03-20",
      "date": "15.03.2026 14:32:00",
      "status": "new",
      "note": ""
    }
  ]
}
```

---

## Deployment Checklist

- [ ] Upload all files to hosting root (or subdirectory)
- [ ] Ensure `data.json` is writable: `chmod 664 data.json`
- [ ] Change admin password in **Settings** tab
- [ ] Set real phone number and address in **Settings**
- [ ] Fill SEO fields in **Content → SEO / Meta**
- [ ] Add Telegram bot token + chat ID for notifications
- [ ] Add Google Analytics ID if needed
- [ ] Update Google Maps embed URL in `index.php` with real coordinates

---

*Built: March 2026*

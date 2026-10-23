---
name: uselesspope-www
description: Architecture and conventions for the "Useless Pope" (Useless Papst) web control panel. Use when working on this repo's PHP pages, jQuery front-end, MariaDB schema, or the AJAX endpoints that drive a physical Raspberry Pi Pope figurine (LEDs, motor, fog, speakers) at Teekränzchen LAN parties.
---

# Useless Pope (Useless Papst) — Web Front-End

This is the web control panel for a physical "Pope" installation: a figurine on a
Raspberry Pi with addressable LEDs, a rotating motor, a fog machine and speakers,
used at "Teekränzchen" LAN parties. Visitors log in, click buttons, and the site
enqueues commands that a broker/daemon later executes on the Pi.

The UI and all server strings are **German** with deliberate church/LAN-party humor
(e.g. "Psalm erklingen lassen", "Apostelberichte", "Sakristei"). Keep that tone when
editing copy.

## Tech stack

- **Backend:** PHP 8.2 (procedural style, `mysqli`), Apache (`.htaccess`), sessions
- **Database:** MariaDB, database name `papst`, user `papst` / password `johannespaul`
- **Front-end:** jQuery 3.7.1, jQuery UI 1.14.1, [party.js](https://party.js.org/)
  (confetti), W3.CSS, custom `framework/style_2025-10-24.css`
- **Ecosystem (referenced from footer, separate repos):**
  - `UselessPope-Broker` (C++) — consumes the command queues
  - `UselessPope-raspi` (Python) — talks to the physical hardware
  - `UselessPope-RemoteButton` (Arduino) — physical remote buttons

## Layout

```
index.php                  # main control panel (entry point)
log.php                    # "Protokoll der Predigt" (sermon log, loaded in iframe)
lib/                       # shared PHP + AJAX endpoints
  global.php               # config, session, print_header/print_footer, helpers
  get_uptime.php           # JSON: uptime, boot time, CPU temp, stats, brightness
  get_sound_files.php      # JSON: sound_button_N -> filename (scans audio dirs)
  play_file.php            # enqueue PLAYSOUND into BUTTON_QUEUE
  change_brightness.php    # adjust CONFIG.brightness
  change_animation.php     # cycle LED animation, queue PLAY_QUEUE_ANIMATION
  change_fog.php           # toggle CONFIG.fog
  lines_of_code.php        # hardcoded LoC counts shown in footer
  user_tables.sql          # USERS / USERRIGHTS / USERPROPERTIES schema
common_login/              # session-based auth + user admin (login/logout/admin_users*)
js/                        # functions.js (AJAX glue), psalme.js (footer "psalms")
framework/                 # jquery, jquery-ui, party.js, w3.css, fonts, css, images
sim/                       # "Digitaler Zwilling" — browser LED/button simulator
sql/pope.slq               # STATUS_LEDS table dump (simulator LED colors)
log.htm / test.htm         # static HTML mockups (log preview, button/party test)
```

## How a button click works (the core flow)

1. `index.php` renders buttons with `id` attributes (e.g. `#fog`, `#led`,
   `#inclight`, `#playhallelujah`, `#sound_button_0`…`#sound_button_99`).
2. `js/functions.js` binds handlers inside `$(document).ready` that fire jQuery
   `$.ajax` GET/POST calls to `lib/*.php`.
3. The `lib/*.php` endpoint checks `logged_in()`, connects to MariaDB, and either
   mutates `CONFIG`/`STATUS_LEDS` directly (fog, brightness, animation) or inserts
   a row into a **queue** table (`BUTTON_QUEUE`, `PLAY_QUEUE_ANIMATION`).
4. The C++ broker polls the queue tables and drives the real hardware.
5. `index.php` polls `lib/get_uptime.php` every 1s and `lib/get_sound_files.php`
   every 1s to refresh stats and populate the dynamic sound buttons.

## Database tables (in use by this repo)

- `USERS`, `USERRIGHTS`, `USERPROPERTIES` — auth + per-user rights/properties
  (schema in `lib/user_tables.sql`; created via `common_login/lib/*.php`)
- `CONFIG` — key/value settings: `brightness`, `fog`, `current_animation`,
  `animation0..N`
- `STATS` — key/value dashboard numbers (rotations, frequency, active LEDs,
  top users, most played sound, total actions)
- `SERMON` — `hist_an_zeit`, `action`, `reaction` rows rendered by `log.php`
- `USERS_ACTIONS` — per-user config: `home_directory`, `button_mac_address`
- `BUTTON_QUEUE` — queued `PLAYSOUND` commands (written by `play_file.php`)
- `PLAY_QUEUE_ANIMATION` — queued LED animation changes
- `STATUS_LEDS` — `name`/`value` LED colors (used by `sim/`)

## Conventions & gotchas

- **Auth gate:** every page/endpoint calls `logged_in()` from `lib/global.php`
  (checks `$_SESSION["USER"]["idx"]`); unauthenticated users are redirected to
  `common_login/login.php`. Don't add an endpoint without this check.
- **DB access:** call `connect_db()` (sets global `$mysqli`) before querying.
  Legacy code uses `addslashes()` rather than prepared statements — follow existing
  style, but prefer `$mysqli->prepare()` for new code (see `sim/lib/button_pressed.php`).
- **JSON endpoints** print JSON manually with `printf`, not `json_encode`. Keep the
  exact key names — the front-end maps them to element `id`s via
  `$('#' + key).text(value)` in `js/functions.js`.
- **Sound buttons** are dynamic: `get_sound_files.php` merges the user's
  `audio_main` + `audio_random` directories and returns `sound_button_N` keys.
  `parse_play_data()` hides buttons beyond the returned count and re-binds clicks.
- **Header/footer:** every page uses `print_header()` / `print_footer()` from
  `global.php`. `print_footer()` emits the psalm block, LoC table and loads
  `../js/psalme.js` (note the hardcoded `../` — index.php is at web root).
- **Tone:** UI copy uses religious puns. New labels should follow suit.

## Common tasks

- **Add a static action button:** add a `<button class="party" id="...">` in
  `index.php` (inside the `#actions.grid`), then bind it in `js/functions.js`
  with an `$.ajax` call to a new/existing `lib/*.php` endpoint.
- **Add a dashboard stat:** add a `<span class="highlight" id="newstat">` row in the
  `#stats` block of `index.php` and make sure `get_uptime.php` returns a matching
  `"newstat"` key.
- **Test LED/button logic without hardware:** open `sim/index.html` ("Digitaler
  Zwilling") — it reads/writes `STATUS_LEDS` directly.

See [references/architecture.md](references/architecture.md) for a deeper walkthrough
of every file and the request/response contracts.

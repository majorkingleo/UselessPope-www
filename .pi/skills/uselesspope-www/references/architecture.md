# Useless Pope — Architecture Reference

Deeper walkthrough of the repo, generated from source analysis (Oct 2025 codebase).

## Front-end: `index.php`

Sections in order (each `h2`):

| Heading | Element | Purpose |
|---|---|---|
| Litaneien | `#actions.grid` | Fixed buttons: `#fog` (white smoke), `#led` (change animation), `#inclight`, `#declight` |
| Magnificat | `.banner` | Text input `#banner` + `#save_banner` to set the scrolling LAN-party banner |
| Psalm erklingen lassen | `#actions.grid` | 100 dynamic buttons `#sound_button_0`…`#sound_button_99` + `#playhallelujah`, `#playmotor` |
| Apostelberichte | `#stats` | Live stats rows (see below) |
| Protokoll der Predigt | `iframe#eventlog` | loads `log.php`, auto-reloads every 60s |
| Sakristei | `#admin` | links to `common_login/logout.php` and `admin_myself.php` |

Inline `<script>` in `index.php` handles:
- **party.js sparkles** on every `.party` click.
- **Modal plumbing** (`openmodal`/`hidemodal`/`closemodal`) via browser history
  (`pushState`/`popstate`) — used by older pages, kept for compatibility.
- **LED jitter animation** — an IIFE that reads `#active-leds-data` (hidden span),
  crawls `#active-leds` toward the real value with easing, adds a bounded random
  walk, pads to 4 digits (`0000`), and refreshes every 100 ms. Real value pulled
  from the server every 10 s via `window.updateActiveLeds()`.
- **Uptime formatter** — reads hidden `#uptimevalue` (seconds), renders
  "X Tage, Y Stunden, Z Minuten" (German plurals), watches it with a
  `MutationObserver`.
- **iframe reload** every 60 s.

### Stats rows (`#stats`)

`boottime`, `uptime` (wraps `#uptimevalue`), `umdrehungen` (rotations, blinking),
`frequenz`, `active-leds` (+ hidden `#active-leds-data`), `helligkeit` (brightness),
`cputemp`, `user1`…`user3` (top believers), `mostplayedsound`, `totalactions`.

## Front-end glue: `js/functions.js`

- `fetch_uptime_data()` → `lib/get_uptime.php` every 1 s → `parse_uptime_data()` maps
  JSON keys to `$('#'+key).text(value)`.
- `fetch_play_data()` → `lib/get_sound_files.php` every 1 s → `parse_play_data()`:
  fills each `#sound_button_N` span, shows it, rebinds click to POST
  `lib/play_file.php` with `file=<encoded filename>`, then hides `sound_button_N`
  beyond the returned count.
- Fixed button bindings (inside the `if( $("#uptime").length )` guard):
  - `#inclight`/`#declight` → GET `change_brightness.php?incbrightness=±0.02`
  - `#playmotor` → POST `play_file.php` `file=/home/papst/audio_motor/Humvee_Idle.mp3`
  - `#led` → POST `change_animation.php`
  - `#fog` → POST `change_fog.php`
  - `#playhallelujah` → POST `play_file.php`
    `file=/home/papst/audio_music/Worms - HOLYGRENADE.WAV`

## Footer psalms: `js/psalme.js`

Runs on every page via `print_footer()`. Picks a random German+Latin tech/religion
pun from an array, renders it into `#latein`, `#deutsch`, plus a random
"Evangelium von Pi K:V" into `#evangelium`. Cross-fades with jQuery and refreshes
every 100 s.

## Backend: `lib/`

### `global.php` (the backbone)

- Sets timezone `Europe/Vienna`, defines `AUDIO_MAIN_DIRECTORY` / `AUDIO_RANDOM_DIRECTORY`.
- `$SITE_NAME = "Useless Papst &middot; Teekränzchen LIVE"`.
- No-cache headers, `session_start()`, locale `de_AT`.
- `connect_db()` → `new mysqli('localhost', $DATABASE, $DBPASSWD, $DATABASE)`.
- `print_header($menu_name, $rel, ...)` emits full `<head>` (W3.CSS, custom CSS,
  jquery, jquery-ui, party.js, functions.js) with `$rel` prefix for subdirectories.
- `print_footer()` emits `#psalm`, LoC table (via `print_lines_of_code()`), logo,
  runtime comment, and loads `../js/psalme.js`.
- `logged_in()`, `check_login()`, `is_admin()`, `mysql_filter_input()`, misc helpers.

### Endpoints

- **`get_uptime.php`** — reads `/proc/uptime`, computes boot time
  (`strftime("%A, %e. %B %Y %T")`), runs `sudo vcgencmd measure_temp` for CPU temp,
  then prints a JSON object: `uptimevalue`, `boottime`, `cputemp`, every `STATS`
  row, and `helligkeit` (brightness%).
- **`get_sound_files.php`** — looks up the logged-in user's `home_directory` in
  `USERS_ACTIONS`, merges `scandir()` of `audio_main` + `audio_random`, returns
  `{ "sound_button_0": "<file>", ... }`.
- **`play_file.php`** — inserts a `PLAYSOUND` row into `BUTTON_QUEUE` with the
  user's `button_mac_address`, `ip_address="website"`, and the chosen file.
- **`change_brightness.php`** — reads `CONFIG.brightness`, adds `incbrightness`
  (clamped 0.02…0.8), writes back.
- **`change_animation.php`** — increments `CONFIG.current_animation` (wraps to 0),
  reads `CONFIG.animationN` for the file, inserts into `PLAY_QUEUE_ANIMATION`.
- **`change_fog.php`** — toggles `CONFIG.fog` (0/1) and stamps `hist_ae_zeit` /
  `hist_ae_user`.

## Auth & admin: `common_login/`

- `login.php` — form posting username/password to `lib/logon.php` via AJAX; on
  success (`reqCode == 1`) redirects to `../index.php`.
- `lib/logon.php` — verifies against `USERS` with MariaDB `PASSWORD()` hashing,
  sets `$_SESSION["USER"]` + `$_SESSION["USERRIGHTS"]`, tracks `failed_count`,
  locks account after 5 failures within an hour.
- `lib/user_rights.php` — `USERRIGHTS` constants (EDIT/CREATE/DEL_OTHER_USERS,
  DO_BACKUP) and helpers.
- `lib/userdata.php` — `USERPROPERTIES` (forename/surename/title) helpers.
- `admin_users.php`, `admin_users_create_user.php`, `admin_users_edit_user.php` —
  user administration (list/create/edit).
- `admin_myself.php` — change own password + profile data.
- `lib/check_password.php` / `lib/change_password.php` / `lib/save_userdata.php` —
  AJAX handlers for the above.
- `lib/set_smb_passwd.sh` — shell helper to sync SMB password (admin).

## Simulator: `sim/` ("Digitaler Zwilling")

A standalone HTML page that mimics the physical Pope for testing without hardware:

- `index.html` — 10 buttons `#b1`…`#b10` and 10 LED divs `#l1`…`#l10` + a pope image.
- `lib/functions.js` — binds button clicks to `button_pressed.php?id=`, polls
  `get_led_status.php` every 1 s and sets each LED's `background-color`.
- `lib/button_pressed.php` — maps `b10`→`l10`, toggles `STATUS_LEDS.value` between
  green (`0x00FF00`) and white (`0xFFFFFF`) with a prepared statement.
- `lib/get_led_status.php` — returns `{ "l1": "#RRGGBB", ... }` from `STATUS_LEDS`.
- `lib/global.php` — its own minimal mysqli connection (duplicated credentials).

## Styling: `framework/style_2025-10-24.css`

- Dark theme via CSS variables (`--backgroundbody: #080808`, etc.).
- `#tk` container gets an animated **conic-gradient rainbow border** (spin 10s) with
  a blurred glow layer.
- `h1` has an animated rainbow gradient text; `h2::after` / `footer::before` draw
  animated gradient divider lines.
- Buttons: 6 rotating gradient hover fills via `:nth-child(6n+N)::after`, gradient
  animation `button_gradient 15s`.
- `.blink_me` blinking stats; `table.log` styles the sermon log; `.beer` renders
  "holy beer" tooltip over struck-through "tea".
- Responsive breakpoints at 1100px and 500px.

## Data flow for a "PLAYSOUND" action

```
browser click → js/functions.js → lib/play_file.php
  → INSERT INTO BUTTON_QUEUE (time_stamp, mac_address, action='PLAYSOUND', file, hist_*_user)
→ C++ broker polls BUTTON_QUEUE → Raspberry Pi plays audio
→ broker/Pi writes reaction back to SERMON
→ log.php renders SERMON rows (iframe auto-refreshes every 60 s)
```

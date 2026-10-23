---
name: uselesspope-database
description: Complete MariaDB schema and conventions for the Useless Pope project — the shared "papst" database used by the PHP web front-end (UselessPope-www), the C++ broker (UselessPope-Broker) and the Python Raspberry Pi scripts (UselessPope-raspi). Use when adding/altering tables, writing queries, or understanding CONFIG/STATS keys and the command queues.
---

# Useless Pope — Database Schema

One MariaDB instance, database **`papst`**, is shared by all three components:

- **UselessPope-www** (PHP) — login, dashboard, and the AJAX endpoints that enqueue commands.
- **UselessPope-Broker** (C++) — polls the queues, plays audio, runs animations, writes back `SERMON`/`STATS`.
- **UselessPope-raspi** (Python) — LED animations + GPIO handlers, reads/writes `CONFIG`/`STATS`.

## Connection

- Host: `localhost` (Unix socket `/run/mysqld/mysqld.sock`)
- User / password: `papst` / `johannespaul` (also in `~/UselessPope-Broker.ini` `[database]`)
- Database: `papst`
- Engine: InnoDB, default charset `utf8`/`utf8mb3` (only `STATUS_LEDS` uses `utf8mb4`)

Authoritative schema source: **`/home/papst/UselessPope-Broker/create.sql`**
(the broker also prints it via `./broker -create-sql`). The `USERS`/`USERRIGHTS`/
`USERPROPERTIES` tables come from `UselessPope-www/lib/user_tables.sql` instead.

## Table overview (16 tables)

| Table | Rows (live) | Purpose | Written by |
|-------|------------|---------|------------|
| `BUTTON_QUEUE` | 0 | active command queue (button/playsound) | www `play_file.php`, broker `-enqueue-*` |
| `P_BUTTON_QUEUE` | 41 864 | processed button-command archive | broker |
| `PLAY_QUEUE_ANIMATION` | 1 | active animation queue | www `change_animation.php`, python `ring_animation.py` |
| `P_PLAY_QUEUE_ANIMATION` | 593 | processed animation archive | broker |
| `PLAY_QUEUE_CHUNKS` | 0 | active sound-chunk queue | broker `-enqueue-chunk` |
| `P_PLAY_QUEUE_CHUNKS` | 4 197 | processed chunk archive (+`sermon_reaction_idx`) | broker |
| `PLAY_QUEUE_MUSIC` | 0 | active music queue | broker `-enqueue-music` |
| `P_PLAY_QUEUE_MUSIC` | 124 | processed music archive | broker |
| `CONFIG` | 16 | runtime settings (key/value) | www, python `config.py`, broker (seeds) |
| `STATS` | 11 | counters/computed stats (key/value) | python animations, broker `FetchStats` |
| `STATUS_LEDS` | 10 | LED colors for the simulator | www `sim/` |
| `SERMON` | 3 719 | event log (action → reaction) | broker |
| `USERS` | 11 | auth (username/password/lockout) | www `common_login/` |
| `USERRIGHTS` | 0 | per-user rights | www admin |
| `USERPROPERTIES` | 5 | per-user profile (name/title) | www admin |
| `USERS_ACTIONS` | 11 | per-user hardware mapping (MAC, home dir, events) | broker |

## Conventions

### The `hist_*` audit columns

Every table **except** `USERS`, `USERRIGHTS`, `USERPROPERTIES` and `STATUS_LEDS`
carries six standard audit columns with identical semantics:

```sql
hist_an_zeit DATETIME DEFAULT current_timestamp()  -- created at
hist_an_user VARCHAR(20) DEFAULT ''                -- created by
hist_ae_zeit DATETIME DEFAULT current_timestamp()  -- edited at
hist_ae_user VARCHAR(20) DEFAULT ''                -- edited by
hist_lo_zeit DATETIME DEFAULT current_timestamp()  -- "deleted"/archived at
hist_lo_user VARCHAR(20) DEFAULT ''                -- archived by
```

On INSERT set `hist_an_*` (and often all three user columns, e.g.
`hist_an_user, hist_ae_user, hist_lo_user`). On UPDATE stamp `hist_ae_zeit` +
`hist_ae_user` only. No row is ever physically deleted; soft-archive via the
`hist_lo_*` columns.

### The `P_*` archive tables

Each **active queue** has a `P_`-prefixed archive twin with an identical (or
superset) schema. The broker consumes rows from the active queue and moves them
to the `P_` table to keep the active queue small:

```
BUTTON_QUEUE            -> P_BUTTON_QUEUE
PLAY_QUEUE_ANIMATION    -> P_PLAY_QUEUE_ANIMATION
PLAY_QUEUE_CHUNKS       -> P_PLAY_QUEUE_CHUNKS   (+ sermon_reaction_idx)
PLAY_QUEUE_MUSIC        -> P_PLAY_QUEUE_MUSIC
```

`P_PLAY_QUEUE_CHUNKS` adds `sermon_reaction_idx INT NOT NULL DEFAULT 0` (indexed),
linking an archived chunk to the `SERMON` row that reacted to it.

### Key/value tables

`CONFIG` and `STATS` share the same shape (`idx`, 6×`hist_*`, `key` VARCHAR(50),
`value` VARCHAR(1024)); `CONFIG.key` is `UNIQUE`. `STATUS_LEDS` is a minimal
key/value table (`name` PK, `value` INT = 0xRRGGBB color).

## Current CONFIG keys (live inventory)

| key | value | meaning |
|-----|-------|---------|
| `brightness` | `0.040000` | global LED brightness (0.02–0.8), hot-reloaded |
| `current_animation` | `5` | index of the active animation |
| `fog` | `0` | fog machine state (0/1) |
| `animation0` | `…/pope_default_rotating_color_wheel.py` | animation file for index 0 |
| `animation1` | `…/pope_red_eyes.py` | |
| `animation2` | `…/matrix_rain.py` | |
| `animation3` | `…/snake_duel.py` | |
| `animation4` | `…/screen_off.py` | |
| `animation5` | `…/text_scroll.py` | text marquee (scroll text) |
| `animation0_title`..`animation5_title` | e.g. `Johannes Paul II`, `LAN 2.1` | web UI titles |
| `scroll_text` | `LAN 2.1` | message shown by `text_scroll.py` |

## Current STATS keys

`umdrehungen` (rotations), `frequenz` (RPM), `active-leds-data` (panel mask),
`mostplayedsound`, `user1`/`user2`/`user3` (top believers), `totalactions`,
`snake_p1_wins`/`snake_p2_wins`, `animation0_umdrehungen`/`animation1_umdrehungen`.

## Command flow through the queues

1. Web button / remote button → row inserted into `BUTTON_QUEUE`
   (`action` ∈ `PLAYSOUND`, `ButtonPressed`, `ButtonReleased`, `Ping`).
2. Broker drains `BUTTON_QUEUE` → performs the action → writes a `SERMON` row
   (`action` = what happened, `reaction` = the Pope's response) → archives to
   `P_BUTTON_QUEUE`.
3. Animation change → `PLAY_QUEUE_ANIMATION` row → broker stops the running
   animation process and launches the new file → archives to `P_PLAY_QUEUE_ANIMATION`.

For the full column-level schema and per-table detail, see
[references/schema.md](references/schema.md).

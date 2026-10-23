# Full schema (live dump + create.sql)

Column types below come from `SHOW CREATE TABLE` on the live `papst` DB and match
`/home/papst/UselessPope-Broker/create.sql`. All tables are InnoDB,
`utf8`/`utf8mb3` (`STATUS_LEDS` is `utf8mb4`).

## Command queues

### BUTTON_QUEUE (active) / P_BUTTON_QUEUE (archive)

```sql
CREATE TABLE `BUTTON_QUEUE` (
  `idx`           int(11)      NOT NULL AUTO_INCREMENT,
  `hist_an_zeit`  datetime     NOT NULL DEFAULT current_timestamp(),
  `hist_an_user`  varchar(20)  NOT NULL DEFAULT '',
  `hist_ae_zeit`  datetime     NOT NULL DEFAULT current_timestamp(),
  `hist_ae_user`  varchar(20)  NOT NULL DEFAULT '',
  `hist_lo_zeit`  datetime     NOT NULL DEFAULT current_timestamp(),
  `hist_lo_user`  varchar(20)  NOT NULL DEFAULT '',
  `time_stamp`    int(11)      NOT NULL,
  `seq`           int(11)      NOT NULL,
  `mac_address`   varchar(17)  NOT NULL,       -- remote-button MAC, or 'website'
  `ip_address`    varchar(15)  NOT NULL,
  `action`        varchar(50)  NOT NULL,       -- PLAYSOUND | ButtonPressed | ButtonReleased | Ping
  `file`          varchar(1024) NOT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB;
```

`action` values observed: `PLAYSOUND` (from the web UI / broker enqueue),
`ButtonPressed`, `ButtonReleased` (from physical remote buttons), `Ping`
(heartbeat), and empty (legacy). `ip_address` is `"website"` for web-originated
commands. `P_BUTTON_QUEUE` is schema-identical.

### PLAY_QUEUE_ANIMATION (active) / P_PLAY_QUEUE_ANIMATION (archive)

```sql
CREATE TABLE `PLAY_QUEUE_ANIMATION` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  -- 6x hist_* audit columns (see SKILL.md)
  `file` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB;
```

`file` is the absolute path to the Python animation script to launch.
`P_PLAY_QUEUE_ANIMATION` is schema-identical.

### PLAY_QUEUE_CHUNKS / PLAY_QUEUE_MUSIC (active) + P_* archives

```sql
CREATE TABLE `PLAY_QUEUE_CHUNKS` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  -- 6x hist_* audit columns
  `file` varchar(1024) NOT NULL,
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB;
```

`PLAY_QUEUE_MUSIC` is identical. The `P_` archive twins are identical **except**
`P_PLAY_QUEUE_CHUNKS`, which adds:

```sql
`sermon_reaction_idx` int(11) NOT NULL DEFAULT 0,
KEY `sermon_reaction_idx` (`sermon_reaction_idx`),
KEY `hist_an_zeit` (`hist_an_zeit`)
```

## Settings & counters

### CONFIG

```sql
CREATE TABLE `CONFIG` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  -- 6x hist_* audit columns
  `key`   varchar(50)   NOT NULL,
  `value` varchar(1024) NOT NULL,
  PRIMARY KEY (`idx`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB;
```

Runtime settings; `key` is unique. See the CONFIG key inventory in SKILL.md.

### STATS

Identical to `CONFIG` (6×`hist_*`, `key` varchar(50), `value` varchar(1024),
`idx` PK) but `key` is **not** declared UNIQUE — it is a free-form counter store.

### STATUS_LEDS

```sql
CREATE TABLE `STATUS_LEDS` (
  `name`  varchar(20) NOT NULL,   -- l1..l10
  `value` int(11)     NOT NULL,   -- 0xRRGGBB (e.g. 65280 = green)
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

Used only by the web simulator (`UselessPope-www/sim/`), not by the physical
LEDs.

## Event log

### SERMON

```sql
CREATE TABLE `SERMON` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  -- 6x hist_* audit columns
  `action`   varchar(1024) NOT NULL DEFAULT '',
  `reaction` varchar(1024) NOT NULL DEFAULT '',
  PRIMARY KEY (`idx`)
) ENGINE=InnoDB;
```

Rendered by `UselessPope-www/log.php` as the "Protokoll der Predigt". Example:
`action = "rotane hat zysten abgespielt."`,
`reaction = "Der Papst überlegt: …"`.

## Users & permissions (from lib/user_tables.sql)

### USERS

```sql
CREATE TABLE `USERS` (
  `idx`                int(11)      NOT NULL AUTO_INCREMENT,
  `username`           varchar(255) NOT NULL,
  `password`           varchar(255) NOT NULL,   -- MariaDB PASSWORD() hash
  `creation_time`      timestamp    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `modification_time`  timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locked`             tinyint(1)   NOT NULL DEFAULT 0,
  `failed_count`       int(11)      NOT NULL DEFAULT 0,
  `last_failed_login`  timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_login`         timestamp    NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idx`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_USERS_username` (`username`)
) ENGINE=InnoDB;
```

Login is verified with `password=PASSWORD('<pw>') AND locked=0` (see
`common_login/lib/logon.php`). Account locks after 5 failures within an hour.

### USERRIGHTS

```sql
CREATE TABLE `USERRIGHTS` (
  `idx`      int(11)      NOT NULL AUTO_INCREMENT,
  `user_idx` int(11)      NOT NULL,
  `name`     varchar(255) NOT NULL,   -- EDIT_OTHER_USERS | DEL_OTHER_USERS | CREATE_OTHER_USERS | DO_BACKUP
  `value`    varchar(255) NOT NULL,
  PRIMARY KEY (`idx`),
  KEY `user_idx` (`user_idx`),
  CONSTRAINT `USERRIGHTS_ibfk_1` FOREIGN KEY (`user_idx`) REFERENCES `USERS` (`idx`) ON DELETE CASCADE,
  CONSTRAINT `USERRIGHTS_ibfk_2` FOREIGN KEY (`user_idx`) REFERENCES `USERS` (`idx`) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### USERPROPERTIES

```sql
CREATE TABLE `USERPROPERTIES` (
  `idx`      int(11)      NOT NULL AUTO_INCREMENT,
  `user_idx` int(11)      NOT NULL,
  `name`     varchar(255) NOT NULL,   -- forename | surename | title
  `value`    varchar(255) NOT NULL,
  PRIMARY KEY (`idx`),
  KEY `user_idx` (`user_idx`),
  CONSTRAINT `USERPROPERTIES_ibfk_1` FOREIGN KEY (`user_idx`) REFERENCES `USERS` (`idx`) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### USERS_ACTIONS

Maps each user to their physical remote button and home audio directory.

```sql
CREATE TABLE `USERS_ACTIONS` (
  `idx` int(11) NOT NULL AUTO_INCREMENT,
  -- 6x hist_* audit columns
  `username`           varchar(20)  NOT NULL,          -- FK -> USERS.username
  `button_mac_address` varchar(17)  NOT NULL,
  `home_directory`     varchar(256) NOT NULL,          -- e.g. /home/majorleo
  `button_press_event0` varchar(50)  NOT NULL,         -- sound played on button press
  `button_press_event1` varchar(50)  NOT NULL,
  `button_press_event2` varchar(50)  NOT NULL,
  `button_press_event3` varchar(50)  NOT NULL,
  `button_press_event4` varchar(50)  NOT NULL,
  `play_chunk0` varchar(1024) NOT NULL,                -- soundboard chunks
  `play_chunk1` varchar(1024) NOT NULL,
  `play_chunk2` varchar(1024) NOT NULL,
  `play_chunk3` varchar(1024) NOT NULL,
  `play_chunk4` varchar(1024) NOT NULL,
  PRIMARY KEY (`idx`),
  KEY `username` (`username`),
  CONSTRAINT `USERS_ACTIONS_ibfk_1` FOREIGN KEY (`username`) REFERENCES `USERS` (`username`),
  CONSTRAINT `USERS_ACTIONS_ibfk_2` FOREIGN KEY (`username`) REFERENCES `USERS` (`username`),
  CONSTRAINT `USERS_ACTIONS_ibfk_3` FOREIGN KEY (`username`) REFERENCES `USERS` (`username`)
) ENGINE=InnoDB;
```

`get_sound_files.php` reads `home_directory` to scan `audio_main` + `audio_random`
subdirectories and list them as soundboard buttons.

## Foreign-key map

- `USERRIGHTS.user_idx` → `USERS.idx` (CASCADE)
- `USERPROPERTIES.user_idx` → `USERS.idx` (CASCADE)
- `USERS_ACTIONS.username` → `USERS.username` (×3, redundant)
- No other FKs; queues/config/stats/sermon are linked only by convention
  (`mac_address`, `username`, `sermon_reaction_idx`).

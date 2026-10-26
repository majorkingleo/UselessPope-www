<?php
include_once( 'lib/global.php' );

if( !logged_in() ) {
    header( "Location: common_login/login.php" );
    exit(0);
}

define( 'BF_STATIC_DIR', __DIR__ . '/static' );

/*
 * Render one of the Battlefield pages into a string. The working directory is
 * set to the web root so their relative includes keep working.
 */
function bf_static_render_page( $file )
{
    global $mysqli;

    $cwd = getcwd();
    chdir( __DIR__ );

    ob_start();
    include $file;
    $html = ob_get_clean();

    chdir( $cwd );

    return $html;
}

// get_bf_stats.php is an endpoint, but its JSON is exactly the snapshot we
// need for the archived page.
function bf_static_render_stats()
{
    global $mysqli;

    $cwd = getcwd();
    chdir( __DIR__ . '/lib' );

    ob_start();
    include 'get_bf_stats.php';
    $json = ob_get_clean();

    chdir( $cwd );

    $data = json_decode( $json, true );

    return is_array( $data ) ? $data : null;
}

// Everything the archived page needs to render without the live installation.
function bf_static_assets()
{
    return array(
        "favicon.ico",
        "framework/w3.css",
        "framework/style_2026-10-24.css",
        "framework/jquery-3.7.1.js",
        "framework/jquery-ui-1.14.1/jquery-ui.min.js",
        "framework/party-2.2.0.min.js",
        "framework/roboto-mono-v22-latin-400.woff",
        "framework/roboto-mono-v22-latin-400.woff2",
        "framework/roboto-mono-v22-latin-600.woff",
        "framework/roboto-mono-v22-latin-600.woff2",
        "framework/logophotocopyvector_2-1.png",
        "js/functions.js",
        "js/psalme.js",
    );
}

function bf_static_copy_assets( $target_dir )
{
    foreach( bf_static_assets() as $asset ) {
        $source = __DIR__ . '/' . $asset;
        $target = $target_dir . '/' . $asset;
        $target_dirname = dirname( $target );

        if( !is_dir( $target_dirname ) &&
            !@mkdir( $target_dirname, 0775, true ) &&
            !is_dir( $target_dirname ) ) {
            return "Der Ordner für " . $asset . " konnte nicht erstellt werden.";
        }

        if( !is_file( $source ) || !@copy( $source, $target ) ) {
            return "Die Datei " . $asset . " konnte nicht kopiert werden.";
        }
    }

    return "";
}

/*
 * The archive lives in static/bf-DATUM/, so links back into the live site need
 * one more level and its scripts sit next to the archived page. This applies to
 * bf.php as well as to the game log embedded from bf_log.php.
 */
function bf_static_rewrite_links( $html )
{
    return str_replace(
        array(
            'src="../js/psalme.js"',
            'href="index.php"',
            'href="bf.php"',
            'href="static.php"',
            'href="common_login/',
        ),
        array(
            'src="js/psalme.js"',
            'href="../../index.php"',
            'href="../../bf.php"',
            'href="../../static.php"',
            'href="../../common_login/',
        ),
        $html
    );
}

/*
 * The archive is a snapshot, so the live round section would only show stale
 * values. Remove "Aktuelles Game" together with its #stats block.
 */
function bf_static_remove_live_game( $html )
{
    return preg_replace(
        '#\s*<h2>\s*Aktuelles Game\s*</h2>.*?(?=\s*<h2>)#s',
        '',
        $html,
        1
    );
}

/*
 * The archived page does not need the live team preview boxes in front of the
 * game log. They belong to the interactive "Team vorschlagen" feature.
 */
function bf_static_remove_team_boxes( $html )
{
    return preg_replace(
        '#\s*<div\s+id="bf-teams"[^>]*>.*?(?=\s*<h2>\s*Game Log\s*</h2>)#s',
        '',
        $html,
        1
    );
}

/*
 * The Sakristei contains login-only actions, so it makes no sense in an
 * archived snapshot.
 */
function bf_static_remove_sakristei( $html )
{
    return preg_replace(
        '#\s*<h2>\s*Sakristei\s*</h2>.*?(?=\s*</main>)#s',
        '',
        $html,
        1
    );
}

function bf_static_build_page( $stats, $log_html )
{
    $html = bf_static_rewrite_links( bf_static_render_page( 'bf.php' ) );
    $html = bf_static_remove_live_game( $html );
    $html = bf_static_remove_team_boxes( $html );
    $html = bf_static_remove_sakristei( $html );

    // The values of the removed live sections are not needed in the archive.
    foreach( array( "bf_map", "bf_hoster", "bf_numplayers", "bf_tickets",
                    "bf_round_time", "bf_online", "bf_teams" ) as $key ) {
        unset( $stats[ $key ] );
    }

    $json = json_encode(
        $stats,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES |
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    );

    if( $json === false ) {
        return null;
    }

    $html = str_replace(
        '</head>',
        '<script>window.BF_STATIC = true; window.BF_STATIC_DATA = ' . $json . ';</script>' . "\n</head>",
        $html
    );

    if( $log_html !== null ) {
        // The game log is embedded as srcdoc so the archive stays a single
        // file. Its own relative links have to point at the archive too.
        $srcdoc = htmlspecialchars(
            bf_static_rewrite_links( $log_html ),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        // Replace the live iframe regardless of attribute order or any future
        // attribute changes in bf.php, but keep its existing attributes.
        $html = preg_replace_callback(
            '#<iframe\b([^>]*\bid="game-log"[^>]*)>\s*</iframe>#i',
            function( $matches ) use ( $srcdoc ) {
                $attributes = preg_replace(
                    '/\s+(?:src|srcdoc)\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i',
                    '',
                    $matches[1]
                );

                return '<iframe' . $attributes . ' srcdoc="' . $srcdoc . '"></iframe>';
            },
            $html
        );
    }

    return $html;
}

function bf_static_delete_dir( $dir )
{
    if( is_link( $dir ) || is_file( $dir ) ) {
        return @unlink( $dir );
    }

    if( !is_dir( $dir ) ) {
        return true;
    }

    $entries = @scandir( $dir );

    if( $entries === false ) {
        return false;
    }

    foreach( $entries as $entry ) {
        if( $entry === "." || $entry === ".." ) {
            continue;
        }

        $path = $dir . "/" . $entry;

        if( is_dir( $path ) && !is_link( $path ) ) {
            if( !bf_static_delete_dir( $path ) ) {
                return false;
            }
        } else if( !@unlink( $path ) ) {
            return false;
        }
    }

    return @rmdir( $dir );
}

function bf_static_archives()
{
    if( !is_dir( BF_STATIC_DIR ) ) {
        return array();
    }

    $archives = array();
    $entries = @scandir( BF_STATIC_DIR );

    if( $entries === false ) {
        return $archives;
    }

    foreach( $entries as $name ) {
        $path = BF_STATIC_DIR . '/' . $name;

        if( is_dir( $path ) && preg_match( '/^bf-\d{4}-\d{2}-\d{2}$/', $name ) ) {
            $index = $path . '/index.html';

            if( is_file( $index ) ) {
                $archives[] = array(
                    "name"  => $name,
                    "type"  => "dir",
                    "href"  => "static/" . rawurlencode( $name ) . "/",
                    "mtime" => filemtime( $index ),
                );
            }
        } else if( is_file( $path ) && preg_match( '/^bf-\d{4}-\d{2}-\d{2}\.html$/', $name ) ) {
            // Keep older single-file archives visible.
            $archives[] = array(
                "name"  => $name,
                "type"  => "file",
                "href"  => "static/" . rawurlencode( $name ),
                "mtime" => filemtime( $path ),
                "size"  => filesize( $path ),
            );
        }
    }

    usort( $archives, function( $a, $b ) {
        $cmp = strcmp( $b["name"], $a["name"] );

        return $cmp !== 0 ? $cmp : strcmp( $a["type"], $b["type"] );
    } );

    return $archives;
}

$message = "";
$error = "";

if( isset( $_SERVER["REQUEST_METHOD"] ) && $_SERVER["REQUEST_METHOD"] === "POST" && isset( $_POST["generate"] ) ) {
    if( !is_dir( BF_STATIC_DIR ) ) {
        @mkdir( BF_STATIC_DIR, 0775, true );
    }

    if( !is_dir( BF_STATIC_DIR ) || !is_writable( BF_STATIC_DIR ) ) {
        $error = "Der Ordner static/ ist nicht beschreibbar.";
    } else {
        $folder_name = "bf-" . date( "Y-m-d" );
        $folder = BF_STATIC_DIR . "/" . $folder_name;
        $path = $folder . "/index.html";
        $existed = file_exists( $folder ) || is_link( $folder );

        // Always start from a clean folder, so removed files or markup from
        // older generator versions do not survive in today's archive.
        if( $existed && !bf_static_delete_dir( $folder ) ) {
            $error = "Der Ordner static/" . $folder_name . "/ konnte nicht gelöscht werden.";
        }

        if( $error === "" &&
            !is_dir( $folder ) &&
            !@mkdir( $folder, 0775, true ) &&
            !is_dir( $folder ) ) {
            $error = "Der Ordner static/" . $folder_name . "/ konnte nicht erstellt werden.";
        }

        if( $error === "" ) {
            $asset_error = bf_static_copy_assets( $folder );
            $stats = bf_static_render_stats();

            if( $asset_error !== "" ) {
                $error = $asset_error;
            } else if( $stats === null ) {
                $error = "Die Battlefield-Statistik konnte nicht geladen werden.";
            } else {
                $log_html = bf_static_render_page( 'bf_log.php' );
                $html = bf_static_build_page( $stats, $log_html );

                if( $html === null ) {
                    $error = "Die statische Version konnte nicht erzeugt werden.";
                } else if( file_put_contents( $path, $html, LOCK_EX ) === false ) {
                    $error = "Die statische Version konnte nicht gespeichert werden.";
                } else {
                    $message = sprintf(
                        '"static/%s/" wurde %s.',
                        $folder_name,
                        $existed ? "neu erzeugt" : "erzeugt"
                    );
                }
            }
        }
    }
}

$archives = bf_static_archives();

print_header();
?>

<body lang="de" id="papst">

<div class="w3-content w3-container" id="tk">

<main>

  <h1>Statische Versionen</h1>

  <div id="admin">
    <a href="bf.php"><button><span>Zurück zum Useless Battlefield</span></button></a>
    <a href="index.php"><button><span>Zurück zum Useless Papst</span></button></a>
  </div>

  <h2>Archiv erzeugen</h2>

  <p>Erzeugt ein eigenständiges Archiv inklusive CSS, JavaScript und Schriften.</p>

  <?php if( $message !== "" ): ?>
    <p class="highlight"><?php echo htmlspecialchars( $message, ENT_QUOTES, 'UTF-8' ); ?></p>
  <?php endif; ?>

  <?php if( $error !== "" ): ?>
    <p><strong>Fehler:</strong> <?php echo htmlspecialchars( $error, ENT_QUOTES, 'UTF-8' ); ?></p>
  <?php endif; ?>

  <form method="post">
    <button type="submit" name="generate" value="1"><span>Statische Version von Battlefield erzeugen</span></button>
  </form>

  <h2>Bereits erzeugte Versionen</h2>

  <?php if( count( $archives ) === 0 ): ?>
    <p>Noch keine statischen Versionen vorhanden.</p>
  <?php else: ?>
    <ul id="static-list">
    <?php foreach( $archives as $archive ): ?>
      <li>
        <a href="<?php echo htmlspecialchars( $archive["href"], ENT_QUOTES, 'UTF-8' ); ?>"><?php echo htmlspecialchars( $archive["name"], ENT_QUOTES, 'UTF-8' ); ?></a>
        <span>
          <?php echo date( "d.m.Y H:i", $archive["mtime"] ); ?>
          <?php if( $archive["type"] === "file" ): ?>
            &middot; <?php echo number_format( $archive["size"] / 1024, 1, ",", "." ); ?> KiB
          <?php endif; ?>
        </span>
      </li>
    <?php endforeach; ?>
    </ul>
  <?php endif; ?>

</main>

<?php print_footer(); ?>

</div>

</body>

</html>
